<?php
// your_app/votewidget/VoteWidgetAsset.php

namespace common\widgets\LedgerCharges;

use yii\web\AssetBundle;

class LedgerAsset extends AssetBundle
{
    public $js = [
      "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"
    ];

    public $css = [
        'css/ledger.css'
    ];

    public $depends = [

    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}