<?php

namespace yii\gii\plus\widgets;

use yii\helpers\Html;
use yii\jui\AutoComplete as YiiJuiAutoComplete;

class AutoComplete extends YiiJuiAutoComplete
{

    /**
     * @var array
     */
    public $source = [];

    /**
     * @var int
     */
    public $minLength = 0;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options['onfocus'] = 'jQuery(this).autocomplete(\'search\');';
        Html::addCssClass($this->options, 'form-control');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->clientOptions = array_merge($this->clientOptions, [
            'source' => $this->source,
            'minLength' => $this->minLength
        ]);
        parent::run();
    }
}
