<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "test_report".
 *
 * @property integer $pk_small_id
 * @property integer $pk_tiny_id
 */
class TestReportBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pk_small_id', 'pk_tiny_id'], 'integer', 'min' => 0],
            [['pk_small_id', 'pk_tiny_id'], 'default', 'value' => '0'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pk_small_id' => Yii::t('app', 'Pk Small ID'),
            'pk_tiny_id' => Yii::t('app', 'Pk Tiny ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\query\TestReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\TestReportQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Test report');
    }
}
