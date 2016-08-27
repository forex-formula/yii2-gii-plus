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

// relations
$singularRelations = [];
$pluralRelations = [];
foreach ($relations as $relationName => $relation) {
    list ($code, $_className, $hasMany) = $relation;
    if ($hasMany) {
        $pluralRelations[] = $relationName;
    } else {
        $singularRelations[] = $relationName;
    }
}
if (count($singularRelations)) {
    echo '
    /**
     * @return array
     */
    public static function singularRelations()
    {
        return [\'', implode('\', \'', $singularRelations), '\'];
    }
';
}
if (count($pluralRelations)) {
    echo '
    /**
     * @return array
     */
    public static function pluralRelations()
    {
        return [\'', implode('\', \'', $pluralRelations), '\'];
    }
';
}

// title
$title = Inflector::titleize($tableName);
$db = $generator->getDbConnection();
if ($generator->generateLabelsFromComments && in_array($db->getDriverName(), ['mysql', 'mysqli'])) {
    $row = $db->createCommand('SHOW CREATE TABLE ' . $db->quoteTableName($tableName))->queryOne();
    if (is_array($row) && (count($row) == 2) && preg_match('~\)([^\)]*)$~', array_values($row)[1], $match)) {
        $tableOptions = $match[1];
        if (preg_match('~COMMENT\s*\=?\s*\'([^\']+)\'~i', $tableOptions, $match)) {
            $title = $match[1];
        }
    }
}
echo '
    /**
     * @return string
     */
    public static function title()
    {
';
if ($generator->enableI18N) {
    echo '        return Yii::t(\'', $generator->messageCategory, '\', \'', $title, '\');
';
} else {
    echo '        return \'', $title, '\';
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
        echo '
    /**
     * @return ', $className, '
     */
    public function new', Inflector::singularize($relationName), '()
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
                echo '        return ', $foreignModelClass::shortName(), '::findListItems($condition, $params, $orderBy);
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
                echo '        return ', $foreignModelClass::shortName(), '::findFilterListItems($condition, $orderBy);
    }
';
            }
        }
    }
}
