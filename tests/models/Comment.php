<?php

namespace app\models;

use app\models\base\CommentBase;
use app\models\query\CommentQuery;
use Yii;

class Comment extends CommentBase
{

    /**
     * @inheritdoc
     * @return CommentQuery
     */
    public static function find()
    {
        return Yii::createObject(CommentQuery::className(), [get_called_class()]);
    }
}
