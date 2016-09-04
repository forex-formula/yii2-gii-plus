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
     * @var bool
     */
    public $isPrimaryKey;

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->key);
    }

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
        $this->key = $table->titleKey;
        $this->isPrimaryKey = false;
        foreach ($this->key as $columnName) {
            if (in_array($columnName, $table->primaryKey)) {
                $this->isPrimaryKey = true;
                break;
            }
        }
    }
}
