<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\search\Generator */
/* @var $form yii\widgets\ActiveForm */

use yii\jui\autocomplete2\AutoComplete;
use yii\gii\plus\helpers\Helper;

echo $form->field($generator, 'modelClass')->widget(AutoComplete::classname(), [
    'source' => Helper::getModelClasses()
]);
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
