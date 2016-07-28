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

    /**
     * @param integer $pkSmallId
     * @param integer $pkTinyId
     * @return self
     */
    public function pk($pkSmallId, $pkTinyId)
    {
        return $this->andWhere([
            $this->a('pk_small_id') => $pkSmallId,
            $this->a('pk_tiny_id') => $pkTinyId
        ]);
    }

    /**
     * @param integer $pkSmallId
     * @param integer $pkTinyId
     * @return self
     */
    public function pkSmallIdPkTinyId($pkSmallId, $pkTinyId)
    {
        return $this->andWhere([
            $this->a('pk_small_id') => $pkSmallId,
            $this->a('pk_tiny_id') => $pkTinyId
        ]);
    }

    /**
     * @param integer $pkSmallId
     * @return self
     */
    public function pkSmallId($pkSmallId)
    {
        return $this->andWhere([$this->a('pk_small_id') => $pkSmallId]);
    }

    /**
     * @param integer $pkTinyId
     * @return self
     */
    public function pkTinyId($pkTinyId)
    {
        return $this->andWhere([$this->a('pk_tiny_id') => $pkTinyId]);
    }
}
