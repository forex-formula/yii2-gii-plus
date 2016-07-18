<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Post]].
 *
 * @see \app\models\Post
 */
class PostQueryBase extends \yii\boost\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Post[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Post|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function init()
    {
        parent::init();
        $this->andWhere(['[[deleted]]' => 0]);
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
     * @param integer $blogId
     * @param string $name
     * @return self
     */
    public function blogIdName($blogId, $name)
    {
        return $this->andWhere([
            '[[blog_id]]' => $blogId,
            '[[name]]' => $name
        ]);
    }

    /**
     * @param integer $blogId
     * @return self
     */
    public function blogId($blogId)
    {
        return $this->andWhere(['[[blog_id]]' => $blogId]);
    }

    /**
     * @param string $name
     * @return self
     */
    public function name($name)
    {
        return $this->andWhere(['[[name]]' => $name]);
    }

    /**
     * @param int|bool $enabled
     * @return self
     */
    public function enabled($enabled = true)
    {
        return $this->andWhere(['[[enabled]]' => $enabled ? 1 : 0]);
    }
}
