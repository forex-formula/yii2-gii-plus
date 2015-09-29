<?php

namespace yii\gii\plus\helpers;

use PDO,
    Yii;


class Helper
{

    public static function getTableNames()
    {
        $db = Yii::$app->getDb();
        if (!$db->getIsActive()) {
            $db->open();
        }
        switch ($db->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return $db->createCommand('SHOW TABLES;')->queryColumn();
            case 'pgsql':
                return $db->createCommand('SELECT table_name FROM information_schema.tables WHERE table_schema = :table_schema;', [':table_schema' => 'public'])->queryColumn();
            default:
                return [];
        }
    }

    public static function getBaseModelClasses()
    {
        $baseModelClasses = [];
        foreach (glob(Yii::getAlias('@app/models/base') . DIRECTORY_SEPARATOR . '*.php') as $filename) {
            $baseModelClasses[] = 'app\models\base\\' . basename($filename, '.php');
        }
        return $baseModelClasses;
    }

    public static function getModelClasses()
    {
        $modelClasses = [];
        foreach (glob(Yii::getAlias('@app/models') . DIRECTORY_SEPARATOR . '*.php') as $filename) {
            $modelClasses[] = 'app\models\\' . basename($filename, '.php');
        }
        return $modelClasses;
    }

    public static function getBaseSearchModelClasses()
    {
        $baseSearchModelClasses = [];
        foreach (glob(Yii::getAlias('@app/models/search/base') . DIRECTORY_SEPARATOR . '*.php') as $filename) {
            $baseSearchModelClasses[] = 'app\models\search\base\\' . basename($filename, '.php');
        }
        return $baseSearchModelClasses;
    }

    public static function getSearchModelClasses()
    {
        $searchModelClasses = [];
        foreach (glob(Yii::getAlias('@app/models/search') . DIRECTORY_SEPARATOR . '*.php') as $filename) {
            $searchModelClasses[] = 'app\models\search\\' . basename($filename, '.php');
        }
        return $searchModelClasses;
    }

    public static function sortUse(array &$use)
    {
        usort($use, function ($use1, $use2) {
            return strcasecmp(preg_replace('~^.+[\\\\ ]([^\\\\ ]+)$~', '$1', $use1), preg_replace('~^.+[\\\\ ]([^\\\\ ]+)$~', '$1', $use2));
        });
    }

    public static function getUseDirective(array $use)
    {
        static::sortUse($use);
        return 'use ' . implode(',' . "\n" . '    ', array_unique($use)) . ';';
    }
}
