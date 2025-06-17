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
class HousingDashboardController extends ActiveController
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
        $application_id = $input['application_id'];
        $cnic = $input['cnic'];
        if(isset($cnic) && !empty($cnic)){
            $application = Applications::find()
                ->join('inner join','members','members.id=applications.member_id')
                ->where(['applications.project_id'=>132])
                ->andWhere(['members.cnic' => $cnic])
                ->select('applications.*')
                ->one();
        }else{
            $application = Applications::find()->where(['id'=>$application_id])->one();
        }
        $data =ApiParser::parseHousingLoanDetail($application);

        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['Profile_detail'] = $data;
            $response['data']['message'] = "Borrower Data";
        }
        else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['data']['message'] = "invalid access token or already logout";
       }
        return JsonHelper::asJson($response);
    }
    public function actionImages()
    {
        $headers = Yii::$app->request->headers;
        $response = [];

        $data =ImageHelper::getVisitImagesLatest(false);
        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['Images'] = $data;
            $response['data']['message'] = "Latest Images";
        }
        else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }
        return JsonHelper::asJson($response);
    }

    public function actionFloodImages()
    {
        $headers = Yii::$app->request->headers;
        $response = [];

        $data =ImageHelper::getVisitFloodImagesLatest(false);
        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['Images'] = $data;
            $response['data']['message'] = "Latest Images";
        }
        else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }
        return JsonHelper::asJson($response);
    }

    public function actionAcagImages()
    {
        $headers = Yii::$app->request->headers;
        $response = [];

        $data =ImageHelper::getVisitAcagImagesLatest(false);
        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['Images'] = $data;
            $response['data']['message'] = "Latest Images";
        }
        else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }
        return JsonHelper::asJson($response);
    }
}
