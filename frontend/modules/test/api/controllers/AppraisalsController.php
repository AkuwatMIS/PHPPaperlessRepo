<?php

namespace frontend\modules\test\api\controllers;


use common\components\DBHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\PushHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\Loans;
use common\models\Members;
use common\models\Operations;
use common\models\ProjectsDisabled;
use common\models\Projects;
use common\models\search\ApplicationsSearch;
use common\models\SocialAppraisal;
use common\models\Users;
use frontend\modules\test\api\models\AppraisalsForm;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;
use yii\helpers\ArrayHelper;


class AppraisalsController extends RestController
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
                        return print_r(json_encode($this->sendFailedResponse('401', 'You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type, UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'update' => ['PUT'],
                        'sync' => ['PUT'],
                        'view' => ['GET'],
                    ],
                ],

            ];
    }

    public function actionSync()
    {
        $appraisals_data = $this->request;
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];

        foreach ($appraisals_data as $appraisal_data) {
            $appraisal_form = AppraisalsForm::findOne(['appraisal_table' => $appraisal_data['table']]);

            foreach ($appraisal_data['data'] as $appraisal) {
                $error = '<ul>';

                $appraisal_form->attributes = $appraisal;
                $model = $appraisal_form->syncAppraisal($appraisal);

                if (!isset($model['data_sync_status'])) {

                    $success[] = ['id' => $model->id,'table' => $appraisal_data['table'], 'temp_id' => $appraisal['temp_id'], 'cnic' => $model->application->member->cnic, 'status' => $model->application->member->full_name . "'s appraisal has synced successfully."];
                    $success_response[] = $appraisal['temp_id'];
                } else {
                    $error .= ApiParser::parseErrors($model['error']);
                }
                $error .= '</ul>';
                if (!in_array($appraisal['temp_id'], $success_response)) {
                    $errors[] = ['temp_id' => $appraisal['temp_id'],'table' => $appraisal_data['table'], 'error' => $error, 'cnic' => $model['cnic']];
                }
            }
        }
        if(empty($success) && !empty($errors)){
            $data['response_status'] = 'error';
        }else if(!empty($success) && empty($errors)){
            $data['response_status'] = 'success';
        }else{
            $data['response_status'] = 'warning';
        }
        $data['success'] = $success;
        $data['errors'] = $errors;
        return $this->sendSuccessResponse(201, $data);

    }

    public function actionUpdate()
    {
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];
        $appraisal = $this->request;
        $table = array_keys($appraisal)[0];
        $error = '<ul>';
        $appraisal_form = AppraisalsForm::findOne(['appraisal_table' => $table]);
        $appraisal_form->attributes = $appraisal[$table];
        $model = $appraisal_form->syncAppraisal($appraisal[$table]);
        if (!isset($model['data_sync_status'])) {
            $success[] = ['id' => $model->id, 'table' => $table,'cnic' => $model->application->member->cnic, 'status' => $model->application->member->full_name . "'s appraisal has synced successfully."];
            $success_response[] = $model->id;
        }
        else {
            $error .= ApiParser::parseErrors($model['error']);
        }
        $error .= '</ul>';
        if (empty($success_response)) {
            $errors[] = ['error' => $error,'table' => $table, 'cnic' => $model['cnic']];
        }
        if(empty($success) && !empty($errors)){
            $data['response_status'] = 'error';
        }else if(!empty($success) && empty($errors)){
            $data['response_status'] = 'success';
        }else{
            $data['response_status'] = 'warning';
        }
        $data['success'] = $success;
        $data['errors'] = $errors;
        return $this->sendSuccessResponse(201, $data);
    }

    public function actionView($id,$table)
    {

        $appraisal_form = AppraisalsForm::findOne(['appraisal_table' => $table]);
        $class = $appraisal_form->getModel();

        if (($model = $class::findOne(['application_id' => $id,'deleted' => 0])) !== null) {
            $data = ApiParser::parseAppraisal($model,$table);
            return $this->sendSuccessResponse(200, $data);
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}