<?php

namespace yii\gii\plus\db;

use yii\db\TableSchema as BaseTableSchema;

class TableSchema extends BaseTableSchema
{

    /**
     * @var string
     */
    public $comment;

    /**
     * @var array
     */
    public $uniqueKeys = [];

    /**
     * @var PrimaryKeySchema
     */
    public $pk;

    /**
     * @var ForeignKeySchema[]
     */
    public $fks = [];

    /**
     * @var UniqueKeySchema[]
     */
    public $uks = [];
}
