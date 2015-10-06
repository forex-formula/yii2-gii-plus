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

    /*protected $fileUseMap = [];
    protected $use = [];*/

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
        /*if (!strlen($this->modelClass) || !strlen($this->queryClass)) {
            $baseName = Inflector::classify($this->tableName);
            if (!strlen($this->modelClass)) {
                $this->modelClass = $baseName . 'Base';
            }
            if (!strlen($this->queryClass)) {
                $this->queryClass = $baseName . 'QueryBase';
            }
        }*/
        /*if (!strlen($this->baseClass)) {
            $nsModelClass = $this->ns . '\\' . $this->modelClass;
            if (class_exists($nsModelClass)) {
                $this->baseClass = get_parent_class($nsModelClass);
                // use
                $modelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($nsModelClass, '\\') . '.php'));
                if (is_file($modelPath) && preg_match('~use([^;]+);~', file_get_contents($modelPath), $match)) {
                    $use = array_filter(array_map('trim', explode(',', $match[1])), 'strlen');
                    foreach ($use as $value) {
                        $this->fileUseMap[preg_replace('~^.+[\\\\ ]([^\\\\ ]+)$~', '$1', $value)] = $value;
                    }
                }
            } else {
                $this->baseClass = 'yii\boost\db\ActiveRecord';
            }
        }*/
        /*if (!strlen($this->queryBaseClass)) {
            $queryNsQueryClass = $this->queryNs . '\\' . $this->queryClass;
            if (class_exists($queryNsQueryClass)) {
                $this->queryBaseClass = get_parent_class($queryNsQueryClass);
            } else {
                $this->queryBaseClass = 'yii\boost\db\ActiveQuery';
            }
        }*/
        return parent::beforeValidate();
    }

    protected function generateClassName($tableName, $useSchemaName = null)
    {
        return parent::generateClassName($tableName, $useSchemaName) . 'Base';
    }

    protected function generateQueryClassName($modelClassName)
    {
        return parent::generateQueryClassName(preg_replace('~Base$~', '', $modelClassName)) . 'Base';
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
