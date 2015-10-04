<?php

namespace yii\gii\plus\helpers;

use yii\db\Connection as DbConnection,
    yii\base\NotSupportedException,
    Yii;


class Helper
{

    public static function getTableNames()
    {
        $tableNames = ['*'];
        $schema = Yii::$app->getDb()->getSchema();
        try {
            $schemaNames = $schema->getSchemaNames(true);
        } catch (NotSupportedException $e) {
            $schemaNames = [];
        }
        if (count($schemaNames)) {
            foreach ($schemaNames as $schemaName) {
                $tableNames[] = $schemaName . '.*';
            }
            foreach ($schemaNames as $schemaName) {
                foreach ($schema->getTableNames($schemaName, true) as $tableName) {
                    $tableNames[] = $schemaName . '.' . $tableName;
                }
            }
        } else {
            foreach ($schema->getTableNames('', true) as $tableName) {
                $tableNames[] = $tableName;
            }
        }
        return $tableNames;
    }

    public static function getDbConnectionDsnMap()
    {
        $dbConnections = [];
        foreach (Yii::$app->getComponents() as $id => $definition) {
            $component = Yii::$app->get($id);
            if ($component instanceof DbConnection) {
                $dbConnections[$id] = $component->dsn;
            }
        }
        return $dbConnections;
    }

    public static function getDbConnectionTableNamesMap()
    {
        $tableNames = [];
        foreach (Yii::$app->getComponents() as $id => $definition) {
            $component = Yii::$app->get($id);
            if ($component instanceof DbConnection) {
                $tableNames[$id] = ['*'];
                $schema = $component->getSchema();
                try {
                    $schemaNames = $schema->getSchemaNames(true);
                } catch (NotSupportedException $e) {
                    $schemaNames = [];
                }
                if (count($schemaNames)) {
                    foreach ($schemaNames as $schemaName) {
                        $tableNames[$id] = $schemaName . '.*';
                    }
                    foreach ($schemaNames as $schemaName) {
                        foreach ($schema->getTableNames($schemaName, true) as $tableName) {
                            $tableNames[$id] = $schemaName . '.' . $tableName;
                        }
                    }
                } else {
                    foreach ($schema->getTableNames('', true) as $tableName) {
                        $tableNames[$id] = $tableName;
                    }
                }
            }
        }
        return $tableNames;
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
