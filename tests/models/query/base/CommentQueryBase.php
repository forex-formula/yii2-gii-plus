<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\base\CommentBase]].
 *
 * @see \app\models\Comment
 */
class CommentQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Comment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Comment|array|null
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
        $model->id = $this->id;
        return $model;
    }
}
