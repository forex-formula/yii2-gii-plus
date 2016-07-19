<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Test]].
 *
 * @see \app\models\Test
 */
class TestQueryBase extends \yii\boost\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\Test[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Test|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer $smallId
     * @param integer $tinyId
     * @return self
     */
    public function pk($smallId, $tinyId)
    {
        return $this->andWhere([
            $this->a('[[small_id]]') => $smallId,
            $this->a('[[tiny_id]]') => $tinyId
        ]);
    }

    /**
     * @param integer $smallId
     * @param integer $tinyId
     * @return self
     */
    public function smallIdTinyId($smallId, $tinyId)
    {
        return $this->andWhere([
            $this->a('[[small_id]]') => $smallId,
            $this->a('[[tiny_id]]') => $tinyId
        ]);
    }

    /**
     * @param integer $smallId
     * @return self
     */
    public function smallId($smallId)
    {
        return $this->andWhere([$this->a('[[small_id]]') => $smallId]);
    }

    /**
     * @param integer $tinyId
     * @return self
     */
    public function tinyId($tinyId)
    {
        return $this->andWhere([$this->a('[[tiny_id]]') => $tinyId]);
    }
}
