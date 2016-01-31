<?php

namespace yii\gii\plus\generators\user\model;

use yii\gii\CodeFile;
use yii\gii\Generator as GiiGenerator;
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
        return 'User Model Generator';
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
     * @var array
     */
    protected $nsPrefixes;

    /**
     * @param string $nsPrefix
     * @param string $path
     * @return array
     */
    protected function getSubNsPrefixes($nsPrefix, $path)
    {
        $nsPrefixes = [$nsPrefix];
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $subPath) {
            $basename = basename($subPath);
            if (($basename != 'base') && ($basename != 'query')) {
                $nsPrefixes = array_merge($nsPrefixes, $this->getSubNsPrefixes($nsPrefix . '\\' . $basename, $subPath));
            }
        }
        return $nsPrefixes;
    }

    /**
     * @return array
     */
    protected function getNsPrefixes()
    {
        if (is_null($this->nsPrefixes)) {
            $this->nsPrefixes = [];
            foreach (['app', 'backend', 'common', 'console', 'frontend'] as $rootNs) {
                $appPath = Yii::getAlias('@' . $rootNs, false);
                if ($appPath) {
                    $this->nsPrefixes = array_merge($this->nsPrefixes, $this->getSubNsPrefixes($rootNs . '\models', $appPath . '/models'));
                    foreach (glob($appPath . '/modules/*', GLOB_ONLYDIR) as $modulePath) {
                        $this->nsPrefixes = array_merge($this->nsPrefixes, $this->getSubNsPrefixes($rootNs . '\modules\\' . basename($modulePath) . '\models', $modulePath . '/models'));
                    }
                }
            }
        }
        return $this->nsPrefixes;
    }

    /**
     * @return JsExpression
     */
    public function getBaseModelClassAutoComplete()
    {
        $data = [];
        foreach ($this->getNsPrefixes() as $nsPrefix) {
            $data[] = $nsPrefix . '\base\*Base';
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
                /* @var $modelBaseClass \yii\db\ActiveRecord */
                $modelBaseClass = $match[1] . '\base\\' . basename($filename, '.php');
                $queryNs = $ns . '\query';
                $queryName = $modelName . 'Query';
                $queryClass = $queryNs . '\\' . $queryName;
                $queryBaseClass = get_class($modelBaseClass::find());
                $params = [
                    'ns' => $ns,
                    'modelName' => $modelName,
                    'modelClass' => $modelClass,
                    'modelBaseClass' => $modelBaseClass,
                    'queryNs' => $queryNs,
                    'queryName' => $queryName,
                    'queryClass' => $queryClass,
                    'queryBaseClass' => $queryBaseClass
                ];
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $ns)) . '/' . $modelName . '.php', $this->render('model.php', $params));
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $queryNs)) . '/' . $queryName . '.php', $this->render('query.php', $params));
            }
        }
        return $files;
    }
}
