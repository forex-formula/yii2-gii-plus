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
     * @var bool
     */
    public $isView;

    /**
     * @var bool
     */
    public $isStatic;

    /**
     * @var array
     */
    public $uniqueKeys = [];

    /**
     * @var string[]
     */
    public $titleKey = [];

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

    /**
     * @var TitleKeySchema
     */
    public $tk;
}
