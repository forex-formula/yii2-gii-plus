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

    /**
     * @param string $fixtureName
     * @dataProvider fixtureNameDataProvider
     */
    public function testModelClass($fixtureName)
    {
        /* @var $fixtureClass string|\yii\boost\test\ActiveFixture */
        $fixtureClass = 'app\fixtures\\' . $fixtureName;
        $fixture = new $fixtureClass;
        static::assertEquals('app\models\\' . $fixtureName, $fixture->modelClass);
    }

    /**
     * @return array
     */
    public function dependsDataProvider()
    {
        return [ // [$fixtureName, $depends]
            ['RootFolder', []],
            ['Folder', []],
            ['File', []],
            ['FileInfo', []],
            ['Sequence', []],
            ['Something', []]
        ];
    }

    /**
     * @param string $fixtureName
     * @param string[] $depends
     * @dataProvider dependsDataProvider
     */
    public function testDepends($fixtureName, $depends)
    {
        $fixtureClass = 'app\fixtures\\' . $fixtureName;
        /* @var $fixture \yii\boost\test\ActiveFixture */
        $fixture = new $fixtureClass;
        static::assertCount(count($depends), $fixture->depends);
        foreach ($depends as $depend) {
            static::assertContains($depend, $fixture->depends);
        }
    }

    /**
     * @return array
     */
    public function backDependsDataProvider()
    {
        return [ // [$fixtureName, $backDepends]
            ['RootFolder', []],
            ['Folder', []],
            ['File', []],
            ['FileInfo', []],
            ['Sequence', []],
            ['Something', []]
        ];
    }

    /**
     * @param string $fixtureName
     * @param string[] $backDepends
     * @dataProvider backDependsDataProvider
     */
    public function testBackDepends($fixtureName, $backDepends)
    {
        $fixtureClass = 'app\fixtures\\' . $fixtureName;
        /* @var $fixture \yii\boost\test\ActiveFixture */
        $fixture = new $fixtureClass;
        static::assertCount(count($backDepends), $fixture->backDepends);
        foreach ($backDepends as $backDepend) {
            static::assertContains($backDepend, $fixture->backDepends);
        }
    }
}
