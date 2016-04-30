<?php

use yii\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        // blog
        $this->createTable('blog', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255) NOT NULL',
        ], $tableOptions);
        // post
        $this->createTable('post', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'blog_id' => 'INT UNSIGNED NOT NULL',
            'name' => 'VARCHAR(255) NOT NULL',
            'text' => 'TEXT NOT NULL'
        ], $tableOptions);
        $this->addForeignKey('post__blog_id', 'post', 'blog_id', 'blog', 'id', 'RESTRICT', 'NO ACTION');
        // comment
        $this->createTable('comment', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'blog_id' => 'INT UNSIGNED NOT NULL',
            'post_id' => 'INT UNSIGNED NOT NULL',
            'parent_id' => 'INT UNSIGNED NULL DEFAULT NULL',
            'text' => 'TEXT NOT NULL'
        ], $tableOptions);
        $this->addForeignKey('comment__blog_id', 'comment', 'blog_id', 'blog', 'id', 'RESTRICT', 'NO ACTION');
        $this->createIndex('id__blog_id', 'post', ['id', 'blog_id'], true);
        $this->addForeignKey('comment__post_id__blog_id', 'comment', ['post_id', 'blog_id'], 'post', ['id', 'blog_id'], 'RESTRICT', 'NO ACTION');
        $this->createIndex('id__blog_id__post_id', 'comment', ['id', 'blog_id', 'post_id'], true);
        $this->addForeignKey('comment__parent_id__blog_id__post_id', 'comment', ['parent_id', 'blog_id', 'post_id'], 'comment', ['id', 'blog_id', 'post_id'], 'RESTRICT', 'NO ACTION');
        // sequence
        $this->createTable('sequence', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'parent_id' => 'INT UNSIGNED NULL DEFAULT NULL',
            'name' => 'VARCHAR(255) NOT NULL'
        ], $tableOptions);
        $this->createIndex('parent_id', 'sequence', 'parent_id', true);
        $this->addForeignKey('sequence__parent_id', 'sequence', 'parent_id', 'sequence', 'id', 'RESTRICT', 'NO ACTION');
    }

    public function down()
    {
        // sequence
        $this->dropTable('sequence');
        $this->dropForeignKey('sequence__parent_id', 'sequence');
        // comment
        $this->dropForeignKey('comment__parent_id__blog_id__post_id', 'comment');
        $this->dropForeignKey('comment__post_id__blog_id', 'comment');
        $this->dropForeignKey('comment__blog_id', 'comment');
        $this->dropTable('comment');
        // post
        $this->dropForeignKey('post__blog_id', 'post');
        $this->dropTable('post');
        // blog
        $this->dropTable('blog');
    }
}
