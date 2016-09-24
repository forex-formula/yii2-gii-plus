<?php

namespace app\tests;

use yii\phpunit\TestCase;

class FixtureTest extends TestCase
{

    /**
     * @return array
     */
    public function noFixtureNameDataProvider()
    {
        return [ // [$noFixtureName]
            ['RootFolderType'],
            ['FileInfoType'],
            ['FileReport']
        ];
    }

    /**
     * @return array
     */
    public function fixtureNameDataProvider()
    {
        return [ // [$fixtureName]
            ['RootFolder'],
            ['Folder'],
            ['File'],
            ['FileInfo'],
            ['Sequence'],
            ['Something']
        ];
    }

    /**
     * @param string $noFixtureName
     * @dataProvider noFixtureNameDataProvider
     */
    public function testClassExistsFalse($noFixtureName)
    {
        static::assertFalse(class_exists('app\fixtures\\' . $noFixtureName));
    }

    /**
     * @param string $fixtureName
     * @dataProvider fixtureNameDataProvider
     */
    public function testClassExists($fixtureName)
    {
        static::assertTrue(class_exists('app\fixtures\\' . $fixtureName));
    }

    /**
     * @param string $fixtureName
     * @dataProvider fixtureNameDataProvider
     */
    public function testGetParentClass($fixtureName)
    {
        static::assertEquals('yii\boost\test\ActiveFixture', get_parent_class('app\fixtures\\' . $fixtureName));
    }
}
