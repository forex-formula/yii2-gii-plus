<?php

namespace yii\gii\plus\db;

use yii\base\Object;

/**
 * @property int $count
 */
class PrimaryKeySchema extends Object
{

    /**
     * @var string[]
     */
    public $key = [];

    /**
     * @var bool
     */
    public $isStatic;

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
        $this->key = $table->primaryKey;
        $this->isStatic = false;
        if ($this->getCount() == 1) {
            $column = $table->getColumn($this->key[0]);
            if ($column && ($column->name == 'id') && $column->getIsInteger() && ($column->size == 3) && !$column->autoIncrement) {
                $this->isStatic = true;
            }
        }
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->key);
    }
}
