<?php

namespace app\models\query\base;

/**
 * This is the ActiveQuery class for [[\app\models\Comment]].
 *
 * @see \app\models\Comment
 */
class CommentQueryBase extends \yii\boost\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\Comment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Comment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function init()
    {
        parent::init();
        $this->where([$this->a('deleted') => 0]);
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
     * @param integer $blogId
     * @return self
     */
    public function blogId($blogId)
    {
        return $this->andWhere([$this->a('blog_id') => $blogId]);
    }

    /**
     * @param integer $parentId
     * @param integer $blogId
     * @param integer $postId
     * @return self
     */
    public function parentIdBlogIdPostId($parentId, $blogId, $postId)
    {
        return $this->andWhere([
            $this->a('parent_id') => $parentId,
            $this->a('blog_id') => $blogId,
            $this->a('post_id') => $postId
        ]);
    }

    /**
     * @param integer $postId
     * @param integer $blogId
     * @return self
     */
    public function postIdBlogId($postId, $blogId)
    {
        return $this->andWhere([
            $this->a('post_id') => $postId,
            $this->a('blog_id') => $blogId
        ]);
    }

    /**
     * @param integer $parentId
     * @return self
     */
    public function parentId($parentId)
    {
        return $this->andWhere([$this->a('parent_id') => $parentId]);
    }

    /**
     * @param integer $postId
     * @return self
     */
    public function postId($postId)
    {
        return $this->andWhere([$this->a('post_id') => $postId]);
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
