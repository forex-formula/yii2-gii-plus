<?php

use yii\gii\plus\widgets\AutoComplete,
    yii\gii\plus\helpers\Helper,
    yii\helpers\Html,
    yii\web\JsExpression,
    yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\model\Generator */
/* @var $form yii\widgets\ActiveForm */

echo $form->field($generator, 'tableName')->widget(AutoComplete::classname(), ['source' => new JsExpression('function (request, response) { var data = ' . Json::htmlEncode(Helper::getDbConnectionTableNamesMap()) . '; response(data[jQuery(\'#' . Html::getInputId($generator, 'db') . '\').val()]); }')]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db')->dropDownList(Helper::getDbConnections());
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
