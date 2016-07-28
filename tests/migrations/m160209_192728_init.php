<?php

use yii\boost\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $this->createTableWithComment('blog_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('Название')->unique(),
        ], 'Тип блога');

        $this->createTableWithComment('blog', [
            'id' => $this->primaryKey(),
            'blog_type_id' => $this->integer()->unsigned()->notNull()->comment('Тип блога'),
            'name' => $this->string(50)->notNull()->comment('Название')->unique(),
            'enabled' => $this->enabledShortcut()->comment('Включено'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ], 'Блог');

        $this->addForeignKey(null, 'blog', 'blog_type_id', 'blog_type', 'id');

        $this->createTableWithComment('post', [
            'id' => $this->primaryKey(),
            'blog_id' => $this->integer()->unsigned()->notNull()->comment('Блог'),
            'name' => $this->string(50)->notNull()->comment('Название'),
            'text' => $this->text()->notNull()->comment('Текст'),
            'enabled' => $this->enabledShortcut()->comment('Включено'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ], 'Пост');
        $this->createUnique(null, 'post', ['blog_id', 'name']);

        $this->addForeignKey(null, 'post', 'blog_id', 'blog', 'id');

        $db = $this->getDb();
        $sql = <<<SQL
CREATE VIEW `post_report` AS
SELECT *
FROM `post`;
SQL;
        $db->createCommand($sql)->execute();

        $this->createTableWithComment('comment', [
            'id' => $this->primaryKey(),
            'blog_id' => $this->integer()->unsigned()->notNull()->comment('Блог'),
            'post_id' => $this->integer()->unsigned()->notNull()->comment('Пост'),
            'parent_id' => $this->integer()->unsigned()->comment('Родительский комментарий'),
            'text' => $this->text()->notNull()->comment('Текст'),
            'enabled' => $this->enabledShortcut()->comment('Включено'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ], 'Комментарий');

        $this->addForeignKey(null, 'comment', 'blog_id', 'blog', 'id');

        $this->createIndex(null, 'post', ['id', 'blog_id']);
        $this->addForeignKey(null, 'comment', ['post_id', 'blog_id'], 'post', ['id', 'blog_id']);

        $this->createIndex(null, 'comment', ['id', 'blog_id', 'post_id']);
        $this->addForeignKey(null, 'comment', ['parent_id', 'blog_id', 'post_id'], 'comment', ['id', 'blog_id', 'post_id']);

        $sql = <<<SQL
CREATE VIEW `comment_report` AS
SELECT *
FROM `comment`;
SQL;
        $db->createCommand($sql)->execute();

        $this->createTable('sequence', [
            'id' => $this->primaryKey(),
            'previous_id' => $this->integer()->unsigned()->unique(),
            'value' => $this->double()->unsigned(),
            'value_expires_at' => $this->timestamp()->null()
        ]);

        $this->addForeignKey(null, 'sequence', 'previous_id', 'sequence', 'id');

        $this->createTable('test', [
            'small_id' => $this->smallInteger()->unsigned(),
            'tiny_id' => $this->tinyInteger()->unsigned()
        ]);
        $this->addPrimaryKey(null, 'test', ['small_id', 'tiny_id']);

        $sql = <<<SQL
CREATE VIEW `test_report` AS
SELECT
    t.small_id AS pk_small_id,
    t.tiny_id AS pk_tiny_id
FROM `test` t;
SQL;
        $db->createCommand($sql)->execute();
    }

    public function down()
    {
        return false;
    }
}
