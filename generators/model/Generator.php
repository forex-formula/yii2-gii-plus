<?php

namespace yii\gii\plus\generators\model;

use yii\gii\CodeFile,
    yii\helpers\StringHelper,
    Yii,
    yii\gii\generators\crud\Generator as YiiGiiCrudGenerator;


class Generator extends YiiGiiCrudGenerator
{

    public $newModelClass;
    public $newQueryClass;

    public function getName()
    {
        return 'Model Generator';
    }

    public function getDescription()
    {
        return '';
    }

    public function attributes()
    {
        $attributes = array_diff(parent::attributes(), ['controllerClass', 'viewPath', 'baseControllerClass', 'indexWidgetType', 'searchModelClass']);
        return array_merge($attributes, ['newModelClass', 'newQueryClass']);
    }

    public function rules()
    {
        $attributes = $this->attributes();
        $rules = [[['newModelClass', 'newQueryClass'], 'required']];
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

    public function generate()
    {
        $newModelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->newModelClass, '\\') . '.php'));
        $newQueryPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->newQueryClass, '\\') . '.php'));
        return [
            new CodeFile($newModelPath, $this->render('model.php')),
            new CodeFile($newQueryPath, $this->render('query.php'))
        ];
    }

    public function getNewModelNamespace()
    {
        return StringHelper::dirname(ltrim($this->newModelClass, '\\'));
    }

    public function getNewModelUseDirective()
    {
        return 'use Yii;' . "\n\n";
    }

    public function getNewModelName()
    {
        return StringHelper::basename($this->newModelClass);
    }

    public function getModelName()
    {
        return StringHelper::basename($this->modelClass);
    }

    public function getNewQueryNamespace()
    {
        return StringHelper::dirname(ltrim($this->newQueryClass, '\\'));
    }

    public function getNewQueryUseDirective()
    {
        return '';
    }

    public function getNewQueryName()
    {
        return StringHelper::basename($this->newQueryClass);
    }

    public function getQueryName()
    {
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;
        return StringHelper::basename(get_class($modelClass::find()));
    }
}
