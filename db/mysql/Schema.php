<?php

namespace yii\gii\plus\db\mysql;

use yii\db\mysql\Schema as MysqlSchema;

class Schema extends MysqlSchema
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

    /**
     * @inheritdoc
     */
    protected function findConstraints($table)
    {
        parent::findConstraints($table);
        if (!count($table->foreignKeys)) {
            foreach ($table->getColumnNames() as $columnName) {
                if (preg_match('~^((?:pk_)?(\w+)_id)$~', $columnName, $match) && $this->getTableSchema($match[2])) {
                    $table->foreignKeys[] = [$match[2], $match[1] => 'id'];
                }
            }
        }
    }
}
