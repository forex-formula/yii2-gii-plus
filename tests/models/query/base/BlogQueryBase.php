<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\base\BlogBase]].
 *
 * @see \app\models\Blog
 */
class BlogQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Blog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Blog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
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
