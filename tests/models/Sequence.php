<?php

namespace app\models;

use app\models\base\SequenceBase;
use app\models\query\SequenceQuery;
use Yii;

class Sequence extends SequenceBase
{

    /**
     * @inheritdoc
     * @return SequenceQuery
     */
    public static function find()
    {
        return Yii::createObject(SequenceQuery::className(), [get_called_class()]);
    }
}
