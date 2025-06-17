<?php
/**
 * Created by PhpStorm.
 * User: junaid.fayyaz
 * Date: 2/26/2018
 * Time: 12:27 PM
 */

namespace common\components\Helpers;


use yii\web\Response;

class JsonHelper
{
    static function asJson($data)
    {
        $response = \Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = $data;
        return $response;
    }
}