<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Appraisals;
use common\models\Diseases;
use common\models\search\SocialAppraisalSearch;
use common\models\SocialAppraisal;
use common\models\SocialAppraisalDiseasesMapping;
use frontend\modules\test\api\models\AppraisalsForm;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;


class SocialappraisalController extends RestController
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
                /*'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],*/
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET', 'POST'],
                        'create' => ['POST'],
                        'syncsocialappraisals' => ['POST'],
                        'update' => ['PUT'],
                        'bulkupdate' => ['PUT'],
                        'view' => ['GET'],
                        'delete' => ['DELETE']
                    ],
                ],

            ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new SocialAppraisalSearch();
        $response = $searchModel->searchApi($params);
        if($response['info']['totalCount'] > 0) {
            $response['data'] = ApiParser::parseSocialAppraisals($response['data']);
            $data = [];
            foreach ($response['data'] as $social_appraisal_data) {
                $social_appraisal_data['diseases'] = AppraisalsHelper::getDiseases($social_appraisal_data['id']);
               // $social_appraisal_data['adult_diseases'] = AppraisalsHelper::getAdultDiseases($social_appraisal_data['id']);
                $data[] = $social_appraisal_data;
            }
            return $this->sendSuccessResponse(200, $data, $response['info']);
        }  else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSyncsocialappraisals()
    {
        $social_appraisals = $this->request;
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];
        foreach ($social_appraisals as $social_appraisal) {
            $error = '<ul>';
            if(!isset($social_appraisal['application_id']))
            {
                $error .= ApiParser::parseErrors("Application ID must be required.");
            }
            else if($social_appraisal['application_id'] <= 0 )
            {
                $error .= ApiParser::parseErrors("Application ID is invalid.");
            }
            else {
                $model = SocialAppraisal::find()->where(['application_id' => $social_appraisal['application_id']])->one();
                $app_model = Applications::findOne(['id' => $social_appraisal['application_id'], 'deleted' => 0]);
                if(!isset($model)) {
                    $model = new SocialAppraisal();
                    $model->attributes = $social_appraisal;

                    if (!isset($app_model)) {
                        $error .= ApiParser::parseErrors("Application ID is Invalid.");
                    } else {
                        if (isset($social_appraisal['date_of_maturity'])) {
                            $model->date_of_maturity = isset($social_appraisal['date_of_maturity']) && ($social_appraisal['date_of_maturity'] != 0) ? strtotime($model->date_of_maturity) : 0;
                        }

                        $model->house_rent_amount = isset($social_appraisal['house_rent_amount']) ? $social_appraisal['house_rent_amount'] : 0;
                        $model->amount = isset($social_appraisal['amount']) ? $social_appraisal['amount'] : 0;
                        $model->loan_amount = isset($social_appraisal['loan_amount']) ? $social_appraisal['loan_amount'] : 0;
                        //$model->social_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);

                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save()) {

                                $action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'social_appraisal']);
                                $action_model->status = 1;
                                $action_model->expiry_date = strtotime('+10 days',strtotime( date('Y-m-d H:i:s')));
                                $action_model->save();

                                /*if (isset($social_appraisal['diseases'])) {
                                    $diseases = $social_appraisal['diseases'];
                                    foreach ($diseases as $disease) {
                                        $disease_model = new SocialAppraisalDiseasesMapping();
                                        $disease_model->type = $disease['type'];
                                        $disease_model->social_appraisal_id = $model->id;
                                        $disease_data = Diseases::findOne(['name' => $disease['disease_id']]);
                                        $disease_model->disease_id = $disease_data['id'];
                                        if (!($flag = $disease_model->save())) {
                                            $transaction->rollBack();
                                            Yii::$app->db->createCommand('ALTER TABLE appraisals_social auto_increment = 1')->execute();
                                            $error .= ApiParser::parseErrors($disease_model->errors);
                                        }
                                    }
                                }*/
                            }
                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $social_appraisal['temp_id'],'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s social appraisal has synced successfully."];
                                $success_response[] = $social_appraisal['temp_id'];
                            } else {
                                $transaction->rollBack();
                                Yii::$app->db->createCommand('ALTER TABLE appraisals_social auto_increment = 1')->execute();
                                $error .= ApiParser::parseErrors($model->errors);
                            }
                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
                else {
                    $model->attributes = $social_appraisal;
                    if (isset($social_appraisal['date_of_maturity']) && ($social_appraisal['date_of_maturity'] != 0)) {
                        $model->date_of_maturity = strtotime($model->date_of_maturity);
                    }
                    //$model->social_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);
                    if (isset($app_model)) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $flag = $model->save();
                           /* if ( $flag = $model->save()) {
                                if (isset($social_appraisal['diseases'])) {
                                    $diseases = $social_appraisal['diseases'];
                                    SocialAppraisalDiseasesMapping::deleteAll(['social_appraisal_id' => $model->id]);
                                    foreach ($diseases as $disease) {
                                        $disease_model = new SocialAppraisalDiseasesMapping();
                                        $disease_model->type = $disease['type'];
                                        $disease_model->social_appraisal_id = $model->id;
                                        $disease_data = Diseases::findOne(['name' => $disease['disease_id']]);
                                        $disease_model->disease_id = $disease_data['id'];
                                        if (!($flag = $disease_model->save())) {
                                            $transaction->rollBack();
                                            $error .= ApiParser::parseErrors($disease_model->errors);
                                        }
                                    }
                                }
                            }*/
                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $social_appraisal['temp_id'],'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s social appraisal has synced successfully."];
                                $success_response[] = $social_appraisal['temp_id'];
                            } else {
                                $transaction->rollBack();
                                $error .= ApiParser::parseErrors($model->errors);
                            }
                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                    else {
                        $error .= ApiParser::parseErrors("Application ID is Invalid");
                    }
                }
            }
            $error .= '</ul>';
            if (!in_array($social_appraisal['temp_id'], $success_response)) {
                $errors[] = ['temp_id' => $social_appraisal['temp_id'], 'error' => $error,'cnic' => $app_model->member->cnic];
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

    public function actionSync($table)
    {
        $social_appraisals = $this->request;
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];
        foreach ($social_appraisals as $social_appraisal) {
            $error = '<ul>';
            $appraisal = AppraisalsForm::findOne(['appraisal_table' => $table]);
            $appraisal->attributes = $social_appraisal;
            $model = $appraisal->syncAppraisal($social_appraisal);
            if (!isset($model['sync_status'])) {
                $success[] = ['id' => $model->id, 'temp_id' => $social_appraisal['temp_id'], 'cnic' => $model->application->member->cnic, 'status' => $model->application->member->full_name . "'s social appraisal has synced successfully."];
                $success_response[] = $social_appraisal['temp_id'];
            } else {
                $error .= ApiParser::parseErrors($model['error']);
            }
            $error .= '</ul>';
            if (!in_array($social_appraisal['temp_id'], $success_response)) {
                $errors[] = ['temp_id' => $social_appraisal['temp_id'], 'error' => $error, 'cnic' => $model['cnic']];
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
        $model = SocialAppraisal::find()->where(['application_id' => $this->request['application_id']])->one();
        $error = '<ul>';
        if(!isset($this->request['application_id']))
        {
            $error .= ApiParser::parseErrors("Application ID must be required.");
        }
        else if($this->request['application_id'] <= 0 )
        {
            $error .= ApiParser::parseErrors("Application ID is invalid.");
        }
        else {
            $app_model = Applications::findOne(['id' => $this->request['application_id'], 'deleted' => 0]);
            if(!isset($model)) {
                $model = new SocialAppraisal();
                $model->attributes = $this->request;

                if (!isset($app_model)) {
                    $error .= ApiParser::parseErrors("Application ID is Invalid.");
                } else {
                    if (isset($this->request['date_of_maturity'])) {
                        $model->date_of_maturity = isset($this->request['date_of_maturity']) && ($this->request['date_of_maturity'] != 0) ? strtotime($model->date_of_maturity) : 0;
                    }

                    $model->house_rent_amount = isset($this->request['house_rent_amount']) ? $this->request['house_rent_amount'] : 0;
                    $model->amount = isset($this->request['amount']) ? $this->request['amount'] : 0;
                    $model->loan_amount = isset($this->request['loan_amount']) ? $this->request['loan_amount'] : 0;

                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save()) {

                            $action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'social_appraisal']);
                            $action_model->status = 1;
                            $action_model->expiry_date = strtotime('+10 days',strtotime( date('Y-m-d H:i:s')));
                            $action_model->save();
                        }
                        if ($flag) {
                            $transaction->commit();
                            $success[] = ['id' => $model->id, 'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s social appraisal has synced successfully."];
                            $success_response[] = $model->id;
                        } else {
                            $transaction->rollBack();
                            Yii::$app->db->createCommand('ALTER TABLE appraisals_social auto_increment = 1')->execute();
                            $error .= ApiParser::parseErrors($model->errors);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
            else {
                $model->attributes = $this->request;
                if (isset($this->request['date_of_maturity']) && ($this->request['date_of_maturity'] != 0)) {
                    $model->date_of_maturity = strtotime($model->date_of_maturity);
                }
                if (isset($app_model)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $flag = $model->save();
                        if ($flag) {
                            $transaction->commit();
                            $success[] = ['id' => $model->id,'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s social appraisal has synced successfully."];
                            $success_response[] = $model->id;
                        } else {
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($model->errors);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
                else {
                    $error .= ApiParser::parseErrors("Application ID is Invalid");
                }
            }
        }
        $error .= '</ul>';
        if (!in_array($model->id, $success_response)) {
            $errors[] = [ 'error' => $error,'cnic' => $app_model->member->cnic];
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

    public function actionView($id)
    {
        if (($model = SocialAppraisal::findOne(['application_id' => $id,'deleted' => 0])) !== null) {
                $data = ApiParser::parseSocialAppraisal($model);
                $data['description_image'] = ApiParser::parseImage(AppraisalsHelper::getSocialAppraisalImage($model->id), $model->id);
                return $this->sendSuccessResponse(200, $data);
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        if ($model->save()) {
            $data = ApiParser::parseSocialAppraisal($model);
            $data['diseases'] = AppraisalsHelper::getDiseases($model->id);
            return $this->sendSuccessResponse(200,$data);
        } else {
            return $this->sendFailedResponse(204,"Enable to delete record.");
        }
    }

    protected function findModel($id)
    {
        if (($model = SocialAppraisal::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}