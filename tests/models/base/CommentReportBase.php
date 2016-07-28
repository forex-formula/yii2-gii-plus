<?php

namespace app\models\base;

use app\models\Blog;
use app\models\BlogType;
use app\models\Post;
use Yii;

/**
 * This is the model class for table "comment_report".
 *
 * @property integer $id
 * @property integer $blog_id
 * @property integer $post_id
 * @property integer $parent_id
 * @property string $text
 * @property integer $enabled
 * @property string $created_at
 * @property string $updated_at
 * @property integer $deleted
 *
 * @property Blog $blog
 * @property BlogType $blogType
 * @property Post $post
 */
class CommentReportBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'deleted'], 'boolean'],
            [['id', 'blog_id', 'post_id', 'parent_id'], 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['blog_id', 'post_id', 'text'], 'required'],
            [['text'], 'string'],
            [['blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blog::className(), 'targetAttribute' => ['blog_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['id', 'enabled', 'deleted'], 'default', 'value' => '0'],
            [['created_at', 'updated_at'], 'default', 'value' => '0000-00-00 00:00:00'],
            [['parent_id'], 'default', 'value' => null],
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
            'post_id' => Yii::t('app', 'Пост'),
            'parent_id' => Yii::t('app', 'Родительский комментарий'),
            'text' => Yii::t('app', 'Текст'),
            'enabled' => Yii::t('app', 'Включено'),
            'created_at' => Yii::t('app', 'Создано в'),
            'updated_at' => Yii::t('app', 'Обновлено в'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @return \app\models\query\BlogQuery
     */
    public function getBlog()
    {
        return $this->hasOne(Blog::className(), ['id' => 'blog_id']);
    }

    /**
     * @return \app\models\query\BlogTypeQuery
     */
    public function getBlogType()
    {
        return $this->hasOne(BlogType::className(), ['id' => 'blog_type_id'])
            ->viaTable('blog via_blog', ['id' => 'blog_id']);
    }

    /**
     * @return \app\models\query\PostQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\CommentReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CommentReportQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Comment report');
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
}
