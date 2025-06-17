<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use Yii;
use common\models\Users;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;

class ActivityController extends \yii\web\Controller
{
    public $modelClass = 'common\models\Branches';

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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionInfo()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];
        $progress       = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $activity_id     = !empty($input['activity_id'])?($input['activity_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $activity = '';
            if($activity_id!=0){
                $sql = 'select a.id, a.name, count(b.activity_id) as loans_count from applications b inner join loans l on l.application_id = b.id inner join activities a on b.activity_id = a.id where l.status in (\'collected\',\'loan completed\') and b.activity_id = "'.$activity_id.'"';
                $command = Yii::$app->db->createCommand($sql);
                $activity = $command->queryOne();
            }

            $activity_details = ApiParser::parseActivity($activity);


            $heading = [
                'sub_heading' => [
                    'last_updated'=> 'as on '.date('d-M-Y')
                ]
            ];
            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Activity Detail";
            $response['data']['heading']    = $heading;
            $response['data']['activity']   = $activity_details;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

