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
use Yii;

class CibTasdeeqHelper
{
    public static function getReport($url, $body, $auth_token)
    {
        $headers = array
        (
            //'Content-Type: application/json',
            'Authorization: '.$auth_token,
        );

        //$response = NetworkHelper::curlCall('http://116.71.135.115/cib/tasdeeq/get-report-live.php', $headers, $body, true);
//        $response = NetworkHelper::curlCall('http://116.71.135.115/cib/tasdeeq/report.php', $headers, $body, true);
        $response = NetworkHelper::curlCall('http://116.58.62.238/cib/tasdeeq/report.php', $headers, $body, true);
        if ($response->statusCode == '111') {
            $res['success'] = true;
            $res['data'] = $response->data;
            return $res;
        } else {
            $res['success'] = false;
            //$res['data'] = $response->statusCode;
            $res['data'] = $response;
            return $res;
        }
    }
    public static function login($url, $cred_model)
    {
        $headers = array
        (
            'Content-Type: application/json',
        );
        // changes label to case sensatve
        $body = [
            "UserName" => $cred_model->username,
            "Password" => $cred_model->password,
            "url" =>$url . '/Authenticate/',
        ];
        //$response = NetworkHelper::curlCall('http://116.71.135.115/cib/tasdeeq/login.php', $headers, $body, true);
//        $response = NetworkHelper::curlCall('http://116.71.135.115/cib/tasdeeq/login.php', $headers, $body, true);
        $response = NetworkHelper::curlCall('http://116.58.62.238/cib/tasdeeq/login.php', $headers, $body, true);
        if ($response->statusCode == '111') {
            $cred_model->last_login_at = time();
            $cred_model->auth_token = $response->data->auth_token;
            $cred_model->save();
            $res['success'] = true;
            $res['data'] = $response->data->auth_token;
            return $res;
        } else {
            $res['success'] = false;
            $res['data'] = $response->message;
            return $res;
        }
    }
    public static function parseRequestBody($application)
    {
        $arr= [
            "CNIC" => isset($application->member->cnic) ? str_replace("-", "", $application->member->cnic) : '',
            "fullName" => isset($application->member->full_name) ? $application->member->full_name : '',
//            "dateOfBirth" => isset($application->member->dob) ? date('d-M-Y', $application->member->dob) : '01-jan-1970',
            "city" => isset($application->branch->city->name) ? $application->branch->city->name : '',
            "loanAmount" => isset($application->req_amount) ? ''.round($application->req_amount).'' : '0',
//            "gender" => isset($application->member->gender) ? ($application->member->gender) : '',
            "gender" => '',
            "currentAddress" => isset($application->member->businessAddress->address) ? $application->member->businessAddress->address : '',
            "fatherHusbandName" => isset($application->member->parentage) ? $application->member->parentage : ''
        ];
        return $arr;
    }
}