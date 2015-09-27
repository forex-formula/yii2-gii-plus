<?php

namespace yii\gii\plus\generators\search;

use yii\gii\CodeFile,
    Yii,
    yii\gii\generators\model\Generator as YiiGiiModelGenerator;


class Generator extends YiiGiiModelGenerator
{

    public $modelClass;
    private $controllerClass;
    private $viewPath;
    private $baseControllerClass;
    private $indexWidgetType;
    private $searchModelClass;
    public $newModelClass;

    public function getName()
    {
        return 'Search Model Generator';
    }

    public function getDescription()
    {
        return '';
    }

    public function rules()
    {
        $attributes = $this->attributes();
        $rules = [['newModelClass', 'required']];
        foreach (parent::rules() as $rule) {
            if (!is_array($rule[0])) {
                $rule[0] = [$rule[0]];
            }
            $key = array_search('searchModelClass', $rule[0]);
            if ($key !== false) {
                $rule[0][$key] = 'newModelClass';
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
        return ['search.php'];
    }

    public function generate()
    {
        $newModelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->newModelClass, '\\') . '.php'));
        return [new CodeFile($newModelPath, $this->render('search.php'))];
    }
}
