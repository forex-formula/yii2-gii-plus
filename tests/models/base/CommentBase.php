<?php

namespace app\models\base;

use app\models\Blog;
use app\models\Comment;
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
 *
 * @property Comment $parent
 * @property Comment[] $comments
 * @property Blog $blog
 * @property Post $post
 */
class CommentBase extends \yii\db\ActiveRecord
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
            [['blog_id', 'post_id', 'text'], 'required'],
            [['blog_id', 'post_id', 'parent_id'], 'integer'],
            [['text'], 'string'],
            [['parent_id', 'blog_id', 'post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['parent_id' => 'id', 'blog_id' => 'blog_id', 'post_id' => 'post_id']],
            [['blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blog::className(), 'targetAttribute' => ['blog_id' => 'id']],
            [['post_id', 'blog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id', 'blog_id' => 'blog_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'blog_id' => 'Blog ID',
            'post_id' => 'Post ID',
            'parent_id' => 'Parent ID',
            'text' => 'Text',
        ];
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
     * @return \app\models\query\BlogQuery
     */
    public function getBlog()
    {
        return $this->hasOne(Blog::className(), ['id' => 'blog_id']);
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
