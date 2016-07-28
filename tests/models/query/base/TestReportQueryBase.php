<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\TestReport]].
 *
 * @see \app\models\TestReport
 */
class TestReportQueryBase extends \yii\boost\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\TestReport[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\TestReport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
