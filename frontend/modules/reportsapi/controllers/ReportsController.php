<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Parsers\ReportsParser\ApiParser;
use common\models\Countries;
use common\models\Districts;
use common\models\Provinces;
use common\models\CreditDivisions;
use common\models\Regions;
use common\models\Areas;
use common\models\Branches;
use common\models\Projects;
use common\models\Cities;
use yii\rest\ActiveController;
use common\models\ProgressReports;
use common\models\ProgressReportDetails;
// namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\Users;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;

/**
 * Site controller
 */
class ReportsController extends ActiveController
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

    public function actionProgress()
    {
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->one();
        //print_r($input);
        //die();
        $date           = isset($input['date'])?($input['date']):0;
        $project_id     = isset($input['project_id'])?($input['project_id']):0;
        $credit_id      = isset($input['credit_id'])?($input['credit_id']):0;
        $region_id      = isset($input['region_id'])?($input['region_id']):0;
        $area_id        = isset($input['area_id'])?($input['area_id']):0;
        $branch_id      = isset($input['branch_id'])?($input['branch_id']):0;
        $country_id     = isset($input['country_id'])?($input['country_id']):0;
        $province_id    = isset($input['province_id'])?($input['province_id']):0;
        $district_id    = isset($input['district_id'])?($input['district_id']):0;

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
//            $user->last_login_token = "logout";
//            $user->save();

            //$conditions['report_date'] =    date('Y-m-d');
            $conditions['project_id']  =    $project_id;
            $conditions['status']  =    1;
            $conditions['deleted']  =    0;
            $conditions['is_verified']  =    1;

            $cond = '';
            $cond1 = '';
            //$heading_projects = 'All Regions and Projects';
            $heading_projects = '';
            $heading_part1 = '';
            $heading_part2 = '';
            $heading_part3 = '';

            if($country_id!=0){
                $cond .= ' && country_id = '.$country_id;
                $countries = Countries::find()->where(['id' => $country_id])->one();
                $heading_part1 = $countries->name;
                if($province_id!=0){
                    $cond .= ' && progress_report_details.province_id = '.$province_id;
                    $cond1 .= ' && prd.province_id = '.$province_id;
                    $provinces = Provinces::find()->where(['id' => $province_id])->one();
                    $heading_part1 = $provinces->name;
                    if($district_id!=0){
                        $cond .= ' && district_id = '.$district_id;
                        $cond1 .= ' && prd.district_id = '.$district_id;
                        $districts = Districts::find()->where(['id' => $district_id])->one();
                        $heading_part1 = $districts->name;
                    }
                }else{
                    if($district_id!=0){
                        $cond .= ' && district_id = '.$district_id;
                        $cond1 .= ' && prd.district_id = '.$district_id;
                        $districts = Districts::find()->where(['id' => $district_id])->one();
                        $heading_part1 = $districts->name;
                    }
                }
            }else{
                if($province_id!=0){
                    $cond .= ' && progress_report_details.province_id = '.$province_id;
                    $cond1 .= ' && prd.province_id = '.$province_id;
                    $provinces = Provinces::find()->where(['id' => $province_id])->one();
                    $heading_part1 = $provinces->name;
                    if($district_id!=0){
                        $cond .= ' && district_id = '.$district_id;
                        $cond1 .= ' && prd.district_id = '.$district_id;
                        $districts = Districts::find()->where(['id' => $district_id])->one();
                        $heading_part1 = $districts->name;
                    }
                }else{
                    if($district_id!=0){
                        $cond .= ' && district_id = '.$district_id;
                        $cond1 .= ' && prd.district_id = '.$district_id;
                        $districts = Districts::find()->where(['id' => $district_id])->one();
                        $heading_part1 = $districts->name;
                    }
                }
            }




            if($region_id!=0){
                $cond .= ' && region_id = '.$region_id;
                $cond1 .= ' && prd.region_id = '.$region_id;
                $region = Regions::find()->where(['id' => $region_id])->one();
                $heading_part2 = $region->name;
                if($area_id!=0){
                    $cond .= ' && area_id = '.$area_id;
                    $cond1 .= ' && prd.area_id = '.$area_id;
                    $areas = Areas::find()->where(['id' => $area_id])->one();
                    $heading_part2 = $areas->name;
                    if($branch_id!=0){
                        $cond .= ' && branch_id = '.$branch_id;
                        $cond1 .= ' && prd.branch_id = '.$branch_id;
                        $branches = Branches::find()->where(['id' => $branch_id])->one();
                        $heading_part2 = $branches->name;
                    }
                }else{
                    if($branch_id!=0){
                        $cond .= ' && branch_id = '.$branch_id;
                        $cond1 .= ' && prd.branch_id = '.$branch_id;
                        $branches = Branches::find()->where(['id' => $branch_id])->one();
                        $heading_part2 = $branches->name;
                    }
                }
            }else{
                if($area_id!=0){
                    $cond .= ' && area_id = '.$area_id;
                    $cond1 .= ' && prd.area_id = '.$area_id;
                    $areas = Areas::find()->where(['id' => $area_id])->one();
                    $heading_part2 = $areas->name;
                    if($branch_id!=0){
                        $cond .= ' && branch_id = '.$branch_id;
                        $cond1 .= ' && prd.branch_id = '.$branch_id;
                        $branches = Branches::find()->where(['id' => $branch_id])->one();
                        $heading_part2 = $branches->name;
                    }
                }else{
                    if($branch_id!=0){
                        $cond .= ' && branch_id = '.$branch_id;
                        $cond1 .= ' && prd.branch_id = '.$branch_id;
                        $branches = Branches::find()->where(['id' => $branch_id])->one();
                        $heading_part2 = $branches->name;
                    }
                }
            }


            if($project_id!=0){
                $project = Projects::find()->where(['id' => $project_id])->one();
                $heading_part3 = $project->name;
            }
            if($credit_id!=0){
                $cond .= ' && division_id = '.$credit_id;
                $cond1 .= ' && prd.division_id = '.$credit_id;
                $c_divisions = CreditDivisions::find()->where(['id' => $credit_id])->one();
                $heading_projects = $c_divisions->name;
            }

            if($heading_part1){
                $heading_projects = $heading_part1;
            }
            if($heading_part2){
                $heading_projects .= '/'.$heading_part2;
            }
            if($heading_part3){
                $heading_projects .= '/'.$heading_part3;
            }
            if(!$heading_projects){
                $heading_projects = 'All Regions and Projects';
            }

            $report = ProgressReports::find()->where($conditions)->orderBy(['report_date'=>SORT_DESC])->one();
            /*print_r($report);
            die();*/
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
                                                        AVG(COALESCE (recovery_percentage,0)) as recovery_percentage,
                                                        sum(cih) as cih,
                                                        sum(mdp) as mdp,
                                                        (select count(distinct branches.id) from progress_report_details prd inner join branches on branches.id=prd.branch_id where branches.status=1  and prd.progress_report_id = '".$report->id."' ".$cond1.") as branch_id,
                                                        (select count(distinct(prd.district_id)) from progress_report_details prd  where prd.progress_report_id = '".$report->id."' ".$cond1.") as districts,
                                                        count(DISTINCT area_id) as area_id,
                                                        count(DISTINCT region_id) as region_id,
                                                        (select AVG(COALESCE (pd.recovery_percentage,0)) from progress_report_details pd inner join progress_reports p on pd.progress_report_id = p.id where p.report_date BETWEEN '".strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month')))."' AND '".strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))."' and p.project_id = '0' and p.deleted = 0 and p.is_verified = 1 $cond) as last_month_recovery_percentage
                                                      FROM progress_report_details where progress_report_id = '".$report->id."' ".$cond;
               // count(branch_id) as branch_id,
               //die($sql);
                $command = Yii::$app->db->createCommand($sql);
                $report_data = $command->queryOne();
            }
            $progress = [
                'headings' => [
                    ['key'=>'Date','value'=>'Progress Report as on '.date('d M Y', (isset($report->report_date)? $report->report_date : date('Y-m-d')))],
                    ['key'=>'Projects','value'=>trim($heading_projects,'/')],
                ],
                'date' => date('d M Y', (isset($report->report_date)? $report->report_date : date('Y-m-d'))),
                'details' => ApiParser::parseProgressWithLastMonthRecovery($report_data),
            ];
            $response['meta']['success'] = true;
//            $response['meta']['filters'] = $sql;
            $response['meta']['code'] = 200;
            $response['data']['progress'] = $progress;
            $response['data']['message'] = "Get Progress Report";
        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }


        return $response;
    }

    public function actionProgressdetail(){
        $input_data = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        if($access_token=='abc123'){
            $progress_data      = [];
            $progress_reports   = ProgressReports::find()->where(['project_id'=>'0'])->all();

            if($progress_reports){
                foreach ($progress_reports as $progress_report){
                    $data_one = [];
                    foreach ($progress_report->details as $detail){
                        $data_one[] = [
                            'credit_division'=>'',
                            'region'=>!empty($detail->region->name)?($detail->region->name):'',
                            'area'=>!empty($detail->area->name)?($detail->area->name):'',
                            'branch'=>!empty($detail->branch->name)?($detail->branch->name):'',
                            'country'=>$detail->country,
                            'province'=>$detail->province,
                            'division'=>$detail->division,
                            'district'=>$detail->district,
                            'city'=>$detail->city,
                            'no_of_loans'=>$detail->no_of_loans,
                            'family_loans'=>$detail->family_loans,
                            'female_loans'=>$detail->female_loans,
                            'active_loans'=>$detail->active_loans,
                            'cum_disb'=>$detail->cum_disb,
                            'cum_due'=>$detail->cum_due,
                            'cum_recv'=>$detail->cum_recv,
                            'overdue_borrowers'=>$detail->overdue_borrowers,
                            'overdue_amount'=>$detail->overdue_amount,
                            'overdue_percentage'=>$detail->overdue_percentage,
                            'par_amount'=>$detail->par_amount,
                            'par_percentage'=>$detail->par_percentage,
                            'not_yet_due'=>$detail->not_yet_due,
                            'olp_amount'=>$detail->olp_amount,
                            'recovery_percentage'=>$detail->recovery_percentage,
                            'cih'=>$detail->overdue_amount,
                            'mdp'=>$detail->mdp,
                            'funding_line'=>'Akhuwat',
                            'loan_product'=>'',
                        ];
                    }

                    $progress_data[] = [
                        'year'=>date('Y',($progress_report->report_date)),
                        'month'=>date('M',($progress_report->report_date)),
                        'data' =>$data_one,
                    ];
                }
            }

            return array('code'=>'200','message'=>'success','data'=>$progress_data);
        }else{
            return array('code'=>'600','message'=>'Invalid Access Token');
        }
    }


    public function actionCustom(){
        $bodyParams = Yii::$app->request->getBodyParams();

        $user = User::findOne(1);

        return array('code'=>'200','message'=>'success');
    }


}
