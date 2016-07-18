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
        $this->andWhere([$this->a(\'[[' . $column->name . ']]\') => 0]);
    }
';
    echo $code;
}

$methods = [];

// primary key
$primaryKey = $tableSchema->primaryKey;
$primaryKeyPhpTypeMap = [];
if (count($primaryKey)) {
    $primaryKeyPhpTypeMap = array_flip($primaryKey);
    foreach ($tableSchema->columns as $column) {
        if (array_key_exists($column->name, $primaryKeyPhpTypeMap)) {
            $primaryKeyPhpTypeMap[$column->name] = $column->phpType;
        }
    }
    if (count($primaryKey) == 1) {
        $methodName = 'pk';
        $methods[] = $methodName;
        $primaryKeyArg = [Inflector::variablize($primaryKey[0])];
        $code = '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function ' . $methodName . '($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([$this->a(\'[[' . $primaryKey[0] . ']]\') => $' . $primaryKeyArg[0] . ']);
    }
';
        $methodName = $primaryKeyArg[0];
        $methods[] = $methodName;
        $code .= '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function ' . $methodName . '($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([$this->a(\'[[' . $primaryKey[0] . ']]\') => $' . $primaryKeyArg[0] . ']);
    }
';
    } else {
        $methodName = 'pk';
        $methods[] = $methodName;
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
    public function ' . $methodName . '($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            $this->a(\'[[' . $primaryKey[$i] . ']]\') => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
        $methodName = Inflector::variablize(implode('_', $primaryKey));
        $methods[] = $methodName;
        $code .= '
    /**
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArg[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function ' . $methodName . '($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            $this->a(\'[[' . $primaryKey[$i] . ']]\') => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
    }
    echo $code;
}

// unique indexes
$uniqueKeyPhpTypeMap = [];
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
            $methodName = $uniqueKeyArg[0];
            $methods[] = $methodName;
            $code = '
    /**
     * @param ' . $uniqueKeyPhpTypeMap[$uniqueKey[0]] . ' $' . $uniqueKeyArg[0] . '
     * @return self
     */
    public function ' . $methodName . '($' . $uniqueKeyArg[0] . ')
    {
        return $this->andWhere([$this->a(\'[[' . $uniqueKey[0] . ']]\') => $' . $uniqueKeyArg[0] . ']);
    }
';
        } else {
            $methodName = Inflector::variablize(implode('_', $uniqueKey));
            $methods[] = $methodName;
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
    public function ' . $methodName . '($' . implode(', $', $uniqueKeyArg) . ')
    {
        return $this->andWhere([
';
            for ($i = 0; $i < count($uniqueKey); $i++) {
                $code .= '            $this->a(\'[[' . $uniqueKey[$i] . ']]\') => $' . $uniqueKeyArg[$i] . (($i < count($uniqueKey) - 1) ? ',' : '') . '
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

// primary/unique keys
$keyPhpTypeMap = array_merge($primaryKeyPhpTypeMap, $uniqueKeyPhpTypeMap);
foreach ($keyPhpTypeMap as $key => $phpType) {
    $keyArg = Inflector::variablize($key);
    $methodName = $keyArg;
    if (!in_array($methodName, $methods)) {
        $methods[] = $methodName;
        $code = '
    /**
     * @param ' . $phpType . ' $' . $keyArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $keyArg . ')
    {
        return $this->andWhere([$this->a(\'[[' . $key . ']]\') => $' . $keyArg . ']);
    }
';
        echo $code;
    }
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
        return $this->andWhere([$this->a(\'[[' . $column->name . ']]\') => $' . $enabledAttributeArg . ' ? 1 : 0]);
    }
';
        echo $code;
    }
}
