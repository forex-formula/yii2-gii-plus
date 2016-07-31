<?php

use yii\boost\db\Migration;

class m160209_192728_init extends Migration
{

    public function up()
    {
        $this->createTableWithComment('type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('Название')->unique()
        ], 'Тип');
    }

    public function down()
    {
        return false;
    }
}
