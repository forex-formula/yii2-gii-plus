<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property integer $small_id
 * @property integer $tiny_id
 */
class TestBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['small_id', 'tiny_id'], 'required'],
            [['small_id', 'tiny_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'small_id' => 'Small ID',
            'tiny_id' => 'Tiny ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\query\TestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\TestQuery(get_called_class());
    }
}
