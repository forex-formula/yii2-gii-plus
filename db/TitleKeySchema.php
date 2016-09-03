<?php

namespace yii\gii\plus\db;

use yii\base\Object;

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
    }
}
