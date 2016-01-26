<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\plus\generators\base\model\Generator */

use yii\jui\autosearch\AutoComplete;

echo $form->field($generator, 'tableName')->widget(AutoComplete::className(), [
    'source' => $generator->getTableNameAutoComplete()
]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns')->widget(AutoComplete::className(), [
    'source' => [
        'app\models\base',
        'backend\models\base',
        'common\models\base',
        'console\models\base',
        'frontend\models\base'
    ]
]);
echo $form->field($generator, 'baseClass')->widget(AutoComplete::className(), [
    'source' => [
        'yii\db\ActiveRecord',
        'yii\boost\db\ActiveRecord'
    ]
]);
echo $form->field($generator, 'db')->dropDownList($generator->getDbListItems());
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs')->widget(AutoComplete::className(), [
    'source' => [
        'app\models\query\base',
        'backend\models\query\base',
        'common\models\query\base',
        'console\models\query\base',
        'frontend\models\query\base'
    ]
]);
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass')->widget(AutoComplete::className(), [
    'source' => [
        'yii\db\ActiveQuery',
        'yii\boost\db\ActiveQuery'
    ]
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory')->widget(AutoComplete::className(), [
    'source' => [
        'app',
        'backend',
        'common',
        'console',
        'frontend'
    ]
]);
