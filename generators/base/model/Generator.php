<?php

namespace yii\gii\plus\generators\base\model;

use yii\helpers\Inflector;
use ReflectionClass;
use Yii;
use yii\gii\generators\model\Generator as YiiGiiModelGenerator;

class Generator extends YiiGiiModelGenerator
{

    public $ns = 'app\models\base';
    public $tableName = '*';
    public $baseClass = 'yii\boost\db\ActiveRecord';
    public $generateLabelsFromComments = true;
    public $useSchemaName = false;
    public $generateQuery = true;
    public $queryNs = 'app\models\query\base';
    public $queryBaseClass = 'yii\boost\db\ActiveQuery';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Base Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a base ActiveRecord class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'query.php'];
    }

    /**
     * @inheritdoc
     */
    public function defaultTemplate()
    {
        $class = new ReflectionClass(get_parent_class(__CLASS__));
        return dirname($class->getFileName()) . '/default';
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (preg_match('~^([^\\\\]+\\\\)models~', $this->ns, $match)) {
            $this->queryNs = preg_replace('~^([^\\\\]+\\\\)models~', $match[1] . 'models', $this->queryNs);
        }
        if (preg_match('~models(\\\\(?:[^\\\\]+\\\\)+)base$~', $this->ns, $match)) {
            $this->queryNs = preg_replace('~models(\\\\(?:[^\\\\]+\\\\)*)query\\\\base$~', 'models' . $match[1] . 'query\base', $this->queryNs);
        }
        return parent::beforeValidate();
    }

    /**
     * @var string|null
     */
    private $_userBaseClass = null;

    /**
     * @var string|null
     */
    private $_userQueryBaseClass = null;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->_userBaseClass = $this->baseClass;
        $this->_userQueryBaseClass = $this->queryBaseClass;
        $files = parent::generate();
        $this->_userBaseClass = null;
        $this->_userQueryBaseClass = null;
        return $files;
    }

    /**
     * @inheritdoc
     */
    protected function generateRelations()
    {
        $allRelations = [];
        foreach (parent::generateRelations() as $tableName => $relations) {
            $allRelations[$tableName] = [];
            foreach ($relations as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                $nsClassName = $this->ns . '\\' . $className;
                $nsClassName2 = preg_replace('~\\\\base$~', '', $this->ns) . '\\' . preg_replace('~Base$~', '', $className);
                if (($nsClassName != $nsClassName2) && class_exists($nsClassName2) && (get_parent_class($nsClassName2) == $nsClassName)) {
                    /* @var $nsClassName2 string|\yii\db\ActiveRecord */
                    if ($nsClassName2::tableName() == $tableName) {
                        $code = str_replace('(' . $className . '::className(),', '(\'' . $nsClassName2 . '\',', $code);
                    } else {
                        $code = str_replace('(' . $className . '::className(),', '(\\' . $nsClassName2 . '::className(),', $code);
                    }
                    if ($hasMany) {
                        $relationName = Inflector::pluralize(preg_replace('~Bases$~', '', $relationName));
                    }
                }
                $allRelations[$tableName][$relationName] = [$code, $className, $hasMany];
            }
        }
        return $allRelations;
    }

    /**
     * @inheritdoc
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {
        $className = parent::generateClassName($tableName, $useSchemaName) . 'Base';
        $nsClassName = $this->ns . '\\' . $className;
        if (class_exists($nsClassName)) {
            $this->baseClass = get_parent_class($nsClassName);
        } elseif (!is_null($this->_userBaseClass)) {
            $this->baseClass = $this->_userBaseClass;
        }
        return $className;
    }

    /**
     * @inheritdoc
     */
    protected function generateQueryClassName($modelClassName)
    {
        $queryClassName = parent::generateQueryClassName(preg_replace('~Base$~', '', $modelClassName)) . 'Base';
        $nsQueryClassName = $this->queryNs . '\\' . $queryClassName;
        if (class_exists($nsQueryClassName)) {
            $this->queryBaseClass = get_parent_class($nsQueryClassName);
        } elseif (!is_null($this->_userQueryBaseClass)) {
            $this->queryBaseClass = $this->_userQueryBaseClass;
        }
        return $queryClassName;
    }

    /**
     * @inheritdoc
     */
    public function render($template, $params = [])
    {
        $output = parent::render($template, $params);
        $output = preg_replace_callback('~@return \\\\(yii\\\\db\\\\ActiveQuery)\n\s*\*/\n\s*public function get([^\(]+)\(\)\n\s*\{\n\s*return \$this\-\>has(?:One|Many)\(([^,]+),~', function ($match) {
            if (strpos($match[3], '\\') !== false) {
                /* @var $modelClass string|\yii\db\ActiveRecord */
                $modelClass = eval('return ' . $match[3] . ';');
                if (class_exists($modelClass)) {
                    return str_replace($match[1], get_class($modelClass::find()), $match[0]);
                }
            }
            return $match[0];
        }, $output);
        $output = preg_replace_callback('~@return \\\\(([^\\\\]+\\\\models\\\\(?:[^\\\\]+\\\\)*)base\\\\(\w+)Base)(?:\[\])?\|array(?:\|null)?\n\s*\*/\n\s*public function (?:all|one)\(~U', function ($match) {
            /* @var $modelClass string|\yii\db\ActiveRecord */
            $modelClass = $match[2] . '\\' . $match[3];
            if (class_exists($modelClass)) {
                return str_replace($match[1], $modelClass, $match[0]);
            }
            return $match[0];
        }, $output);
        return $output;
    }
}
