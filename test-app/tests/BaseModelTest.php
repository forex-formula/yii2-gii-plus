<?php

namespace app\tests;

use ReflectionClass;
use yii\phpunit\TestCase;

class BaseModelTest extends TestCase
{

    /**
     * @return array
     */
    public function modelNameDataProvider()
    {
        return [ // [$modelName]
            ['RootFolderType'],
            ['RootFolder'],
            ['Folder'],
            ['File'],
            ['FileInfoType'],
            ['FileInfo'],
            ['FileReport'],
            ['Something']
        ];
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testClassExists($modelName)
    {
        static::assertTrue(class_exists('app\models\base\\' . $modelName . 'Base'));
        static::assertTrue(class_exists('app\models\query\base\\' . $modelName . 'QueryBase'));
        static::assertTrue(class_exists('app\models\\' . $modelName));
        static::assertTrue(class_exists('app\models\query\\' . $modelName . 'Query'));
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testGetParentClass($modelName)
    {
        static::assertEquals('yii\boost\db\ActiveRecord', get_parent_class('app\models\base\\' . $modelName . 'Base'));
        static::assertEquals('yii\boost\db\ActiveQuery', get_parent_class('app\models\query\base\\' . $modelName . 'QueryBase'));
        static::assertEquals('app\models\base\\' . $modelName . 'Base', get_parent_class('app\models\\' . $modelName));
        static::assertEquals('app\models\query\base\\' . $modelName . 'QueryBase', get_parent_class('app\models\query\\' . $modelName . 'Query'));
    }

    /**
     * @return array
     */
    public function tableNameDataProvider()
    {
        return [ // [$modelName, $tableName]
            ['RootFolderType', 'root_folder_type'],
            ['RootFolder', 'root_folder'],
            ['Folder', 'folder'],
            ['File', 'file'],
            ['FileInfoType', 'file_info_type'],
            ['FileInfo', 'file_info'],
            ['FileReport', 'file_report'],
            ['Something', 'something']
        ];
    }

    /**
     * @param string $modelName
     * @param string $tableName
     * @dataProvider tableNameDataProvider
     */
    public function testTableName($modelName, $tableName)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        static::assertEquals($tableName, $modelClass::tableName());
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testFind($modelName)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        static::assertEquals('app\models\query\\' . $modelName . 'Query', get_class($modelClass::find()));
    }

    /**
     * @return array
     */
    public function tableIsViewStaticDataProvider()
    {
        return [ // [$modelName, $tableIsView]
            ['RootFolderType', false, true],
            ['RootFolder', false, false],
            ['Folder', false, false],
            ['File', false, false],
            ['FileInfoType', false, true],
            ['FileInfo', false, false],
            ['FileReport', true, false],
            ['Something', false, false]
        ];
    }

    /**
     * @param string $modelName
     * @param bool $tableIsView
     * @param bool $tableIsStatic
     * @dataProvider tableIsViewStaticDataProvider
     */
    public function testTableIsView($modelName, $tableIsView, $tableIsStatic)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        static::assertEquals($tableIsView, $modelClass::tableIsView());
    }

    /**
     * @param string $modelName
     * @param bool $tableIsView
     * @param bool $tableIsStatic
     * @dataProvider tableIsViewStaticDataProvider
     */
    public function testTableIsStatic($modelName, $tableIsView, $tableIsStatic)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        static::assertEquals($tableIsStatic, $modelClass::tableIsStatic());
    }

//    /**
//     * @return array
//     */
//    public function singularRelationsDataProvider()
//    {
//        return [
//            ['Type', []],
//            ['RootFolder', ['type']],
//            ['Folder', ['rootFolder', 'type']],
//            ['File', ['folder', 'rootFolder']]
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string[] $singularRelations
//     * @dataProvider singularRelationsDataProvider
//     */
//    public function testMethodSingularRelations($modelName, array $singularRelations)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('singularRelations'));
//        static::assertTrue($reflection->getMethod('singularRelations')->isStatic());
//        static::assertEquals($singularRelations, $modelClass::singularRelations());
//    }
//
//    /**
//     * @return array
//     */
//    public function pluralRelationsDataProvider()
//    {
//        return [
//            ['Type', ['rootFolders']],
//            ['RootFolder', ['folders']],
//            ['Folder', ['files']],
//            ['File', []]
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string[] $pluralRelations
//     * @dataProvider pluralRelationsDataProvider
//     */
//    public function testMethodPluralRelations($modelName, array $pluralRelations)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('pluralRelations'));
//        static::assertTrue($reflection->getMethod('pluralRelations')->isStatic());
//        static::assertEquals($pluralRelations, $modelClass::pluralRelations());
//    }
//
//    /**
//     * @return array
//     */
//    public function classShortNameDataProvider()
//    {
//        return [
//            ['Type', 'Type'],
//            ['RootFolder', 'RootFolder'],
//            ['Folder', 'Folder'],
//            ['File', 'File']
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string $classShortName
//     * @dataProvider classShortNameDataProvider
//     */
//    public function testMethodShortName($modelName, $classShortName)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('classShortName'));
//        static::assertTrue($reflection->getMethod('classShortName')->isStatic());
//        static::assertEquals($classShortName, $modelClass::classShortName());
//    }
//
//    /**
//     * @return array
//     */
//    public function modelTitleDataProvider()
//    {
//        return [
//            ['Type', 'Тип'],
//            ['RootFolder', 'Корневая папка'],
//            ['Folder', 'Папка'],
//            ['File', 'File']
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string $modelTitle
//     * @dataProvider modelTitleDataProvider
//     */
//    public function testMethodModelTitle($modelName, $modelTitle)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('modelTitle'));
//        static::assertTrue($reflection->getMethod('modelTitle')->isStatic());
//        static::assertEquals($modelTitle, $modelClass::modelTitle());
//    }
//
//    /**
//     * @return array
//     */
//    public function primaryKeyDataProvider()
//    {
//        return [
//            ['Type', ['id']],
//            ['RootFolder', ['id']],
//            ['Folder', ['id']],
//            ['File', ['id']]
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string[] $primaryKey
//     * @dataProvider primaryKeyDataProvider
//     */
//    public function testMethodPrimaryKey($modelName, array $primaryKey)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        static::assertEquals($primaryKey, $modelClass::primaryKey());
//    }
//
//    /**
//     * @return array
//     */
//    public function displayFieldDataProvider()
//    {
//        return [
//            ['Type', ['name']],
//            ['RootFolder', ['type_id', 'name']],
//            ['Folder', ['root_folder_id', 'name']],
//            ['File', ['folder_id', 'name']]
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param string[] $displayField
//     * @dataProvider displayFieldDataProvider
//     */
//    public function testMethodDisplayField($modelName, array $displayField)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('displayField'));
//        static::assertTrue($reflection->getMethod('displayField')->isStatic());
//        static::assertEquals($displayField, $modelClass::displayField());
//    }
//
//    /**
//     * @return array
//     */
//    public function getDisplayFieldDataProvider()
//    {
//        return [
//            ['Type', ['name' => 'Type name'], 'Type name'],
//            ['RootFolder', ['type_id' => 1, 'name' => 'Root Folder name'], '1 Root Folder name'],
//            ['Folder', ['root_folder_id' => 2, 'name' => 'Folder name'], '2 Folder name'],
//            ['File', ['folder_id' => 3, 'name' => 'File name'], '3 File name']
//        ];
//    }
//
//    /**
//     * @param string $modelName
//     * @param array $values
//     * @param string $displayField
//     * @dataProvider getDisplayFieldDataProvider
//     */
//    public function testMethodGetDisplayField($modelName, array $values, $displayField)
//    {
//        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
//        $modelClass = 'app\models\\' . $modelName;
//        $reflection = new ReflectionClass($modelClass);
//        static::assertTrue($reflection->hasMethod('getDisplayField'));
//        static::assertNotTrue($reflection->getMethod('getDisplayField')->isStatic());
//        /* @var $model \yii\boost\db\ActiveRecord */
//        $model = new $modelClass;
//        $model->setAttributes($values, false);
//        static::assertEquals($displayField, $model->getDisplayField());
//    }
//
////    public function testMethodGetTypeOfFolder()
////    {
////        $reflection = new ReflectionClass('app\models\Folder');
////        static::assertTrue($reflection->hasMethod('getType'));
////        static::assertFalse($reflection->getMethod('getType')->isStatic());
////        static::assertEquals('app\models\query\TypeQuery', get_class((new \app\models\Folder)->getType()));
////    }
////
////    public function testMethodGetFilesOfFolder()
////    {
////        $reflection = new ReflectionClass('app\models\Folder');
////        static::assertTrue($reflection->hasMethod('getFiles'));
////        static::assertFalse($reflection->getMethod('getFiles')->isStatic());
////        static::assertEquals('app\models\query\FileQuery', get_class((new \app\models\Folder)->getFiles()));
////    }
////
////    public function testMethodGetFolderOfFile()
////    {
////        $reflection = new ReflectionClass('app\models\File');
////        static::assertTrue($reflection->hasMethod('getFolder'));
////        static::assertFalse($reflection->getMethod('getFolder')->isStatic());
////        static::assertEquals('app\models\query\FolderQuery', get_class((new \app\models\File)->getFolder()));
////    }
////
////    public function testMethodGetTypeOfFile()
////    {
////        $reflection = new ReflectionClass('app\models\File');
////        static::assertTrue($reflection->hasMethod('getType'));
////        static::assertFalse($reflection->getMethod('getType')->isStatic());
////        $query = (new \app\models\File)->getType();
////        static::assertEquals('app\models\query\TypeQuery', get_class($query));
////        static::assertInstanceOf('yii\db\ActiveQuery', $query->via);
////        /* @var $viaQuery \yii\db\ActiveQuery */
////        $viaQuery = $query->via;
////        static::assertInternalType('array', $viaQuery->from);
////        static::assertEquals(['folder via_folder'], $viaQuery->from);
////    }
////
////    public function testMethodNewFolderOfType()
////    {
////        $reflection = new ReflectionClass('app\models\Type');
////        static::assertTrue($reflection->hasMethod('newFolder'));
////        static::assertFalse($reflection->getMethod('newFolder')->isStatic());
////        $type = new \app\models\Type;
////        $type->id = mt_rand(1, 10);
////        $folder = $type->newFolder();
////        static::assertEquals($type->id, $folder->type_id);
////    }
////
////    public function testMethodNewFileOfFolder()
////    {
////        $reflection = new ReflectionClass('app\models\Folder');
////        static::assertTrue($reflection->hasMethod('newFile'));
////        static::assertFalse($reflection->getMethod('newFile')->isStatic());
////        $folder = new \app\models\Folder;
////        $folder->id = mt_rand(1, 10);
////        $file = $folder->newFile();
////        static::assertEquals($folder->id, $file->folder_id);
////    }
}
