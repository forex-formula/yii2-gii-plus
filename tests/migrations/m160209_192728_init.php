<?php

use yii\boost\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $this->createTableWithComment('type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique()
        ], 'Тип');

        $this->createTable('folder', [
            'id' => $this->primaryKey(),
            'type_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(50)->notNull()
        ]);
        $this->createUnique(null, 'folder', ['type_id', 'name']);

        $this->addForeignKey(null, 'folder', ['type_id'], 'type', ['id']);

        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'folder_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(50)->notNull(),
            'created_at' => $this->createdAtShortcut(),
            'updated_at' => $this->updatedAtShortcut(),
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
