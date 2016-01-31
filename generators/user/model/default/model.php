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
/* @var $queryBaseClass string */

echo '<?php', "\n";
?>

namespace <?= $ns ?>;

use <?= $baseModelClass ?>;
use <?= $queryClass ?>;
use Yii;

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
