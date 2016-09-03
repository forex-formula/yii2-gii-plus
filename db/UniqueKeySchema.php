<?php

namespace yii\gii\plus\db;

use yii\base\Object;

class UniqueKeySchema extends Object
{

    /**
     * @var string[]
     */
    public $key = [];

    /**
     * @param TableSchema $table
     * @param array $key
     */
    public function fix(TableSchema $table, array $key)
    {
    }
}
