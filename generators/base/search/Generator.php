<?php

namespace yii\gii\plus\generators\base\search;

use yii\gii\CodeFile,
    yii\helpers\Inflector,
    ReflectionClass,
    Yii,
    yii\gii\generators\crud\Generator as YiiGiiCrudGenerator;


class Generator extends YiiGiiCrudGenerator
{

    public function getName()
    {
        return 'Base Search Model Generator';
    }

    public function getDescription()
    {
        return '';
    }

    public function attributes()
    {
        return array_diff(parent::attributes(), ['controllerClass', 'viewPath', 'baseControllerClass', 'indexWidgetType']);
    }

    public function rules()
    {
        $attributes = $this->attributes();
        $rules = [];
        foreach (parent::rules() as $rule) {
            if (!is_array($rule[0])) {
                $rule[0] = [$rule[0]];
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

    public function defaultTemplate()
    {
        $class = new ReflectionClass('yii\gii\generators\crud\Generator');
        return dirname($class->getFileName()) . '/default';
    }

    public function validateNewClass($attribute, $params)
    {
        if (strlen($this->$attribute)) {
            parent::validateNewClass($attribute, $params);
        }
    }

    private $_searchModelClass = '';

    public function generate()
    {
        $this->_searchModelClass = $this->searchModelClass;
        if (!strlen($this->_searchModelClass)) {
            $appNs = preg_match('~^([^\\\\]+)\\\\models\\\\~', $this->modelClass, $match) ? $match[1] : 'app';
            /* @var $modelClass \yii\db\ActiveRecord */
            $modelClass = $this->modelClass;
            $baseName = Inflector::classify($modelClass::tableName());
            $this->_searchModelClass = $appNs . '\models\search\base\\' . $baseName . 'SearchBase';
        }
        $searchModelPath = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->_searchModelClass, '\\') . '.php'));
        return [new CodeFile($searchModelPath, $this->render('search.php'))];
    }

    public function getSearchModelClass()
    {
        return $this->_searchModelClass;
    }
}
