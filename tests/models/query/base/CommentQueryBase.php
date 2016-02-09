<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\base\CommentBase]].
 *
 * @see \app\models\base\CommentBase
 */
class CommentQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\base\CommentBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\base\CommentBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}