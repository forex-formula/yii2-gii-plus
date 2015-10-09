<?php

namespace yii\gii\plus\widgets;

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
     * @var array
     */
    public $options = [
        'class' => 'form-control',
        'onfocus' => 'jQuery(this).autocomplete(\'search\');'
    ];

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
