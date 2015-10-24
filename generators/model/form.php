<?php

use yii\jui\autocomplete2\AutoComplete;
use yii\gii\plus\helpers\Helper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\model\Generator */
/* @var $form yii\widgets\ActiveForm */

echo $form->field($generator, 'modelClass')->widget(AutoComplete::classname(), [
    'source' => Helper::getBaseModelClasses()
]);
echo $form->field($generator, 'newModelClass');
echo $form->field($generator, 'newQueryClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
