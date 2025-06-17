<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\ProgressReportHelper;
use Yii;
use common\models\Users;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;

class AnalysisController extends \yii\web\Controller
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

    public function actionInfo()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $attribute     = !empty($input['attribute'])?($input['attribute']):'';
        $search_by     = !empty($input['search_by'])?($input['search_by']):'';
       
        $sort_by     = !empty($input['sort_by'])?($input['sort_by']):'';
        $limit     = !empty($input['limit'])?($input['limit']):'';

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        $report_date =    strtotime("-1 days",strtotime(date('Y-m-d 00:00:00'))) .','. strtotime("-1 days",strtotime(date('Y-m-d 23:59:59')));

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $analysis = '';
            if($attribute){
                switch ($attribute) {
                    case "MDP":
                        $column = 'mdp';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "DISB":
                        $column = 'disb';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "CDUE":
                        $column = 'cum_due';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "CRECOVERY":
                        $column = 'cum_recv';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "OD":
                        $column = 'overdue_borrowers';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "ODA":
                        $column = 'overdue_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "ODP":
                        $column = 'overdue_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "PAR":
                        $column = 'par_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "PARP":
                        $column = 'par_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "LOANS":
                        $column = 'no_of_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "ACTIVELOANS":
                        $column = 'active_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "MALELOANS":
                        $column = 'family_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "FEMALELOANS":
                        $column = 'female_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "CDISB":
                        $column = 'cum_disb';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "RECVPER":
                        $column = 'recovery_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "OLP":
                        $column = 'olp_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "BENEFICIARIES":
                        $column = 'members_count';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "MDP/BORROWER":
                        $column = 'mdp/borrower';
                        $analysis = ProgressReportHelper::getAttributeWiseProgress($user,$search_by,$sort_by,$limit,$report_date,$column);
                        break;
                    case "":
                        echo  'No Arrtibute Selected';
                        break;
                }
                if($column == 'mdp')
                {
                    $analysis_info = ApiParser::parseAnalysisInfoMdp($analysis);
                } else {
                    $analysis_info = ApiParser::parseAnalysisInfo($analysis);
                }
            }else{
                $analysis_info = 'Arrtibute not selected';
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Analysis Info";
            $response['data']['title']    = "Branches wise ".$attribute;
            $response['data']['last_date_updated']   = 'as on '.date('d-M-Y');
            $response['data']['month_year']   = date('M-Y');
            $response['data']['details']   = empty($analysis_info) ? [] : $analysis_info;
            $response['data']['content']   = empty($analysis_info) ? 'Record Not Found' : '';
        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionAnalytics()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $attribute     = !empty($input['attribute'])?($input['attribute']):'';
        $attribute_id     = !empty($input['attribute_id'])?($input['attribute_id']):'';
        $search_by     = !empty($input['search_by'])?($input['search_by']):'';
        $sort_by     = !empty($input['sort_by'])?($input['sort_by']):'';
        $limit     = !empty($input['limit'])?($input['limit']):'';

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $analysis = '';
            if($attribute){
                switch ($attribute) {
                    case "MDP":
                        $column = 'mdp';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "CDUE":
                        $column = 'cum_due';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "CRECOVERY":
                        $column = 'cum_recv';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "OD":
                        $column = 'overdue_borrowers';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "ODA":
                        $column = 'overdue_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "ODP":
                        $column = 'overdue_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "PAR":
                        $column = 'par_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "PARP":
                        $column = 'par_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "LOANS":
                        $column = 'no_of_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "ACTIVELOANS":
                        $column = 'active_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "MALELOANS":
                        $column = 'family_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "FEMALELOANS":
                        $column = 'female_loans';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "CDISB":
                        $column = 'cum_disb';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "RECVPER":
                        $column = 'recovery_percentage';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "OLP":
                        $column = 'olp_amount';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "BENEFICIARIES":
                        $column = 'members_count';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "DISB":
                        $column = 'disb';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        break;
                    case "MDP/BORROWER":
                        $column = 'mdp';
                        $analysis = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column,$attribute_id);
                        $column1 = 'active_loans';
                        $analysis1 = ProgressReportHelper::getAttributeWiseYearlyTrend($user,$search_by,$column1,$attribute_id);
                        foreach ($analysis as $k=>$v){
                            if(isset($analysis1[$k+1])){
                                $analysis[$k]['attribute']=round($v['attribute']/$analysis1[$k+1]['attribute'],2);
                            }else{
                                $analysis[$k]['attribute']=round($v['attribute']/$analysis1[$k]['attribute'],2);
                            }
                        }
                        break;
                    case "":
                        echo  'No Arrtibute Selected';
                        break;
                }
                $analysis_info = ApiParser::parseAnalysisTrend($analysis,$attribute);
            }else{
                $analysis_info = 'Arrtibute not selected';
            }

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Analysis Trends";
            $response['data']['title']    = "Branches wise ".$attribute;
            $response['data']['last_date_updated']   = 'as on '.date('d-M-Y');
            $response['data']['month_year']   = date('M-Y');
            $response['data']['details']   = empty($analysis_info) ? [] : $analysis_info;
            $response['data']['content']   = empty($analysis_info) ? 'Record Not Found' : '';
        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionSummary()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }


        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        $report_date =    strtotime("-1 days",strtotime(date('Y-m-d 00:00:00'))) .','. strtotime("-1 days",strtotime(date('Y-m-d 23:59:59')));

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $analysis = '';
            $analysis = ProgressReportHelper::getProgressSummary($user,$report_date);
            $analysis_info = ApiParser::parseAnalysisSummary($analysis['a'][0],$analysis['b'][0]);

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Analysis Info";
            $response['data']['title']    = "Overall Analysis";
            $response['data']['last_date_updated']   = 'as on '.date('d-M-Y');
            $response['data']['month_year']   = date('M-Y');
            $response['data']['details']   = empty($analysis_info) ? [] : $analysis_info;
            $response['data']['content']   = empty($analysis_info) ? 'Record Not Found' : '';
        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

