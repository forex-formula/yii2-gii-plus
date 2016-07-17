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
$primaryKeyPhpTypeMap = array_flip($primaryKey);
foreach ($tableSchema->columns as $column) {
    if ($column->isPrimaryKey) {
        $primaryKeyPhpTypeMap[$column->name] = $column->phpType;
    }
}

if (count($primaryKey) == 1) {
    $code = '
    /**
     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[0]] . ' $' . Inflector::variablize($primaryKey[0]) . '
     * @return self
     */
    public function pk($' . Inflector::variablize($primaryKey[0]) . ')
    {
        return $this->andWhere([\'[[' . $primaryKey[0] . ']]\' => $' . Inflector::variablize($primaryKey[0]) . ']);
    }
';
} else {
    $code = '
    /**
';
    $primaryKeyArgs = [];
    for ($i = 0; $i < count($primaryKey); $i++) {
        $primaryKeyArgs[$i] = Inflector::variablize($primaryKey[$i]);
        $code .= '     * @param ' . $primaryKeyPhpTypeMap[$primaryKey[$i]] . ' $' . $primaryKeyArgs[$i] . '
';
    }
    $code .= '     * @return self
     */
    public function pk($' . implode(', $', $primaryKeyArgs) . ')
    {
        return $this->andWhere([
';
    for ($i = 0; $i < count($primaryKey); $i++) {
        $code .= '            \'[[' . $primaryKey[$i] . ']]\' => $' . $primaryKeyArgs[$i] . (($i < count($primaryKey) - 1) ? ',' : '') . '
';
    }
    $code .= '        ]);
    }
';
}
echo $code;
