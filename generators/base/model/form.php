<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\plus\generators\base\model\Generator */

use yii\jui\autosearch\AutoComplete;
use yii\helpers\Html;
use yii\web\JsExpression;

echo $form->field($generator, 'includeFilter');
echo $form->field($generator, 'excludeFilter');
echo $form->field($generator, 'tableName')->widget(AutoComplete::className(), [
    'source' => $generator->getTableNameAutoComplete(true)
]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns')->widget(AutoComplete::className(), [
    'source' => $generator->getNsAutoComplete(),
    'clientOptions' => ['select' => new JsExpression('function (event, ui) { jQuery(\'#' . Html::getInputId($generator, 'queryNs') . '\').val(\'\'); }')]
]);
echo $form->field($generator, 'baseClass')->widget(AutoComplete::className(), [
    'source' => $generator->getBaseClassAutoComplete()
]);
echo $form->field($generator, 'db')->dropDownList($generator->getDbListItems());
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'useSchemaName')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs')->widget(AutoComplete::className(), [
    'source' => $generator->getQueryNsAutoComplete()
]);
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass')->widget(AutoComplete::className(), [
    'source' => $generator->getQueryBaseClassAutoComplete()
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
