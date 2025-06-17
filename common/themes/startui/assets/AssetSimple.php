<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace common\themes\startui\assets;

class AssetSimple extends \yii\web\AssetBundle
{
    //public $basePath = '@webroot';
    public $sourcePath = '@common/themes/startui';
    //public $baseUrl = '@web';
    public $css = [
        'build/css/separate/pages/login.min.css',
        'build/css/lib/font-awesome/font-awesome.min.css',
        'build/css/lib/bootstrap/bootstrap.min.css',
        'build/css/main.css',
    ];

    public $js = [
        'build/js/lib/jquery/jquery-3.2.1.min.js',
        'build/js/lib/popper/popper.min.js',
        'build/js/lib/tether/tether.min.js',
        'build/js/lib/bootstrap/bootstrap.min.js',
        'build/js/plugins.js',
        'build/js/lib/match-height/jquery.matchHeight.min.js',
    ];

    public $depends = [
    ];
}
