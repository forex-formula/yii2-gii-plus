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
            ['Type'], ['Folder'], ['File']
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
     * @param string $modelName
     * @dataProvider modelNameDataProvider
     */
    public function testMethodFind($modelName)
    {
        /* @var $modelClass string|\yii\db\ActiveRecord */
        $modelClass = 'app\models\\' . $modelName;
        $this->assertEquals('app\models\query\\' . $modelName . 'Query', get_class($modelClass::find()));
    }

    public function testMethodModelLabel()
    {
        $reflection = new ReflectionClass('app\models\Type');
        $this->assertTrue($reflection->hasMethod('modelLabel'));
        $this->assertTrue($reflection->getMethod('modelLabel')->isStatic());
        $this->assertEquals('Тип', \app\models\Type::modelLabel());
    }

    public function testMethodPrimaryKey()
    {
        $primaryKey = \app\models\Type::primaryKey();
        $this->assertInternalType('array', $primaryKey);
        $this->assertEquals(['id'], $primaryKey);
    }

    public function testMethodDisplayField1()
    {
        $reflection = new ReflectionClass('app\models\Type');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\Type::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['name'], $displayField);
    }

    public function testMethodDisplayField2()
    {
        $reflection = new ReflectionClass('app\models\Folder');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\Folder::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['type_id', 'name'], $displayField);
    }

    public function testMethodDisplayField3()
    {
        $reflection = new ReflectionClass('app\models\File');
        $this->assertTrue($reflection->hasMethod('displayField'));
        $this->assertTrue($reflection->getMethod('displayField')->isStatic());
        $displayField = \app\models\File::displayField();
        $this->assertInternalType('array', $displayField);
        $this->assertEquals(['folder_id', 'name'], $displayField);
    }
}
