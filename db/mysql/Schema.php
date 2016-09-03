<?php

namespace yii\gii\plus\db\mysql;

use yii\gii\plus\db\ColumnSchema;
use yii\db\mysql\Schema as MysqlSchema;
use yii\base\NotSupportedException;
use PDO;
use yii\gii\plus\db\TableSchema;

class Schema extends MysqlSchema
{

    /**
     * @inheritdoc
     * @return TableSchema
     */
    protected function loadTableSchema($name)
    {
        $table = parent::loadTableSchema($name);
        if (is_object($table)) {
            $table = new TableSchema(get_object_vars($table));
            try {
                $table->uniqueKeys = $this->findUniqueIndexes($table);
            } catch (NotSupportedException $e) {
                // do nothing
            }
            $this->findComment($table);
            $this->findTitleKey($table);
            $table->fix();
        }
        return $table;
    }

    /**
     * @inheritdoc
     * @return ColumnSchema
     */
    protected function loadColumnSchema($info)
    {
        $column = parent::loadColumnSchema($info);
        if (is_object($column)) {
            $column = new ColumnSchema(get_object_vars($column));
        }
        return $column;
    }

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

    /**
     * @param TableSchema $table
     */
    protected function findComment(TableSchema $table)
    {
        $sql = 'SHOW CREATE TABLE ' . $this->db->quoteTableName($table->fullName);
        $row = $this->db->createCommand($sql)->queryOne(PDO::FETCH_NUM);
        if (is_array($row) && (count($row) == 2) && preg_match('~\)([^\)]*)$~', $row[1], $match)) {
            $tableOptions = $match[1];
            if (preg_match('~COMMENT\s*\=?\s*\'([^\']+)\'~', $tableOptions, $match)) {
                $table->comment = $match[1];
            }
        }
    }

    /**
     * @param TableSchema $table
     */
    protected function findTitleKey(TableSchema $table)
    {
        $table->titleKey = $table->primaryKey;
        foreach ($table->uniqueKeys as $uniqueKey) {
            $types = [];
            foreach ($uniqueKey as $attribute) {
                $types[] = $table->getColumn($attribute)->type;
            }
            if (in_array(Schema::TYPE_CHAR, $types) || in_array(Schema::TYPE_STRING, $types)) {
                $table->titleKey = $uniqueKey;
                break;
            }
        }
    }
}
