<?php

namespace yii\gii\plus\tests;

use yii\phpunit\TestCase;
use ReflectionClass;

class BaseModelTest extends TestCase
{

    public function testClassExists()
    {
        $this->assertTrue(class_exists('app\models\base\TypeBase'));
        $this->assertTrue(class_exists('app\models\query\base\TypeQueryBase'));
        $this->assertTrue(class_exists('app\models\Type'));
        $this->assertTrue(class_exists('app\models\query\TypeQuery'));
    }

    public function testGetParentClass()
    {
        $this->assertEquals('yii\boost\db\ActiveRecord', get_parent_class('app\models\base\TypeBase'));
        $this->assertEquals('yii\boost\db\ActiveQuery', get_parent_class('app\models\query\base\TypeQueryBase'));
        $this->assertEquals('app\models\base\TypeBase', get_parent_class('app\models\Type'));
        $this->assertEquals('app\models\query\base\TypeQueryBase', get_parent_class('app\models\query\TypeQuery'));
    }

    public function testMethodFind()
    {
        $this->assertEquals('app\models\query\TypeQuery', get_class(\app\models\Type::find()));
    }

    public function testMethodModelLabel()
    {
        $reflection = new ReflectionClass('app\models\Type');
        $this->assertTrue($reflection->hasMethod('modelLabel'));
        $this->assertTrue($reflection->getMethod('modelLabel')->isStatic());
        $this->assertEquals('Тип', \app\models\Type::modelLabel());
    }
}
