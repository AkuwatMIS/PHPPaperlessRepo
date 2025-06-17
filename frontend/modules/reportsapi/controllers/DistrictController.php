<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\StructureHelper;
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

class DistrictController extends \yii\web\Controller
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

    public function actionDetail()
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
        $district_id     = !empty($input['district_id'])?($input['district_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
           // $conditions['report_date'] = strtotime('last day of previous month');
            $conditions['project_id']  =    0;
            $conditions['status']  =    1;
            $cond = '';
            $district_details = '';
            $heading_projects = '';
            $staff_region_id = 0;
            if($district_id!=0){
                $cond = '&& district_id = '."$district_id".'';

                /*$districts = StructureHelper::getStructure('districts');
                $district = array_column($districts, 'id');*/
                $district = Districts::find()->where(['id' => $district_id])->one();

                /*print_r($district);
                die();*/
                $district_details = ApiParser::parseDistrictDetail($district);
                $heading_projects = $district->name;
            }

            $report = ProgressReports::find()->where($conditions)->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->one();
            $sql = '';
            $report_data = [];

            if($report){
                $sql = "SELECT
                                sum(members_count) as no_of_beneficiaries,
                                sum(no_of_loans) as no_of_loans,
                                sum(family_loans) as family_loans,
                                sum(female_loans) as female_loans,
                                sum(active_loans) as active_loans,
                                sum(cum_disb) as cum_disb,
                                sum(cum_due) as cum_due,
                                sum(cum_recv) as cum_recv,
                                sum(overdue_borrowers) as overdue_borrowers,
                                sum(overdue_amount) as overdue_amount,
                                AVG(overdue_percentage) as overdue_percentage,
                                sum(par_amount) as par_amount,
                                AVG(par_percentage) as par_percentage,
                                sum(not_yet_due) as not_yet_due,
                                sum(olp_amount) as olp_amount,
                                AVG(recovery_percentage) as recovery_percentage,
                                sum(cih) as cih,
                                sum(mdp) as mdp,
                                count(branch_id) as branch_id
                                FROM progress_report_details where no_of_loans != 0 and progress_report_id = '".$report->id."' ".$cond;
                $command = Yii::$app->db->createCommand($sql);
                $report_data = $command->queryOne();
            }

            //die($sql);
            $progress = ApiParser::parseProgress($report_data);

            //$designations = array('CEO','COO','CFO','CCO','PM','RM','AM');
            $staff_condition = [];
            /*if($region_id !=0){
                $designations = array('RM');
                $staff_condition['users.region_id'] = $staff_region_id;
            }elseif ($area_id!=0){
                $designations = array('AM');
                $staff_condition['users.area_id'] = $area_id;
            }elseif ($branch_id!=0){
                $designations = array('BM');
                $staff_condition['users.branch_id'] = $branch_id;
                //$staff_condition['users.region_id'] = $staff_region_id;
            }else{

            }*/
            $designations = array('RM','AM','BM');
            $users = Users::find()
                ->joinWith(['designation'])
                ->where(['in', 'designations.code', $designations])
                //->andWhere(['users.region_id' => $staff_region_id])
                ->andWhere($staff_condition)
                ->orderBy(['designations.sorting'=>SORT_ASC])
                ->limit(5)
                ->all();

            $staff = ApiParser::parseStaff($users);
            $branches = Branches::find()->where(['status' => 1, 'district_id'=>$district_id])->orderBy(['name'=>SORT_ASC])->all();
            $branches_response = [];

            foreach($branches as $branch){
                $branches_response[] = ApiParser::parseBranch($branch);
            }

            $district_detail = [
                'info'=>$district_details,
                'progress' => [
                    'headings' => [
                        ['key'=>'Date','value'=>'Progress as on '.date('d M Y', ($report->report_date))],
                        ['key'=>'Projects','value'=>$heading_projects],
                    ],
                    'date' => date('d M Y', (isset($report->report_date)? $report->report_date : date('Y-m-d'))),
                    'details' => $progress,
                ],
                'branches_list' => $branches_response,
                'staf' => $staff,
            ];

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get the district detail";
            $response['data']['detail']    = $district_detail;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

