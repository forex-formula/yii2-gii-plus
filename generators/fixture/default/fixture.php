<?php

use yii\gii\plus\helpers\Helper;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\fixture\Generator */
/* @var $ns string */
/* @var $modelName string */
/* @var $modelClass string|\yii\db\ActiveRecord */
/* @var $fixtureNs string */
/* @var $fixtureName string */
/* @var $fixtureClass string|\yii\test\ActiveFixture */
/* @var $baseFixtureName string */
/* @var $baseFixtureClass string|\yii\test\ActiveFixture */

$uses = [
    $baseFixtureClass
];
Helper::sortUses($uses);

echo '<?php

namespace ', $fixtureNs, ';

use ', implode(';' . "\n" . 'use ', $uses), ';

/**
 * ', Inflector::titleize($fixtureName), ' Fixture
 * @see \\', $modelClass, '
 */
class ', $fixtureName, ' extends ', $baseFixtureName, '
{

    public $modelClass = \'', $modelClass, '\';

    public $depends = [];
}
';
