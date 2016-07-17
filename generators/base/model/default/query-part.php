<?php

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\model\Generator */
/* @var $tableName string */
/* @var $className string */
/* @var $queryClassName string */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] */
/* @var $rules string[] */
/* @var $relations array */
/* @var $modelClassName string */

$primaryKey = $tableSchema->primaryKey;
if (count($primaryKey)) {
    $primaryKeyPhpTypeMap = array_flip($primaryKey);
    foreach ($tableSchema->columns as $column) {
        if ($column->isPrimaryKey) {
            $primaryKeyPhpTypeMap[$column->name] = $column->phpType;
        }
    }
    if (count($primaryKey) == 1) {
        $primaryKeyArg = [Inflector::variablize($primaryKey[0])];
        $code = '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function pk($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([\'[[' . $primaryKey[0] . ']]\' => $' . $primaryKeyArg[0] . ']);
    }
';
        $code .= '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . $primaryKeyArg[0] . '
     * @return self
     */
    public function ' . $primaryKeyArg[0] . '($' . $primaryKeyArg[0] . ')
    {
        return $this->andWhere([\'[[' . $primaryKey[0] . ']]\' => $' . $primaryKeyArg[0] . ']);
    }
';
    } else {
        $primaryKeyArg = [];
        $code = '
    /**
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $primaryKeyArg[$i] = Inflector::variablize($primaryKey[$i]);
            $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArg[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function pk($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            \'[[' . $primaryKey[$i] . ']]\' => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
        $code .= '
    /**
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArg[$i] . '
';
        }
        $code .= '     * @return self
     */
    public function ' . Inflector::variablize(implode('_', $primaryKey)) . '($' . implode(', $', $primaryKeyArg) . ')
    {
        return $this->andWhere([
';
        for ($i = 0; $i < count($primaryKey); $i++) {
            $code .= '            \'[[' . $primaryKey[$i] . ']]\' => $' . $primaryKeyArg[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
        }
        $code .= '        ]);
    }
';
    }
    echo $code;
}
