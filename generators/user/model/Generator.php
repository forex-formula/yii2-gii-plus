<?php

namespace yii\gii\plus\generators\user\model;

use yii\gii\Generator as GiiGenerator;
use yii\web\JsExpression;
use yii\helpers\Json;
use Yii;

class Generator extends GiiGenerator
{

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
            ['baseModelClass', 'match', 'pattern' => '~^(?:\w+\\\\)+base\\\\(?:\w+|\*)Base$~'],
            ['baseModelClass', 'validateBaseModelClass']
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
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $subpath) {
            $basename = basename($subpath);
            if (($basename != 'base') && ($basename != 'query')) {
                $nsPrefixes = array_merge($nsPrefixes, $this->getSubNsPrefixes($nsPrefix . '\\' . $basename, $subpath));
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
        return $files;
    }

    public function validateBaseModelClass()
    {

    }
}
