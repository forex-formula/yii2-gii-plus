<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\user\model\Generator */
/* @var $ns string */
/* @var $modelName string */
/* @var $modelClass string */
/* @var $modelBaseClass string */
/* @var $queryNs string */
/* @var $queryName string */
/* @var $queryClass string */
/* @var $queryBaseClass string */

echo '<?php', "\n";
?>

namespace <?= $ns ?>;

use Yii;

class <?= $modelName ?> extends \<?= $modelBaseClass ?>

{

    /**
     * @inheritdoc
     * @return \<?= $queryClass ?>

     */
    public static function find()
    {
        return Yii::createObject(\<?= $queryClass ?>::className(), [get_called_class()]);
    }
}
