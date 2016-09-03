<?php

namespace yii\gii\plus\db;

use yii\base\Object;

/**
 * @property int $count
 */
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
        unset($key[0]);
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->key);
    }
}
