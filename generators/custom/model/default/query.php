<?php

use yii\gii\plus\helpers\Helper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\custom\model\Generator */
/* @var $ns string */
/* @var $modelName string */
/* @var $modelClass string */
/* @var $baseModelName string */
/* @var $baseModelClass string */
/* @var $queryNs string */
/* @var $queryName string */
/* @var $queryClass string */
/* @var $baseQueryName string */
/* @var $baseQueryClass string */

$uses = [
    $baseQueryClass
];
Helper::sortUses($uses);

echo '<?php', "\n";
?>

namespace <?= $queryNs ?>;

use <?= implode(';' . "\n" . 'use ', $uses) ?>;

class <?= $queryName ?> extends <?= $baseQueryName ?>

{
}
