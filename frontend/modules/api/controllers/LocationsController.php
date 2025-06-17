<?php

namespace frontend\modules\api\controllers;


use common\components\DBHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\PushHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\ApplicationDetails;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\AppraisalsBusiness;
use common\models\Branches;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\Operations;
use common\models\ProjectAppraisalsMapping;
use common\models\ProjectsDisabled;
use common\models\Projects;
use common\models\Provinces;
use common\models\search\ApplicationsSearch;
use common\models\SocialAppraisal;
use common\models\Users;
use common\models\Visits;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;
use yii\helpers\ArrayHelper;


class LocationsController extends RestController
{
    public $rbac_type = 'api';

    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback' => []
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type, UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'getapplications' => ['GET'],
                        'addlocations' => ['POST'],
                    ],
                ],

            ];
    }

    public function actionGetapplications($id){
        $project_ids=[78,79];

        $application_model=Applications::find()->where(['branch_id'=>$id])
       ->andWhere(['deleted'=>0])
        ->andWhere(['!=','status','rejected'])
        ->andWhere(['in','project_id',$project_ids])->all();


        foreach ($application_model as $key=> $application){
            $bussiness_apprisals_model=AppraisalsBusiness::find()->select(['latitude','longitude'])->where(['application_id'=>$application->id])->one();
            $member_name=Members::find()->select(['full_name'])->where(['id'=>$application->member_id])->one();

            $application_action=ApplicationActions::find()->select('status')->where(['parent_id'=>$application->id])->andWhere(['action'=>'business_appraisal'])->one();

                if ($application_action->status == 1) {


                    $application_data[$key]['id'] = $application->id;
                    $application_data[$key]['member_id'] = $application->member_id;
                    $application_data[$key]['member_name']=$member_name->full_name;
                    $application_data[$key]['application_no'] = $application->application_no;
                    $application_data[$key]['require_amount'] = $application->req_amount;
                    $application_data[$key]['project_name'] = $application->project->name;
                    $application_data[$key]['location'] = isset($bussiness_apprisals_model) ? $bussiness_apprisals_model : ["latitude" => "",
                        "longitude" => ""];
                }

        }
        $response=[

            "applications"=>is_array($application_data)? array_values($application_data): array(),

        ];
        if(isset($response)) {
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "No Record Found.");
        }

    }
    public function actionAddlocations()
    {
        $request = $this->request;
        foreach ($request as $g) {
            $application_id=$g['application_id'];
            $longitude=$g['longitude'];
            $latitude=$g['latitude'];


            $appraisal = AppraisalsBusiness::find()->where(['application_id' => $application_id])->one();

            $appraisal->longitude =  $longitude;
            $appraisal->latitude = $latitude;
            if ($appraisal->save(false)) {
               $response['message'] = 'data save successfully';
            } else {
                var_dump($appraisal->getErrors());
                return $this->sendFailedResponse(400, "Record  not Saved");
            }

        }
        return $this->sendSuccessResponse(200, $response);

    }
}