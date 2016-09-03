<?php

namespace yii\gii\plus\db;

use yii\db\ColumnSchema as BaseColumnSchema;
use yii\db\Schema;

/**
 * @property bool $isBoolean
 * @property bool $isInteger
 * @property bool $isNumber
 * @property bool $isDate
 * @property bool $isTime
 * @property bool $isDatetime
 * @property bool $hasPattern
 * @property string|null $pattern
 * @property bool $hasFormat
 * @property string|null $format
 */
class ColumnSchema extends BaseColumnSchema
{

    /**
     * @param TableSchema $table
     */
    public function fix(TableSchema $table)
    {
        if (($this->type == Schema::TYPE_SMALLINT) && ($this->size == 1) && $this->unsigned) {
            $this->type = Schema::TYPE_BOOLEAN;
        }
    }

    /**
     * @return bool
     */
    public function getIsBoolean()
    {
        return $this->type == Schema::TYPE_BOOLEAN;
    }

    /**
     * @return bool
     */
    public function getIsInteger()
    {
        return in_array($this->type, [Schema::TYPE_SMALLINT, Schema::TYPE_INTEGER, Schema::TYPE_BIGINT]);
    }

    /**
     * @return bool
     */
    public function getIsNumber()
    {
        return in_array($this->type, [Schema::TYPE_FLOAT, Schema::TYPE_DOUBLE, Schema::TYPE_DECIMAL, Schema::TYPE_MONEY]);
    }

    /**
     * @return bool
     */
    public function getIsDate()
    {
        return $this->type == Schema::TYPE_DATE;
    }

    /**
     * @return bool
     */
    public function getIsTime()
    {
        return $this->type == Schema::TYPE_TIME;
    }

    /**
     * @return bool
     */
    public function getIsDatetime()
    {
        return in_array($this->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP]);
    }

    /**
     * @return bool
     */
    public function getHasPattern()
    {
        return in_array($this->type, [Schema::TYPE_DECIMAL, Schema::TYPE_MONEY]);
    }

    /**
     * @return string|null
     */
    public function getPattern()
    {
        if (in_array($this->type, [Schema::TYPE_DECIMAL, Schema::TYPE_MONEY])) {
            $scale = $this->scale;
            $whole = $this->precision - $scale;
            $pattern = '~^';
            if (!$this->unsigned) {
                $pattern .= '\-?';
            }
            if ($whole > 0) {
                if ($whole == 1) {
                    $pattern .= '\d';
                } else {
                    $pattern .= '\d{1,' . $whole . '}';
                }
            } else {
                $pattern .= '0';
            }
            if ($scale > 0) {
                if ($scale == 1) {
                    $pattern .= '(?:\.\d)?';
                } else {
                    $pattern .= '(?:\.\d{1,' . $scale . '})?';
                }
            }
            $pattern .= '$~';
            return $pattern;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function getHasFormat()
    {
        return in_array($this->type, [Schema::TYPE_DATE, Schema::TYPE_TIME, Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP]);
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        if ($this->type == Schema::TYPE_DATE) {
            return 'php:Y-m-d';
        } elseif ($this->type == Schema::TYPE_TIME) {
            return 'php:H:i:s';
        } elseif (in_array($this->type, [Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP])) {
            return 'php:Y-m-d H:i:s';
        }
        return null;
    }
}
