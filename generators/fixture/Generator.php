<?php

namespace yii\gii\plus\generators\fixture;

use yii\gii\CodeFile;
use yii\gii\Generator as GiiGenerator;
use yii\gii\plus\helpers\Helper;
use yii\helpers\Inflector;
use yii\web\JsExpression;
use yii\helpers\Json;
use Yii;

class Generator extends GiiGenerator
{

    /**
     * @var string
     */
    public $modelClass = 'app\models\*';

    /**
     * @var string
     */
    public $fixtureNs;

    /**
     * @var string
     */
    public $fixtureBaseClass = 'yii\boost\test\ActiveFixture';

    /**
     * @var bool
     */
    public $generateDataFile = false;

    /**
     * @var string
     */
    public $dataPath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (Yii::getAlias('@common', false)) {
            $this->modelClass = 'common\models\*';
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Fixture Generator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['modelClass', 'fixtureNs', 'fixtureBaseClass', 'dataPath'], 'filter', 'filter' => 'trim'],
            [['modelClass', 'fixtureBaseClass'], 'required'],
            [['modelClass'], 'match', 'pattern' => '~^(?:\w+\\\\)+(?:\w+|\*)$~'],
            [['fixtureNs'], 'default', 'value' => function (Generator $model, $attribute) {
                return preg_replace('~\\\\models\\\\(?:\w+|\*)$~', '\fixtures', $model->modelClass);
            }],
            [['fixtureNs'], 'match', 'pattern' => '~\\\\fixtures$~'],
            [['fixtureBaseClass'], 'validateClass', 'params' => ['extends' => 'yii\boost\test\ActiveFixture']],
            [['generateDataFile'], 'boolean'],
            [['dataPath'], 'default', 'value' => function (Generator $model, $attribute) {
                return '@' . str_replace('\\', '/', preg_replace('~\\\\fixtures$~', '\tests\data', $model->fixtureNs));
            }]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['fixture.php', 'data-file.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['modelClass', 'fixtureNs', 'fixtureBaseClass', 'dataPath']);
    }

    /**
     * @return JsExpression
     */
    public function getModelClassAutoComplete()
    {
        $data = [];
        foreach (Helper::getModelDeepNamespaces() as $modelNs) {
            $data[] = $modelNs . '\*';
        }
        return new JsExpression('function (request, response) { response(' . Json::htmlEncode($data) . '); }');
    }

    /**
     * @return string[]
     */
    public function getFixtureBaseClassAutoComplete()
    {
        return ['yii\boost\test\ActiveFixture'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        if (preg_match('~^((?:\w+\\\\)*\w+)\\\\(\w+|\*)$~', $this->modelClass, $match)) {
            foreach (glob(Yii::getAlias('@' . str_replace('\\', '/', $match[1])) . '/' . $match[2] . '.php') as $filename) {
                $ns = $match[1];
                $modelName = basename($filename, '.php');
                $modelClass = $ns . '\\' . $modelName;
                $fixtureNs = $this->fixtureNs;
                $fixtureName = $modelName;
                $fixtureClass = $fixtureNs . '\\' . $fixtureName;
                $baseFixtureName = preg_replace('~^(?:\w+\\\\)*\w+\\\\(\w+)$~', '$1', $this->fixtureBaseClass);
                $baseFixtureClass = $this->fixtureBaseClass;
                $dataFile = $this->dataPath . '/' . Inflector::underscore($modelName) . '.php';
                /* @var $modelClass string|\yii\boost\db\ActiveRecord */
                $params = [
                    'ns' => $ns,
                    'modelName' => $modelName,
                    'modelClass' => $modelClass,
                    'fixtureNs' => $fixtureNs,
                    'fixtureName' => $fixtureName,
                    'fixtureClass' => $fixtureClass,
                    'baseFixtureName' => $baseFixtureName,
                    'baseFixtureClass' => $baseFixtureClass,
                    'dataFile' => $dataFile,
                    'tableSchema' => $modelClass::getTableSchema()
                ];

                // static table
                $ignore = false;
                /* @var $modelClass \yii\boost\db\ActiveRecord */
                $primaryKey = $modelClass::primaryKey();
                foreach ($primaryKey as $primaryKey1) {
                    $column = $modelClass::getTableSchema()->getColumn($primaryKey1);
                    if (!$column->isPrimaryKey) {
                        $ignore = true;
                        break;
                    }
                }

                if (!$ignore && (count($primaryKey) == 1) && ($primaryKey[0] == 'id')) {
                    $column = $modelClass::getTableSchema()->getColumn($primaryKey[0]);
                    if (($column->type == \yii\db\Schema::TYPE_SMALLINT) && ($column->size == 3) && !$column->autoIncrement) {
                        $ignore = true;
                    }
                }

                if (!$ignore) {
                    $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $fixtureNs)) . '/' . $fixtureName . '.php', $this->render('fixture.php', $params));
                    if ($this->generateDataFile) {
                        $files[] = new CodeFile(Yii::getAlias($dataFile), $this->render('data-file.php', $params));
                    }
                }
            }
        }
        return $files;
    }
}
