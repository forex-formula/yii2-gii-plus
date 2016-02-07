<?php

namespace yii\gii\plus\generators\base\model;

use yii\gii\generators\model\Generator as GiiModelGenerator;
use yii\gii\plus\helpers\Helper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\JsExpression;
use yii\helpers\Json;
use ReflectionClass;
use Yii;

class Generator extends GiiModelGenerator
{

    public $ns = 'app\models\base';
    public $tableName = '*';
    public $generateLabelsFromComments = true;
    public $useSchemaName = false;
    public $generateQuery = true;
    public $queryNs;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (Yii::getAlias('@common', false)) {
            $this->ns = 'common\models\base';
        }
        if (class_exists('yii\boost\db\ActiveRecord')) {
            $this->baseClass = 'yii\boost\db\ActiveRecord';
        }
        if (class_exists('yii\boost\db\ActiveQuery')) {
            $this->queryBaseClass = 'yii\boost\db\ActiveQuery';
        }
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
            ['ns', 'match', 'pattern' => '~\\\\base$~'],
            ['modelClass', 'match', 'pattern' => '~Base$~'],
            ['queryNs', 'default', 'value' => function (Generator $model, $attribute) {
                return preg_replace('~\\\\base$~', '\query\base', $model->ns);
            }],
            ['queryNs', 'match', 'pattern' => '~\\\\query\\\\base$~'],
            ['queryClass', 'match', 'pattern' => '~QueryBase$~']
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
    public function stickyAttributes()
    {
        return array_diff(parent::stickyAttributes(), ['queryNs']);
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
     * @param bool $refresh
     * @return JsExpression
     */
    public function getTableNameAutoComplete($refresh = false)
    {
        $data = [];
        foreach (Helper::getDbConnections() as $id => $db) {
            $data[$id] = ['*'];
            $schemaNames = Helper::getSchemaNames($db, $refresh);
            foreach ($schemaNames as $schemaName) {
                $data[$id][] = $schemaName . '.*';
            }
            $schema = $db->getSchema();
            foreach ($schema->getTableNames('', $refresh) as $tableName) {
                $data[$id][] = $tableName;
            }
            foreach ($schemaNames as $schemaName) {
                foreach ($schema->getTableNames($schemaName, $refresh) as $tableName) {
                    $data[$id][] = $schemaName . '.' . $tableName;
                }
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @param bool $refresh
     * @return JsExpression
     */
    public function getNsAutoComplete($refresh = false)
    {
        $data = [];
        foreach (Helper::getDbConnections() as $id => $db) {
            $data[$id] = [];
            $schemaNames = Helper::getSchemaNames($db, $refresh);
            foreach (Helper::getModelNamespaces() as $modelNs) {
                $data[$id][] = $modelNs . '\base';
                foreach ($schemaNames as $schemaName) {
                    $data[$id][] = $modelNs . '\\' . $schemaName . '\base';
                }
                $data[$id][] = $modelNs . '\\' . $id . '\base';
                foreach ($schemaNames as $schemaName) {
                    $data[$id][] = $modelNs . '\\' . $id . '\\' . $schemaName . '\base';
                }
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @return array
     */
    public function getBaseClassAutoComplete()
    {
        $data = ['yii\db\ActiveRecord'];
        if (class_exists('yii\boost\db\ActiveRecord')) {
            $data[] = 'yii\boost\db\ActiveRecord';
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getDbListItems()
    {
        $ids = array_keys(Helper::getDbConnections());
        return array_combine($ids, $ids);
    }

    /**
     * @param bool $refresh
     * @return JsExpression
     */
    public function getQueryNsAutoComplete($refresh = false)
    {
        $data = [];
        foreach (Helper::getDbConnections() as $id => $db) {
            $data[$id] = [];
            $schemaNames = Helper::getSchemaNames($db, $refresh);
            foreach (Helper::getModelNamespaces() as $modelNs) {
                $data[$id][] = $modelNs . '\query\base';
                foreach ($schemaNames as $schemaName) {
                    $data[$id][] = $modelNs . '\\' . $schemaName . '\query\base';
                }
                $data[$id][] = $modelNs . '\\' . $id . '\query\base';
                foreach ($schemaNames as $schemaName) {
                    $data[$id][] = $modelNs . '\\' . $id . '\\' . $schemaName . '\query\base';
                }
            }
        }
        return $this->createAutoComplete($data);
    }

    /**
     * @return array
     */
    public function getQueryBaseClassAutoComplete()
    {
        $data = ['yii\db\ActiveQuery'];
        if (class_exists('yii\boost\db\ActiveQuery')) {
            $data[] = 'yii\boost\db\ActiveQuery';
        }
        return $data;
    }

    /**
     * @var string
     */
    protected $userBaseClass;

    /**
     * @var string
     */
    protected $userQueryBaseClass;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->userBaseClass = $this->baseClass;
        $this->userQueryBaseClass = $this->queryBaseClass;
        $files = parent::generate();
        $this->baseClass = $this->userBaseClass;
        $this->queryBaseClass = $this->userQueryBaseClass;
        return $files;
    }

    /**
     * @var array
     */
    protected $allRelations;

    /**
     * @inheritdoc
     */
    protected function generateRelations()
    {
        $allRelations = [];
        foreach (parent::generateRelations() as $tableName => $relations) {
            $allRelations[$tableName] = [];
            foreach ($relations as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                /*
                $nsClassName = $this->ns . '\\' . $className;
                if (preg_match('~^((?:\w+\\\\)*\w+)\\\\base\\\\(\w+)Base$~', $nsClassName, $match)) {
                    $nsClassName2 = $match[1] . '\\' . $match[2];
                    if (class_exists($nsClassName2) && is_subclass_of($nsClassName2, $nsClassName)) {
                        $code = str_replace('(' . $className . ':', '(\\' . $nsClassName2 . ':', $code);
                        $className = $match[2];
                        if ($hasMany) {
                            $relationName = Inflector::pluralize($className);
                        }
                    }
                }
                */
                $allRelations[$tableName][$relationName] = [$code, $className, $hasMany];
            }
        }
        $this->allRelations = $allRelations;
        return $allRelations;
    }

    /**
     * @inheritdoc
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (is_null($this->allRelations)) {
            return parent::generateClassName($tableName, $useSchemaName);
        }
        $className = parent::generateClassName($tableName, $useSchemaName) . 'Base';
        if (!is_null($this->userBaseClass)) {
            $nsClassName = $this->ns . '\\' . $className;
            if (class_exists($nsClassName)) {
                $this->baseClass = get_parent_class($nsClassName);
            } else {
                $this->baseClass = $this->userBaseClass;
            }
        }
        return $className;
    }

    /**
     * @inheritdoc
     */
    protected function generateQueryClassName($modelClassName)
    {
        $queryClassName = parent::generateQueryClassName(preg_replace('~Base$~', '', $modelClassName)) . 'Base';
        if (!is_null($this->userQueryBaseClass)) {
            $nsQueryClassName = $this->queryNs . '\\' . $queryClassName;
            if (class_exists($nsQueryClassName)) {
                $this->queryBaseClass = get_parent_class($nsQueryClassName);
            } else {
                $this->queryBaseClass = $this->userQueryBaseClass;
            }
        }
        return $queryClassName;
    }

    /**
     * @inheritdoc
     */
    public function render($template, $params = [])
    {
        $output = parent::render($template, $params);
        if (array_key_exists('className', $params)) {
            $nsClassName = $this->ns . '\\' . $params['className'];
            if (class_exists($nsClassName) && is_subclass_of($nsClassName, 'yii\db\ActiveRecord')) {
                $model = new $nsClassName;
                $output = preg_replace_callback('~@return \\\\(yii\\\\db\\\\ActiveQuery)\s+\*/\s+public function ([^\(]+)\(\)~', function ($match) use ($model) {
                    if (method_exists($model, $match[2])) {
                        return str_replace($match[1], get_class(call_user_func([$model, $match[2]])), $match[0]);
                    } else {
                        return $match[0];
                    }
                }, $output);
            }
        }
        return $output;
    }
}
