<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\models\Countries;
use common\models\News;
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
use common\models\ProgressReports;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;
use common\components\Helpers\ReportsHelper\StringHelper;
use common\components\Parsers\ReportsParser\ApiParser;

class NewsController extends \yii\web\Controller
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

        $headers        = Yii::$app->request->headers;
        $version_code   = $headers->get('version_code');

        return parent::beforeAction($action);
    }

    public function actionLatest()
    {
        $authenticate   = Yii::$app->request->getBodyParams();
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

        $region_id      = isset($input['region_id'])?($input['region_id']):0;
        $area_id        = isset($input['area_id'])?($input['area_id']):0;
        $branch_id      = isset($input['branch_id'])?($input['branch_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
        //if (true) {
            $latest_news = News::find()->where(['status'=>1])->orderBy(['id'=>SORT_DESC])->all();

            $news = ApiParser::parseNews($latest_news);

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "News List";
            $response['data']['detail']     = $news;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }


}

