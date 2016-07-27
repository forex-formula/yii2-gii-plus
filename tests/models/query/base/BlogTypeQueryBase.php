<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\BlogType]].
 *
 * @see \app\models\BlogType
 */
class BlogTypeQueryBase extends \yii\boost\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\BlogType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\BlogType|array|null
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
     * @param string $name
     * @return self
     */
    public function name($name)
    {
        return $this->andWhere([$this->a('name') => $name]);
    }
}
