<?php

namespace yii\gii\plus\generators\fixture;

use yii\gii\CodeFile;
use yii\gii\Generator as GiiGenerator;
use yii\gii\plus\helpers\Helper;
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
    public $fixtureBaseClass = 'yii\boost\test\ActiveFixture';

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
            [['modelClass', 'fixtureBaseClass'], 'filter', 'filter' => 'trim'],
            [['modelClass', 'fixtureBaseClass'], 'required'],
            [['modelClass'], 'match', 'pattern' => '~^(?:\w+\\\\)+(?:\w+|\*)$~']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['fixture.php'];
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
                $fixtureNs = preg_replace('~\\\\models$~', '\fixtures', $ns);
                $fixtureName = $modelName;
                $fixtureClass = $fixtureNs . '\\' . $fixtureName;
                $baseFixtureName = preg_replace('~^(?:\w+\\\\)*\w+\\\\(\w+)$~', '$1', $this->fixtureBaseClass);
                $baseFixtureClass = $this->fixtureBaseClass;
                $params = [
                    'ns' => $ns,
                    'modelName' => $modelName,
                    'modelClass' => $modelClass,
                    'fixtureNs' => $fixtureNs,
                    'fixtureName' => $fixtureName,
                    'fixtureClass' => $fixtureClass,
                    'baseFixtureName' => $baseFixtureName,
                    'baseFixtureClass' => $baseFixtureClass
                ];
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $fixtureNs)) . '/' . $fixtureName . '.php', $this->render('fixture.php', $params));
            }
        }
        return $files;
    }
}
