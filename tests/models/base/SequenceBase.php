<?php

namespace app\models\base;

use app\models\Sequence;
use Yii;

/**
 * This is the model class for table "sequence".
 *
 * @property integer $id
 * @property integer $previous_id
 * @property double $value
 * @property string $value_expires_at
 *
 * @property Sequence $previous
 * @property Sequence $sequence
 */
class SequenceBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sequence';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['previous_id'], 'integer', 'min' => 0],
            [['value'], 'number', 'min' => 0],
            [['value_expires_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['previous_id'], 'unique'],
            [['previous_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sequence::className(), 'targetAttribute' => ['previous_id' => 'id']],
            [['previous_id', 'value', 'value_expires_at'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'previous_id' => Yii::t('app', 'Previous ID'),
            'value' => Yii::t('app', 'Value'),
            'value_expires_at' => Yii::t('app', 'Value Expires At'),
        ];
    }

    /**
     * @return \app\models\query\SequenceQuery
     */
    public function getPrevious()
    {
        return $this->hasOne(Sequence::className(), ['id' => 'previous_id']);
    }

    /**
     * @return \app\models\query\SequenceQuery
     */
    public function getSequence()
    {
        return $this->hasOne(Sequence::className(), ['previous_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\SequenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\SequenceQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Sequence');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @return string[]
     */
    public static function displayField()
    {
        return ['id'];
    }

    /**
     * @return Sequence
     */
    public function newSequence()
    {
        $model = new Sequence;
        $model->previous_id = $this->id;
        return $model;
    }
}
