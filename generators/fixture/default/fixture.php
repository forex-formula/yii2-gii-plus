<?php

use yii\gii\plus\helpers\Helper;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\fixture\Generator */
/* @var $ns string */
/* @var $modelName string */
/* @var $modelClass string */
/* @var $fixtureNs string */
/* @var $fixtureName string */
/* @var $fixtureClass string */
/* @var $baseFixtureName string */
/* @var $baseFixtureClass string */

$uses = [
    $baseFixtureClass
];
Helper::sortUses($uses);

echo '<?php', "\n";
?>

namespace <?= $fixtureNs ?>;

use <?= implode(';' . "\n" . 'use ', $uses) ?>;

/**
 * <?= Inflector::titleize($fixtureName) ?> Fixture
 * @see \<?= $modelClass ?>

 */
class <?= $fixtureName ?> extends <?= $baseFixtureName ?>

{

    public $modelClass = '<?= $modelClass ?>';
}
