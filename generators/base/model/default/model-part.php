<?php

use yii\gii\plus\helpers\Helper;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\model\Generator */
/* @var $tableName string */
/* @var $className string */
/* @var $queryClassName string */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] */
/* @var $rules string[] */
/* @var $relations array */
/* @var $relationUses array */
/* @var $buildRelations array */

$methods = [];

// singular/plural relations
$singularRelations = [];
$pluralRelations = [];
foreach ($relations as $relationName => $relation) {
    list ($code, $_className, $hasMany) = $relation;
    if ($hasMany) {
        $pluralRelations[] = lcfirst($relationName);
    } else {
        $singularRelations[] = lcfirst($relationName);
    }
}
if (count($singularRelations)) {
    echo '
    /**
     * @return string[]
     */
    public static function singularRelations()
    {
        return ', Helper::implode($singularRelations, 2), ';
    }
';
}
if (count($pluralRelations)) {
    echo '
    /**
     * @return string[]
     */
    public static function pluralRelations()
    {
        return ', Helper::implode($pluralRelations, 2), ';
    }
';
}

// boolean/date/datetime attributes
$booleanAttributes = [];
$dateAttributes = [];
$datetimeAttributes = [];
foreach ($tableSchema->columns as $column) {
    if (in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
        $booleanAttributes[] = $column->name;
    } elseif ($column->type == Schema::TYPE_DATE) {
        $dateAttributes[] = $column->name;
    } elseif (in_array($column->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
        $datetimeAttributes[] = $column->name;
    }
}
if (count($booleanAttributes)) {
    echo '
    /**
     * @return string[]
     */
    public static function booleanAttributes()
    {
        return ', Helper::implode($booleanAttributes, 2), ';
    }
';
}
if (count($dateAttributes)) {
    echo '
    /**
     * @return string[]
     */
    public static function dateAttributes()
    {
        return ', Helper::implode($dateAttributes, 2), ';
    }
';
}
if (count($datetimeAttributes)) {
    echo '
    /**
     * @return string[]
     */
    public static function datetimeAttributes()
    {
        return ', Helper::implode($datetimeAttributes, 2), ';
    }
';
}

// model title
$modelTitle = Inflector::titleize($tableName);
$db = $generator->getDbConnection();
if ($generator->generateLabelsFromComments && in_array($db->getDriverName(), ['mysql', 'mysqli'])) {
    $row = $db->createCommand('SHOW CREATE TABLE ' . $db->quoteTableName($tableName))->queryOne();
    if (is_array($row) && (count($row) == 2) && preg_match('~\)([^\)]*)$~', array_values($row)[1], $match)) {
        $tableOptions = $match[1];
        if (preg_match('~COMMENT\s*\=?\s*\'([^\']+)\'~i', $tableOptions, $match)) {
            $modelTitle = $match[1];
        }
    }
}
echo '
    /**
     * @return string
     */
    public static function modelTitle()
    {
';
if ($generator->enableI18N) {
    echo '        return Yii::t(\'', $generator->messageCategory, '\', \'', $modelTitle, '\');
';
} else {
    echo '        return \'', $modelTitle, '\';
';
}
echo '    }
';

// primary key
$primaryKey = $tableSchema->primaryKey;
if (count($primaryKey)) {
    echo '
    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return [\'', implode('\', \'', $primaryKey), '\'];
    }
';
}

// use
if (array_key_exists($tableName, $relationUses) && in_array('yii\db\Expression', $relationUses[$tableName])) {
    $dbExpression = 'Expression';
} else {
    $dbExpression = '\yii\db\Expression';
}

// display field
$displayField = $primaryKey;
try {
    $uniqueIndexes = $db->getSchema()->findUniqueIndexes($tableSchema);
    foreach ($uniqueIndexes as $uniqueKey) {
        $uniqueKeyTypeMap = [];
        foreach ($uniqueKey as $attribute) {
            $uniqueKeyTypeMap[$attribute] = $tableSchema->getColumn($attribute)->type;
        }
        if (in_array(Schema::TYPE_CHAR, $uniqueKeyTypeMap) || in_array(Schema::TYPE_STRING, $uniqueKeyTypeMap)) {
            $displayField = $uniqueKey;
            break;
        }
    }
} catch (NotSupportedException $e) {
    // do nothing
}
if (count($displayField)) {
    echo '
    /**
     * @return string[]|', $dbExpression, '
     */
    public static function displayField()
    {
        return [\'', implode('\', \'', $displayField), '\'];
    }

    /**
     * @return string
     */
    public function getDisplayField()
    {
        return $this->', implode(' . static::DISPLAY_FIELD_SEPARATOR . $this->', $displayField), ';
    }
';
}

// build relations
if (array_key_exists($tableName, $buildRelations)) {
    foreach ($buildRelations[$tableName] as $relationName => $buildRelation) {
        list ($nsClassName, $className, $foreignKey) = $buildRelation;
        $methodName = 'new' . Inflector::singularize($relationName);
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            echo '
    /**
     * @return ', $className, '
     */
    public function ', $methodName, '()
    {
        $model = new ', $className, ';
';
            foreach ($foreignKey as $key1 => $key2) {
                echo '        $model->', $key1, ' = $this->', $key2, ';
';
            }
            echo '        return $model;
    }
';
        }
    }
}

// list items
foreach ($tableSchema->foreignKeys as $foreignKey) {
    $foreignTableName = $foreignKey[0];
    unset($foreignKey[0]);
    /* @var $foreignModelClass string|\yii\boost\db\ActiveRecord */
    $foreignModelClass = Helper::getModelClassByTableName($foreignTableName);
    if ($foreignModelClass && class_exists($foreignModelClass)) {
        $primaryKey = $foreignModelClass::primaryKey();
        if (count($primaryKey) == 1) {
            $attribute = array_search($primaryKey[0], $foreignKey);
            if ($attribute) {
                $attributeArg = Inflector::variablize($attribute);
                $listItemConditions = [];
                if (count($foreignKey) > 1) {
                    foreach (array_diff($foreignKey, $primaryKey) as $key1 => $key2) {
                        $listItemConditions[] = '\'' . $key2 . '\' => $this->' . $key1;
                    }
                    if (count($listItemConditions) == 1) {
                        $listItemConditions = $listItemConditions[0];
                    } else {
                        $listItemConditions = '
                ' . implode(',
                ', $listItemConditions) . '
            ';
                    }
                }
                echo '
    /**
     * @param string|array|', $dbExpression, ' $condition
     * @param array $params
     * @param string|array|', $dbExpression, ' $orderBy
     * @return array
     */
    public function ', $attributeArg, 'ListItems($condition = null, $params = [], $orderBy = null)
    {
';
                if ($listItemConditions) {
                    echo '        if (is_null($condition)) {
            $condition = [', $listItemConditions, '];
        }
';
                }
                echo '        return ', $foreignModelClass::classShortName(), '::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|', $dbExpression, ' $orderBy
     * @return array
     */
    public function ', $attributeArg, 'FilterListItems(array $condition = [], $orderBy = null)
    {
';
                if ($listItemConditions) {
                    echo '        if (!count($condition)) {
            $condition = [', $listItemConditions, '];
        }
';
                }
                echo '        return ', $foreignModelClass::classShortName(), '::findFilterListItems($condition, $orderBy);
    }
';
            }
        }
    }
}
