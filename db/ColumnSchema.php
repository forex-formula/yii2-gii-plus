<?php

namespace yii\gii\plus\db;

use yii\db\ColumnSchema as BaseColumnSchema;
use yii\db\Schema;

class ColumnSchema extends BaseColumnSchema
{

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
        if (($this->type == Schema::TYPE_SMALLINT) && ($this->size == 1) && $this->unsigned) {
            $this->type = Schema::TYPE_BOOLEAN;
        }
    }

    /**
     * @return bool
     */
    public function getIsBoolean()
    {
        return $this->type == Schema::TYPE_BOOLEAN;
    }

    /**
     * @return bool
     */
    public function getIsInteger()
    {
        return in_array($this->type, [Schema::TYPE_SMALLINT, Schema::TYPE_INTEGER, Schema::TYPE_BIGINT]);
    }

    /**
     * @return bool
     */
    public function getIsNumber()
    {
        return in_array($this->type, [Schema::TYPE_FLOAT, Schema::TYPE_DOUBLE, Schema::TYPE_DECIMAL, Schema::TYPE_MONEY]);
    }
}
