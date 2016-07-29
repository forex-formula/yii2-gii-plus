<?php

namespace app\models\base;

use app\models\BlogType;
use yii\db\Expression;
use app\models\Post;
use app\models\PostReport;
use Yii;

/**
 * This is the model class for table "blog".
 *
 * @property integer $id
 * @property integer $blog_type_id
 * @property string $name
 * @property integer $enabled
 * @property string $created_at
 * @property string $updated_at
 * @property integer $deleted
 *
 * @property BlogType $blogType
 * @property Post[] $posts
 * @property PostReport[] $postReports
 */
class BlogBase extends \yii\boost\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'deleted'], 'boolean'],
            [['blog_type_id'], 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['blog_type_id', 'name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
            [['blog_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlogType::className(), 'targetAttribute' => ['blog_type_id' => 'id']],
            [['created_at', 'updated_at'], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
            [['enabled', 'deleted'], 'default', 'value' => '0'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'blog_type_id' => Yii::t('app', 'Тип блога'),
            'name' => Yii::t('app', 'Название'),
            'enabled' => Yii::t('app', 'Включено'),
            'created_at' => Yii::t('app', 'Создано в'),
            'updated_at' => Yii::t('app', 'Обновлено в'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @return \app\models\query\BlogTypeQuery
     */
    public function getBlogType()
    {
        return $this->hasOne(BlogType::className(), ['id' => 'blog_type_id']);
    }

    /**
     * @return \app\models\query\PostQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['blog_id' => 'id']);
    }

    /**
     * @return \app\models\query\PostReportQuery
     */
    public function getPostReports()
    {
        return $this->hasMany(PostReport::className(), ['blog_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\BlogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlogQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function modelLabel()
    {
        return Yii::t('app', 'Блог');
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
     * @return Post
     */
    public function newPost()
    {
        $model = new Post;
        $model->blog_id = $this->id;
        return $model;
    }

    /**
     * @return PostReport
     */
    public function newPostReport()
    {
        $model = new PostReport;
        $model->blog_id = $this->id;
        return $model;
    }
}
