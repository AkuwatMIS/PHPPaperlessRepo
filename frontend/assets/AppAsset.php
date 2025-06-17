<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
        'css/custom.css',
        'css/jqx.base.css',
    ];
    public $js = [
        //'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'
        'js/jquery.cookie.js',
        'js/jqxcore.js',
        'js/jqxdata.js',
        'js/jqxdata.export.js',
        'js/jqxbuttons.js',
        'js/jqxscrollbar.js',
        'js/jqxdatatable.js',
        'js/jqxtreegrid.js',
        'js/demos.js',
        'js/projects.js',
        "https://canvasjs.com/assets/script/canvasjs.min.js",
        //'https://code.jquery.com/jquery-2.2.4.min.js'
    ];
    public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
