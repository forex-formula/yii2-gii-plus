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
    public $excludeFilter = 'migration|cache|source_message|message|log|auth_\w+|trans_\w+';

    public $ns = 'app\models\base';
    public $tableName = '*';
    public $baseClass = 'yii\boost\db\ActiveRecord';
    public $generateLabelsFromComments = true;
    public $useSchemaName = false;
    public $generateQuery = true;
    public $queryNs;
    public $queryBaseClass = 'yii\boost\db\ActiveQuery';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $db = $this->getDbConnection();
        if (in_array($db->getDriverName(), ['mysql', 'mysqli'])) {
            $db->schemaMap = array_merge($db->schemaMap, [
                'mysql' => 'yii\gii\plus\db\mysql\Schema',
                'mysqli' => 'yii\gii\plus\db\mysql\Schema'
            ]);
        }
        if (Yii::getAlias('@common', false)) {
            $this->ns = 'common\models\base';
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
            [['ns'], 'match', 'pattern' => '~\\\\base$~'],
            [['modelClass'], 'match', 'pattern' => '~Base$~'],
            [['baseClass'], 'validateClass', 'params' => ['extends' => 'yii\boost\db\ActiveRecord']],
            [['queryNs'], 'default', 'value' => function (Generator $model, $attribute) {
                return preg_replace('~\\\\base$~', '\query\base', $model->ns);
            }],
            [['queryNs'], 'match', 'pattern' => '~\\\\query\\\\base$~'],
            [['queryClass'], 'match', 'pattern' => '~QueryBase$~'],
            [['queryBaseClass'], 'validateClass', 'params' => ['extends' => 'yii\boost\db\ActiveQuery']]
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
        return ['model.php', 'model-part.php', 'query.php', 'query-part.php'];
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
     * @return string[]
     */
    public function getBaseClassAutoComplete()
    {
        return ['yii\boost\db\ActiveRecord'];
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
     * @return string[]
     */
    public function getQueryBaseClassAutoComplete()
    {
        return ['yii\boost\db\ActiveQuery'];
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
    protected $relationsDone = false;

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
        $booleanAttributes = [];
        $integerAttributes = [];
        $uIntegerAttributes = [];
        $numberAttributes = [];
        $uNumberAttributes = [];
        $matchPatterns = [];
        $dateAttributes = [];
        $timeAttributes = [];
        $datetimeAttributes = [];
        $defaultExpressions = [];
        $defaultValues = [];
        $defaultNullAttributes = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
                $booleanAttributes[] = $column->name;
            } elseif (in_array($column->type, [Schema::TYPE_SMALLINT, Schema::TYPE_INTEGER, Schema::TYPE_BIGINT])) {
                if ($column->unsigned) {
                    $uIntegerAttributes[] = $column->name;
                } else {
                    $integerAttributes[] = $column->name;
                }
            } elseif (in_array($column->type, [Schema::TYPE_FLOAT, Schema::TYPE_DOUBLE, Schema::TYPE_DECIMAL, Schema::TYPE_MONEY])) {
                if ($column->unsigned) {
                    $uNumberAttributes[] = $column->name;
                } else {
                    $numberAttributes[] = $column->name;
                }
                if (in_array($column->type, [Schema::TYPE_DECIMAL, Schema::TYPE_MONEY])) {
                    $scale = $column->scale;
                    $whole = $column->precision - $scale;
                    if ($whole > 0) {
                        if ($whole == 1) {
                            $pattern = '~^\d';
                        } else {
                            $pattern = '~^\d{1,' . $whole . '}';
                        }
                    } else {
                        $pattern = '~^0';
                    }
                    if ($scale > 0) {
                        if ($scale == 1) {
                            $pattern .= '(?:\.\d)?$~';
                        } else {
                            $pattern .= '(?:\.\d{1,' . $scale . '})?$~';
                        }
                    } else {
                        $pattern .= '$~';
                    }
                    $matchPatterns[$pattern][] = $column->name;
                }
            } elseif ($column->type == Schema::TYPE_DATE) {
                $dateAttributes[] = $column->name;
            } elseif ($column->type == Schema::TYPE_TIME) {
                $timeAttributes[] = $column->name;
            } elseif (in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
                $datetimeAttributes[] = $column->name;
            }
            if (!is_null($column->defaultValue)) {
                if ($column->defaultValue instanceof Expression) {
                    $this->relationUses[$table->fullName][] = 'yii\db\Expression';
                    $defaultExpressions[$column->defaultValue->expression][] = $column->name;
                } else {
                    $defaultValues[$column->defaultValue][] = $column->name;
                }
            } elseif ($column->allowNull) {
                $defaultNullAttributes[] = $column->name;
            }
        }
        $rules = [];
        if (count($booleanAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $booleanAttributes) . '\'], \'filter\', \'filter\' => function ($value) {' . "\n" . '                return $value ? 1 : 0;' . "\n" . '            }, \'skipOnEmpty\' => true]';
            $rules[] = '[[\'' . implode('\', \'', $booleanAttributes) . '\'], \'boolean\']';
        }
        if (count($integerAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $integerAttributes) . '\'], \'integer\']';
        }
        if (count($uIntegerAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $uIntegerAttributes) . '\'], \'integer\', \'min\' => 0]';
        }
        if (count($numberAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $numberAttributes) . '\'], \'number\']';
        }
        if (count($uNumberAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $uNumberAttributes) . '\'], \'number\', \'min\' => 0]';
        }
        foreach ($matchPatterns as $matchPattern => $attributes) {
            $rules[] = '[[\'' . implode('\', \'', $attributes) . '\'], \'match\', \'pattern\' => \'' . $matchPattern . '\']';
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
        foreach (parent::generateRules($table) as $rule) {
            if (!preg_match('~, \'(?:safe|boolean|integer|number)\'\]$~', $rule)) {
                $rules[] = $rule;
            }
        }
        foreach ($defaultExpressions as $defaultExpression => $attributes) {
            $rules[] = '[[\'' . implode('\', \'', $attributes) . '\'], \'default\', \'value\' => new Expression(\'' . $defaultExpression . '\')]';
        }
        foreach ($defaultValues as $defaultValue => $attributes) {
            $rules[] = '[[\'' . implode('\', \'', $attributes) . '\'], \'default\', \'value\' => \'' . $defaultValue . '\']';
        }
        if (count($defaultNullAttributes)) {
            $rules[] = '[[\'' . implode('\', \'', $defaultNullAttributes) . '\'], \'default\', \'value\' => null]';
        }
        return $rules;
    }

    /**
     * @var array
     */
    protected $relationUses = [];

    /**
     * @var array
     */
    protected $buildRelations = [];

    /**
     * @inheritdoc
     */
    protected function generateRelations()
    {
        $db = $this->getDbConnection();
        $relations = [];
        $this->relationUses = [];
        $this->buildRelations = [];
        $generatedRelations = parent::generateRelations();
        foreach ($generatedRelations as $tableName => $tableRelations) {
            $tableSchema = $db->getTableSchema($tableName);
            $relations[$tableName] = [];
            $this->relationUses[$tableName] = [];
            $this->buildRelations[$tableName] = [];
            foreach ($tableRelations as $relationName => $relation) {
                list ($code, $className, $hasMany) = $relation;
                /* @var $nsClassName string|\yii\db\ActiveRecord */
                $nsClassName = Helper::getModelClassByTableName(array_search($className, $this->classNames));
                if ($nsClassName && class_exists($nsClassName)) {
                    $relations[$tableName][$relationName] = [$code, $className, $hasMany];
                    $this->relationUses[$tableName][] = $nsClassName;
                    if ($hasMany || ($relationName == $className)) {
                        foreach ($nsClassName::getTableSchema()->foreignKeys as $foreignKey) {
                            if ($foreignKey[0] == $tableName) {
                                unset($foreignKey[0]);
                                $this->buildRelations[$tableName][$relationName] = [$nsClassName, $className, $foreignKey];
                                break;
                            }
                        }
                    }
                    // via relations
                    if (!$hasMany) {
                        $subTableName = $nsClassName::getTableSchema()->fullName;
                        if ($tableName != $subTableName) {
                            $viaLink = '[]';
                            foreach ($tableSchema->foreignKeys as $foreignKey) {
                                if ($foreignKey[0] == $subTableName) {
                                    unset($foreignKey[0]);
                                    $viaLink = $this->generateRelationLink(array_flip($foreignKey));
                                    break;
                                }
                            }
                            foreach ($generatedRelations[$subTableName] as $subRelationName => $subRelation) {
                                list ($subCode, $subClassName, $subHasMany) = $subRelation;
                                /* @var $subNsClassName string|\yii\db\ActiveRecord */
                                    $subTableName2 = array_search($subClassName, $this->classNames);
                                    if ($subTableName2 != $tableName) {
                                $subNsClassName = Helper::getModelClassByTableName($subTableName2);
                                if ($subNsClassName && class_exists($subNsClassName)) {
                                    if (!$subHasMany && ($subRelationName != $className)) {
                                        if (!array_key_exists($subRelationName, $generatedRelations[$tableName])) {
                                            $subCode = preg_replace('~;$~', "\n" . '            ->viaTable(\'' . $subTableName . ' via_' . $subTableName . '\', ' . $viaLink . ');', $subCode);
                                            $relations[$tableName][$subRelationName] = [$subCode, $subClassName, $subHasMany];
                                            $this->relationUses[$tableName][] = $subNsClassName;
                                        }
                                    }
                                }
                                    }
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
    public function getDbConnection()
    {
        return parent::getDbConnection();
    }

    /**
     * @inheritdoc
     */
    public function render($template, $params = [])
    {
        $output = parent::render($template, $params);
        switch ($template) {
            case 'model.php':
                // fix uses
                $tableName = $params['tableName'];
                if (array_key_exists($tableName, $this->relationUses) && $this->relationUses[$tableName]) {
                    $uses = array_unique($this->relationUses[$tableName]);
                    Helper::sortUses($uses);
                    $output = str_replace('use Yii;', 'use Yii;' . "\n" . 'use ' . implode(';' . "\n" . 'use ', $uses) . ';', $output);
                }
                // fix rules
                $output = preg_replace('~\'targetClass\' \=\> (\w+)Base\:\:className\(\)~', '\'targetClass\' => $1::className()', $output);
                // fix relations
                $nsClassName = $this->ns . '\\' . $params['className'];
                if (class_exists($nsClassName) && is_subclass_of($nsClassName, 'yii\db\ActiveRecord')) {
                    $model = new $nsClassName;
                    $output = preg_replace_callback('~@return \\\\(yii\\\\db\\\\ActiveQuery)\s+\*/\s+public function ([^\(]+)\(\)~', function ($match) use ($model) {
                        if (method_exists($model, $match[2])) {
                            return str_replace($match[1], get_class(call_user_func([$model, $match[2]])) . '|\\' . $match[1], $match[0]);
                        } else {
                            return $match[0];
                        }
                    }, $output);
                }
                $params['relationUses'] = $this->relationUses;
                $params['buildRelations'] = $this->buildRelations;
                $output = preg_replace('~\}(\s*)$~', parent::render('model-part.php', $params) . '}$1', $output);
                break;
            case
            'query.php':
                $code = <<<CODE
    /*public function active()
    {
        return \$this->andWhere('[[status]]=1');
    }*/

CODE;
                $output = str_replace($code, '', $output);
                $output = preg_replace('~\}(\s*)$~', parent::render('query-part.php', $params) . '}$1', $output);
                break;
        }
        $output = preg_replace_callback('~(@return |return new )\\\\((?:\w+\\\\)*\w+\\\\query)\\\\base\\\\(\w+Query)Base~', function ($match) {
            $nsClassName = $match[2] . '\\' . $match[3];
            if (class_exists($nsClassName)) {
                return $match[1] . '\\' . $nsClassName;
            } else {
                return $match[0];
            }
        }, $output);
        $output = preg_replace_callback('~(@see | @return |\[\[)\\\\((?:\w+\\\\)*\w+)\\\\base\\\\(\w+)Base~', function ($match) {
            $nsClassName = $match[2] . '\\' . $match[3];
            if (class_exists($nsClassName)) {
                return $match[1] . '\\' . $nsClassName;
            } else {
                return $match[0];
            }
        }, $output);
        return $output;
    }
}
