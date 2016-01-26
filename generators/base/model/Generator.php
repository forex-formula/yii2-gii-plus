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
        if (Yii::getAlias('@common/models', false)) {
            $this->ns = 'common\models\base';
        }
        if (Yii::getAlias('@yii/boost/db', false)) {
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
                return preg_replace('~\\\\base$~', '\query\base', $model->ns);
            }]
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
     * @var Connection[]
     */
    protected $dbConnections;

    /**
     * @return Connection[]
     */
    protected function getDbConnections()
    {
        if (is_null($this->dbConnections)) {
            $this->dbConnections = [];
            foreach (Yii::$app->getComponents() as $id => $definition) {
                $db = Yii::$app->get($id);
                if ($db instanceof Connection) {
                    $this->dbConnections[$id] = $db;
                }
            }
        }
        return $this->dbConnections;
    }

    /**
     * @var array
     */
    protected $nsPrefixes;

    /**
     * @return array
     */
    protected function getNsPrefixes()
    {
        if (is_null($this->nsPrefixes)) {
            $this->nsPrefixes = ['app', 'backend', 'common', 'console', 'frontend'];
        }
        return $this->nsPrefixes;
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
        $data = [];
        foreach ($this->getDbConnections() as $id => $db) {
            $data[$id] = ['*'];
            foreach ($db->getSchema()->getTableNames() as $tableName) {
                $data[$id][] = $tableName;
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @return JsExpression
     */
    public function getNsAutoComplete()
    {
        $data = [];
        foreach ($this->getDbConnections() as $id => $db) {
            $data[$id] = [];
            foreach ($this->getNsPrefixes() as $nsPrefix) {
                if (Yii::getAlias('@' . $nsPrefix . '/models', false)) {
                    $data[$id] = array_merge($data[$id], [
                        $nsPrefix . '\models\base',
                        $nsPrefix . '\models\\' . $id . '\base'
                    ]);
                }
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @return JsExpression
     */
    public function getQueryNsAutoComplete()
    {
        $data = [];
        foreach ($this->getDbConnections() as $id => $db) {
            $data[$id] = [];
            foreach ($this->getNsPrefixes() as $nsPrefix) {
                if (Yii::getAlias('@' . $nsPrefix . '/models', false)) {
                    $data[$id] = array_merge($data[$id], [
                        $nsPrefix . '\models\query\base',
                        $nsPrefix . '\models\\' . $id . '\query\base'
                    ]);
                }
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @return array
     */
    public function getDbListItems()
    {
        $ids = array_keys($this->getDbConnections());
        return array_combine($ids, $ids);
    }

    /**
     * @var string
     */
    protected $commonBaseClass;

    /**
     * @var string
     */
    protected $commonQueryBaseClass;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->commonBaseClass = $this->baseClass;
        $this->commonQueryBaseClass = $this->queryBaseClass;
        $files = parent::generate();
        $this->baseClass = $this->commonBaseClass;
        $this->queryBaseClass = $this->commonQueryBaseClass;
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
        } else {
            $this->baseClass = $this->commonBaseClass;
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
            $this->queryBaseClass = $this->commonQueryBaseClass;
        }
        return $queryClassName;
    }
}
