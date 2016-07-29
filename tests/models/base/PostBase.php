<?php

namespace app\models\base;

use app\models\Blog;
use app\models\BlogType;
use app\models\Comment;
use app\models\CommentReport;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property integer $blog_id
 * @property string $name
 * @property string $text
 * @property integer $enabled
 * @property string $created_at
 * @property string $updated_at
 * @property integer $deleted
 *
 * @property Comment[] $comments
 * @property CommentReport[] $commentReports
 * @property Blog $blog
 * @property BlogType $blogType
 */
class PostBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'deleted'], 'boolean'],
            [['blog_id'], 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['blog_id', 'name', 'text'], 'required'],
            [['text'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['blog_id', 'name'], 'unique', 'targetAttribute' => ['blog_id', 'name'], 'message' => 'The combination of Блог and Название has already been taken.'],
            [['blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blog::className(), 'targetAttribute' => ['blog_id' => 'id']],
            [['created_at', 'updated_at'], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
            [['enabled', 'deleted'], 'default', 'value' => '0'],
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
     * @return \app\models\query\CommentQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * @return \app\models\query\CommentReportQuery
     */
    public function getCommentReports()
    {
        return $this->hasMany(CommentReport::className(), ['post_id' => 'id']);
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
     * @inheritdoc
     * @return \app\models\query\PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PostQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Пост');
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
        return ['blog_id', 'name'];
    }

    /**
     * @return Comment
     */
    public function newComment()
    {
        $model = new Comment;
        $model->post_id = $this->id;
        return $model;
    }

    /**
     * @return CommentReport
     */
    public function newCommentReport()
    {
        $model = new CommentReport;
        $model->post_id = $this->id;
        return $model;
    }
}
