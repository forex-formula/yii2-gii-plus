<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\extra\model\Generator */

echo '<?php';
?>


namespace <?php echo $generator->getNewModelNamespace(); ?>;

<?php echo $generator->getModelUseDirective(); ?>

class <?php echo $generator->getNewModelName(); ?> extends <?php echo $generator->getModelAlias(); ?>

{

    /**
     * @inheritdoc
     * @return <?php echo $generator->getNewQueryName(); ?> the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(<?php echo $generator->getNewQueryName(); ?>::className(), [get_called_class()]);
    }
}
