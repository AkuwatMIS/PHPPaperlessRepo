<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\models\ProjectCharges;
use common\models\Projects;
use Yii;
use common\models\Users;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;

class ChargesController extends \yii\web\Controller
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

    public function actionGetServiceCharges(){
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $project_id     = !empty($input['project_id'])?($input['project_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            if($project_id!=0){
                $project_charges = Projects::find()->where(['id'=>$project_id,'sc_status' => 1])->one();
                if(!empty($project_charges)){
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 200;
                    $response['data']['message']    = "Get the project service charges";
                    $response['data']['detail']    = ApiParser::parseProjectCharges($project_charges);
                }else{
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 404;
                    $response['data']['message']    = "Data Not Found";
                }
            }else{
                $response['meta']['success']    = true;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "Data Not Found";
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

