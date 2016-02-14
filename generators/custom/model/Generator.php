<?php

namespace yii\gii\plus\generators\custom\model;

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
    public $baseModelClass = 'app\models\base\*Base';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (Yii::getAlias('@common', false)) {
            $this->baseModelClass = 'common\models\base\*Base';
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Custom Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['baseModelClass', 'filter', 'filter' => 'trim'],
            ['baseModelClass', 'required'],
            ['baseModelClass', 'match', 'pattern' => '~^(?:\w+\\\\)+base\\\\(?:\w+|\*)Base$~']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'query.php'];
    }

    /**
     * @return JsExpression
     */
    public function getBaseModelClassAutoComplete()
    {
        $data = [];
        foreach (Helper::getModelDeepNamespaces() as $modelNs) {
            $data[] = $modelNs . '\base\*Base';
        }
        return new JsExpression('function (request, response) { response(' . Json::htmlEncode($data) . '); }');
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        if (preg_match('~^((?:\w+\\\\)*\w+)\\\\base\\\\(\w+|\*)Base$~', $this->baseModelClass, $match)) {
            foreach (glob(Yii::getAlias('@' . str_replace('\\', '/', $match[1])) . '/base/' . $match[2] . 'Base.php') as $filename) {
                $ns = $match[1];
                $modelName = basename($filename, 'Base.php');
                $modelClass = $ns . '\\' . $modelName;
                $baseModelName = basename($filename, '.php');
                $baseModelClass = $ns . '\base\\' . $baseModelName;
                $queryNs = $ns . '\query';
                $queryName = $modelName . 'Query';
                $queryClass = $queryNs . '\\' . $queryName;
                $baseQueryName = $modelName . 'QueryBase';
                $baseQueryClass = $queryNs . '\base\\' . $baseQueryName;
                $params = [
                    'ns' => $ns,
                    'modelName' => $modelName,
                    'modelClass' => $modelClass,
                    'baseModelName' => $baseModelName,
                    'baseModelClass' => $baseModelClass,
                    'queryNs' => $queryNs,
                    'queryName' => $queryName,
                    'queryClass' => $queryClass,
                    'baseQueryName' => $baseQueryName,
                    'baseQueryClass' => $baseQueryClass
                ];
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $ns)) . '/' . $modelName . '.php', $this->render('model.php', $params));
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $queryNs)) . '/' . $queryName . '.php', $this->render('query.php', $params));
            }
        }
        return $files;
    }
}
