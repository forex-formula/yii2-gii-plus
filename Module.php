<?php

namespace yii\gii\plus;

use yii\web\Application as WebApplication;
use Yii;
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
            $this->setViewPath(Yii::getAlias('@yii/gii/views'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function coreGenerators()
    {
        return array_merge(parent::coreGenerators(), [
            'base-model' => ['class' => 'yii\gii\plus\generators\base\model\Generator']
        ]);
    }
}
