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
}
