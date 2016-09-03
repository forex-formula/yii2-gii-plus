<?php

namespace yii\gii\plus\db;

use yii\base\Object;

/**
 * @property int $count
 */
class TitleKeySchema extends Object
{

    /**
     * @var string[]
     */
    public $key = [];

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
        $this->key = $table->titleKey;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->key);
    }
}
