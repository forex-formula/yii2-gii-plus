<?php

use yii\boost\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $this->createTableWithComment('blog', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(50)->notNull()->comment('Название')->unique(),
            'enabled' => $this->boolean()->notNull()->defaultValue(0)->comment('Включено'),
            'deleted' => $this->boolean()->notNull()->defaultValue(0)
        ], 'Блог');

        $this->createTableWithComment('post', [
            'id' => $this->primaryKey()->unsigned(),
            'blog_id' => $this->integer()->unsigned()->notNull()->comment('Блог'),
            'name' => $this->string(50)->notNull()->comment('Название')->unique(),
            'text' => $this->text()->notNull()->comment('Текст'),
            'enabled' => $this->boolean()->notNull()->defaultValue(0)->comment('Включено'),
            'deleted' => $this->boolean()->notNull()->defaultValue(0)
        ], 'Пост');
        $this->createUnique(null, 'post', ['blog_id', 'name']);

        $this->addForeignKey(null, 'post', 'blog_id', 'blog', 'id', static::RESTRICT, static::NO_ACTION);

        $this->createTableWithComment('comment', [
            'id' => $this->primaryKey()->unsigned(),
            'blog_id' => $this->integer()->unsigned()->notNull()->comment('Блог'),
            'post_id' => $this->integer()->unsigned()->notNull()->comment('Пост'),
            'parent_id' => $this->integer()->unsigned()->comment('Родительский комментарий'),
            'text' => $this->text()->notNull()->comment('Текст'),
            'enabled' => $this->boolean()->notNull()->defaultValue(0)->comment('Включено'),
            'deleted' => $this->boolean()->notNull()->defaultValue(0)
        ], 'Комментарий');

        $this->addForeignKey(null, 'comment', 'blog_id', 'blog', 'id', static::RESTRICT, static::NO_ACTION);

        $this->createIndex(null, 'post', ['id', 'blog_id']);
        $this->addForeignKey(null, 'comment', ['post_id', 'blog_id'], 'post', ['id', 'blog_id'], static::RESTRICT, static::NO_ACTION);

        $this->createIndex(null, 'comment', ['id', 'post_id']);
        $this->addForeignKey(null, 'comment', ['parent_id', 'post_id'], 'comment', ['id', 'post_id'], static::RESTRICT, static::NO_ACTION);

        $this->createTable('sequence', [
            'id' => $this->primaryKey()->unsigned(),
            'previous_id' => $this->integer()->unsigned()->unique()
        ]);

        $this->addForeignKey(null, 'sequence', 'previous_id', 'sequence', 'id', static::RESTRICT, static::NO_ACTION);
    }

    public function down()
    {
        return false;
    }
}
