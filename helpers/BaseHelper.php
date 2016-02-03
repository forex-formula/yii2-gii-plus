<?php

namespace yii\gii\plus\helpers;

use yii\db\Connection;
use Yii;

class BaseHelper
{

    /**
     * @var Connection[]
     */
    protected static $dbConnections;

    /**
     * @return Connection[]
     */
    public static function getDbConnections()
    {
        if (is_null(static::$dbConnections)) {
            static::$dbConnections = [];
            foreach (Yii::$app->getComponents() as $id => $definition) {
                $db = Yii::$app->get($id);
                if ($db instanceof Connection) {
                    static::$dbConnections[$id] = $db;
                }
            }
        }
        return static::$dbConnections;
    }
}
