<?php

namespace yii\gii\plus;

use yii\web\Application as WebApplication;
use yii\gii\Module as YiiGiiModule;

class Module extends YiiGiiModule
{

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
        if ($app instanceof WebApplication) {
            $this->setViewPath('@yii/gii/views');
        }
    }

    /**
     * @inheritdoc
     */
    protected function coreGenerators()
    {
        return array_merge(parent::coreGenerators(), [
            'base-model' => ['class' => 'yii\gii\plus\generators\base\model\Generator'],
            'extra-model' => ['class' => 'yii\gii\plus\generators\extra\model\Generator'],
            'base-search' => ['class' => 'yii\gii\plus\generators\base\search\Generator'],
            'extra-search' => ['class' => 'yii\gii\plus\generators\extra\search\Generator']
        ]);
    }
}
