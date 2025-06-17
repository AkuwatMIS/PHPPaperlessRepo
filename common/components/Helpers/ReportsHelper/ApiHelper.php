<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 14/09/17
 * Time: 11:19 AM
 */

namespace common\components\Helpers\ReportsHelper;

use common\components\Helpers\CacheHelper;
use common\models\AccessTokens;
use common\models\Settings;
use Yii;

class ApiHelper
{
    public static function checkVersion($code)
    {
        $key = 'report_app_version';
        $version = CacheHelper::getConfig($key);
        if(empty($version)) {
            $settings = Settings::find()->where(['text' => 'version'])->one();
            $version = $settings->val;
            CacheHelper::setConfig($key, $version);
        }
        if ($code >= $version) {
            return true;
        }else{
            return false;
        }
    }

}