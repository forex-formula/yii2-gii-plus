<?php

namespace yii\gii\plus\generators\base\model;

use yii\gii\plus\helpers\Helper,
    yii\helpers\Inflector,
    ReflectionClass,
    Yii,
    yii\gii\generators\model\Generator as YiiGiiModelGenerator;


class Generator extends YiiGiiModelGenerator
{

    public $ns = 'app\models\base';
    public $modelClass = '';
    public $baseClass = 'yii\boost\db\ActiveRecord';
    public $generateLabelsFromComments = true;
    public $useSchemaName = false;
    public $generateQuery = true;
    public $queryNs = 'app\models\query\base';
    public $queryClass = '';
    public $queryBaseClass = 'yii\boost\db\ActiveQuery';

    public function getName()
    {
        return 'Base Model Generator';
    }

    public function getDescription()
    {
        return 'This generator generates a base ActiveRecord class for the specified database table.';
    }

    public function requiredTemplates()
    {
        return ['model.php', 'query.php'];
    }

    public function defaultTemplate()
    {
        $class = new ReflectionClass(get_parent_class(__CLASS__));
        return dirname($class->getFileName()) . '/default';
    }

    public function beforeValidate()
    {
        if (preg_match('~^([^\\\\]+)\\\\models~', $this->ns, $match)) {
            $this->queryNs = preg_replace('~^([^\\\\]+)\\\\models~', $match[1] . '\models', $this->queryNs);
        }
        if (preg_match('~models\\\\([^\\\\]+)\\\\base$~', $this->ns, $match)) {
            $this->queryNs = preg_replace('~models\\\\([^\\\\]+)\\\\base$~', 'models\\' . $match[1] . '\base$', $this->queryNs);
        }
        return parent::beforeValidate();
    }

    private $_baseClass = '';
    private $_queryBaseClass = '';

    private $_fileUseMap = [];

    //private $_use = [];

    public function generate()
    {
        $this->_baseClass = $this->baseClass;
        $this->_queryBaseClass = $this->queryBaseClass;
        return parent::generate();
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
            // use
            $this->_fileUseMap[$className] = [];
            $path = Yii::getAlias('@' . str_replace('\\', '/', ltrim($nsClassName, '\\') . '.php'));
            if (is_file($path) && preg_match('~use([^;]+);~', file_get_contents($path), $match)) {
                $use = array_filter(array_map('trim', explode(',', $match[1])), 'strlen');
                foreach ($use as $value) {
                    $this->_fileUseMap[$className][preg_replace('~^.+[\\\\ ]([^\\\\ ]+)$~', '$1', $value)] = $value;
                }
            }
        } else {
            $this->baseClass = $this->_baseClass;
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
        } else {
            $this->queryBaseClass = $this->_queryBaseClass;
        }
        return $queryClassName;
    }

    /*protected function generateRelations()
    {
        $this->use = ['Yii'];
        $allRelations = parent::generateRelations();
        if (($this->ns != 'app\models') && array_key_exists($this->tableName, $allRelations)) {
            $relations = [];
            foreach ($allRelations[$this->tableName] as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                if ($className == $this->modelClass) { // itself
                    $baseName = Inflector::classify($this->tableName);
                    $code = str_replace('(' . $className . '::className()', '(\'app\models\\' . $baseName . '\'', $code);
                    if ($hasMany) {
                        $relationName = Inflector::camelize(Inflector::pluralize($this->tableName));
                    } elseif ($relationName == $className) {
                        $relationName = $baseName;
                    }
                } else {
                    if (array_key_exists($className, $this->fileUseMap)) {
                        $this->use[] = $this->fileUseMap[$className];
                    } else {
                        $this->use[] = 'app\models\\' . $className;
                    }
                }
                $relations[$relationName] = [$code, $className, $hasMany];
            }
            $allRelations[$this->tableName] = $relations;
        }
        return $allRelations;
    }*/

    /*public function render($template, $params = [])
    {
        return str_replace('use Yii;', Helper::getUseDirective($this->use), parent::render($template, $params));
    }*/
}
