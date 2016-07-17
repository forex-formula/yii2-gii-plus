<?php

namespace app\models;

use app\models\base\TestBase;
use app\models\query\TestQuery;
use Yii;

class Test extends TestBase
{

    /**
     * @inheritdoc
     * @return TestQuery
     */
    public static function find()
    {
        return Yii::createObject(TestQuery::className(), [get_called_class()]);
    }
}
