<?php

namespace app\models\base;

use app\models\Comment;
use app\models\Post;
use Yii;

/**
 * This is the model class for table "blog".
 *
 * @property integer $id
 * @property string $name
 * @property integer $enabled
 * @property integer $deleted
 *
 * @property Comment[] $comments
 * @property Post[] $posts
 */
class BlogBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['enabled', 'deleted'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'enabled' => 'Включено',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \app\models\query\CommentQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['blog_id' => 'id']);
    }

    /**
     * @return \app\models\query\PostQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['blog_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\BlogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlogQuery(get_called_class());
    }

    /**
     * @return Comment
     */
    public function newComment()
    {
        $model = new Comment;
        $model->blog_id = $this->id;
        return $model;
    }

    /**
     * @return Post
     */
    public function newPost()
    {
        $model = new Post;
        $model->blog_id = $this->id;
        return $model;
    }
}
