<?php
namespace common\components;

use common\components\Helpers\JsonHelper;
use common\components\Helpers\StringHelper;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use common\models\User;

use common\models\AuthorizationCodes;
use common\models\AccessTokens;
use yii\web\Response;

/**
 * Class for common API functions
 */
class Api extends Component
{

    public function sendFailedResponse($code, $message)
    {
        $this->setHeader($code);

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
        JsonHelper::asJson(array('status_type' => "error", 'errors' => $errors_array));
        //echo json_encode(array('status_type' => "error", 'errors' => $errors_array), JSON_PRETTY_PRINT);

        Yii::$app->end();
    }

    public function sendSuccessResponse($code, $data = false,$additional_info = false)
    {
        $this->setHeader($code);

        $response = [];
        $response['status_type'] = "success";

        if (is_array($data))
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }

        $response = Json::encode($response, JSON_PRETTY_PRINT);

        if (isset($_GET['callback'])) {
            /* this is required for angularjs1.0 client factory API calls to work*/
            $response = $_GET['callback'] . "(" . $response . ")";

            echo $response;
        } else {
            echo $response;
        }

        //Yii::$app->end();

    }
    public function setHeadergz($status)
    {

        $text = $this->_getStatusCodeMessage($status);

        /*Yii::$app->response->setStatusCode($status, $text);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;*/

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/x-gzip; charset=utf-8";


        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Akhuwat <www.akhuwat.org.pk>");
        header('Access-Control-Allow-Origin:*');


    }
    public function setHeader($status)
    {

        $text = $this->_getStatusCodeMessage($status);

        Yii::$app->response->setStatusCode($status, $text);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/json; charset=utf-8";


        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Akhuwat <www.akhuwat.org.pk>");
        header('Access-Control-Allow-Origin:*');


    }

    protected function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    public function createAuthorizationCode($user_id)
    {
        $model = new AuthorizationCodes;

        $model->code = md5(uniqid());

        $model->expires_at = time() + (60 * 5);

        $model->user_id = $user_id;

        if (isset($_SERVER['HTTP_X_HAIKUJAM_APPLICATION_ID']))
            $app_id = $_SERVER['HTTP_X_HAIKUJAM_APPLICATION_ID'];
        else
            $app_id = null;

        $model->app_id = $app_id;

        $model->created_at = time();

        $model->updated_at = time();

        $model->save(false);

        return ($model);

    }

    public function createAccesstoken($authorization_code)
    {

        $auth_code = AuthorizationCodes::findOne(['code' => $authorization_code]);

        $model = new AccessTokens();

        $model->token = md5(uniqid());

        $model->auth_code = $auth_code->code;

        $model->expires_at = time() + (60 * 60 * 24 * 60); // 60 days

        // $model->expires_at=time()+(60 * 2);// 2 minutes

        $model->user_id = $auth_code->user_id;

        $model->created_at = time();

        $model->updated_at = time();

        $model->save(false);

        return ($model);

    }

    public function refreshAccesstoken($token)
    {
        $access_token = AccessTokens::findOne(['token' => $token]);
        if ($access_token) {

            $access_token->delete();
            $new_access_token = $this->createAccesstoken($access_token->auth_code);
            return ($new_access_token);
        } else {

            Yii::$app->api->sendFailedResponse(400,"Invalid Access token2");
        }
    }

}
