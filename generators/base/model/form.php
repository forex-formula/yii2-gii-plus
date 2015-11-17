<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\base\model\Generator */
/* @var $form yii\widgets\ActiveForm */

use yii\jui\autocomplete2\AutoComplete;
use yii\gii\plus\helpers\Helper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Json;

$jsPattern = <<<JS
function (request, response) {
    var data = {data};
    response(data[jQuery('#{db}').val()]);
}
JS;

$jsExpression = new JsExpression(strtr($jsPattern, [
    '{data}' => Json::htmlEncode(Helper::getDbConnectionTableNames()),
    '{db}' => Html::getInputId($generator, 'db')
]));

echo $form->field($generator, 'tableName')->widget(AutoComplete::classname(), [
    'source' => $jsExpression
]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns')->widget(AutoComplete::classname(), [
    'source' => ['app\models\base']
]);
echo $form->field($generator, 'baseClass')->widget(AutoComplete::classname(), [
    'source' => ['yii\db\ActiveRecord', 'yii\boost\db\ActiveRecord']
]);
$dbConnections = Helper::getDbConnections();
echo $form->field($generator, 'db')->dropDownList(array_combine($dbConnections, $dbConnections));
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs')->widget(AutoComplete::classname(), [
    'source' => ['app\models\query\base']
]);
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass')->widget(AutoComplete::classname(), [
    'source' => ['yii\db\ActiveQuery', 'yii\boost\db\ActiveQuery']
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
