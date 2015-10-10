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
    protected function generateRelations()
    {
        $allRelations = [];
        foreach (parent::generateRelations() as $tableName => $relations) {
            $allRelations[$tableName] = [];
            foreach ($relations as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                $nsClassName = $this->ns . '\\' . $className;
                $nsClassName1 = preg_replace('~\\\\base$~', '', $this->ns) . '\\' . preg_replace('~Base$~', '', $className);
                if (class_exists($nsClassName1) && (get_parent_class($nsClassName1) == $nsClassName)) {
                    if ($nsClassName1::tableName() == $tableName) {
                        $code = str_replace('(' . $className . '::className(),', '(\'' . $nsClassName1 . '\',', $code);
                    } else {
                        $code = str_replace('(' . $className . '::className(),', '(\\' . $nsClassName1 . '::className(),', $code);
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
}
