<?php

namespace yii\gii\plus\db\mysql;

use yii\db\mysql\Schema as YiiMysqlSchema;

class Schema extends YiiMysqlSchema
{

    /**
     * @inheritdoc
     */
    protected function findColumns($table)
    {
        if (parent::findColumns($table)) {
            if (!count($table->primaryKey)) {
                $columnNames = $table->getColumnNames();
                foreach ($columnNames as $columnName) {
                    if (preg_match('~^pk_~', $columnName)) {
                        $table->primaryKey[] = $columnName;
                    }
                }
                if (!count($table->primaryKey)) {
                    $table->primaryKey[] = $columnNames[0];
                }
            }
            return true;
        }
        return false;
    }
}
