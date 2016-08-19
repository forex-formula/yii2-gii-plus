<?php

use yii\jui\autosearch\AutoComplete;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\plus\generators\fixture\Generator */

echo $form->field($generator, 'modelClass')->widget(AutoComplete::className(), [
    'source' => $generator->getModelClassAutoComplete()
]);
echo $form->field($generator, 'fixtureBaseClass');
