<?php

namespace yii\gii\plus\db;

use yii\base\Object;

class ForeignKeySchema extends Object
{

    /**
     * @var array
     */
    public $key = [];

    /**
     * @param TableSchema $table
     * @param array $key
     */
    public function fix(TableSchema $table, array $key)
    {
        $this->key = $key;
    }
}
