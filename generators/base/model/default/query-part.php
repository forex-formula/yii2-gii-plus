<?php

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
/* @var $modelClassName string */

// deleted filter
$column = $tableSchema->getColumn('deleted');
if ($column && in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
    $code = '
    public function init()
    {
        parent::init();
        $this->andWhere([\'[[' . $column->name . ']]\' => 0]);
    }
';
    echo $code;
}

// primary key
$primaryKey = $tableSchema->primaryKey;
if (count($primaryKey)) {
    $primaryKeyPhpTypeMap = array_flip($primaryKey);
    foreach ($tableSchema->columns as $column) {
        if (array_key_exists($column->name, $primaryKeyPhpTypeMap)) {
            $primaryKeyPhpTypeMap[$column->name] = $column->phpType;
        }
    }
    if (count($primaryKey) == 1) {
        $primaryKeyArg = [Inflector::variablize($primaryKey[0])];
        $code = '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function pk($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([\'[[' . $primaryKey[0] . ']]\' => $' . $primaryKeyArg[0] . ']);
    }
';
        $code .= '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function ' . $primaryKeyArg[0] . '($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([\'[[' . $primaryKey[0] . ']]\' => $' . $primaryKeyArg[0] . ']);
    }
';
    } else {
        $primaryKeyArg = [];
        $code = '
    /**
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $primaryKeyArg[$i] = Inflector::variablize($primaryKey[$i]);
            $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArg[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function pk($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            \'[[' . $primaryKey[$i] . ']]\' => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
        $code .= '
    /**
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArg[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function ' . Inflector::variablize(implode('_', $primaryKey)) . '($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            \'[[' . $primaryKey[$i] . ']]\' => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
    }
    echo $code;
}

// unique indexes
try {
    $uniqueIndexes = $generator->getDbConnection()->getSchema()->findUniqueIndexes($tableSchema);
    foreach ($uniqueIndexes as $uniqueKey) {
        $uniqueKeyPhpTypeMap = array_flip($uniqueKey);
        foreach ($tableSchema->columns as $column) {
            if (array_key_exists($column->name, $uniqueKeyPhpTypeMap)) {
                $uniqueKeyPhpTypeMap[$column->name] = $column->phpType;
            }
        }
        if (count($uniqueKey) == 1) {
            $uniqueKeyArg = [Inflector::variablize($uniqueKey[0])];
            $code = '
    /**
     * @param ' . $uniqueKeyPhpTypeMap[$uniqueKey[0]] . ' $' . $uniqueKeyArg[0] . '
     * @return self
     */
    public function ' . $uniqueKeyArg[0] . '($' . $uniqueKeyArg[0] . ')
    {
        return $this->andWhere([\'[[' . $uniqueKey[0] . ']]\' => $' . $uniqueKeyArg[0] . ']);
    }
';
        } else {
            $uniqueKeyArg = [];
            $code = '
    /**
';
            for ($i = 0; $i < count($uniqueKey); $i++) {
                $uniqueKeyArg[$i] = Inflector::variablize($uniqueKey[$i]);
                $code .= '     * @param ' . $uniqueKeyPhpTypeMap[$uniqueKey[$i]] . ' $' . $uniqueKeyArg[$i] . '
';
            }
            $code .= '     * @return self
     */
    public function ' . Inflector::variablize(implode('_', $uniqueKey)) . '($' . implode(', $', $uniqueKeyArg) . ')
    {
        return $this->andWhere([
';
            for ($i = 0; $i < count($uniqueKey); $i++) {
                $code .= '            \'[[' . $uniqueKey[$i] . ']]\' => $' . $uniqueKeyArg[$i] . (($i < count($uniqueKey) - 1) ? ',' : '') . '
';
            }
            $code .= '        ]);
    }
';
        }
        echo $code;
    }
} catch (NotSupportedException $e) {
    // do nothing
}

// enabled filters
$enabledAttributes = ['enabled', 'active', 'activated', 'approved'];
foreach ($enabledAttributes as $enabledAttribute) {
    $column = $tableSchema->getColumn($enabledAttribute);
    if ($column && in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
        $enabledAttributeArg = Inflector::variablize($enabledAttribute);
        $code = '
    /**
     * @param int|bool $' . $enabledAttributeArg . '
     * @return self
     */
    public function ' . $column->name . '($' . $enabledAttributeArg . ' = true)
    {
        return $this->andWhere([\'[[' . $column->name . ']]\' => $' . $enabledAttributeArg . ' ? 1 : 0]);
    }
';
        echo $code;
    }
}
