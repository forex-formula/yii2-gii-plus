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
            'folder_id' => $this->integer()->unsigned()->notNull()->comment('Папка'),
            'name' => $this->string(50)->notNull()->comment('Название'),
            'visible' => $this->boolean()->notNull()->defaultValue(1)->comment('Видимый'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновлено в'),
            'deleted' => $this->deletedShortcut()
        ]);
        $this->createUnique(null, 'file', ['folder_id', 'name']);

        $this->addForeignKey(null, 'file', ['folder_id'], 'folder', ['id']);
    }

    public function down()
    {
        return false;
    }
}
