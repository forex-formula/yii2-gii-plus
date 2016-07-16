<?php

namespace app\models\base;

use app\models\Blog;
use app\models\Comment;
use Yii;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property integer $blog_id
 * @property string $name
 * @property string $text
 * @property integer $enabled
 * @property integer $deleted
 *
 * @property Comment[] $comments
 * @property Blog $blog
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
            [['blog_id', 'name', 'text'], 'required'],
            [['blog_id', 'enabled', 'deleted'], 'integer'],
            [['text'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['blog_id', 'name'], 'unique', 'targetAttribute' => ['blog_id', 'name'], 'message' => 'The combination of Блог and Название has already been taken.'],
            [['blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blog::className(), 'targetAttribute' => ['blog_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'blog_id' => 'Блог',
            'name' => 'Название',
            'text' => 'Текст',
            'enabled' => 'Включено',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \app\models\query\CommentQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id', 'blog_id' => 'blog_id']);
    }

    /**
     * @return \app\models\query\BlogQuery
     */
    public function getBlog()
    {
        return $this->hasOne(Blog::className(), ['id' => 'blog_id']);
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
     * @return Comment
     */
    public function newComment()
    {
        $model = new Comment;
        $model->post_id = $this->id;
        $model->blog_id = $this->blog_id;
        return $model;
    }
}
