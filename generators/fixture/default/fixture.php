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
        /* @var $relationFixtureClass string|\yii\boost\test\ActiveFixture */
        $relationFixtureClass = $fixtureNs . '\\' . $relationClass::shortName();
        if (class_exists($relationFixtureClass)) {
            $depends[] = $relationFixtureClass;
        }
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
        /* @var $relationFixtureClass string|\yii\boost\test\ActiveFixture */
        $relationFixtureClass = $fixtureNs . '\\' . $relationClass::shortName();
        if (class_exists($relationFixtureClass)) {
            $backDepends[] = $relationFixtureClass;
        }
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
