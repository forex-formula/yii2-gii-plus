<?php

use yii\gii\plus\widgets\AutoComplete,
    yii\gii\plus\helpers\FormHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\search\Generator */
/* @var $form yii\widgets\ActiveForm */

echo $form->field($generator, 'modelClass')->widget(AutoComplete::classname(), [
    'clientOptions' => [
        'source' => FormHelper::getBaseSearchModelClasses(),
        'minLength' => 0
    ]
]);
echo $form->field($generator, 'newModelClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
