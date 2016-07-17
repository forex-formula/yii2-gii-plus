<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Sequence]].
 *
 * @see \app\models\Sequence
 */
class SequenceQueryBase extends \yii\boost\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Sequence[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Sequence|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer $id
     * @return self
     */
    public function pk($id)
    {
        return $this->andWhere(['[[id]]' => $id]);
    }

    /**
     * @param integer $id
     * @return self
     */
    public function id($id)
    {
        return $this->andWhere(['[[id]]' => $id]);
    }

    /**
     * @param integer $previousId
     * @return self
     */
    public function previousId($previousId)
    {
        return $this->andWhere(['[[previous_id]]' => $previousId]);
    }
}
