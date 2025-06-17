<?php
namespace frontend\modules\reportsapi\controllers;

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
class AuditsController extends ActiveController
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

    public function actionData()
    {
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $response = [];
        $sql = $input['sql'];

       $data = Yii::$app->db->createCommand($sql)->queryAll();

       if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['audit_data'] = $data;
            $response['data']['message'] = "Audit Data";
        }
        else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['data']['message'] = "invalid access token or already logout";
       }


        return $response;
    }
}
