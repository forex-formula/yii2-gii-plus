<?php

use yii\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $this->createTable('blog', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255) NOT NULL',
        ], $options);
        $this->createTable('post', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'blog_id' => 'INT UNSIGNED NOT NULL',
            'name' => 'VARCHAR(255) NOT NULL',
            'text' => 'TEXT NOT NULL'
        ], $options);
        $this->createTable('comment', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'post_id' => 'INT UNSIGNED NOT NULL',
            'parent_id' => 'INT UNSIGNED NULL DEFAULT NULL',
            'text' => 'TEXT NOT NULL'
        ], $options);
        $this->addForeignKey('post__blog_id', 'post', 'blog_id', 'blog', 'id', 'RESTRICT', 'NO ACTION');
        $this->addForeignKey('comment__post_id', 'comment', 'post_id', 'post', 'id', 'RESTRICT', 'NO ACTION');
        $this->addForeignKey('comment__parent_id', 'comment', 'parent_id', 'comment', 'id', 'RESTRICT', 'NO ACTION');
    }

    public function down()
    {
        $this->dropForeignKey('comment__parent_id', 'comment');
        $this->dropForeignKey('comment__post_id', 'comment');
        $this->dropForeignKey('post__blog_id', 'post');
        $this->dropTable('comment');
        $this->dropTable('post');
        $this->dropTable('blog');
    }
}
