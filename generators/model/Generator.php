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

    public $modelClass = null;
    public $newModelClass = null;
    public $newQueryClass = null;

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

    public function beforeValidate()
    {
        if (is_null($this->newModelClass) || is_null($this->newQueryClass)) {
            /* @var $modelClass \yii\db\ActiveRecord */
            $modelClass = $this->getModelClass();
            $baseName = Inflector::classify($modelClass::tableName());
            $appNs = preg_match('~^([^\\\\]+)\\\\models\\\\~', $modelClass, $match) ? $match[1] : 'app';
            if (is_null($this->newModelClass)) {
                $this->newModelClass = $appNs . '\models\\' . $baseName;
            }
            if (is_null($this->newQueryClass)) {
                $this->newQueryClass = $appNs . '\models\query\\' . $baseName . 'Query';
            }
        }
        return parent::beforeValidate();
    }

    public function generate()
    {
        $newModelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->newModelClass, '\\') . '.php'));
        $newQueryPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->newQueryClass, '\\') . '.php'));
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
        return $this->newModelClass;
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
        return $this->newQueryClass;
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
