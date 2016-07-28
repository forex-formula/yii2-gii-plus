<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\PostReport]].
 *
 * @see \app\models\PostReport
 */
class PostReportQueryBase extends \yii\boost\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\PostReport[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\PostReport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function init()
    {
        parent::init();
        $this->where(new \yii\boost\db\Expression('{a}.deleted = 0', [], ['query' => $this]));
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
     * @param int|bool $enabled
     * @return self
     */
    public function enabled($enabled = true)
    {
        return $this->andWhere([$this->a('enabled') => $enabled ? 1 : 0]);
    }
}
