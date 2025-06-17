<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\RbacHelper;
use common\models\Countries;
use common\models\Provinces;
use common\models\Divisions;
use common\models\Districts;
use common\models\Recoveries;
use yii\rest\ActiveController;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\Users;
use common\models\Regions;
use common\models\Areas;
use common\models\Branches;
use common\models\Projects;
use common\models\ProgressReports;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;
use common\components\Helpers\ReportsHelper\StringHelper;
use common\components\Parsers\ReportsParser\ApiParser;

class ProductController extends \yii\web\Controller
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
        $product_id     = !empty($input['product_id'])?($input['product_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $product = '';
            if($product_id!=0){
                //$sql = 'select p.id, p.name, count(b.product_id) as loans_count from borrowers b inner join loans l on l.borrower_id = b.id inner join products p on b.product_id = p.id where l.dsb_status in (\'Collected\',\'Loan Completed\') and b.product_id = "'.$product_id.'"';
                $sql = 'select p.id, p.name, count(a.product_id) as loans_count from applications a inner join loans l on l.application_id = a.id inner join products p on a.product_id = p.id where l.status in (\'collected\',\'loan completed\') and a.product_id = "'.$product_id.'"';
                $command = Yii::$app->db->createCommand($sql);
                $product = $command->queryOne();
            }

            $product_details = ApiParser::parseProduct($product);
            $heading = [
                'sub_heading' => [
                    'last_updated'=> 'as on '.date('Y-m-d')
                ]
            ];
            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Product Detail";
            $response['data']['heading']    = $heading;
            $response['data']['product']   = $product_details;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

