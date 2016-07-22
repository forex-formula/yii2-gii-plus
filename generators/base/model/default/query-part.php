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
        $this->andWhere([$this->a(\'' . $column->name . '\') => 0]);
    }
';
    echo $code;
}

$keyAttributes = [];
$methods = [];

// primary key
$primaryKey = $tableSchema->primaryKey;
if (count($primaryKey)) {
    $keyAttributes = $primaryKey;
    if (count($primaryKey) == 1) {
        $methodName = 'pk';
        $methods[] = $methodName;
        $attribute = $primaryKey[0];
        $attributeArg = Inflector::variablize($attribute);
        $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
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
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
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
            $code .= '            $this->a(\'' . $attribute . '\') => $' . $attributeArgs[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
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
            $code .= '            $this->a(\'' . $attribute . '\') => $' . $attributeArgs[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
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
        $keyAttributes = array_merge($keyAttributes, $uniqueKey);
        if (count($uniqueKey) == 1) {
            $attribute = $uniqueKey[0];
            $attributeArg = Inflector::variablize($attribute);
            $methodName = $attributeArg;
            if (!in_array($methodName, $methods)) {
                $methods[] = $methodName;
                $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
    }
';
                echo $code;
            }
        } else {
            $methodName = Inflector::variablize(implode('_', $uniqueKey));
            if (!in_array($methodName, $methods)) {
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
                    $code .= '            $this->a(\'' . $attribute . '\') => $' . $attributeArgs[$i] . (($i < count($uniqueKey) - 1) ? ',' : '') . '
';
                }
                $code .= '        ]);
    }
';
                echo $code;
            }
        }
    }
} catch (NotSupportedException $e) {
    // do nothing
}

// primary/unique keys
foreach ($keyAttributes as $attribute) {
    $attributeArg = Inflector::variablize($attribute);
    $methodName = $attributeArg;
    if (!in_array($methodName, $methods)) {
        $methods[] = $methodName;
        $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return self
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
    }
';
        echo $code;
    }
}

// enabled filters
$enabledAttributes = [
    'enabled',
    'active',
    'activated',
    'approved'
];
foreach ($enabledAttributes as $attribute) {
    $column = $tableSchema->getColumn($attribute);
    if ($column && in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
        $attributeArg = Inflector::variablize($attribute);
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $code = '
    /**
     * @param int|bool $' . $attributeArg . '
     * @return self
     */
    public function ' . $attribute . '($' . $attributeArg . ' = true)
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ' ? 1 : 0]);
    }
';
            echo $code;
        }
    }
}
