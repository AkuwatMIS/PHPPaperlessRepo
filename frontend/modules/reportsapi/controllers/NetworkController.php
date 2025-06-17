<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\ReportsHelper\RbacHelper;
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

class NetworkController extends \yii\web\Controller
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
        $region_id      = !empty($input['region_id'])?($input['region_id']):0;
        $area_id        = !empty($input['area_id'])?($input['area_id']):0;
        $branch_id      = !empty($input['branch_id'])?($input['branch_id']):0;

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            //$conditions['report_date'] = ('last day of previous month');
            $conditions['project_id']  = $project_id;
            $report_data = [];
            $cond = '';
            $branch = '';
            $heading_projects = '';
            $staff_region_id = 0;

            if($region_id!=0){
                $cond .= ' && region_id = '.$region_id;
                $region = Regions::find()->where(['id' => $region_id])->one();
                $branch = ApiParser::parseNetworkRegion($region);
                $heading_projects = isset($region->name) ? $region->name : '';
                $staff_region_id = isset($region->id) ? $region->id : 0;
            }
            if($area_id!=0){
                $cond .= ' && area_id = '.$area_id;
                $area = Areas::find()->where(['id' => $area_id])->one();
                $branch = ApiParser::parseNetworkArea($area);
                $heading_projects = isset($area->name) ? $area->name : '';
                $staff_region_id = isset($area->region_id) ? $area->region_id : 0;
            }
            if($branch_id!=0){
                $cond .= ' && branch_id = '.$branch_id;
                $branchs = Branches::find()->where(['id' => $branch_id])->one();
                $branch = ApiParser::parseNetworkBranch($branchs);
                $heading_projects = isset($branchs->name) ? $branchs->name : '';
                $staff_region_id = isset($branchs->region_id) ? $branchs->region_id : 0;
                $area_id = $branchs->area_id;
            }
            if($project_id!=0){
                $project = Projects::find()->where(['id' => $project_id])->one();
                $heading_projects .= '/'.$project->name;
            }
            $report = ProgressReports::find()->where($conditions)->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->andWhere(['status'=>1])->one();
            $sql = '';


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
                            FROM progress_report_details where /*recovery_percentage != 0 and*/ progress_report_id = '".$report->id."' ".$cond;
                $command = Yii::$app->db->createCommand($sql);
                $report_data = $command->queryOne();
            }

            $progress = ApiParser::parseProgress($report_data);

            //$designations = array('CEO','COO','CFO','CCO','PM','RM','AM');
            $staff_condition = [];
            if($region_id !=0){
                $designations = array('RM');
                $staff_condition['region.obj_id'] = $staff_region_id;
            }elseif ($area_id!=0){
                $designations = array('AM');
                $staff_condition['area.obj_id'] = $area_id;
            }elseif ($area_id!=0){
                $designations = array('BM');
                $staff_condition['area.obj_id'] = $area_id;
                //$staff_condition['users.region_id'] = $staff_region_id;
            }else{
                $designations = array('RM','AM','BM');
            }

            $users = Users::find()
                ->joinWith(['designation'])
                ->joinWith(['regionNetwork as region'])
                ->joinWith(['areaNetwork as area'])
                ->joinWith(['branchNetwork as branch'])
                ->where(['in', 'designations.code', $designations])
                ->andWhere(['=','users.status', 1])
                //->andWhere(['users.region_id' => $staff_region_id])
                ->andWhere($staff_condition)
                ->orderBy(['designations.sorting'=>SORT_ASC])
                ->limit(5)
                ->all();

            $staff = ApiParser::parseStaff($users);
            $branch_detail = [
                'info'=>$branch,
                'progress' => [
                    'headings' => [
                        ['key'=>'Date','value'=>'Progress Report As on '.date('d M Y', ($report->report_date))],
                        ['key'=>'Projects','value'=>$heading_projects],
                    ],
                    'date' => date('d M Y', (isset($report->report_date)? $report->report_date : date('Y-m-d'))),
                    'details' => $progress,
                ],
                'staf' => $staff,
            ];

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get the network detail";
            $response['data']['detail']    = $branch_detail;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionStaff()
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

            $designations = array('CEO','COO','CFO','CCO','PM','RM','AM','DM','Founder');
            $users = Users::find()
                ->joinWith(['designation'])
                ->where(['in', 'designations.code', $designations])
                ->andWhere(['=','users.status', 1])
                ->orderBy(['designations.sorting'=>SORT_ASC])
                //->limit(60)
                ->all();

            $staff = ApiParser::parseStaff($users);
            $staff_all = ApiParser::parseNetworkStaff($users);

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Staff List";
            $response['data']['detail']     = $staff;
            $response['data']['detail_all']     = $staff_all;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }


}

