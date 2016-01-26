<?php

namespace yii\gii\plus\generators\base\model;

use yii\db\Connection;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Json;
use yii\gii\generators\model\Generator as ModelGenerator;
use ReflectionClass;
use Yii;

class Generator extends ModelGenerator
{

    public $db = 'db';
    public $ns = 'app\models\base';
    public $tableName = '*';
    public $modelClass;
    public $baseClass = 'yii\db\ActiveRecord';
    public $generateRelations = true;
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $useSchemaName = true;
    public $generateQuery = true;
    public $queryNs;
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (Yii::getAlias('@common', false)) {
            $this->ns = 'common\models\base';
        }
        if (Yii::getAlias('@yii\boost', false)) {
            $this->baseClass = 'yii\boost\db\ActiveRecord';
            $this->queryBaseClass = 'yii\boost\db\ActiveQuery';
        }
    }

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
    public function rules()
    {
        $rules = [];
        foreach (parent::rules() as $rule) {
            if (!is_array($rule[0])) {
                $rule[0] = [$rule[0]];
            }
            if ($rule[1] == 'required') {
                $rule[0] = array_diff($rule[0], ['queryNs']);
            }
            if (count($rule[0])) {
                $rules[] = $rule;
            }
        }
        return array_merge($rules, [
            ['queryNs', 'default', 'value' => function (Generator $model, $attribute) {
                return preg_replace('~\\\\models(\\\\|$)~i', '\models\query$1', $model->ns);
            }]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Base Model Class',
            'queryClass' => 'Base ActiveQuery Class'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function autoCompleteData()
    {
        return [];
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
     * @return Connection[]
     */
    protected function getDbConnections()
    {
        $dbConnections = [];
        foreach (Yii::$app->getComponents() as $id => $definition) {
            $db = Yii::$app->get($id);
            if ($db instanceof Connection) {
                $dbConnections[$id] = $db;
            }
        }
        return $dbConnections;
    }

    /**
     * @param array $data
     * @return JsExpression
     */
    protected function createAutoComplete(array $data)
    {
        return new JsExpression('function (request, response) { response(' . Json::htmlEncode($data) . '[jQuery(\'#' . Html::getInputId($this, 'db') . '\').val()]); }');
    }

    /**
     * @return JsExpression
     */
    public function getTableNameAutoComplete()
    {
        $tableNameListItems = [];
        foreach ($this->getDbConnections() as $id => $db) {
            $tableNameListItems[$id] = ['*'];
            foreach ($db->getSchema()->getTableNames() as $tableName) {
                $tableNameListItems[$id][] = $tableName;
            }
        }
        return $this->createAutoComplete($tableNameListItems);
    }

    /**
     * @return array
     */
    public function getDbListItems()
    {
        $ids = array_keys($this->getDbConnections());
        return array_combine($ids, $ids);
    }
}
