<?php

use yii\gii\plus\helpers\Helper;
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

$uses = [
    $baseFixtureClass
];
Helper::sortUses($uses);

echo '<?php

namespace ', $fixtureNs, ';

use ', implode(';' . "\n" . 'use ', $uses), ';

/**
 * ', Inflector::titleize($fixtureName), ' fixture
 * @see \\', $modelClass, '
 */
class ', $fixtureName, ' extends ', $baseFixtureName, '
{

    public $modelClass = \'', $modelClass, '\';
';

/* @var $model \yii\boost\db\ActiveRecord */
$model = new $modelClass;

// depends
$depends = [];
foreach ($modelClass::singularRelations() as $relationName) {
    /* @var $relationClass string|\yii\boost\db\ActiveRecord */
    $relationClass = $model->getRelationClass($relationName);
    if ($relationClass && class_exists($relationClass)) {
        $depends[] = $fixtureNs . '\\' . $relationClass::shortName();
    }
}
if (count($depends)) {
    if (count($depends) == 1) {
        echo '
    public $depends = [\'', $depends[0], '\'];
';
    } else {
        echo '
    public $depends = [
        \'', implode('\',
        \'', $depends), '\'
    ];
';
    }
}

// backDepends
$backDepends = [];
foreach ($modelClass::pluralRelations() as $relationName) {
    /* @var $relationClass string|\yii\boost\db\ActiveRecord */
    $relationClass = $model->getRelationClass($relationName);
    if ($relationClass && class_exists($relationClass)) {
        $backDepends[] = $fixtureNs . '\\' . $relationClass::shortName();
    }
}
if (count($backDepends)) {
    if (count($backDepends) == 1) {
        echo '
    public $backDepends = [\'', $backDepends[0], '\'];
';
    } else {
        echo '
    public $backDepends = [
        \'', implode('\',
        \'', $backDepends), '\'
    ];
';
    }
}

echo '
    public $dataFile = \'', $dataFile, '\';
}
';
