<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\JsonHelper;
use common\models\Awp;
use common\models\ProgressReportDetails;
use Yii;


class MisSourceController extends RestController
{
    public $rbac_type = 'api';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }


    public function actionAwpProjectWiseBudget()
    {
        $date1 = date('Y-m');
        $date2 = date('Y-m-t', (strtotime('-1 months', strtotime($date1))));

        $sql = "SELECT pr.id,pr.name,COALESCE((pr.total_fund),0) as total_fund,fund_received,fund_source,
               (select  COALESCE (sum(d.olp_amount),0) from progress_reports p INNER JOIN progress_report_details d ON d.progress_report_id=p.id where p.do_delete=0 and p.project_id=pr.id and p.gender = '0' and from_unixtime(p.report_date , '%Y-%m-%d') = '" . ($date2) ."') as olp,
               (select  COALESCE(sum(a.actual_no_of_loans),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as actual_no_of_loans,
               (select  COALESCE(sum(a.monthly_recovery),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as expected_recovery,
               (select  COALESCE(sum(a.disbursement_amount),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as disbursement_target_amount
          from projects pr where pr.total_fund>0";
        $query = Yii::$app->db->createCommand($sql);
        $resultData = $query->queryAll();

        $response['meta'] = [
            'error' => true,
            'message' => 'Successfully data fetched!',
            'status_code' => 200
        ];
        $response['data'] = $resultData;
        return JsonHelper::asJson($response);

    }

    public function actionProjectWiseDetail(){

        $months = strtotime(date("Y-m-t", strtotime( date( 'Y-m-01' )." -1 months")));
        $sql = "SELECT 
                    a.project_id,
                    sum(b.members_count) 'Total Benefiting Families',
                    sum(b.no_of_loans) 'Total Loans',
                    sum(b.cum_disb) 'Total Disbursement',
                    sum(b.active_loans) 'Active Loans',
                    sum(b.olp_amount) 'OLP',
                    AVG(COALESCE (recovery_percentage,0)) as 'Recovery Percentage',
                    sum(b.cum_recv) 'Recovery'
                FROM progress_reports a
                inner join progress_report_details b on a.id=b.progress_report_id
                where a.project_id != 0 and a.gender=0 and a.status=1 AND a.report_date = $months GROUP BY a.project_id;";
        $query = Yii::$app->db->createCommand($sql);
        $resultDataProjectWise = $query->queryAll();

        // ========================================Month Wise Project Data=============================================================

        $resultDataMonthWise = [];
        for ($i = 0; $i <= 11; $i++) {
            $months = date("Y-m", strtotime( date( 'Y-m' )." -$i months"));

                $sql = "SELECT `month`,
                        sum(`target_loans`) as 'Target loans',
                        sum(`target_amount`) as 'Target amount',
                        sum(`achieved_loans`) as 'Achieved loans',
                        sum(`achieved_amount`) as 'Achieved amount',
                        sum(`loans_dif`) as 'Total overdue loans' 
                         FROM `awp_target_vs_achievement`
                   where `month`='$months'
                   and deleted=0
                   GROUP BY month;";
            $query = Yii::$app->db->createCommand($sql);
            $resultDataMonthWise[$months] = $query->queryAll();
        }


        $response['meta'] = [
            'error' => true,
            'message' => 'Successfully data fetched!',
            'status_code' => 200
        ];
        $response['project_wise'] = $resultDataProjectWise;
        $response['month_wise'] = $resultDataMonthWise;
        return JsonHelper::asJson($response);
    }

    public function actionFetchAwp(){
        $paramsStartDate = (isset($this->request['startDate']) && !empty($this->request['startDate'])) ? $this->request['startDate'] : 0;
        $paramsPreviousDate = (isset($this->request['previousDate']) && !empty($this->request['previousDate'])) ? $this->request['previousDate'] : 0;

        if($paramsStartDate!=0){
            $awpPrev = Awp::find()->where(['month' => $paramsPreviousDate])->all();
            $awp = Awp::find()->where(['>=','month' , $paramsStartDate])->all();
            $response['meta'] = [
                'error' => false,
                'message' => 'Successfully data fetched!',
                'status_code' => 200
            ];
            $response['pre_data'] = $awpPrev;
            $response['data'] = $awp;
        }else{
            $response['meta'] = [
                'error' => true,
                'message' => 'No data found!',
                'status_code' => 201
            ];
        }
        return JsonHelper::asJson($response);
    }

}