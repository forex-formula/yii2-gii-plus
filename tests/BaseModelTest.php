<?php

namespace yii\gii\plus\tests;

use ReflectionClass;
use yii\phpunit\TestCase;

class BaseModelTest extends TestCase
{

    /**
     * @return array
     */
    public function modelNameDataProvider()
    {
        return [
            ['Type'],
            ['RootFolder'],
            ['Folder'],
            ['File']
        ];
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testClassExists($modelName)
    {
        $this->assertTrue(class_exists('app\models\base\\' . $modelName . 'Base'));
        $this->assertTrue(class_exists('app\models\query\base\\' . $modelName . 'QueryBase'));
        $this->assertTrue(class_exists('app\models\\' . $modelName));
        $this->assertTrue(class_exists('app\models\query\\' . $modelName . 'Query'));
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testGetParentClass($modelName)
    {
        $this->assertEquals('yii\boost\db\ActiveRecord', get_parent_class('app\models\base\\' . $modelName . 'Base'));
        $this->assertEquals('yii\boost\db\ActiveQuery', get_parent_class('app\models\query\base\\' . $modelName . 'QueryBase'));
        $this->assertEquals('app\models\base\\' . $modelName . 'Base', get_parent_class('app\models\\' . $modelName));
        $this->assertEquals('app\models\query\base\\' . $modelName . 'QueryBase', get_parent_class('app\models\query\\' . $modelName . 'Query'));
    }

    /**
     * @return array
     */
    public function tableNameDataProvider()
    {
        return [
            ['Type', 'type'],
            ['RootFolder', 'root_folder'],
            ['Folder', 'folder'],
            ['File', 'file']
        ];
    }

    /**
     * @param string $modelName
     * @param string $tableName
     * @dataProvider tableNameDataProvider
     */
    public function testMethodTableName($modelName, $tableName)
    {
        /* @var $modelClass string|\yii\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $this->assertEquals($tableName, $modelClass::tableName());
    }

    /**
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testMethodFind($modelName)
    {
        /* @var $modelClass string|\yii\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $this->assertEquals('app\models\query\\' . $modelName . 'Query', get_class($modelClass::find()));
    }

    /**
     * @return array
     */
    public function singularRelationsDataProvider()
    {
        return [
            ['Type', []],
            ['RootFolder', ['Type']],
            ['Folder', ['RootFolder', 'Type']],
            ['File', ['Folder', 'RootFolder']]
        ];
    }

    /**
     * @param string $modelName
     * @param string[] $singularRelations
     * @dataProvider singularRelationsDataProvider
     */
    public function testMethodSingularRelations($modelName, array $singularRelations)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('singularRelations'));
        $this->assertTrue($reflection->getMethod('singularRelations')->isStatic());
        $this->assertEquals($singularRelations, $modelClass::singularRelations());
    }

    /**
     * @return array
     */
    public function pluralRelationsDataProvider()
    {
        return [
            ['Type', ['RootFolders']],
            ['RootFolder', ['Folders']],
            ['Folder', ['Files']],
            ['File', []]
        ];
    }

    /**
     * @param string $modelName
     * @param string[] $pluralRelations
     * @dataProvider pluralRelationsDataProvider
     */
    public function testMethodPluralRelations($modelName, array $pluralRelations)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('pluralRelations'));
        $this->assertTrue($reflection->getMethod('pluralRelations')->isStatic());
        $this->assertEquals($pluralRelations, $modelClass::pluralRelations());
    }

    /**
     * @return array
     */
    public function shortNameDataProvider()
    {
        return [
            ['Type', 'Type'],
            ['RootFolder', 'RootFolder'],
            ['Folder', 'Folder'],
            ['File', 'File']
        ];
    }

    /**
     * @param string $modelName
     * @param string $shortName
     * @dataProvider shortNameDataProvider
     */
    public function testMethodShortName($modelName, $shortName)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('shortName'));
        $this->assertTrue($reflection->getMethod('shortName')->isStatic());
        $this->assertEquals($shortName, $modelClass::shortName());
    }

    /**
     * @return array
     */
    public function titleDataProvider()
    {
        return [
            ['Type', 'Тип'],
            ['RootFolder', 'Корневая папка'],
            ['Folder', 'Папка'],
            ['File', 'File']
        ];
    }

    /**
     * @param string $modelName
     * @param string $title
     * @dataProvider titleDataProvider
     */
    public function testMethodTitle($modelName, $title)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('title'));
        $this->assertTrue($reflection->getMethod('title')->isStatic());
        $this->assertEquals($title, $modelClass::title());
    }

    /**
     * @return array
     */
    public function primaryKeyDataProvider()
    {
        return [
            ['Type', ['id']],
            ['RootFolder', ['id']],
            ['Folder', ['id']],
            ['File', ['id']]
        ];
    }

    /**
     * @param string $modelName
     * @param string[] $primaryKey
     * @dataProvider primaryKeyDataProvider
     */
    public function testMethodPrimaryKey($modelName, array $primaryKey)
    {
        /* @var $modelClass string|\yii\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $this->assertEquals($primaryKey, $modelClass::primaryKey());
    }

    /**
     * @return array
     */
    public function displayFieldDataProvider()
    {
        return [
            ['Type', ['name']],
            ['RootFolder', ['type_id', 'name']],
            ['Folder', ['root_folder_id', 'name']],
            ['File', ['folder_id', 'name']]
        ];
    }

    /**
     * @param string $modelName
     * @param string[] $displayField
     * @dataProvider displayFieldDataProvider
     */
    public function testMethodDisplayField($modelName, array $displayField)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $this->assertEquals($displayField, $modelClass::displayField());
    }

    /**
     * @return array
     */
    public function getDisplayFieldDataProvider()
    {
        return [
            ['Type', ['name' => 'Type name'], 'Type name'],
            ['RootFolder', ['type_id' => 1, 'name' => 'Root Folder name'], '1 Root Folder name'],
            ['Folder', ['root_folder_id' => 2, 'name' => 'Folder name'], '2 Folder name'],
            ['File', ['folder_id' => 3, 'name' => 'File name'], '3 File name']
        ];
    }

    /**
     * @param string $modelName
     * @param array $values
     * @param string $displayField
     * @dataProvider getDisplayFieldDataProvider
     */
    public function testMethodGetDisplayField($modelName, array $values, $displayField)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('getDisplayField'));
        $this->assertNotTrue($reflection->getMethod('getDisplayField')->isStatic());
        /* @var $model \yii\boost\db\ActiveRecord */
        $model = new $modelClass;
        $model->setAttributes($values, false);
        $this->assertEquals($displayField, $model->getDisplayField());
    }

//    public function testMethodGetTypeOfFolder()
//    {
//        $reflection = new ReflectionClass('app\models\Folder');
//        $this->assertTrue($reflection->hasMethod('getType'));
//        $this->assertFalse($reflection->getMethod('getType')->isStatic());
//        $this->assertEquals('app\models\query\TypeQuery', get_class((new \app\models\Folder)->getType()));
//    }
//
//    public function testMethodGetFilesOfFolder()
//    {
//        $reflection = new ReflectionClass('app\models\Folder');
//        $this->assertTrue($reflection->hasMethod('getFiles'));
//        $this->assertFalse($reflection->getMethod('getFiles')->isStatic());
//        $this->assertEquals('app\models\query\FileQuery', get_class((new \app\models\Folder)->getFiles()));
//    }
//
//    public function testMethodGetFolderOfFile()
//    {
//        $reflection = new ReflectionClass('app\models\File');
//        $this->assertTrue($reflection->hasMethod('getFolder'));
//        $this->assertFalse($reflection->getMethod('getFolder')->isStatic());
//        $this->assertEquals('app\models\query\FolderQuery', get_class((new \app\models\File)->getFolder()));
//    }
//
//    public function testMethodGetTypeOfFile()
//    {
//        $reflection = new ReflectionClass('app\models\File');
//        $this->assertTrue($reflection->hasMethod('getType'));
//        $this->assertFalse($reflection->getMethod('getType')->isStatic());
//        $query = (new \app\models\File)->getType();
//        $this->assertEquals('app\models\query\TypeQuery', get_class($query));
//        $this->assertInstanceOf('yii\db\ActiveQuery', $query->via);
//        /* @var $viaQuery \yii\db\ActiveQuery */
//        $viaQuery = $query->via;
//        $this->assertInternalType('array', $viaQuery->from);
//        $this->assertEquals(['folder via_folder'], $viaQuery->from);
//    }
//
//    public function testMethodNewFolderOfType()
//    {
//        $reflection = new ReflectionClass('app\models\Type');
//        $this->assertTrue($reflection->hasMethod('newFolder'));
//        $this->assertFalse($reflection->getMethod('newFolder')->isStatic());
//        $type = new \app\models\Type;
//        $type->id = mt_rand(1, 10);
//        $folder = $type->newFolder();
//        $this->assertEquals($type->id, $folder->type_id);
//    }
//
//    public function testMethodNewFileOfFolder()
//    {
//        $reflection = new ReflectionClass('app\models\Folder');
//        $this->assertTrue($reflection->hasMethod('newFile'));
//        $this->assertFalse($reflection->getMethod('newFile')->isStatic());
//        $folder = new \app\models\Folder;
//        $folder->id = mt_rand(1, 10);
//        $file = $folder->newFile();
//        $this->assertEquals($folder->id, $file->folder_id);
//    }
}
