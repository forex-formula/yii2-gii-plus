<?php

namespace yii\gii\plus\generators\base\model;

use yii\db\Connection;
use yii\gii\generators\model\Generator as ModelGenerator;
use Yii;

class Generator extends ModelGenerator
{

    public $generateQuery = true;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Base Model Generator';
    }

    /**
     * @return array
     */
    public function getDbListItems()
    {
        $dbListItems = [];
        foreach (Yii::$app->getComponents() as $id => $definition) {
            if (Yii::$app->get($id) instanceof Connection) {
                $dbListItems[$id] = $id;
            }
        }
        return $dbListItems;
    }
}
