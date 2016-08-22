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
            ['Folder'],
            ['RootFolder'],
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
            ['Folder', 'folder'],
            ['RootFolder', 'root_folder'],
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
    public function modelLabelDataProvider()
    {
        return [
            ['Type', 'Тип'],
            ['Folder', 'Папка'],
            ['RootFolder', 'Корневая папка'],
            ['File', 'File']
        ];
    }

    /**
     * @param string $modelName
     * @param string $modelLabel
     * @dataProvider modelLabelDataProvider
     */
    public function testMethodModelLabel($modelName, $modelLabel)
    {
        /* @var $modelClass string|\yii\boost\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $reflection = new ReflectionClass($modelClass);
        $this->assertTrue($reflection->hasMethod('modelLabel'));
        $this->assertTrue($reflection->getMethod('modelLabel')->isStatic());
        $this->assertEquals($modelLabel, $modelClass::modelLabel());
    }

    /**
     * @return array
     */
    public function primaryKeyDataProvider()
    {
        return [
            ['Type', ['id']],
            ['Folder', ['id']],
            ['RootFolder', ['id']],
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

    public function testMethodDisplayFieldOfType()
    {
        $reflection = new ReflectionClass('app\models\Type');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\Type::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['name'], $displayField);
    }

    public function testMethodDisplayFieldOfFolder()
    {
        $reflection = new ReflectionClass('app\models\Folder');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\Folder::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['type_id', 'name'], $displayField);
    }

    public function testMethodDisplayFieldOfFile()
    {
        $reflection = new ReflectionClass('app\models\File');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\File::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['folder_id', 'name'], $displayField);
    }

    public function testMethodGetTypeOfFolder()
    {
        $reflection = new ReflectionClass('app\models\Folder');
        $this->assertTrue($reflection->hasMethod('getType'));
        $this->assertFalse($reflection->getMethod('getType')->isStatic());
        $this->assertEquals('app\models\query\TypeQuery', get_class((new \app\models\Folder)->getType()));
    }

    public function testMethodGetFilesOfFolder()
    {
        $reflection = new ReflectionClass('app\models\Folder');
        $this->assertTrue($reflection->hasMethod('getFiles'));
        $this->assertFalse($reflection->getMethod('getFiles')->isStatic());
        $this->assertEquals('app\models\query\FileQuery', get_class((new \app\models\Folder)->getFiles()));
    }

    public function testMethodGetFolderOfFile()
    {
        $reflection = new ReflectionClass('app\models\File');
        $this->assertTrue($reflection->hasMethod('getFolder'));
        $this->assertFalse($reflection->getMethod('getFolder')->isStatic());
        $this->assertEquals('app\models\query\FolderQuery', get_class((new \app\models\File)->getFolder()));
    }

    public function testMethodGetTypeOfFile()
    {
        $reflection = new ReflectionClass('app\models\File');
        $this->assertTrue($reflection->hasMethod('getType'));
        $this->assertFalse($reflection->getMethod('getType')->isStatic());
        $query = (new \app\models\File)->getType();
        $this->assertEquals('app\models\query\TypeQuery', get_class($query));
        $this->assertInstanceOf('yii\db\ActiveQuery', $query->via);
        /* @var $viaQuery \yii\db\ActiveQuery */
        $viaQuery = $query->via;
        $this->assertInternalType('array', $viaQuery->from);
        $this->assertEquals(['folder via_folder'], $viaQuery->from);
    }

    public function testMethodNewFolderOfType()
    {
        $reflection = new ReflectionClass('app\models\Type');
        $this->assertTrue($reflection->hasMethod('newFolder'));
        $this->assertFalse($reflection->getMethod('newFolder')->isStatic());
        $type = new \app\models\Type;
        $type->id = mt_rand(1, 10);
        $folder = $type->newFolder();
        $this->assertEquals($type->id, $folder->type_id);
    }

    public function testMethodNewFileOfFolder()
    {
        $reflection = new ReflectionClass('app\models\Folder');
        $this->assertTrue($reflection->hasMethod('newFile'));
        $this->assertFalse($reflection->getMethod('newFile')->isStatic());
        $folder = new \app\models\Folder;
        $folder->id = mt_rand(1, 10);
        $file = $folder->newFile();
        $this->assertEquals($folder->id, $file->folder_id);
    }
}
