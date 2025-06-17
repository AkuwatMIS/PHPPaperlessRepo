<?php

namespace frontend\modules\reportsapi\controllers;


use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\ReportsHelper\NumberHelper;
use common\components\Helpers\ReportsHelper\RbacHelper;
use common\components\Helpers\StructureHelper;
use common\models\AppImages;
use common\models\BranchProjectsMapping;
use common\models\Countries;
use common\models\Divisions;
use common\models\ProgressReportDetails;
use common\models\ProgressReports;
use common\models\Projects;
use common\models\Provinces;
use common\models\Districts;
use yii\helpers\Url;
use Yii;
use common\models\Users;
use common\models\Regions;
use common\models\Areas;
use common\models\Branches;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;


class DataController extends \yii\web\Controller
{
    public $modelClass = 'common\models\Users';

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

    // User logout api=========================================================================
    public function actionStructure()
    {
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

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            //assign roles
            $data_array = RbacHelper::apiRbacNetwork($user);

            $region_array = $data_array['region_array'];
            $area_array = $data_array['area_array'];
            $branch_array = $data_array['branch_array'];

            //
            $regions = Regions::find()->where(['status' => 1])->andFilterWhere(['in', 'id' , $region_array])->orderBy(['name'=>SORT_ASC])->all();
            $regions_response = [];

            foreach($regions as $region){
                $regions_response[] = ApiParser::parseRegion($region);
            }

            $areas = Areas::find()->where(['status' => 1])->andFilterWhere(['in', 'id' , $area_array])->orderBy(['name'=>SORT_ASC])->all();
            $area_response = [];

            foreach($areas as $area){
                $area_response[] = ApiParser::parseArea($area);
            }

            $branches = Branches::find()->where(['deleted' => 0])->andFilterWhere(['in', 'id' , $branch_array])->andFilterWhere(['status'=>1])->orderBy(['name'=>SORT_ASC])->all();
            $branches_response = [];

            foreach($branches as $branch){
                $branches_response[] = ApiParser::parseBranch($branch);
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Network data downloaded";
//            $response['data']['countries']  = $countries_response;
//            $response['data']['provinces']  = $provinces_response;
//            $response['data']['districts']  = $districts_response;
            $response['data']['regions']    = $regions_response;
            $response['data']['areas']      = $area_response;
            $response['data']['branches']   = $branches_response;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionDistricts()
    {
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

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $countries_response = [];
            $countries_response = StructureHelper::getStructure('countries');
            foreach($countries_response as $key=>$cresponse){
                $branches_count=Branches::find()->where(['country_id'=>$cresponse['id']])->count();
                $countries_response[$key]['branches_count']=$branches_count;
            }
            /*$countries          = Countries::find()->orderBy(['name'=>SORT_ASC])->all();
            $countries_response = [];

            foreach($countries as $country){
                $countries_response[] = $country->attributes;
            }*/
            $provinces_response = [];
            $provinces_response = StructureHelper::getStructure('provinces');
            foreach($provinces_response as $key=>$presponse){
                $districts_count=0;
                /*$divisions=Divisions::find()->where(['province_id'=>$response['id']])->all();
                foreach ($divisions as $d){
                    $districts_c=Districts::find()->where(['division_id'=>$d->id])->count();
                    $districts_count=$districts_count+$districts_c;
                }*/
                $progress=ProgressReports::find()->where(['project_id'=>0])->andWhere(['status'=>1,'is_verified'=>1])->orderBy('id desc')->one();
                $detail_p=ProgressReportDetails::find()->select('distinct(district_id)')->where(['progress_report_id'=>$progress->id,'province_id'=>$presponse['id']])->all();
                $districts_count=count($detail_p);
                $branches_count=Branches::find()->where(['province_id'=>$presponse['id'],'status' => 1])->count();
                $provinces_response[$key]['branches_count']=$branches_count;
                $provinces_response[$key]['districts_count']=$districts_count;
            }

            /*$provinces          = Provinces::find()->orderBy(['name'=>SORT_ASC])->all();
            $provinces_response = [];

            foreach($provinces as $province){
                $provinces_response[] = $province->attributes;
            }*/


            $districts_response = [];
            $districts = StructureHelper::getStructure('districts');
            /*$districts  = Districts::find()->orderBy(['name'=>SORT_ASC])->all();*/
            foreach($districts as $district){
                $branches_count=Branches::find()->where(['district_id'=>$district['id']])->count();
                $district = $district;
                $districts_response[] = [
                    'id' => $district['id'],
                    'name' => $district['name'],
                    'code' => $district['code'],
                    'provice_id' => $district['province_id'],
                    'provice_name' => $district['province_name'],
                    'branches_count' => $branches_count
                ];
                //$district->attributes;
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Districts data downloaded";
            $response['data']['countries']  = $countries_response;
            $response['data']['provinces']  = array_values($provinces_response);
            $response['data']['districts']  = $districts_response;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionProjects()
    {
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

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $projects_array = RbacHelper::apiRbacNetwork($user);
            /*print_r($projects_array['project_array']);
            die();*/
            $projects          = Projects::find()->where(['status'=>1])->filterWhere(['in','id',$projects_array['project_array']])->orderBy(['name'=>SORT_ASC])->all();
            $projects_response = [];

            foreach($projects as $project){
                $report = ProgressReports::find()->where(['project_id'=>$project->id])->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->andWhere(['status'=>1])->one();
                //$report = ProgressReports::find()->where(['project_id'=>$project->id])->andWhere(['between','report_date',1588204800,1588291199])->one();
                if(empty($report)){
                    $report = ProgressReports::find()->where(['project_id'=>$project->id])->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime("-1 month",strtotime('last day of previous month')))),strtotime(date('Y-m-d 23:59:59',strtotime("-1 month",strtotime('last day of previous month'))))])->andWhere(['status'=>1])->one();
                    if(empty($report)){
                        $report = ProgressReports::find()->where(['project_id'=>$project->id])->andWhere(['between','report_date',1588204800,1588291199])->andWhere(['status'=>1])->one();
                    }
                }
                if($report){
                    $sql_p = "SELECT
                                                        sum(olp_amount) as olp_amount,
                                                        sum(no_of_loans) as no_of_loans,
                                                        AVG(recovery_percentage) as recovery_percentage,
                                                        count(branch_id) as total_branches
                                                    FROM progress_report_details where recovery_percentage != 0 and no_of_loans!=0 and progress_report_id = '".$report->id."' ";

                    $command_p = Yii::$app->db->createCommand($sql_p);
                    $report_data_p = $command_p->queryOne();
                }
                if($report_data_p['total_branches']>0){
                    $provinces = [];
                    $provinces_data = Provinces::find()->select('provinces.name')->join('inner join','branches','branches.province_id=provinces.id')->join('inner join','branch_projects_mapping','branch_projects_mapping.branch_id=branches.id')->where(['branch_projects_mapping.project_id' => $project->id])->andWhere(['!=','branches.id',814])->distinct()->all();
                    foreach ($provinces_data as $province)
                    {
                        array_push($provinces,$province->name);
                    }
                    $p['id'] = $project->id;
                    $p['name'] = $project->name;
                    $p['code'] = $project->code;
                    $p['donor'] = $project->donor;
                    $p['funding_line'] = $project->funding_line;
                    $p['started_date'] = date('d M Y',$project->started_date);
                    $p['logo'] = Url::to('@web/uploads/projects/'.'akhuwat.png',true);
                    $p['total_fund'] = NumberHelper::getFormattedNumberAmount($project->total_fund);
                    $p['description'] = $project->description;
                    $p['status'] = $project->status;
                    $p['sector'] = $project->sector;
                    $p['total_branches'] = isset($report_data_p['total_branches'])?$report_data_p['total_branches']:0;
                    $p['provinces'] = $provinces ;
                    $projects_response[] = $p;
                }
                //$p['total_branches'] = BranchProjectsMapping::find()->where(['project_id' => $project->id])->count('branch_id');
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Projects List";
            $response['data']['projects']   = $projects_response;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionProductsactivities()
    {
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

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $key = 'products_list';
            $products = CacheHelper::getReports($key);
            if(empty($products)) {
                $sql = 'select id, name, value as max, updated_at,ROUND(value*100/(select sum(value) from cache_reports where type="product"),2) as percentage from cache_reports where type="product"';
                $command = Yii::$app->db->createCommand($sql);
                $products_list = $command->queryAll();
                $products = [];
                foreach ($products_list as $p)
                {
                    $data = $p;
                    $data['logo'] = ImageHelper::getAttachmentApiPath(). '?type=products&file_name=' . $p['name'] .'.png&download=true';
                    $products[] = $data;
                }
                CacheHelper::setReports($key,$products);
            }

            $activity_key = 'activities_list';
            $activity = CacheHelper::getReports($activity_key);
            if(empty($activity)) {
                //$sql = 'select a.id, a.name, a.product_id, count(b.activity_id) as status from borrowers b inner join loans l on l.borrower_id = b.id inner join activities a on b.activity_id = a.id where l.dsb_status in (\'Collected\',\'Loan Completed\') group by b.activity_id';
                $sql = 'select id, name, value as status, updated_at,ROUND(value*100/(select sum(value) from cache_reports where type="activity"),2) as percentage from cache_reports where type="activity"';
                $command = Yii::$app->db->createCommand($sql);
                $activity_list = $command->queryAll();
                $activity = [];
                foreach ($activity_list as $a)
                {
                    $data = $a;
                    $act_name=str_replace("&","",$a['name']);
                    $data['logo'] = ImageHelper::getAttachmentApiPath(). '?type=activities&file_name=' . $act_name .'.png&download=true';
                    $activity[] = $data;
                }
                CacheHelper::setReports($activity_key,$activity);
            }
            /*$current_date = date('Y-m-d H:i:s');
            $last_updated = $products[0]['updated_at'];

            $datetime1 = new \DateTime($current_date);
            $datetime2 = new \DateTime($last_updated);
            $interval = $datetime1->diff($datetime2);

            if($interval->format('%D') >= 1) {
                $sql_query = 'select p.id, p.name, p.code, p.inst_type, p.min, count(a.activity_id) as max, p.status from applications a inner join loans l on l.application_id = a.id inner join products p on a.product_id = p.id where l.status != \'not collected\' AND l.deleted = 0 group by a.product_id';
                $command = Yii::$app->db->createCommand($sql_query);
                $new_products = $command->queryAll();
                $date = date('Y-m-d H:i:s');
                foreach ($new_products as $p) {
                    $insert = 'update cache_reports set value = "' . $p['max'] . '" , updated_at = "' . $date . '" where name = "' . $p['name'] . '" and type = "product"';
                    $command = Yii::$app->db->createCommand($insert);
                    $command->execute();
                }
                $products = $new_products;
                CacheHelper::setReports($key,$products);

                $sql_query = 'select a.id, a.name, a.product_id, count(b.activity_id) as status from applications b inner join loans l on l.application_id = b.id inner join activities a on b.activity_id = a.id where a.product_id = 1 and l.status != \'not collected\' AND l.deleted = 0 group by b.activity_id';
                $command = Yii::$app->db->createCommand($sql_query);
                $new_activity = $command->queryAll();
                $date = date('Y-m-d H:i:s');
                foreach ($new_activity as $p){
                    $insert = 'update cache_reports set value = "'.$p['status'].'" , updated_at = "'.$date.'" where name = "'.$p['name'].'" and type = "activity"';
                    $command = Yii::$app->db->createCommand($insert);
                    $command->execute();
                }
                $activity = $new_activity;
                CacheHelper::setReports($activity_key,$activity);
            }*/

            $heading = [
                'sub_heading' => [
                    'last_updated'=> 'as on '.date('d-M-Y')
                ]
            ];

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Products List";
            $response['data']['heading']    = $heading;
            $response['data']['products']   = $products;
            $response['data']['activities']   = $activity;
        } else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionProducts()
    {
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

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $key = 'products_list';
            $products = CacheHelper::getReports($key);
            if(empty($products)) {

                /*$sql_query = 'select p.id, p.name, p.code, p.inst_type, p.min, count(a.activity_id) as max, p.status from applications a inner join loans l on l.application_id = a.id inner join products p on a.product_id = p.id where l.status in (\'collected\',\'loan completed\') group by a.product_id';
                $command = Yii::$app->db->createCommand($sql_query);
                $products = $command->queryAll();*/
                $sql = 'select id, name, value as max, updated_at,ROUND(value*100/(select sum(value) from cache_reports where type="product"),2) as percentage from cache_reports where type="product"';
                $command = Yii::$app->db->createCommand($sql);
                $products = $command->queryAll();
                CacheHelper::setReports($key,$products);
            }

            $current_date = date('Y-m-d H:i:s');
            $last_updated = $products[0]['updated_at'];

            $datetime1 = new \DateTime($current_date);
            $datetime2 = new \DateTime($last_updated);
            $interval = $datetime1->diff($datetime2);

            if($interval->format('%D') >= 1) {
                $sql_query = 'select p.id, p.name, p.code, p.inst_type, p.min, count(a.activity_id) as max, p.status from applications a inner join loans l on l.application_id = a.id inner join products p on a.product_id = p.id where l.status != \'not collected\' AND l.deleted = 0 group by a.product_id';
                $command = Yii::$app->db->createCommand($sql_query);
                $new_products = $command->queryAll();
                $date = date('Y-m-d H:i:s');
                foreach ($new_products as $p) {
                    $insert = 'update cache_reports set value = "' . $p['max'] . '" , updated_at = "' . $date . '" where name = "' . $p['name'] . '" and type = "product"';
                    $command = Yii::$app->db->createCommand($insert);
                    $command->execute();
                }
                $products = $new_products;
                CacheHelper::setReports($key,$products);
            }


            $heading = [
                'sub_heading' => [
                    'last_updated'=> 'as on '.date('d-M-Y')
                ]
            ];

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Products List";
            $response['data']['heading']    = $heading;
            $response['data']['products']   = $products;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionActivities()
    {
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $save = $headers->get('save');

        $response = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            /*$key = 'activities_list';
            $activity = CacheHelper::getReports($key);
            if(empty($activity)) {*/
                //$sql = 'select a.id, a.name, a.product_id, count(b.activity_id) as status from borrowers b inner join loans l on l.borrower_id = b.id inner join activities a on b.activity_id = a.id where l.dsb_status in (\'Collected\',\'Loan Completed\') group by b.activity_id';
                $sql = 'select id, name, value as status, updated_at,ROUND(value*100/(select sum(value) from cache_reports where type="activity"),2) as percentage from cache_reports where type="activity"';
                $command = Yii::$app->db->createCommand($sql);
                $activity = $command->queryAll();
                /*CacheHelper::setReports($key,$activity);
            }*/
            $current_date = date('Y-m-d H:i:s');
            $last_updated = $activity[0]['updated_at'];

            $datetime1 = new \DateTime($current_date);
            $datetime2 = new \DateTime($last_updated);
            $interval = $datetime1->diff($datetime2);

            if($interval->format('%D') >= 01){
                $sql_query = 'select a.id, a.name, a.product_id, count(l.activity_id) as status from loans l inner join activities a on l.activity_id = a.id where a.product_id = 1 and l.status in(\'collected\',\'loan completed\') AND l.date_disbursed>0 AND l.deleted = 0 group by l.activity_id';
                $command = Yii::$app->db->createCommand($sql_query);
                $new_activity = $command->queryAll();
                $date = date('Y-m-d H:i:s');
                foreach ($new_activity as $p){
                    $insert = 'update cache_reports set value = "'.$p['status'].'" , updated_at = "'.$date.'" where name = "'.$p['name'].'" and type = "activity"';
                    $command = Yii::$app->db->createCommand($insert);
                    $command->execute();
                }
                $activity = $new_activity;
                //CacheHelper::setReports($key,$activity);
            }

            $heading = [
                'sub_heading' => [
                    'last_updated'=> 'as on '.date('d-M-Y')
                ]
            ];
            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Activities List";
            $response['data']['heading']    = $heading;
            $response['data']['activities']   = $activity;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }
    public function actionAppImages()
    {
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $save = $headers->get('save');

        $response = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $app_images=AppImages::find()->where(['type'=>'splash'])->orderBy(['sort_order' => SORT_ASC])->all();
            foreach ($app_images as $img){
                $banner[]=[
                    "path"=>ImageHelper::getAttachmentApiPath(). '?type=banners&file_name=' .$img->path  .'&download=true',
                    "dest"=>$img->target
                ];
            }
            $app_banners=AppImages::find()->where(['type'=>'banner'])->orderBy(['sort_order' => SORT_ASC])->all();
            foreach ($app_banners as $ban){
                $images[]=[
                    "path"=>ImageHelper::getAttachmentApiPath(). '?type=banners&file_name=' .$ban->path  .'&download=true',
                    "dest"=>$ban->target
                ];
            }
            /*$banner= [[
                    "path"=>ImageHelper::getAttachmentApiPath(). '?type=banners&file_name=' .'banner_1'  .'.png&download=true',
                    "dest"=>'http://donate.akhuwat.org.pk'
            ]];*/
            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Images List";
            $response['data']['banner']    = $banner;
            $response['data']['images']    = $images;
            //$response['data']['activities']   = $activity;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }
}

