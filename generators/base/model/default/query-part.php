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

$keyAttributes = [];
$methods = [];

// deleted
$column = $tableSchema->getColumn('deleted');
if ($column && in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
    $methods[] = 'init';
    $attribute = $column->name;
    echo '
    public function init()
    {
        parent::init();
        $this->where(new \yii\boost\db\Expression(\'{a}.', $attribute, ' = 0\', [], [\'query\' => $this]));
    }
';
}

// primary key
$primaryKey = $tableSchema->primaryKey;
if (count($primaryKey)) {
    $keyAttributes = array_merge($keyAttributes, $primaryKey);
    if (count($primaryKey) == 1) {
        $methodName = 'pk';
        $methods[] = $methodName;
        $attribute = $primaryKey[0];
        $attributeArg = Inflector::variablize($attribute);
        $attributeType = $tableSchema->getColumn($attribute)->phpType;
        echo '
    /**
     * @param ', $attributeType, ' $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ')
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ']);
    }
';
        $methodName = $attributeArg;
        $methods[] = $methodName;
        echo '
    /**
     * @param ', $attributeType, ' $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ')
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ']);
    }
';
    } else {
        $methodName = 'pk';
        $methods[] = $methodName;
        $attributeArgs = [];
        $attributeTypes = [];
        echo '
    /**
';
        foreach ($primaryKey as $i => $attribute) {
            $attributeArgs[$i] = Inflector::variablize($attribute);
            $attributeTypes[$i] = $tableSchema->getColumn($attribute)->phpType;
            echo '     * @param ', $attributeTypes[$i], ' $', $attributeArgs[$i], '
';
        }
        echo '     * @return $this
     */
    public function ', $methodName, '($', implode(', $', $attributeArgs), ')
    {
        return $this->andWhere($this->a([
';
        foreach ($primaryKey as $i => $attribute) {
            $comma = ($i < count($primaryKey) - 1) ? ',' : '';
            echo '            \'', $attribute, '\' => $', $attributeArgs[$i], $comma, '
';
        }
        echo '        ]));
    }
';
        $methodName = Inflector::variablize(implode('_', $primaryKey));
        $methods[] = $methodName;
        echo '
    /**
';
        foreach ($primaryKey as $i => $attribute) {
            echo '     * @param ', $attributeTypes[$i], ' $', $attributeArgs[$i], '
';
        }
        echo '     * @return $this
     */
    public function ', $methodName, '($', implode(', $', $attributeArgs), ')
    {
        return $this->andWhere($this->a([
';
        foreach ($primaryKey as $i => $attribute) {
            $comma = ($i < count($primaryKey) - 1) ? ',' : '';
            echo '            \'', $attribute, '\' => $', $attributeArgs[$i], $comma, '
';
        }
        echo '        ]));
    }
';
    }
}

// foreign keys
foreach ($tableSchema->foreignKeys as $foreignKey) {
    unset($foreignKey[0]);
    $foreignKey = array_keys($foreignKey);
    $keyAttributes = array_merge($keyAttributes, $foreignKey);
    if (count($foreignKey) == 1) {
        $attribute = $foreignKey[0];
        $attributeArg = Inflector::variablize($attribute);
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $attributeType = $tableSchema->getColumn($attribute)->phpType;
            echo '
    /**
     * @param ', $attributeType, ' $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ')
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ']);
    }
';
        }
    } else {
        $methodName = Inflector::variablize(implode('_', $foreignKey));
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $attributeArgs = [];
            $attributeTypes = [];
            echo '
    /**
';
            foreach ($foreignKey as $i => $attribute) {
                $attributeArgs[$i] = Inflector::variablize($attribute);
                $attributeTypes[$i] = $tableSchema->getColumn($attribute)->phpType;
                echo '     * @param ', $attributeTypes[$i], ' $', $attributeArgs[$i], '
';
            }
            echo '     * @return $this
     */
    public function ', $methodName, '($', implode(', $', $attributeArgs), ')
    {
        return $this->andWhere($this->a([
';
            foreach ($foreignKey as $i => $attribute) {
                $comma = ($i < count($foreignKey) - 1) ? ',' : '';
                echo '            \'', $attribute, '\' => $', $attributeArgs[$i], $comma, '
';
            }
            echo '        ]));
    }
';
        }
    }
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
                $attributeType = $tableSchema->getColumn($attribute)->phpType;
                echo '
    /**
     * @param ', $attributeType, ' $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ')
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ']);
    }
';
            }
        } else {
            $methodName = Inflector::variablize(implode('_', $uniqueKey));
            if (!in_array($methodName, $methods)) {
                $methods[] = $methodName;
                $attributeArgs = [];
                $attributeTypes = [];
                echo '
    /**
';
                foreach ($uniqueKey as $i => $attribute) {
                    $attributeArgs[$i] = Inflector::variablize($attribute);
                    $attributeTypes[$i] = $tableSchema->getColumn($attribute)->phpType;
                    echo '     * @param ', $attributeTypes[$i], ' $', $attributeArgs[$i], '
';
                }
                echo '     * @return $this
     */
    public function ', $methodName, '($', implode(', $', $attributeArgs), ')
    {
        return $this->andWhere($this->a([
';
                foreach ($uniqueKey as $i => $attribute) {
                    $comma = ($i < count($uniqueKey) - 1) ? ',' : '';
                    echo '            \'', $attribute, '\' => $', $attributeArgs[$i], $comma, '
';
                }
                echo '        ]));
    }
';
            }
        }
    }
} catch (NotSupportedException $e) {
    // do nothing
}

// primary/foreign/unique keys
foreach ($keyAttributes as $attribute) {
    $attributeArg = Inflector::variablize($attribute);
    $methodName = $attributeArg;
    if (!in_array($methodName, $methods)) {
        $methods[] = $methodName;
        $attributeType = $tableSchema->getColumn($attribute)->phpType;
        echo '
    /**
     * @param ', $attributeType, ' $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ')
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ']);
    }
';
    }
}

// boolean
foreach ($tableSchema->columns as $column) {
    if (in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
        $attribute = $column->name;
        $attributeArg = Inflector::variablize($attribute);
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            echo '
    /**
     * @param int|bool $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ' = true)
    {
        return $this->andWhere([$this->a(\'', $attribute, '\') => $', $attributeArg, ' ? 1 : 0]);
    }
';
        }
    }
}

// ...expires_at
foreach ($tableSchema->columns as $column) {
    $attribute = $column->name;
    if (preg_match('~(?:^|_)expires_at$~', $attribute) && in_array($column->type, [Schema::TYPE_DATE, Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
        $attributeArg = Inflector::variablize(str_replace('expires_at', 'not_expired', $attribute));
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            echo '
    /**
     * @param bool $', $attributeArg, '
     * @return $this
     */
    public function ', $methodName, '($', $attributeArg, ' = true)
    {
';
            $funcName = ($column->type == Schema::TYPE_DATE) ? 'CURDATE' : 'NOW';
            if ($column->allowNull) {
                echo '        $columnName = $this->a(\'', $attribute, '\');
        if ($', $attributeArg, ') {
            return $this->andWhere($columnName . \' IS NULL OR \' . $columnName . \' > ', $funcName, '()\');
        } else {
            return $this->andWhere($columnName . \' IS NOT NULL AND \' . $columnName . \' <= ', $funcName, '()\');
        }
';
            } else {
                echo '        if ($', $attributeArg, ') {
            return $this->andWhere($this->a(\'', $attribute, '\') . \' > ', $funcName, '()\');
        } else {
            return $this->andWhere($this->a(\'', $attribute, '\') . \' <= ', $funcName, '()\');
        }
';
            }
            echo '    }
';
        }
    }
}
