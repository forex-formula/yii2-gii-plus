<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Blog]].
 *
 * @see \app\models\Blog
 */
class BlogQueryBase extends \yii\boost\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Blog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Blog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function init()
    {
        parent::init();
        $this->andWhere([$this->a('[[deleted]]') => 0]);
    }

    /**
     * @param integer $id
     * @return self
     */
    public function pk($id)
    {
        return $this->andWhere([$this->a('[[id]]') => $id]);
    }

    /**
     * @param integer $id
     * @return self
     */
    public function id($id)
    {
        return $this->andWhere([$this->a('[[id]]') => $id]);
    }

    /**
     * @param string $name
     * @return self
     */
    public function name($name)
    {
        return $this->andWhere([$this->a('[[name]]') => $name]);
    }

    /**
     * @param int|bool $enabled
     * @return self
     */
    public function enabled($enabled = true)
    {
        return $this->andWhere([$this->a('[[enabled]]') => $enabled ? 1 : 0]);
    }
}
