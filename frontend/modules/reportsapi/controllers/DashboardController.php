<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\models\ProgressReports;
use common\models\Projects;
use Yii;
use common\models\Users;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;

class DashboardController extends \yii\web\Controller
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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionReport(array $filter,$section_id,$section_type)
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];
        $max_amount = 0;
        $min_amount = 0;
        $project_id = 0;
        $graph_data = [];
        if(isset($filter['project_id']))
        {
            $project_id = $filter['project_id'];
        }
        $date2 = strtotime(date('Y-m-d'));
        if($filter['type'] == 'current month') {
            $date1 = strtotime(date('Y-m-01'));
        } else if($filter['type'] == 'last three months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-3 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last six months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-6 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last year') {
            $date1 = strtotime(date('Y-m-t',strtotime('-12 months',strtotime(date('Y-m-d')))));
        }

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $query = ProgressReports::find()->select('progress_reports.*,progress_report_details.'.$filter['column'])->joinWith('progressReportDetails')->joinWith('project');
            $query->where([$section_type.'_id' => $section_id]);
            $query->andWhere(['project_id' => $project_id]);
            $query->andWhere(['between', 'report_date', $date1,$date2]);

            if($filter['type'] == 'current month') {
                $query->andWhere(['period' => 'daily']);
            }
            else {
                $query->andWhere(['period' => 'monthly']);
            }

            if(isset($filter['limit'])) {
                $query->limit($filter['limit']);
            }

            if(isset($filter['order'])) {
                $query->orderBy($filter['column']. ' '.$filter['order']);
            }

            $report_data = $query->asArray()->all();

            if(!empty($report_data))
            {
                $min_amount = $report_data[0][$filter['column']];
            }

            foreach ($report_data as $data)
            {
                if($data[$filter['column']] > $max_amount)
                {
                    $max_amount = $data[$filter['column']];
                }
                if($data[$filter['column']] < $min_amount)
                {
                    $min_amount = $data[$filter['column']];
                }
                $graph_data[] = ['x' =>date('Y-m-d', strtotime($data['report_date'])), 'y' => $data[$filter['column']]];
            }


            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            //$response['data']['message']    = "Get Activity Detail";
            $response['data']['range']   = ['max'=> $max_amount, 'min' =>$min_amount];
            $response['data']['legends']   = ['x'=> 'Date', 'y' => $filter['column']];
            if(isset($filter['project_id']))
            {
                $project = isset($report_data[0]['project']) ? $report_data[0]['project']['name'] : "";
                $response['data']['projects'] = ['name' => $project, 'coordinates' => $graph_data];
            } else {
                $response['data']['coordinates'] = $graph_data;
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }


    public function actionFieldreport(array $filter,$section_type)
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];
        $max_amount = 0;
        $min_amount = 0;
        $graph_data = [];

        $date2 = strtotime(date('Y-m-d'));
        if($filter['type'] == 'current month') {
            $date1 = strtotime(date('Y-m-01'));
        } else if($filter['type'] == 'last three months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-3 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last six months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-6 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last year') {
            $date1 = strtotime(date('Y-m-t',strtotime('-12 months',strtotime(date('Y-m-d')))));
        }

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $query = ProgressReports::find()->select('progress_reports.period,progress_reports.id,progress_reports.project_id,regions.name as region_name,areas.name as area_name,branches.name as branch_name,sum(progress_report_details.'.$filter['column'].') as total')->joinWith('progressReportDetails')->joinWith('project')->joinWith('progressReportDetails.region')->joinWith('progressReportDetails.area')->joinWith('progressReportDetails.branch');
            $query->where(['project_id' => 0]);
            $query->andWhere(['between', 'report_date', $date1,$date2]);

            if($filter['type'] == 'current month') {
                $query->andWhere(['period' => 'daily']);
            }
            else {
                $query->andWhere(['period' => 'monthly']);
            }

            if(isset($filter['limit'])) {
                $query->limit($filter['limit']);
            }

            if(isset($filter['order'])) {
                $query->orderBy('total'. ' '.$filter['order']);
            }

            $query->groupBy('progress_report_details.'.$section_type. '_id');
             $report_data = $query->createCommand()->queryAll();
            if(!empty($report_data))
            {
                $min_amount = $report_data[0]['total'];
            }

            foreach ($report_data as $data)
            {
                if($data['total'] > $max_amount)
                {
                    $max_amount = $data['total'];
                }
                if($data['total'] < $min_amount)
                {
                    $min_amount = $data['total'];
                }
                $graph_data[] = ['x' =>$data[$section_type.'_name'], 'y' => $data['total']];
            }


            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            //$response['data']['message']    = "Get Activity Detail";
            $response['data']['range']   = ['max'=> $max_amount, 'min' =>$min_amount];
            $response['data']['legends']   = ['x'=> $section_type, 'y' => $filter['column']];
            $response['data']['coordinates'] = $graph_data;


        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionProjectsreport(array $filter,$section_id,$section_type)
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];
        $max_amount = 0;
        $min_amount = 0;
        $response_data = [];

        $date2 = strtotime(date('Y-m-d'));
        if($filter['type'] == 'current month') {
            $date1 = strtotime(date('Y-m-01'));
        } else if($filter['type'] == 'last three months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-3 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last six months') {
            $date1 = strtotime(date('Y-m-t',strtotime('-6 months',strtotime(date('Y-m-d')))));
        } else if($filter['type'] == 'last year') {
            $date1 = strtotime(date('Y-m-t',strtotime('-12 months',strtotime(date('Y-m-d')))));
        }

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $projects = Projects::find()->all();
            foreach ($projects as $project) {
                $graph_data = [];
                $query = ProgressReports::find()->select('progress_reports.*,progress_report_details.' . $filter['column'])->joinWith('progressReportDetails')->joinWith('project');
                $query->where([$section_type . '_id' => $section_id]);
                $query->andWhere(['project_id' => $project->id]);
                $query->andWhere(['between', 'report_date', $date1, $date2]);

                if ($filter['type'] == 'current month') {
                    $query->andWhere(['period' => 'daily']);
                } else {
                    $query->andWhere(['period' => 'monthly']);
                }

                if (isset($filter['limit'])) {
                    $query->limit($filter['limit']);
                }

                if (isset($filter['order'])) {
                    $query->orderBy($filter['column'] . ' ' . $filter['order']);
                }

                $report_data = $query->asArray()->all();
                if (isset($report_data) && !empty($report_data)) {
                    if (!empty($report_data)) {
                        $min_amount = $report_data[0][$filter['column']];
                    }

                    foreach ($report_data as $data) {
                        if ($data[$filter['column']] > $max_amount) {
                            $max_amount = $data[$filter['column']];
                        }
                        if ($data[$filter['column']] < $min_amount) {
                            $min_amount = $data[$filter['column']];
                        }
                        $graph_data[] = ['x' => date('Y-m-d', strtotime($data['report_date'])), 'y' => $data[$filter['column']]];
                    }

                    $project = isset($report_data[0]['project']) ? $report_data[0]['project']['name'] : "";
                    $response_data[] = ['name' => $project, 'coordinates' => $graph_data];
                }
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['range']   = ['max'=> $max_amount, 'min' =>$min_amount];
            $response['data']['legends']   = ['x'=> 'Date', 'y' => $filter['column']];
            $response['data']['projects']   = $response_data;


        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }
}

