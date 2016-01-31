<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\user\model\Generator */
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
usort($uses, function ($use1, $use2) {
    if (preg_match('~[\\\\\s]([^\\\\\s]+)$~', $use1, $match)) {
        $use1 = $match[1];
    }
    if (preg_match('~[\\\\\s]([^\\\\\s]+)$~', $use2, $match)) {
        $use2 = $match[1];
    }
    return strcasecmp($use1, $use2);
});

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
