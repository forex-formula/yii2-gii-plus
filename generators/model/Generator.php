<?php

namespace yii\gii\plus\generators\model;

use yii\gii\CodeFile,
    Yii,
    yii\gii\generators\crud\Generator as YiiGiiCrudGenerator;


class Generator extends YiiGiiCrudGenerator
{

    public $modelClass;
    private $controllerClass;
    private $viewPath;
    private $baseControllerClass;
    private $indexWidgetType;
    private $searchModelClass;
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
}
