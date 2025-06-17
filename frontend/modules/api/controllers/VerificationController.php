<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\AppraisalsEmergency;
use common\models\AppraisalsHousing;
use common\models\AppraisalsSocial;
use common\models\Groups;
use common\models\Guarantors;
use common\models\search\VerificationSearch;
use common\models\SocialAppraisal;
use common\models\Verification;

use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;
use Yii;


class VerificationController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET'],
                        'verified' => ['POST'],
                    ],
                ],
            ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $params['order'] = 'applications.created_at desc';
        $searchModel = new VerificationSearch();
        $data = $searchModel->searchApi($params);
        $response = [];
        if(!empty($data['data'])){
            $response = [
                'members' => ApiParser::parseVerificationMembers($data['data']),
                'page' => $data['info']['page'],
                'size' => $data['info']['size'],
                'total_count' => $data['info']['totalCount']
                //'total_count' => 5
            ];
            return $this->sendSuccessResponse(200,$response);
        }else{
            return $this->sendFailedResponse(204, "Record not found");
        }
        /*$response = ApplicationHelper::getApplicationsForBMVerification();
        if(empty($response)){
            return $this->sendFailedResponse(204, "Record not found");
        }else{
            return $this->sendSuccessResponse(200,$response);
        }*/
    }

    public function actionMemberrecord($application_id)
    {
        /*$user_id = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $getRolesByUser = Yii::$app->authManager->getRolesByUser($user_id);
        $role = '';
        foreach ($getRolesByUser as $r) {
            $role = $r->name;
        }
        if($role == "CE")
        {
            $response = ApplicationHelper::getInfoByApplicationForSE($application_id);
        } else {*/
        $response = ApplicationHelper::getInfoByApplication($application_id);
        //}
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionBmverification()
    {
        $id = isset($this->request['application_id']) ? $this->request['application_id'] : '';
        $status = isset($this->request['status']) ? $this->request['status'] : '';
        $location_type = isset($this->request['location_type']) ? $this->request['location_type'] : '';
        if($status == 'approved'){
            $application = Applications::findOne($id);
            $application->status = $status;
            $application->comments = $this->request['comments'];
            if($application->save()){
                if($location_type=='social_appraisal'){
                    $appraisal = AppraisalsSocial::find()->where(['application_id'=>$id])->one();
                } else if($location_type=='business_appraisal'){
                    $appraisal = AppraisalsBusiness::find()->where(['application_id'=>$id])->one();
                } else if($location_type=='housing_appraisal'){
                    $appraisal = AppraisalsHousing::find()->where(['application_id'=>$id])->one();
                }else if($location_type=='emergency_appraisal'){
                    $appraisal = AppraisalsEmergency::find()->where(['application_id'=>$id])->one();
                }

                $appraisal->bm_verify_latitude = isset($this->request['bm_verify_latitude']) ? $this->request['bm_verify_latitude'] : '0.0';
                $appraisal->bm_verify_longitude = isset($this->request['bm_verify_longitude']) ? $this->request['bm_verify_longitude'] : '0.0';
                if($appraisal->save()){
                    ActionsHelper::updateAction('application',$application->id,'approved/rejected');
                    ActionsHelper::insertActions('verification',$application->project_id,$application->id,$application->created_by,1);
                    $response['message'] = "Application Approved Successfully";
                    return $this->sendSuccessResponse(200,$response);
                }else{
                    return $this->sendFailedResponse(400, $appraisal->getErrors());
                }
            }else{
                return $this->sendFailedResponse(400, $application->getErrors());
            }
        }else{
            $application = Applications::findOne($id);
            $application->status = $status;
            $application->reject_type = $this->request['reject_type'];
            $application->reject_reason = $this->request['reject_reason'];
            $application->comments = $this->request['comments'];
            if(isset($application->group))
            {
                $group = Groups::findOne(['id' => $application->group_id]);
                if($group->grp_type == 'IND')
                {
                    $group->deleted = 1;
                    $guarantors = Guarantors::find()->where(['group_id' => $group->id,'deleted' => 0])->all();
                    if(isset($guarantors))
                    {
                        foreach ($guarantors as $g)
                        {
                            $g->deleted = 1;
                            $g->save();
                        }
                    }
                } else {
                    $group->group_size = $group->group_size - 1;
                    if ($group->group_size < 3) {
                        $group->status = 'incomplete';
                    }
                }
                $group->save();

                $application->group_id = 0;
            }
            if($application->save()){
                $action_model = ApplicationActions::findOne(['parent_id' => $application->id, 'action' => 'group_formation']);
                if(isset($action_model))
                {
                    $action_model->status = 0;
                    $action_model->expiry_date = 0;
                    $action_model->save();
                }
                if(isset($this->request['bm_verify_latitude']) && isset($this->request['bm_verify_longitude'])) {
                    if ($location_type == 'social_appraisal') {
                        $appraisal = AppraisalsSocial::find()->where(['application_id' => $id])->one();
                    } else if ($location_type == 'business_appraisal') {
                        $appraisal = AppraisalsBusiness::find()->where(['application_id' => $id])->one();
                    } else if ($location_type == 'housing_appraisal') {
                        $appraisal = AppraisalsHousing::find()->where(['application_id' => $id])->one();
                    }else if($location_type=='emergency_appraisal'){
                        $appraisal = AppraisalsEmergency::find()->where(['application_id'=>$id])->one();
                    }
                    $appraisal->bm_verify_latitude = isset($this->request['bm_verify_latitude']) ? $this->request['bm_verify_latitude'] : '0.0';
                    $appraisal->bm_verify_longitude = isset($this->request['bm_verify_longitude']) ? $this->request['bm_verify_longitude'] : '0.0';
                    if ($appraisal->save()) {
                        $response['message'] = "Application Rejected Successfully";
                        return $this->sendSuccessResponse(200, $response);
                    } else {
                        return $this->sendFailedResponse(400, $appraisal->getErrors());
                    }
                } else {
                    $response['message'] = "Application Rejected Successfully";
                    return $this->sendSuccessResponse(200, $response);
                }
            }
            else{
                return $this->sendFailedResponse(400, $application->getErrors());
            }
        }

    }

    public function actionVerified()
    {
        $this->request['application_id'];
        $verification = Verification::findOne(['application_id' => $this->request['application_id'], 'status' => "pending"]);
        if(isset($verification))
        {
            if($this->request['status'] == "skip")
            {
                $verification->status = $this->request['status'];
                $verification->skip_reason = $this->request['skip_reason'];
                //$verification->thumb_impression = addslashes(json_encode($s));
                if($verification->save())
                {
                    $response['message'] = "Application Skipped Successfully";
                    return $this->sendSuccessResponse(200,$response);
                } else {
                    return $this->sendFailedResponse(400, $verification->getErrors());
                }
            } else if($this->request['status'] == "verified"){
                $verification->status = $this->request['status'];
                $verification->verified_at = time();
                $verification->thumb_impression = '';
                $verification->latitude = $this->request['latitude'];
                $verification->longitude = $this->request['longitude'];

                if($verification->save()){
                    $response['message'] = "Application Verified Successfully";
                    return $this->sendSuccessResponse(200,$response);
                } else {
                    return $this->sendFailedResponse(400, $verification->getErrors());
                }
            }
        } else {
            return $this->sendFailedResponse(400, "Invalid Record requested");
        }
        $response = ApplicationHelper::getApplicationsForVerification();
        return $this->sendSuccessResponse(200,$response);
    }

    protected function findModel($id)
    {
        if (($model = Verification::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }

}