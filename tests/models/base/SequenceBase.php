<?php

namespace app\models\base;

use app\models\Sequence;
use Yii;

/**
 * This is the model class for table "sequence".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 *
 * @property Sequence $parent
 * @property Sequence $sequence
 */
class SequenceBase extends \yii\db\ActiveRecord
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
            [['parent_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['parent_id'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sequence::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \app\models\query\SequenceQuery
     */
    public function getParent()
    {
        return $this->hasOne(Sequence::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \app\models\query\SequenceQuery
     */
    public function getSequence()
    {
        return $this->hasOne(Sequence::className(), ['parent_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\SequenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\SequenceQuery(get_called_class());
    }
}
