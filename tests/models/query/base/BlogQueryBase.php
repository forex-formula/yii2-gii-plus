<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\base\BlogBase]].
 *
 * @see \app\models\base\BlogBase
 */
class BlogQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\base\BlogBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\base\BlogBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}