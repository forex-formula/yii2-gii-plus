<?php

namespace app\models;

use app\models\base\BlogBase;
use app\models\query\BlogQuery;
use Yii;

class Blog extends BlogBase
{

    /**
     * @inheritdoc
     * @return BlogQuery
     */
    public static function find()
    {
        return Yii::createObject(BlogQuery::className(), [get_called_class()]);
    }
}
