<?php

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\fixture\Generator */
/* @var $ns string */
/* @var $modelName string */
/* @var $modelClass string|\yii\boost\db\ActiveRecord */
/* @var $fixtureNs string */
/* @var $fixtureName string */
/* @var $fixtureClass string|\yii\boost\test\ActiveFixture */
/* @var $baseFixtureName string */
/* @var $baseFixtureClass string|\yii\boost\test\ActiveFixture */
/* @var $dataFile string */

echo '<?php

/* ', Inflector::titleize($fixtureName), ' data-file */
/* @see ', $fixtureClass, ' */
/* @see ', $modelClass, ' */

return [
    /*[
';
$columns = array_values($modelClass::getTableSchema()->columns);
foreach ($columns as $i => $column) {
    $comma = ($i < count($columns) - 1) ? ',' : '';
    echo '        \'', $column->name, '\' => \'\'', $comma, '
';
}
echo '    ]*/
];
';
