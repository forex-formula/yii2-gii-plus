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
/* @var $hasManyRelations array */

// model label
$modelLabel = Inflector::titleize($tableName);
$db = $generator->getDbConnection();
if ($generator->generateLabelsFromComments && ($db->getDriverName() == 'mysql')) {
    $row = $db->createCommand('SHOW CREATE TABLE ' . $db->quoteTableName($tableName))->queryOne();
    if (is_array($row) && (count($row) == 2) && preg_match('~\)([^\)]*)$~', array_values($row)[1], $match)) {
        $tableOptions = $match[1];
        if (preg_match('~COMMENT\s*\=?\s*\'([^\']+)\'~i', $tableOptions, $match)) {
            $modelLabel = $match[1];
        }
    }
}
$code = '
    /**
     * @return string
     */
    public function modelLabel()
    {
';
if ($generator->enableI18N) {
    $code .= '        return Yii::t(\'' . $generator->messageCategory . '\', \'' . $modelLabel . '\');
';
} else {
    $code .= '        return \'' . $modelLabel . '\';
';
}
$code .= '    }
';
echo $code;

// display field
$displayField = $tableSchema->primaryKey;
if (count($displayField)) {
    $code = '
    /**
     * @return string[]
     */
    public function displayField()
    {
        return [\'' . implode('\', \'', $displayField) . '\'];
    }
';
    echo $code;
}

// relations
if (array_key_exists($tableName, $hasManyRelations)) {
    foreach ($hasManyRelations[$tableName] as $relationName => $hasManyRelation) {
        list ($nsClassName, $className, $foreignKey) = $hasManyRelation;
        $code = '
    /**
     * @return ' . $className . '
     */
    public function new' . Inflector::singularize($relationName) . '()
    {
        $model = new ' . $className . ';
';
        foreach ($foreignKey as $key1 => $key2) {
            $code .= '        $model->' . $key1 . ' = $this->' . $key2 . ';
';
        }
        $code .= '        return $model;
    }
';
        echo $code;
    }
}
