<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\BaAssets;
use common\models\BaBusinessExpenses;
use common\models\BaDetails;
use common\models\BaExistingInvestment;
use common\models\BaFixedBusinessAssets;
use common\models\BaNewRequiredAssets;
use common\models\BaRunningCapital;
use common\models\AppraisalsBusiness;
use common\models\search\BusinessAppraisalSearch;
use common\models\search\SocialAppraisalSearch;
use common\models\SocialAppraisal;
use Mpdf\Gif\Image;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;


class BusinessappraisalController extends RestController
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
                        return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET', 'POST'],
                        'syncbusinessappraisals' => ['POST'],
                        'update' => ['PUT'],
                        'view' => ['GET'],
                        'delete' => ['DELETE']
                    ],
                ],

            ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new BusinessAppraisalSearch();
        $response = $searchModel->searchApi($params);
        if($response['info']['totalCount'] > 0) {
            $response['data'] = ApiParser::parseBusinessAppraisals($response['data']);
            return $this->sendSuccessResponse(200, $response['data'], $response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSyncbusinessappraisals()
    {
        $business_appraisals = $this->request;
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];
        foreach ($business_appraisals as $business_appraisal) {

            $error = '<ul>';
            if(!isset($business_appraisal['application_id']))
            {
                $error .= ApiParser::parseErrors("Application ID must be required.");
            } else {
                $model = AppraisalsBusiness::find()->where(['application_id' => $business_appraisal['application_id']])->one();
                $app_model = Applications::findOne(['id' => $business_appraisal['application_id'], 'deleted' => 0]);
                if (!isset($model)) {
                    $model = new AppraisalsBusiness();
                    $model->attributes = $business_appraisal;
                    if (!isset($app_model)) {
                        $error .= ApiParser::parseErrors("Application ID is Invalid.");
                    } else {
                        $model->business_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save()) {

                                $prev_action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'social_appraisal', 'status' => 1]);
                                $action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'business_appraisal']);
                                if (!isset($prev_action_model)) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors("Social Appraisal must be done before performing Business Appraisal");
                                } else {
                                    $action_model->status = 1;
                                    $action_model->pre_action = $prev_action_model->id;
                                    $action_model->expiry_date = strtotime('+10 days', strtotime(date('Y-m-d H:i:s')));
                                    $action_model->save();

                                    $action_model = new ApplicationActions();
                                    $action_model->parent_id = $model->application_id;
                                    $action_model->user_id = $model->created_by;
                                    $action_model->action = "approved/rejected";
                                    $action_model->save();
                                }

                                if (isset($business_appraisal['fixed_business_assets']) && isset($business_appraisal['fixed_business_assets_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "fixed_business_assets";
                                    $ba_assets_model->assets_list = $business_appraisal['fixed_business_assets'];
                                    $ba_assets_model->total_amount = $business_appraisal['fixed_business_assets_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['running_capital']) && isset($business_appraisal['running_capital_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "running_capital";
                                    $ba_assets_model->assets_list = $business_appraisal['running_capital'];
                                    $ba_assets_model->total_amount = $business_appraisal['running_capital_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['business_expenses']) && isset($business_appraisal['business_expenses_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "business_expenses";
                                    $ba_assets_model->assets_list = $business_appraisal['business_expenses'];
                                    $ba_assets_model->total_amount = $business_appraisal['business_expenses_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "new_required_assets";
                                    $ba_assets_model->assets_list = $business_appraisal['new_required_assets'];
                                    $ba_assets_model->total_amount = $business_appraisal['new_required_assets_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }
                            }
                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $business_appraisal['temp_id'],'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s business appraisal has synced successfully."];
                                $success_response[] = $business_appraisal['temp_id'];
                            } else {
                                $transaction->rollBack();
                                Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                $error .= ApiParser::parseErrors($model->errors);
                            }
                        } catch (Exception $e) {
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($model->errors);
                        }
                    }
                }
                else {
                    $model->attributes = $business_appraisal;

                    $app_model = Applications::findOne(['id' => $model->application_id, 'deleted' => 0]);
                    if (isset($app_model)) {

                        $model->business_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save()) {
                                if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                    BaAssets::deleteAll(['ba_id' => $model->id]);
                                }
                                if (isset($business_appraisal['fixed_business_assets']) && isset($business_appraisal['fixed_business_assets_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "fixed_business_assets";
                                    $ba_assets_model->assets_list = $business_appraisal['fixed_business_assets'];
                                    $ba_assets_model->total_amount = $business_appraisal['fixed_business_assets_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['running_capital']) && isset($business_appraisal['running_capital_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "running_capital";
                                    $ba_assets_model->assets_list = $business_appraisal['running_capital'];
                                    $ba_assets_model->total_amount = $business_appraisal['running_capital_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['business_expenses']) && isset($business_appraisal['business_expenses_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "business_expenses";
                                    $ba_assets_model->assets_list = $business_appraisal['business_expenses'];
                                    $ba_assets_model->total_amount = $business_appraisal['business_expenses_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }

                                if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                    $ba_assets_model = new BaAssets();
                                    $ba_assets_model->type = "new_required_assets";
                                    $ba_assets_model->assets_list = $business_appraisal['new_required_assets'];
                                    $ba_assets_model->total_amount = $business_appraisal['new_required_assets_amount'];
                                    $ba_assets_model->ba_id = $model->id;
                                    $ba_assets_model->application_id = $model->application_id;
                                    if (!$flag = $ba_assets_model->save()) {
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                        $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                    }
                                }
                            }
                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $business_appraisal['temp_id'],'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s business appraisal has synced successfully."];
                                $success_response[] = $business_appraisal['temp_id'];
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
            if (!in_array($business_appraisal['temp_id'], $success_response)) {
                $errors[] = ['temp_id' => $business_appraisal['temp_id'], 'error' => $error,'cnic' => $app_model->member->cnic];
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
        $business_appraisal = $this->request;
        $errors = [];
        $success =[];
        $success_response=[];
        $data = [];

        $error = '<ul>';
        if(!isset($business_appraisal['application_id']))
        {
            $error .= ApiParser::parseErrors("Application ID must be required.");
        } else {
            $model = AppraisalsBusiness::find()->where(['application_id' => $business_appraisal['application_id']])->one();
            $app_model = Applications::findOne(['id' => $business_appraisal['application_id'], 'deleted' => 0]);
            if (!isset($model)) {
                $model = new AppraisalsBusiness();
                $model->attributes = $business_appraisal;
                if (!isset($app_model)) {
                    $error .= ApiParser::parseErrors("Application ID is Invalid.");
                } else {

                    $model->business_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save()) {

                            $prev_action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'social_appraisal', 'status' => 1]);
                            $action_model = ApplicationActions::findOne(['parent_id' => $model->application_id, 'action' => 'business_appraisal']);
                            if (!isset($prev_action_model)) {
                                $transaction->rollBack();
                                Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                $error .= ApiParser::parseErrors("Social Appraisal must be done before performing Business Appraisal");
                            } else {
                                $action_model->status = 1;
                                $action_model->pre_action = $prev_action_model->id;
                                $action_model->expiry_date = strtotime('+10 days', strtotime(date('Y-m-d H:i:s')));
                                $action_model->save();

                                $action_model = new ApplicationActions();
                                $action_model->parent_id = $model->application_id;
                                $action_model->user_id = $model->created_by;
                                $action_model->action = "approved/rejected";
                                $action_model->save();
                            }

                            if (isset($business_appraisal['fixed_business_assets']) && isset($business_appraisal['fixed_business_assets_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "fixed_business_assets";
                                $ba_assets_model->assets_list = $business_appraisal['fixed_business_assets'];
                                $ba_assets_model->total_amount = $business_appraisal['fixed_business_assets_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['running_capital']) && isset($business_appraisal['running_capital_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "running_capital";
                                $ba_assets_model->assets_list = $business_appraisal['running_capital'];
                                $ba_assets_model->total_amount = $business_appraisal['running_capital_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['business_expenses']) && isset($business_appraisal['business_expenses_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "business_expenses";
                                $ba_assets_model->assets_list = $business_appraisal['business_expenses'];
                                $ba_assets_model->total_amount = $business_appraisal['business_expenses_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "new_required_assets";
                                $ba_assets_model->assets_list = $business_appraisal['new_required_assets'];
                                $ba_assets_model->total_amount = $business_appraisal['new_required_assets_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $success[] = ['id' => $model->id,'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s business appraisal has synced successfully."];
                            $success_response[] = $model->id;
                        } else {
                            $transaction->rollBack();
                            Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                            $error .= ApiParser::parseErrors($model->errors);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $error .= ApiParser::parseErrors($model->errors);
                    }
                }
            }
            else {
                $model->attributes = $business_appraisal;

                $app_model = Applications::findOne(['id' => $model->application_id, 'deleted' => 0]);
                if (isset($app_model)) {

                    $model->business_appraisal_address = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save()) {

                            if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                BaAssets::deleteAll(['ba_id' => $model->id]);
                            }
                            if (isset($business_appraisal['fixed_business_assets']) && isset($business_appraisal['fixed_business_assets_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "fixed_business_assets";
                                $ba_assets_model->assets_list = $business_appraisal['fixed_business_assets'];
                                $ba_assets_model->total_amount = $business_appraisal['fixed_business_assets_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['running_capital']) && isset($business_appraisal['running_capital_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "running_capital";
                                $ba_assets_model->assets_list = $business_appraisal['running_capital'];
                                $ba_assets_model->total_amount = $business_appraisal['running_capital_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['business_expenses']) && isset($business_appraisal['business_expenses_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "business_expenses";
                                $ba_assets_model->assets_list = $business_appraisal['business_expenses'];
                                $ba_assets_model->total_amount = $business_appraisal['business_expenses_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }

                            if (isset($business_appraisal['new_required_assets']) && isset($business_appraisal['new_required_assets_amount'])) {
                                $ba_assets_model = new BaAssets();
                                $ba_assets_model->type = "new_required_assets";
                                $ba_assets_model->assets_list = $business_appraisal['new_required_assets'];
                                $ba_assets_model->total_amount = $business_appraisal['new_required_assets_amount'];
                                $ba_assets_model->ba_id = $model->id;
                                $ba_assets_model->application_id = $model->application_id;
                                if (!$flag = $ba_assets_model->save()) {
                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE appraisals_business auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($ba_assets_model->errors);
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $success[] = ['id' => $model->id,'cnic' => $model->application->member->cnic, 'status' =>$model->application->member->full_name."'s business appraisal has synced successfully."];
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
            $errors[] = ['error' => $error,'cnic' => $app_model->member->cnic];
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
        if (($model = AppraisalsBusiness::findOne(['application_id' => $id,'deleted' => 0])) !== null) {
            $data = ApiParser::parseBusinessAppraisal($model);
            /*$data['ba_details'] = AppraisalsHelper::getDetails($model->id);
            $data['ba_details']['description_image'] = ApiParser::parseImage(AppraisalsHelper::getBusinessAppraisalImage($model->id), $model->id);*/
            $data['ba_assets'] = AppraisalsHelper::getAssets($model->id);
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
            $data = ApiParser::parseBusinessAppraisal($model);
            return $this->sendSuccessResponse($data);
        } else {
            return $this->sendFailedResponse(204,"Enable to delete record.");
        }
    }

    protected function findModel($id)
    {
        if (($model = AppraisalsBusiness::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}