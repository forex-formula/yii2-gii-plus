<?php

namespace yii\gii\plus\generators\user\model;

use yii\gii\Generator as GiiGenerator;

class Generator extends GiiGenerator
{

    public $baseModelClass = 'app\models\base\*Base';

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
            [['baseModelClass'], 'filter', 'filter' => 'trim'],
            [['baseModelClass'], 'required'],
            [['baseModelClass'], 'match', 'pattern' => '~^(?:\w+\\\\)+base\\\\(?:\w+|\*)Base$~'],
            [['baseModelClass'], 'validateBaseModelClass']
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
