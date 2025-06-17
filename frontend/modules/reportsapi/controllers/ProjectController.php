<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\ReportsHelper\RbacHelper;
use common\models\BranchProjectsMapping;
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

class ProjectController extends \yii\web\Controller
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
        $project_id     = !empty($input['project_id'])?($input['project_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

           // $conditions['report_date'] = strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month')))."," .strtotime(date('Y-m-d 24:00:00',strtotime('last day of previous month'))) ;
            $conditions['project_id']  = $project_id;
            $conditions['status']  = 1;

            $cond = '';
            $project_details = '';
            $heading_projects = '';
            $staff_region_id = 0;
            if($project_id!=0){
                $project = Projects::find()->where(['id' => $project_id])->one();
                $project_details = ApiParser::parseProjectDetail($project);
                $heading_projects = $project->name;
            }
            $sql = '';
            $report = ProgressReports::find()->where($conditions)->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->andWhere(['gender' => '0'])->one();
            
            if(empty($report->progressReportDetails)){
                $date = strtotime(date('Y-m-t 00:00:00',strtotime('-2 months')));
                //$report = ProgressReports::find()->where($conditions)->andWhere(['between','report_date',1588204800,1588291199])->one();
                $report = ProgressReports::find()->where($conditions)->andWhere(['between','report_date',$date,$date])->one();
            }
            if($report){

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
                                                        (select GROUP_CONCAT(branch_id) from progress_report_details prd where prd.progress_report_id = '".$report->id."' and prd.no_of_loans != 0) as ids,
                                                        (select count(branch_id) from progress_report_details prd where prd.progress_report_id = '".$report->id."' and prd.no_of_loans != 0) as branch_id
                                                      FROM progress_report_details where recovery_percentage != 0 and progress_report_id = '".$report->id."' ".$cond;
//(select count(branches.id) from branches inner join branch_projects_mapping on branch_projects_mapping.branch_id=branches.id where branches.status = 1 and branch_projects_mapping.project_id = '".$project_id."') as branch_id
                    $command = Yii::$app->db->createCommand($sql);
                    $report_data = $command->queryOne();
                }
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
                    $designations = array('RM','AM','BM');
                }*/
                $designations = array('PM','DM');
                $users = Users::find()
                    ->joinWith(['designation'])
                    ->join('inner join','user_projects_mapping','user_projects_mapping.user_id=users.id')
                    ->where(['in', 'designations.code', $designations])
                    ->andWhere(['user_projects_mapping.project_id' => $project_id])
                    //->andWhere($staff_condition)
                    ->orderBy(['designations.sorting'=>SORT_ASC])
                    ->limit(5)
                    ->all();

                $staff = ApiParser::parseStaff($users);
                $branches_arr=[];
                $branches_arr = explode (",",$report_data['ids']);
                //$branches = Branches::find()->join('inner join','branch_projects_mapping','branch_projects_mapping.branch_id=branches.id')->where(['branches.status' => 1,'branch_projects_mapping.project_id'=>$project_id])->orderBy(['branches.name'=>SORT_ASC])->all();
                $branches = Branches::find()->where(['in','id',$branches_arr])->orderBy(['branches.name'=>SORT_ASC])->all();

                /*print_r($branches);
                die();*/

                $branches_response = [];

                foreach($branches as $branch){
                    $branches_response[] = ApiParser::parseBranch($branch);
                }

                $project_detail = [
                    'info'=>$project_details,
                    'progress' => [
                        'headings' => [
                            ['key'=>'Date','value'=>'Progress Report as on '.date('d M Y', ($report->report_date))],
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
                $response['data']['message']    = "Get the project detail";
                $response['data']['detail']    = $project_detail;
            }else{
                $response['meta']['success']    = true;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "Data Not Found";
                //$response['data']['detail']    = $project_detail;
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionGetBranchProjects(){
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
        $branch_id     = !empty($input['branch_id'])?($input['branch_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            if($branch_id!=0){
                $project = BranchProjectsMapping::find()->select('branch_projects_mapping.project_id,name')->innerJoin('projects','projects.id=branch_projects_mapping.project_id')->where(['branch_id'=>$branch_id])->asArray()->all();
                /*print_r($project);
                die();*/

                if(!empty($project)){
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 200;
                    $response['data']['message']    = "Get the project detail";
                    $response['data']['detail']    = $project;
                }else{
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 404;
                    $response['data']['message']    = "Data Not Found";
                }
            }else{
                $response['meta']['success']    = true;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "Data Not Found";
                //$response['data']['detail']    = $project_detail;
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }


    public function actionGetDistrictProjects(){
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

            if($district_id!=0){
                $project = BranchProjectsMapping::find()
                    ->select('distinct(branch_projects_mapping.project_id),projects.name')
                    ->innerJoin('projects','projects.id=branch_projects_mapping.project_id')
                    ->innerJoin('branches','branches.id=branch_projects_mapping.branch_id')
                    ->where(['branches.district_id'=>$district_id])
                    ->asArray()
                    ->all();
                /*print_r($project);
                die();*/
                if(!empty($project)){
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 200;
                    $response['data']['message']    = "Get the project detail";
                    $response['data']['detail']    = $project;
                }else{
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 404;
                    $response['data']['message']    = "Data Not Found";
                }


            }else{
                $response['meta']['success']    = true;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "Data Not Found";
                //$response['data']['detail']    = $project_detail;
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }
    public function actionGetProjects(){
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
        $district_id     = !empty($input['id'])?($input['id']):0;
        $attribute     = !empty($input['attribute'])?($input['attribute']):'province';

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            if($district_id!=0){
                if($attribute=='district') {
                    $project = BranchProjectsMapping::find()
                        ->select('distinct(branch_projects_mapping.project_id),projects.name as full_name,projects.sector,projects.short_name as name')
                        ->innerJoin('projects', 'projects.id=branch_projects_mapping.project_id')
                        ->innerJoin('branches', 'branches.id=branch_projects_mapping.branch_id')
                        ->where(['branches.district_id' => $district_id])
                        ->asArray()
                        ->all();
                }else{
                    $project = BranchProjectsMapping::find()
                        ->select('distinct(branch_projects_mapping.project_id),projects.name as full_name,projects.sector,projects.short_name as name')
                        ->innerJoin('projects', 'projects.id=branch_projects_mapping.project_id')
                        ->innerJoin('branches', 'branches.id=branch_projects_mapping.branch_id')
                        ->where(['branches.province_id' => $district_id])
                        ->asArray()
                        ->all();
                }
                /*print_r($project);
                die();*/
                if(!empty($project)){
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 200;
                    $response['data']['message']    = "Get the project detail";
                    $response['data']['detail']    = $project;
                }else{
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 404;
                    $response['data']['message']    = "Data Not Found";
                }


            }else{
                $response['meta']['success']    = true;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "Data Not Found";
                //$response['data']['detail']    = $project_detail;
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }
}

