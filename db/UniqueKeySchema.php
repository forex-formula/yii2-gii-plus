<?php

namespace yii\gii\plus\db;

use yii\base\Object;

/**
 * @property int $count
 */
class UniqueKeySchema extends Object
{

    /**
     * @var string[]
     */
    public $key = [];

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->key);
    }

    /**
     * @param TableSchema $table
     * @param array $key
     */
    public function fix(TableSchema $table, array $key)
    {
        $this->key = $key;
    }
}
