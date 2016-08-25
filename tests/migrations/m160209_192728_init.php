<?php

use yii\boost\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $this->createTableWithComment('type', [
            'id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(25)->notNull()->unique()->comment('Название'),
            'code' => $this->string(25)->notNull()->unique()->comment('Код')
        ], 'Тип');
        $this->addPrimaryKey(null, 'type', ['id']);

        $this->insert('type', ['id' => 1, 'name' => 'Музыка', 'code' => 'music']);
        $this->insert('type', ['id' => 2, 'name' => 'Видео', 'code' => 'video']);

        $this->createTableWithComment('root_folder', [
            'id' => $this->primaryKey(),
            'type_id' => $this->tinyInteger()->unsigned()->notNull()->comment('Тип'),
            'name' => $this->string(50)->notNull()->comment('Название')
        ], 'Корневая папка');
        $this->createUnique(null, 'root_folder', ['type_id', 'name']);

        $this->addForeignKey(null, 'root_folder', ['type_id'], 'type', ['id']);

        $this->createTableWithComment('folder', [
            'id' => $this->primaryKey(),
            'root_folder_id' => $this->integer()->unsigned()->notNull()->comment('Корневая папка'),
            'name' => $this->string(50)->notNull()->comment('Название'),
            'visible' => $this->boolean()->notNull()->defaultValue(1)->comment('Видимый'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ], 'Папка');
        $this->createUnique(null, 'folder', ['root_folder_id', 'name']);

        $this->addForeignKey(null, 'folder', ['root_folder_id'], 'root_folder', ['id']);

        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'root_folder_id' => $this->integer()->unsigned()->notNull()->comment('Корневая папка'),
            'folder_id' => $this->integer()->unsigned()->notNull()->comment('Папка'),
            'name' => $this->string(50)->notNull()->comment('Название'),
            'visible' => $this->boolean()->notNull()->defaultValue(1)->comment('Видимый'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ]);
        $this->createUnique(null, 'file', ['folder_id', 'name']);

        $this->createIndex(null, 'folder', ['id', 'root_folder_id']);
        $this->addForeignKey(null, 'file', ['folder_id', 'root_folder_id'], 'folder', ['id', 'root_folder_id']);

        $this->createTable('something', [
            'tiny_id' => $this->tinyInteger()->unsigned(),
            'small_id' => $this->smallInteger()->unsigned(),
            'expires_at' => $this->date(),
            'second_expires_at' => $this->date()->notNull(),
            'third_expires_at' => $this->dateTime(),
            'fourth_expires_at' => $this->dateTime()->notNull()
        ]);
        $this->addPrimaryKey(null, 'something', ['tiny_id', 'small_id']);
    }

    public function down()
    {
        return false;
    }
}
