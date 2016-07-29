<?php

namespace app\models\base;

use app\models\Blog;
use Yii;

/**
 * This is the model class for table "blog_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Blog[] $blogs
 */
class BlogTypeBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
        ];
    }

    /**
     * @return \app\models\query\BlogQuery
     */
    public function getBlogs()
    {
        return $this->hasMany(Blog::className(), ['blog_type_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\BlogTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlogTypeQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Тип блога');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @return string[]|\yii\db\Expression
     */
    public static function displayField()
    {
        return ['name'];
    }

    /**
     * @return Blog
     */
    public function newBlog()
    {
        $model = new Blog;
        $model->blog_type_id = $this->id;
        return $model;
    }
}
