<?php

namespace yii\gii\plus\db;

use yii\db\ColumnSchema as BaseColumnSchema;

class ColumnSchema extends BaseColumnSchema
{

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
    }
}
