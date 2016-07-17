<?php

namespace app\models\base;

use app\models\Blog;
use app\models\Comment;
use yii\db\Expression;
use app\models\Post;
use Yii;

/**
 * This is the model class for table "comment".
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
 * @property Comment $parent
 * @property Comment[] $comments
 * @property Post $post
 */
class CommentBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
            [['enabled', 'deleted'], 'default', 'value' => '0'],
            [['parent_id'], 'default', 'value' => null],
            [['blog_id', 'post_id', 'text'], 'required'],
            [['blog_id', 'post_id', 'parent_id', 'enabled', 'deleted'], 'integer'],
            [['text'], 'string'],
            [['blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blog::className(), 'targetAttribute' => ['blog_id' => 'id']],
            [['parent_id', 'blog_id', 'post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['parent_id' => 'id', 'blog_id' => 'blog_id', 'post_id' => 'post_id']],
            [['post_id', 'blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id', 'blog_id' => 'blog_id']],
            [['enabled', 'deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
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
            'post_id' => 'Пост',
            'parent_id' => 'Родительский комментарий',
            'text' => 'Текст',
            'enabled' => 'Включено',
            'created_at' => 'Создано в',
            'updated_at' => 'Обновлено в',
            'deleted' => 'Deleted',
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
     * @return \app\models\query\CommentQuery
     */
    public function getParent()
    {
        return $this->hasOne(Comment::className(), ['id' => 'parent_id', 'blog_id' => 'blog_id', 'post_id' => 'post_id']);
    }

    /**
     * @return \app\models\query\CommentQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['parent_id' => 'id', 'blog_id' => 'blog_id', 'post_id' => 'post_id']);
    }

    /**
     * @return \app\models\query\PostQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id', 'blog_id' => 'blog_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CommentQuery(get_called_class());
    }

    /**
     * @return Comment
     */
    public function newComment()
    {
        $model = new Comment;
        $model->parent_id = $this->id;
        $model->blog_id = $this->blog_id;
        $model->post_id = $this->post_id;
        return $model;
    }
}
