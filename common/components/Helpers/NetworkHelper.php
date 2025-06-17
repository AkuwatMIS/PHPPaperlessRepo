<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\CibTypes;
use common\models\ConnectionBanks;
use Guzzle\Http\ReadLimitEntityBody;
use Yii;

class NetworkHelper
{
    public static function curlCall($url,$headers,$body,$type)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if($result === false)
        {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        $rest = self::isJson($result);
        if($rest == 1){
            return json_decode($result);
        }else{
            return $result;
        }
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}