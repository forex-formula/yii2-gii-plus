<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\plus\generators\user\model\Generator */

use yii\jui\autosearch\AutoComplete;

echo $form->field($generator, 'baseModelClass')->widget(AutoComplete::className(), [
    'source' => $generator->getBaseModelClassAutoComplete()
]);
