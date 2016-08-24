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
        return $this->andWhere([
';
        foreach ($primaryKey as $i => $attribute) {
            $comma = ($i < count($primaryKey) - 1) ? ',' : '';
            echo '            $this->a(\'', $attribute, '\') => $', $attributeArgs[$i], $comma, '
';
        }
        echo '        ]);
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
        return $this->andWhere([
';
        foreach ($primaryKey as $i => $attribute) {
            $comma = ($i < count($primaryKey) - 1) ? ',' : '';
            echo '            $this->a(\'', $attribute, '\') => $', $attributeArgs[$i], $comma, '
';
        }
        echo '        ]);
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
            $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return $this
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
    }
';
            echo $code;
        }
    } else {
        $methodName = Inflector::variablize(implode('_', $foreignKey));
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $attributeArgs = [];
            $code = '
    /**
';
            foreach ($foreignKey as $i => $attribute) {
                $attributeArgs[$i] = Inflector::variablize($attribute);
                $code .= '     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArgs[$i] . '
';
            }
            $code .= '     * @return $this
     */
    public function ' . $methodName . '($' . implode(', $', $attributeArgs) . ')
    {
        return $this->andWhere([
';
            foreach ($foreignKey as $i => $attribute) {
                $code .= '            $this->a(\'' . $attribute . '\') => $' . $attributeArgs[$i] . (($i < count($foreignKey) - 1) ? ',' : '') . '
';
            }
            $code .= '        ]);
    }
';
            echo $code;
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
                $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return $this
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
                $code .= '     * @return $this
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

// primary/foreign/unique keys
foreach ($keyAttributes as $attribute) {
    $attributeArg = Inflector::variablize($attribute);
    $methodName = $attributeArg;
    if (!in_array($methodName, $methods)) {
        $methods[] = $methodName;
        $code = '
    /**
     * @param ' . $tableSchema->getColumn($attribute)->phpType . ' $' . $attributeArg . '
     * @return $this
     */
    public function ' . $methodName . '($' . $attributeArg . ')
    {
        return $this->andWhere([$this->a(\'' . $attribute . '\') => $' . $attributeArg . ']);
    }
';
        echo $code;
    }
}

// boolean
foreach ($tableSchema->columns as $column) {
    if (($column->name != 'deleted') && in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT]) && ($column->size == 1) && $column->unsigned) {
        $attributeArg = Inflector::variablize($column->name);
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $code = '
    /**
     * @param int|bool $' . $attributeArg . '
     * @return $this
     */
    public function ' . $methodName . '($' . $attributeArg . ' = true)
    {
        return $this->andWhere([$this->a(\'' . $column->name . '\') => $' . $attributeArg . ' ? 1 : 0]);
    }
';
            echo $code;
        }
    }
}

// expires_at
foreach ($tableSchema->columns as $column) {
    if (preg_match('~(?:^|_)expires_at$~', $column->name) && in_array($column->type, [Schema::TYPE_DATE, Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
        $attributeArg = Inflector::variablize(str_replace('expires_at', 'not_expired', $column->name));
        $methodName = $attributeArg;
        if (!in_array($methodName, $methods)) {
            $methods[] = $methodName;
            $code = '
    /**
     * @param bool $' . $attributeArg . '
     * @return $this
     */
    public function ' . $methodName . '($' . $attributeArg . ' = true)
    {
';
            $func = ($column->type == Schema::TYPE_DATE) ? 'CURDATE' : 'NOW';
            if ($column->allowNull) {
                $code .= '        $columnName = $this->a(\'' . $column->name . '\');        
        if ($' . $attributeArg . ') {
            return $this->andWhere($columnName . \' IS NULL OR \' . $columnName . \' > ' . $func . '()\');
        } else {
            return $this->andWhere($columnName . \' IS NOT NULL AND \' . $columnName . \' <= ' . $func . '()\');
        }
';
            } else {
                $code .= '        if ($' . $attributeArg . ') {
            return $this->andWhere($this->a(\'' . $column->name . '\') . \' > ' . $func . '()\');
        } else {
            return $this->andWhere($this->a(\'' . $column->name . '\') . \' <= ' . $func . '()\');
        }
';
            }
            $code .= '    }
';
            echo $code;
        }
    }
}
