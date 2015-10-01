<?php

namespace yii\gii\plus\generators\model;

use yii\gii\CodeFile,
    yii\gii\plus\helpers\Helper,
    yii\helpers\Inflector,
    yii\helpers\StringHelper,
    Yii,
    yii\gii\generators\crud\Generator as YiiGiiCrudGenerator;


class Generator extends YiiGiiCrudGenerator
{

    public $newModelClass = '';
    public $newQueryClass = '';

    public function getName()
    {
        return 'Model Generator';
    }

    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class for the specified base ActiveRecord class.';
    }

    public function attributes()
    {
        $attributes = array_diff(parent::attributes(), ['controllerClass', 'viewPath', 'baseControllerClass', 'indexWidgetType', 'searchModelClass']);
        return array_merge($attributes, ['newModelClass', 'newQueryClass']);
    }

    public function rules()
    {
        $attributes = $this->attributes();
        $rules = [];
        foreach (parent::rules() as $rule) {
            if (!is_array($rule[0])) {
                $rule[0] = [$rule[0]];
            }
            $key = array_search('searchModelClass', $rule[0]);
            if ($key !== false) {
                $rule[0][$key] = 'newModelClass';
                $rule[0][] = 'newQueryClass';
            }
            $rule[0] = array_intersect($rule[0], $attributes);
            if (count($rule[0])) {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    public function requiredTemplates()
    {
        return ['model.php', 'query.php'];
    }

    public function validateNewClass($attribute, $params)
    {
        if (strlen($this->$attribute)) {
            parent::validateNewClass($attribute, $params);
        }
    }

    private $_newModelClass = '';
    private $_newQueryClass = '';

    public function generate()
    {
        $this->_newModelClass = $this->newModelClass;
        $this->_newQueryClass = $this->newQueryClass;
        if (!strlen($this->_newModelClass) || !strlen($this->_newQueryClass)) {
            $appNs = preg_match('~^([^\\\\]+)\\\\models\\\\~', $this->modelClass, $match) ? $match[1] : 'app';
            /* @var $modelClass \yii\db\ActiveRecord */
            $modelClass = $this->modelClass;
            $baseName = Inflector::classify($modelClass::tableName());
            if (!strlen($this->_newModelClass)) {
                $this->_newModelClass = $appNs . '\models\\' . $baseName;
            }
            if (!strlen($this->_newQueryClass)) {
                $this->_newQueryClass = $appNs . '\models\query\\' . $baseName . 'Query';
            }
        }
        $newModelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->_newModelClass, '\\') . '.php'));
        $newQueryPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->_newQueryClass, '\\') . '.php'));
        return [
            new CodeFile($newModelPath, $this->render('model.php')),
            new CodeFile($newQueryPath, $this->render('query.php'))
        ];
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getModelNamespace()
    {
        return StringHelper::dirname(ltrim($this->getModelClass(), '\\'));
    }

    public function getModelName()
    {
        return StringHelper::basename($this->getModelClass());
    }

    public function getModelAlias()
    {
        $modelAlias = $this->getModelName();
        if ($modelAlias == $this->getNewModelName()) {
            $modelAlias .= 'Base';
        }
        return $modelAlias;
    }

    public function getQueryClass()
    {
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->getModelClass();
        return get_class($modelClass::find());
    }

    public function getQueryNamespace()
    {
        return StringHelper::dirname(ltrim($this->getQueryClass(), '\\'));
    }

    public function getQueryName()
    {
        return StringHelper::basename($this->getQueryClass());
    }

    public function getQueryAlias()
    {
        $queryAlias = $this->getQueryName();
        if ($queryAlias == $this->getNewQueryName()) {
            $queryAlias .= 'Base';
        }
        return $queryAlias;
    }

    public function getNewModelClass()
    {
        return $this->_newModelClass;
    }

    public function getNewModelNamespace()
    {
        return StringHelper::dirname(ltrim($this->getNewModelClass(), '\\'));
    }

    public function getNewModelName()
    {
        return StringHelper::basename($this->getNewModelClass());
    }

    public function getNewQueryClass()
    {
        return $this->_newQueryClass;
    }

    public function getNewQueryNamespace()
    {
        return StringHelper::dirname(ltrim($this->getNewQueryClass(), '\\'));
    }

    public function getNewQueryName()
    {
        return StringHelper::basename($this->getNewQueryClass());
    }

    public function getModelUseDirective()
    {
        $use = ['Yii'];
        if ($this->getModelNamespace() != $this->getNewModelNamespace()) {
            $modelAlias = $this->getModelAlias();
            if ($modelAlias == $this->getModelName()) {
                $use[] = $this->getModelClass();
            } else {
                $use[] = $this->getModelClass() . ' as ' . $modelAlias;
            }
        }
        if ($this->getNewQueryNamespace() != $this->getNewModelNamespace()) {
            $use[] = $this->getNewQueryClass();
        }
        if (count($use)) {
            return Helper::getUseDirective($use) . "\n\n";
        } else {
            return '';
        }
    }

    public function getQueryUseDirective()
    {
        $use = [];
        if ($this->getQueryNamespace() != $this->getNewQueryNamespace()) {
            $queryAlias = $this->getQueryAlias();
            if ($queryAlias == $this->getQueryName()) {
                $use[] = $this->getQueryClass();
            } else {
                $use[] = $this->getQueryClass() . ' as ' . $queryAlias;
            }
        }
        if (count($use)) {
            return Helper::getUseDirective($use) . "\n\n";
        } else {
            return '';
        }
    }

    public function getPrimaryKey()
    {
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->getModelClass();
        return $modelClass::primaryKey();
    }
}
