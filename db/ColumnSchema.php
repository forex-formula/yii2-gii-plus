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
}
