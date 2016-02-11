<?php

namespace app\models\base;

use app\models\Comment;
use app\models\Post;
use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $parent_id
 * @property string $text
 *
 * @property Comment $parent
 * @property Comment[] $comments
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
            [['post_id', 'text'], 'required'],
            [['post_id', 'parent_id'], 'integer'],
            [['text'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
        return $this->hasOne(Comment::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \app\models\query\CommentQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['parent_id' => 'id']);
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
     * @return \app\models\query\CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CommentQuery(get_called_class());
    }
}
