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
    $baseModelClass,
    $queryClass,
    'Yii'
];
Helper::sortUses($uses);

echo '<?php', "\n";
?>

namespace <?= $ns ?>;

use <?= implode(';' . "\n" . 'use ', $uses) ?>;

class <?= $modelName ?> extends <?= $baseModelName ?>

{

    /**
     * @inheritdoc
     * @return <?= $queryName ?>

     */
    public static function find()
    {
        return Yii::createObject(<?= $queryName ?>::className(), [get_called_class()]);
    }
}
