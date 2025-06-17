<?php

namespace frontend\modules\test\api\controllers;

use common\components\Api;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use common\models\LoginForm;



class RestController extends Controller
{

    public $request;

    public $enableCsrfValidation = false;

    public $headers;


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                // 'Access-Control-Allow-Origin' => ['*', 'http://haikuwebapp.local.com:81','http://localhost:81'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]

        ];
        return $behaviors;
    }

    public function init()
    {
        //$this->request = json_decode(file_get_contents('php://input'), true);
        $this->request = Yii::$app->request->getBodyParams();

        if($this->request&&!is_array($this->request)){
            Yii::$app->api->sendFailedResponse(['Invalid Json']);

        }

    }

    public function sendSuccessResponse($code, $data = false,$additional_info = false)
    {

        Yii::$app->api->setHeader($code);

        $response = [];
        $response['status_type'] = "success";

        if (is_array($data))
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }

        return $response;
    }

    public function sendSuccessResponseSimple($code, $data = false,$additional_info = false)
    {

        Yii::$app->api->setHeadergz($code);

        $response = [];
        $response['status_type'] = "success";

        if (is_array($data))
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }

        return gzencode(json_encode($response), 9, FORCE_GZIP);
    }

    public function sendFailedResponse($code, $message)
    {
        Yii::$app->api->setHeader($code);

        $errors_array = array();
        if(isset($message) && !empty($message)){
            if(is_array($message)){
                $i = 0;
                $errors_array = array();
                if (!empty($message)) {
                    foreach ($message as $e){
                        $errors_array[$i]['message'] = $e[0];
                        $errors_array[$i]['reason'] = $e[0];
                        $i++;
                    }
                }
            }else if (isset($message)) {
                $errors_array[0]['message'] = $message;
                $errors_array[0]['reason'] = $message;
            }
        }

        return (array('status_type' => "error", 'errors' => $errors_array));

    }

    public function sendFailedResponseSimple($code, $message)
    {
        Yii::$app->api->setHeadergz($code);

        $errors_array = array();
        if(isset($message) && !empty($message)){
            if(is_array($message)){
                $i = 0;
                $errors_array = array();
                if (!empty($message)) {
                    foreach ($message as $e){
                        $errors_array[$i]['message'] = $e[0];
                        $errors_array[$i]['reason'] = $e[0];
                        $i++;
                    }
                }
            }else if (isset($message)) {
                $errors_array[0]['message'] = $message;
                $errors_array[0]['reason'] = $message;
            }
        }
        return gzencode(json_encode(array('status_type' => "error", 'errors' => $errors_array)), 9, FORCE_GZIP);
        /*$r = gzencode(json_encode(array('status_type' => "error", 'errors' => $errors_array)), 9, FORCE_GZIP);
        return gzdecode($r);*/

    }
}


