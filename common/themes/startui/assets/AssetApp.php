<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace common\themes\startui\assets;

class AssetApp extends \yii\web\AssetBundle
{
    //public $basePath = '@webroot';
    public $sourcePath = '@common/themes/startui';
    //public $baseUrl = '@web';
    public $css = [
        'build/css/lib/lobipanel/lobipanel.min.css',
        'build/css/separate/vendor/lobipanel.min.css',
        'build/css/lib/jqueryui/jquery-ui.min.css',
        'build/css/separate/pages/widgets.min.css',
        'build/css/separate/vendor/slick.min.css',
        'build/css/separate/pages/profile.min.css',
        'build/css/separate/pages/profile-2.min.css',
        'build/css/lib/font-awesome/font-awesome.min.css',
        'build/css/lib/bootstrap/bootstrap.min.css',
        'build/css/lib/summernote/summernote.css',
        "build/css/separate/elements/cards.min.css",
        "build/css/separate/pages/editor.min.css",
        "build/css/lib/multipicker/multipicker.min.css",
        "build/css/separate/vendor/multipicker.min.css",
        "build/css/separate/pages/gallery.min.css",
        //"build/css/separate/vendor/tags_editor.min.css",
        'build/css/main.css',
    ];

    public $js = [
        //'build/js/lib/jquery/jquery-3.2.1.min.js',
        'build/js/lib/popper/popper.min.js',
        'build/js/lib/tether/tether.min.js',
        'build/js/plugins.js',
        'build/js/lib/bootstrap/bootstrap.min.js',
        'build/js/lib/jqueryui/jquery-ui.min.js',
        'build/js/lib/lobipanel/lobipanel.min.js',
        'build/js/lib/summernote/summernote.min.js',
        'build/js/lib/match-height/jquery.matchHeight.min.js',
        'https://www.gstatic.com/charts/loader.js',
        "build/js/lib/multipicker/multipicker.min.js",
        "build/js/lib/jquery-tag-editor/jquery.caret.min.js",
        "build/js/lib/jquery-tag-editor/jquery.tag-editor.min.js",
        'build/js/app.js',
    ];

    public $depends = [
    ];
}
