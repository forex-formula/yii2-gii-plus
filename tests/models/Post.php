<?php

namespace app\models;

use app\models\base\PostBase;
use app\models\query\PostQuery;
use Yii;

class Post extends PostBase
{

    /**
     * @inheritdoc
     * @return PostQuery
     */
    public static function find()
    {
        return Yii::createObject(PostQuery::className(), [get_called_class()]);
    }
}
