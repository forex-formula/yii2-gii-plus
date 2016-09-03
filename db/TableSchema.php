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

    public function fix()
    {
        $this->pk = new PrimaryKeySchema;
        $this->pk->fix($this);
        $this->fks = [];
        foreach ($this->foreignKeys as $foreignKey) {
            $fk = new ForeignKeySchema;
            $fk->fix($this, $foreignKey);
            $this->fks[] = $fk;
        }
        $this->uks = [];
        foreach ($this->uniqueKeys as $uniqueKey) {
            $uk = new UniqueKeySchema;
            $uk->fix($this, $uniqueKey);
            $this->uks[] = $uk;
        }
        $this->tk = new TitleKeySchema;
        $this->tk->fix($this);
    }
}
