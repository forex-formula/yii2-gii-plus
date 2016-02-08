<?php

namespace yii\gii\plus\helpers;

use yii\db\Connection;
use yii\base\NotSupportedException;
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

    /**
     * @param Connection $db
     * @param bool $refresh
     * @return array
     */
    public static function getSchemaNames(Connection $db, $refresh = false)
    {
        try {
            $schemaNames = array_diff($db->getSchema()->getSchemaNames($refresh), ['public']);
        } catch (NotSupportedException $e) {
            $schemaNames = [];
        }
        return $schemaNames;
    }

    /**
     * @var array
     */
    protected static $modelNamespaces;

    /**
     * @return array
     */
    public static function getModelNamespaces()
    {
        if (is_null(static::$modelNamespaces)) {
            static::$modelNamespaces = [];
            foreach (['app', 'backend', 'common', 'console', 'frontend'] as $appNs) {
                $appPath = Yii::getAlias('@' . $appNs, false);
                if ($appPath) {
                    static::$modelNamespaces[] = $appNs . '\models';
                    foreach (glob($appPath . '/modules/*', GLOB_ONLYDIR) as $modulePath) {
                        static::$modelNamespaces[] = $appNs . '\modules\\' . basename($modulePath) . '\models';
                    }
                }
            }
        }
        return static::$modelNamespaces;
    }

    /**
     * @var array
     */
    protected static $modelDeepNamespaces;

    /**
     * @param string $modelNs
     * @return array
     */
    protected static function getModelSubNamespaces($modelNs)
    {
        $modelSubNamespaces = [];
        foreach (glob(Yii::getAlias('@' . str_replace('\\', '/', $modelNs)) . '/*', GLOB_ONLYDIR) as $path) {
            $basename = basename($path);
            if (($basename != 'base') && ($basename != 'query')) {
                $modelSubNs = $modelNs . '\\' . $basename;
                $modelSubNamespaces[] = $modelSubNs;
                $modelSubNamespaces = array_merge($modelSubNamespaces, static::getModelSubNamespaces($modelSubNs));
            }
        }
        return $modelSubNamespaces;
    }

    /**
     * @return array
     */
    public static function getModelDeepNamespaces()
    {
        if (is_null(static::$modelDeepNamespaces)) {
            static::$modelDeepNamespaces = [];
            foreach (static::getModelNamespaces() as $modelNs) {
                static::$modelDeepNamespaces[] = $modelNs;
                static::$modelDeepNamespaces = array_merge(static::$modelDeepNamespaces, static::getModelSubNamespaces($modelNs));
            }
        }
        return static::$modelDeepNamespaces;
    }

    /**
     * @var array
     */
    protected static $modelClasses;

    /**
     * @return array
     */
    public static function getModelClasses()
    {
        if (is_null(static::$modelClasses)) {
            static::$modelClasses = [];
            foreach (static::getModelDeepNamespaces() as $modelNs) {
                foreach (glob(Yii::getAlias('@' . str_replace('\\', '/', $modelNs)) . '/*.php') as $modelPath) {
                    $modelClass = $modelNs . '\\' . basename($modelPath, '.php');
                    if (class_exists($modelClass) && is_subclass_of($modelClass, 'yii\db\ActiveRecord')) {
                        static::$modelClasses[] = $modelClass;
                    }
                }
            }
        }
        return static::$modelClasses;
    }

    /**
     * @var array
     */
    protected static $modelClassTableMap;

    /**
     * @return array
     */
    public static function getModelClassTableMap()
    {
        if (is_null(static::$modelClassTableMap)) {
            static::$modelClassTableMap = [];
            foreach (static::getModelClasses() as $modelClass) {
                /* @var $modelClass string|\yii\db\ActiveRecord */
                static::$modelClassTableMap[$modelClass] = $modelClass::tableName();
            }
        }
        return static::$modelClassTableMap;
    }

    /**
     * @param array $uses
     * @return bool
     */
    public static function sortUses(array &$uses)
    {
        return usort($uses, function ($use1, $use2) {
            if (preg_match('~[\\\\\s]([^\\\\\s]+)$~', $use1, $match)) {
                $use1 = $match[1];
            }
            if (preg_match('~[\\\\\s]([^\\\\\s]+)$~', $use2, $match)) {
                $use2 = $match[1];
            }
            return strcasecmp($use1, $use2);
        });
    }
}
