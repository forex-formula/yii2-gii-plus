<?php

namespace yii\gii\plus;

use yii\gii\Module as GiiModule;
use yii\web\Application as WebApplication;
use Yii;

class Module extends GiiModule
{

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
        if ($app instanceof WebApplication) {
            $this->setViewPath(Yii::getAlias('@yii/gii/views'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function coreGenerators()
    {
        return array_merge(parent::coreGenerators(), [
            'base_model' => ['class' => 'yii\gii\plus\generators\base\model\Generator'],
            'custom_model' => ['class' => 'yii\gii\plus\generators\custom\model\Generator'],
            'fixture' => ['class' => 'yii\gii\plus\generators\fixture\Generator']
        ]);
    }
}
