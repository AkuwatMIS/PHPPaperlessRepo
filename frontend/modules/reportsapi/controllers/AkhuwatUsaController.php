<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Loans;
use yii\rest\ActiveController;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

/**
 * Site controller
 */
class AkhuwatUsaController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                //'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'de',
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        //unset($actions['index']);
        return $actions;
    }

    public function actionGetProfile()
    {
        //$headers = Yii::$app->request->headers;
        $headers   = Yii::$app->request->get();
        $response = [];
        $data =ApiParser::parseVigaProfiles($headers);
        if(!empty($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['profiles'] = $data;
            $response['data']['message'] = "Profile Data";
        }else{
            $response['meta']['success']    = false;
            $response['meta']['code']       = 404;
            $response['data']['message']    = "No Profile Found";
        }
        /*else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['data']['message'] = "invalid access token or already logout";
       }*/
        return JsonHelper::asJson($response);
    }

    public function actionGetListing()
    {
        //$headers = Yii::$app->request->headers;
        $headers   = Yii::$app->request->get();
        $response = [];
        $data =ApiParser::parseListing();
        if(!empty($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['lists'] = $data;
            $response['data']['message'] = "Listing Data";
        }else{
            $response['meta']['success']    = false;
            $response['meta']['code']       = 404;
            $response['data']['message']    = "No Data Found";
        }
        /*else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['data']['message'] = "invalid access token or already logout";
       }*/
        return JsonHelper::asJson($response);
    }
}
