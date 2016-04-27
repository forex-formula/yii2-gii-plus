<?php

use yii\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('blog', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255) NOT NULL',
        ], $tableOptions);
        $this->createTable('post', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'blog_id' => 'INT UNSIGNED NOT NULL',
            'name' => 'VARCHAR(255) NOT NULL',
            'text' => 'TEXT NOT NULL'
        ], $tableOptions);
        $this->createTable('comment', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'blog_id' => 'INT UNSIGNED NOT NULL',
            'post_id' => 'INT UNSIGNED NOT NULL',
            'parent_id' => 'INT UNSIGNED NULL DEFAULT NULL',
            'text' => 'TEXT NOT NULL'
        ], $tableOptions);
        $this->addForeignKey('post__blog_id', 'post', 'blog_id', 'blog', 'id', 'RESTRICT', 'NO ACTION');
        $this->addForeignKey('comment__blog_id', 'comment', 'blog_id', 'blog', 'id', 'RESTRICT', 'NO ACTION');
        $this->createIndex('id__blog_id', 'post', ['id', 'blog_id']);
        $this->addForeignKey('comment__post_id__blog_id', 'comment', ['post_id', 'blog_id'], 'post', ['id', 'blog_id'], 'RESTRICT', 'NO ACTION');
        $this->createIndex('id__post_id__blog_id', 'comment', ['id', 'post_id', 'blog_id']);
        $this->addForeignKey('comment__parent_id__post_id__blog_id', 'comment', ['parent_id', 'post_id', 'blog_id'], 'comment', ['id', 'post_id', 'blog_id'], 'RESTRICT', 'NO ACTION');
    }

    public function down()
    {
        $this->dropForeignKey('comment__parent_id__post_id__blog_id', 'comment');
        $this->dropForeignKey('comment__post_id__blog_id', 'comment');
        $this->dropForeignKey('comment__blog_id', 'comment');
        $this->dropForeignKey('post__blog_id', 'post');
        $this->dropTable('comment');
        $this->dropTable('post');
        $this->dropTable('blog');
    }
}
