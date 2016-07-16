<?php

namespace yii\gii\plus\generators\base\model;

use yii\base\ErrorException;
use yii\db\Expression;
use yii\gii\generators\model\Generator as GiiModelGenerator;
use yii\gii\plus\helpers\Helper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Json;
use yii\db\Schema;
use Yii;

class Generator extends GiiModelGenerator
{

    /**
     * @var string
     */
    public $includeFilter = '.*';

    /**
     * @var string
     */
    public $excludeFilter = 'migration|cache|source_message|message|log|auth_\w+';

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
            [['includeFilter', 'excludeFilter'], 'filter', 'filter' => 'trim'],
            [['includeFilter', 'excludeFilter'], 'required'],
            [['includeFilter', 'excludeFilter'], 'validatePattern'],
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
     * @param string $attribute
     * @param array $params
     */
    public function validatePattern($attribute, $params)
    {
        if (!$this->hasErrors($attribute)) {
            try {
                preg_match('~^(?:' . $this->$attribute . ')$~', 'migration');
            } catch (ErrorException $exception) {
                $this->addError($attribute, $exception->getMessage());
            }
        }
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
        return array_merge(array_diff(parent::stickyAttributes(), ['queryNs']), ['includeFilter', 'excludeFilter']);
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
    protected $commonBaseClass;

    /**
     * @var string
     */
    protected $commonQueryBaseClass;

    /**
     * @var bool
     */
    protected $relationsDone;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->commonBaseClass = $this->baseClass;
        $this->commonQueryBaseClass = $this->queryBaseClass;
        $this->relationsDone = false;
        $this->classNames = [];
        $files = parent::generate();
        $this->baseClass = $this->commonBaseClass;
        $this->queryBaseClass = $this->commonQueryBaseClass;
        return $files;
    }

    /**
     * @inheritdoc
     */
    public function generateRules($table)
    {
        $defaultExpressions = [];
        $defaultValues = [];
        $defaultNullAttributes = [];
        $booleanAttributes = [];
        $dateAttributes = [];
        $timeAttributes = [];
        $datetimeAttributes = [];
        foreach ($table->columns as $column) {
            if (!is_null($column->defaultValue)) {
                if ($column->defaultValue instanceof Expression) {
                    $this->relationUses[$table->fullName][] = 'yii\db\Expression';
                    $defaultExpressions[$column->defaultValue->expression][] = $column->name;
                } else {
                    $defaultValues[$column->defaultValue][] = $column->name;
                }
            } elseif ($column->allowNull && is_null($column->defaultValue)) {
                $defaultNullAttributes[] = $column->name;
            } elseif (in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
                $booleanAttributes[] = $column->name;
            } elseif ($column->type == Schema::TYPE_DATE) {
                $dateAttributes[] = $column->name;
            } elseif ($column->type == Schema::TYPE_TIME) {
                $timeAttributes[] = $column->name;
            } elseif (in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
                $datetimeAttributes[] = $column->name;
            }
        }
        $rules = [];
        foreach ($defaultExpressions as $defaultExpression => $attributes) {
            $rules[] = '[[\'' . implode('\', \'', $attributes) . '\'], \'default\', \'value\' => new Expression(\'' . $defaultExpression . '\')]';
        }
        foreach ($defaultValues as $defaultValue => $attributes) {
            $rules[] = '[[\'' . implode('\', \'', $attributes) . '\'], \'default\', \'value\' => \'' . $defaultValue . '\']';
        }
        if (count($defaultNullAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $defaultNullAttributes) . '\'], \'default\', \'value\' => null]';
        }
        foreach (parent::generateRules($table) as $rule) {
            if (!preg_match('~, \'(?:safe|boolean)\'\]$~', $rule)) {
                $rules[] = $rule;
            }
        }
        if (count($booleanAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $booleanAttributes) . '\'], \'boolean\']';
        }
        if (count($dateAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $dateAttributes) . '\'], \'date\', \'format\' => \'php:Y-m-d\']';
        }
        if (count($timeAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $timeAttributes) . '\'], \'date\', \'format\' => \'php:H:i:s\']';
        }
        if (count($datetimeAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $datetimeAttributes) . '\'], \'date\', \'format\' => \'php:Y-m-d H:i:s\']';
        }
        return $rules;
    }

    /**
     * @var array
     */
    protected $relationUses;

    /**
     * @var array
     */
    protected $hasManyRelations;

    /**
     * @inheritdoc
     */
    protected function generateRelations()
    {
        $relations = [];
        $this->relationUses = [];
        $this->hasManyRelations = [];
        $modelClassTableNameMap = Helper::getModelClassTableNameMap();
        foreach (parent::generateRelations() as $tableName => $tableRelations) {
            $relations[$tableName] = [];
            $this->relationUses[$tableName] = ['Yii'];
            $this->hasManyRelations[$tableName] = [];
            foreach ($tableRelations as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                $nsClassName = array_search(array_search($className, $this->classNames), $modelClassTableNameMap);
                if (($nsClassName !== false) && class_exists($nsClassName)) {
                    $relations[$tableName][$relationName] = [$code, $className, $hasMany];
                    $this->relationUses[$tableName][] = $nsClassName;
                    if ($hasMany || ($relationName == $className)) {
                        /* @var $nsClassName \yii\db\ActiveRecord */
                        foreach ($nsClassName::getTableSchema()->foreignKeys as $foreignKey) {
                            if ($foreignKey[0] == $tableName) {
                                unset($foreignKey[0]);
                                $this->hasManyRelations[$tableName][$relationName] = [$nsClassName, $className, $foreignKey];
                            }
                        }
                    }
                }
            }
        }
        $this->relationsDone = true;
        $this->classNames = [];
        return $relations;
    }

    /**
     * @inheritdoc
     */
    protected function getTableNames()
    {
        try {
            $this->tableNames = array_filter(parent::getTableNames(), function ($tableName) {
                return preg_match('~^(?:' . $this->includeFilter . ')$~i', $tableName) && !preg_match('~^(?:' . $this->excludeFilter . ')$~i', $tableName);
            });
        } catch (ErrorException $e) {
        }
        return $this->tableNames;
    }

    /**
     * @inheritdoc
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (!$this->relationsDone) {
            return parent::generateClassName($tableName, $useSchemaName);
        }
        $className = parent::generateClassName($tableName, $useSchemaName) . 'Base';
        if ($this->commonBaseClass) {
            $nsClassName = $this->ns . '\\' . $className;
            if (class_exists($nsClassName)) {
                $this->baseClass = get_parent_class($nsClassName);
            } else {
                $this->baseClass = $this->commonBaseClass;
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
        if ($this->commonQueryBaseClass) {
            $nsQueryClassName = $this->queryNs . '\\' . $queryClassName;
            if (class_exists($nsQueryClassName)) {
                $this->queryBaseClass = get_parent_class($nsQueryClassName);
            } else {
                $this->queryBaseClass = $this->commonQueryBaseClass;
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
        switch ($template) {
            case 'model.php':
                $tableName = $params['tableName'];
                if (is_array($this->relationUses) && array_key_exists($tableName, $this->relationUses)) {
                    $uses = array_unique($this->relationUses[$tableName]);
                    Helper::sortUses($uses);
                    $output = str_replace('use Yii;', 'use ' . implode(';' . "\n" . 'use ', $uses) . ';', $output);
                }
                $params['hasManyRelations'] = $this->hasManyRelations;
                $output = preg_replace('~\}(\s*)$~', parent::render('model-part.php', $params) . '}\1', $output);
                break;
            case 'query.php':
                $output = preg_replace('~\}(\s*)$~', parent::render('query-part.php', $params) . '}\1', $output);
                break;
        }
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
        $output = preg_replace_callback('~(@return |return new )\\\\((?:\w+\\\\)*\w+\\\\query)\\\\base\\\\(\w+Query)Base~', function ($match) {
            $nsClassName = $match[2] . '\\' . $match[3];
            if (class_exists($nsClassName)) {
                return $match[1] . '\\' . $nsClassName;
            } else {
                return $match[0];
            }
        }, $output);
        $output = preg_replace_callback('~(@see |@return )\\\\((?:\w+\\\\)*\w+)\\\\base\\\\(\w+)Base~', function ($match) {
            $nsClassName = $match[2] . '\\' . $match[3];
            if (class_exists($nsClassName)) {
                return $match[1] . '\\' . $nsClassName;
            } else {
                return $match[0];
            }
        }, $output);
        $output = preg_replace('~\'targetClass\' \=\> (\w+)Base\:\:className\(\)~', '\'targetClass\' => \1::className()', $output);
        return $output;
    }
}
