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
        $attribute = $primaryKey[0];
        $attributeArg = Inflector::variablize($attribute);
        $methodName = 'pk';
        $methods[] = $methodName;
        $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'[[' . $attribute . ']]\') => $' . $attributeArg . ']);
    }
';
        $methodName = $attributeArg;
        $methods[] = $methodName;
        $code .= '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'[[' . $attribute . ']]\') => $' . $attributeArg . ']);
    }
';
    } else {
        $methodName = 'pk';
        $methods[] = $methodName;
        $attributeArgs = [];
        $code = '
    /**
';
        foreach ($primaryKey as $i => $attribute) {
            $attributeArgs[$i] = Inflector::variablize($attribute);
            $code .= '     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArgs[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function ' . $methodName . '($' . implode(', $', $attributeArgs) . ')
    {
        return $this->andWhere([
';
        foreach ($primaryKey as $i => $attribute) {
            $code .= '            $this->a(\'[[' . $attribute . ']]\') => $' . $attributeArgs[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
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
        foreach ($primaryKey as $i => $attribute) {
            $code .= '     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArgs[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function ' . $methodName . '($' . implode(', $', $attributeArgs) . ')
    {
        return $this->andWhere([
';
        foreach ($primaryKey as $i => $attribute) {
            $code .= '            $this->a(\'[[' . $attribute . ']]\') => $' . $attributeArgs[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
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
        $uniqueKeyPhpTypeMap = array_merge($uniqueKeyPhpTypeMap, array_flip($uniqueKey));
        foreach ($tableSchema->columns as $column) {
            if (array_key_exists($column->name, $uniqueKeyPhpTypeMap)) {
                $uniqueKeyPhpTypeMap[$column->name] = $column->phpType;
            }
        }
        if (count($uniqueKey) == 1) {
            $attribute = $uniqueKey[0];
            $attributeArg = Inflector::variablize($attribute);
            $methodName = $attributeArg;
            $methods[] = $methodName;
            $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'[[' . $attribute . ']]\') => $' . $attributeArg . ']);
    }
';
        } else {
            $methodName = Inflector::variablize(implode('_', $uniqueKey));
            $methods[] = $methodName;
            $attributeArgs = [];
            $code = '
    /**
';
            foreach ($uniqueKey as $i => $attribute) {
                $attributeArgs[$i] = Inflector::variablize($attribute);
                $code .= '     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArgs[$i] . '
';
            }
            $code .= '     * @return self
     */
    public function ' . $methodName . '($' . implode(', $', $attributeArgs) . ')
    {
        return $this->andWhere([
';
            foreach ($uniqueKey as $i => $attribute) {
                $code .= '            $this->a(\'[[' . $attribute . ']]\') => $' . $attributeArgs[$i] . (($i < count($uniqueKey) - 1) ? ',' : '') . '
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
