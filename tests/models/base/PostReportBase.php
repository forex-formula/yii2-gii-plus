<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "post_report".
 *
 * @property integer $id
 * @property integer $blog_id
 * @property string $name
 * @property string $text
 * @property integer $enabled
 * @property string $created_at
 * @property string $updated_at
 * @property integer $deleted
 */
class PostReportBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'deleted'], 'boolean'],
            [['id', 'blog_id'], 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['blog_id', 'name', 'text'], 'required'],
            [['text'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['id', 'enabled', 'deleted'], 'default', 'value' => '0'],
            [['created_at', 'updated_at'], 'default', 'value' => '0000-00-00 00:00:00'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'blog_id' => Yii::t('app', 'Блог'),
            'name' => Yii::t('app', 'Название'),
            'text' => Yii::t('app', 'Текст'),
            'enabled' => Yii::t('app', 'Включено'),
            'created_at' => Yii::t('app', 'Создано в'),
            'updated_at' => Yii::t('app', 'Обновлено в'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\query\PostReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PostReportQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Post report');
    }
}
