<?php

use yii\gii\plus\helpers\Helper;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\model\Generator */
/* @var $tableName string */
/* @var $className string */
/* @var $queryClassName string */
/* @var $tableSchema yii\gii\plus\db\TableSchema */
/* @var $labels string[] */
/* @var $rules string[] */
/* @var $relations array */
/* @var $relationUses array */
/* @var $extendedRelations array */

$methods = [];

// isView/isStatic
if ($tableSchema->isView) {
    echo '
    /**
     * @inheritdoc
     */
    public static function tableIsView()
    {
        return true;
    }
';
}
if ($tableSchema->isStatic) {
    echo '
    /**
     * @inheritdoc
     */
    public static function tableIsStatic()
    {
        return true;
    }
';
}

// singular/plural relations
$singularRelations = [];
$pluralRelations = [];
foreach ($relations as $relationName => $relation) {
    list ($code, $_className, $hasMany) = $relation;
    if (strpos($code, '->via') === false) {
        if ($hasMany) {
            $pluralRelations[] = lcfirst($relationName);
        } else {
            $singularRelations[] = lcfirst($relationName);
        }
    }
}
if (count($singularRelations)) {
    echo '
    /**
     * @inheritdoc
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
     * @inheritdoc
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
    if ($column->getIsBoolean()) {
        $booleanAttributes[] = $column->name;
    } elseif ($column->getIsDate()) {
        $dateAttributes[] = $column->name;
    } elseif ($column->getIsDatetime()) {
        $datetimeAttributes[] = $column->name;
    }
}
if (count($booleanAttributes)) {
    echo '
    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public static function datetimeAttributes()
    {
        return ', Helper::implode($datetimeAttributes, 2), ';
    }
';
}

// model title
$modelTitle = Inflector::titleize($tableName);
if ($generator->generateLabelsFromComments && $tableSchema->comment) {
    $modelTitle = $tableSchema->comment;
}
echo '
    /**
     * @inheritdoc
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
$primaryKey = $tableSchema->pk;
if ($primaryKey) {
    echo '
    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ', Helper::implode($primaryKey->key, 2), ';
    }
';
}

// title key
$titleKey = $tableSchema->tk;
if ($titleKey) {
    echo '
    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return ', Helper::implode($titleKey->key, 2), ';
    }

    /**
     * @inheritdoc
     */
    public function getTitleText()
    {
        return $this->', implode(' . static::TITLE_SEPARATOR . $this->', $titleKey->key), ';
    }
';
}

// methods "new"
foreach ($extendedRelations as $relationName => $extendedRelation) {
    list ($code, $className, $hasMany, $nsClassName, $link, $directLink) = $extendedRelation;
    if (!$directLink) {
        if ($hasMany) {
            $methodName = 'new' . Inflector::singularize($relationName);
        } else {
            $methodName = 'new' . $relationName;
        }
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            echo '
    /**
     * @param array $config
     * @return ', $className, '
     */
    public function ', $methodName, '(array $config = [])
    {
        $model = new ', $className, '($config);
';
            foreach ($link as $key1 => $key2) {
                echo '        $model->', $key1, ' = $this->', $key2, ';
';
            }
            echo '        return $model;
    }
';
        }
    }
}

// use
if (array_key_exists($tableName, $relationUses) && in_array('yii\db\Expression', $relationUses[$tableName])) {
    $dbExpression = 'Expression';
} else {
    $dbExpression = '\yii\db\Expression';
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

// primary key by unique keys
$primaryKey = $tableSchema->pk;
if ($primaryKey) {
    if ($primaryKey->getCount() == 1) {
        // unique keys
        foreach ($tableSchema->uks as $uniqueKey) {
            if ($uniqueKey->getCount() == 1) {
                $attribute1 = $primaryKey->key[0];
                $attribute1Type = $tableSchema->getColumn($attribute1)->phpType;
                $attribute2 = $uniqueKey->key[0];
                $attribute2Arg = Inflector::variablize($attribute2);
                $attribute2Type = $tableSchema->getColumn($attribute2)->phpType;
                $methodName = Inflector::variablize(implode('_', ['pk', 'by', $attribute2]));
                if (!in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                    echo '
    /**
     * @param ', $attribute2Type, ' $', $attribute2Arg, '
     * @return ', $attribute1Type, '
     */
    public static function ', $methodName, '($', $attribute2Arg, ')
    {
        return static::find()->select([\'', $attribute1, '\'])->', $attribute2Arg, '($', $attribute2Arg, ')->scalar();
    }
';
                }
                $methodName = Inflector::variablize(implode('_', [$attribute1, 'by', $attribute2]));
                if (!in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                    echo '
    /**
     * @param ', $attribute2Type, ' $', $attribute2Arg, '
     * @return ', $attribute1Type, '
     */
    public static function ', $methodName, '($', $attribute2Arg, ')
    {
        return static::find()->select([\'', $attribute1, '\'])->', $attribute2Arg, '($', $attribute2Arg, ')->scalar();
    }
';
                }
            }
        }
    }
}

// unique keys by primary key
foreach ($tableSchema->uks as $uniqueKey) {
    if ($uniqueKey->getCount() == 1) {
        // primary key
        $primaryKey = $tableSchema->pk;
        if ($primaryKey) {
            if ($primaryKey->getCount() == 1) {
                $attribute1 = $uniqueKey->key[0];
                $attribute1Type = $tableSchema->getColumn($attribute1)->phpType;
                $attribute2 = $primaryKey->key[0];
                $attribute2Arg = Inflector::variablize($attribute2);
                $attribute2Type = $tableSchema->getColumn($attribute2)->phpType;
                $methodName = Inflector::variablize(implode('_', [$attribute1, 'by', 'pk']));
                if (!in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                    echo '
    /**
     * @param ', $attribute2Type, ' $', $attribute2Arg, '
     * @return ', $attribute1Type, '
     */
    public static function ', $methodName, '($', $attribute2Arg, ')
    {
        return static::find()->select([\'', $attribute1, '\'])->pk($', $attribute2Arg, ')->scalar();
    }
';
                }
                $methodName = Inflector::variablize(implode('_', [$attribute1, 'by', $attribute2]));
                if (!in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                    echo '
    /**
     * @param ', $attribute2Type, ' $', $attribute2Arg, '
     * @return ', $attribute1Type, '
     */
    public static function ', $methodName, '($', $attribute2Arg, ')
    {
        return static::find()->select([\'', $attribute1, '\'])->', $attribute2Arg, '($', $attribute2Arg, ')->scalar();
    }
';
                }
            }
        }
    }
}

// unique keys by unique keys
foreach ($tableSchema->uks as $uniqueKey1) {
    if ($uniqueKey1->getCount() == 1) {
        foreach ($tableSchema->uks as $uniqueKey2) {
            if (($uniqueKey2->getCount() == 1) && ($uniqueKey1->key[0] != $uniqueKey2->key[0])) {
                $attribute1 = $uniqueKey1->key[0];
                $attribute1Type = $tableSchema->getColumn($attribute1)->phpType;
                $attribute2 = $uniqueKey2->key[0];
                $attribute2Arg = Inflector::variablize($attribute2);
                $attribute2Type = $tableSchema->getColumn($attribute2)->phpType;
                $methodName = Inflector::variablize(implode('_', [$attribute1, 'by', $attribute2]));
                if (!in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                    echo '
    /**
     * @param ', $attribute2Type, ' $', $attribute2Arg, '
     * @return ', $attribute1Type, '
     */
    public static function ', $methodName, '($', $attribute2Arg, ')
    {
        return static::find()->select([\'', $attribute1, '\'])->pk($', $attribute2Arg, ')->scalar();
    }
';
                }
            }
        }
    }
}
