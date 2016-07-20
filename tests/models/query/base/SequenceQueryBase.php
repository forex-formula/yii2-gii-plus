<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Sequence]].
 *
 * @see \app\models\Sequence
 */
class SequenceQueryBase extends \yii\boost\db\ActiveQuery
{

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
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param integer $id
     * @return self
     */
    public function id($id)
    {
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param integer $previousId
     * @return self
     */
    public function previousId($previousId)
    {
        return $this->andWhere([$this->a('previous_id') => $previousId]);
    }
}
