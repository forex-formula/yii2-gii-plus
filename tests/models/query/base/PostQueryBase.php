<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\base\PostBase]].
 *
 * @see \app\models\Post
 */
class PostQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Post[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Post|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return Comment
     */
    public function newComments()
    {
        $model = new Comment;
        $model->post_id = $this->id;
        $model->blog_id = $this->blog_id;
        return $model;
    }
}
