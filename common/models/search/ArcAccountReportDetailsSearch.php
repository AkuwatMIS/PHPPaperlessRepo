<?php

namespace common\models\search;

use common\models\ArcAccountReportDetails;
use common\models\ArcAccountReports;
use common\models\FundRequests;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * ProgressReportDetailsSearch represents the model behind the search form about `common\models\ProgressReportDetails`.
 */
class ArcAccountReportDetailsSearch extends ArcAccountReportDetails
{
    public $project_id;
    public $report_date;
    public $project_ids;
    public $code;
    public $from_date;
    public $to_date;
    public $report_defination_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'arc_account_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'country_id', 'province_id', 'district_id', 'city_id', 'objects_count', 'amount', 'rejected_applications', 'disbursed_applications','assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['branch_code', 'deleted','project_id','project_ids','code','report_date','to_date','from_date'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ArcAccountReportDetails::find()->select(['region_id','district_id','branch_code','country_id','province_id','district_id','city_id','area_id','branch_id','sum(objects_count) as objects_count','sum(disbursed_applications) as disbursed_applications','sum(rejected_applications) as rejected_applications','sum(amount) as amount']);

        $query->joinWith('report');
        $query->joinWith('report.project');

        $this->load($params);
        if(!isset($this->project_id) || empty($this->project_id)){
            $this->project_id=0;
        }

        /*print_r($params);
        die();*/
        Yii::$app->Permission->searchArcAccountReportsFilters($query,$params['controller'],$params['method'],$params['rbac_type']);

        if (!isset($this->to_date) && !isset($this->from_date))
        {
            $start_date = strtotime(date('Y-m-01'));
            $end_date = strtotime(date('Y-m-t 23:59:59'));
            $query->andFilterWhere(['between', 'arc_account_reports.report_date', $start_date,$end_date]);
        }

        /*if($this->period_type == 0)
        {
            if(isset($this->report_date)){

                $date = explode(' - ', $this->report_date);
                $start_date = strtotime($date[0]);
                $end_date = strtotime(date('Y-m-d 23:59:59',strtotime($date[1])));
                $query->andFilterWhere(['between', 'arc_account_reports.report_date', $start_date,$end_date]);

            }
        }
        else if($this->period_type == 1)
        {*/
            if(isset($this->to_date) && isset($this->from_date)){

                $start_date = strtotime(date('Y-m-01',strtotime($this->from_date)));
                $end_date = strtotime(date('Y-m-t 23:59:59',strtotime($this->to_date)));
                $query->andFilterWhere(['between', 'arc_account_reports.report_date', $start_date,$end_date]);

            }
        //}
        $query->andFilterWhere([
            'id' => $this->id,
            'arc_account_report_id' => $this->arc_account_report_id,
            'division_id' => $this->division_id,
            'arc_account_report_details.region_id' => $this->region_id,
            'arc_account_report_details.area_id' => $this->area_id,
            'arc_account_report_details.branch_id' => $this->branch_id,
            'arc_account_report_details.team_id' => $this->team_id,
            'arc_account_report_details.field_id' => $this->field_id,

            'objects_count' => $this->objects_count,
            /*'from_date' => $this->from_date,
            'period_type' => $this->period_type,
            'report_date' => $this->report_date,*/
            'arc_account_reports.code' => $this->code,
            'arc_account_reports.deleted' => 0,
            'arc_account_reports.project_id' => $this->project_id,
            'amount' => $this->amount,
            'rejected_applications' => $this->rejected_applications,
            'disbursed_applications' => $this->disbursed_applications,
        ]);

        $query->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'division', $this->division])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'city', $this->city])
            //->andFilterWhere(['like', 'DATE_FORMAT(progress_reports.report_date,\'%Y-%m-%d\')', $this->report_date])
            //->andFilterWhere(['=', 'progress_reports.id', $this->project_id]);
            //->andFilterWhere(['<>', 'objects_count', 0])
            ->andFilterWhere(['=', 'arc_account_reports.status', 1]);
        $query->groupBy('branch_id');
        $query->orderBy([
            'region_id' => SORT_ASC,
            'area_id'=>SORT_ASC,
            'branch_id'=>SORT_ASC
        ]);

        /*print_r($params);
        print_r($this);*/
        /*echo '<pre>';
        print_r($query->asArray()->all());
        die();*/
        return $query->asArray()->all();
        //return $dataProvider;
    }
    public function search_mdp_per_borrower($params)
    {
        $this->load($params);
        if(!isset($this->project_id) || empty($this->project_id)){
            $this->project_id=0;
        }
        if (!isset($this->to_date) || empty($this->to_date))
        {
            $previouse_month_start = '2019-06-30';
            $previouse_month_end =  '2019-06-30 23:59:59';
            $start_date = '2019-07-01';
            $end_date ='2019-07-31 23:59:59';
        }


        if(isset($this->to_date)){
            $previouse_month_start=(strtotime(date("Y-m-t", strtotime($this->to_date)) . '-1 month'));
            $previouse_month_end =(strtotime(date("Y-m-t 23:59:59", strtotime($this->to_date)) . '-1 month'));
            $start_date = strtotime(date('Y-m-01',strtotime($this->to_date)));
            $end_date = strtotime(date('Y-m-t 23:59:59',strtotime($this->to_date)));
        }
        /*$query = ArcAccountReportDetails::find()->select('region_id,district_id,branch_code,country_id,province_id,district_id,city_id,area_id,branch_id,
               sum(objects_count) as objects_count_cur,
               sum(disbursed_applications) as disbursed_applications,
               sum(rejected_applications) as rejected_applications,
               sum(amount) as amount,
               (select coalesce(sum(objects_count),0) as objects_count
                 from arc_account_report_details r inner join arc_account_reports p on p.id=r.arc_account_report_id
                  where(p.project_id='.$this->project_id.' and arc_account_report_details.branch_id = r.branch_id and p.code="don" and p.report_date between "'.$previouse_month_start.'" and "'.$previouse_month_end.'") 
              )as objects_count
             ');*/
        $query = ArcAccountReportDetails::find()->select("region_id,district_id,branch_code,country_id,province_id,district_id,city_id,area_id,branch_id,
               sum(objects_count) as objects_count_cur,
               sum(disbursed_applications) as disbursed_applications,
               sum(rejected_applications) as rejected_applications,
               sum(amount) as amount,
               (select  sum(d.active_loans) from progress_reports p INNER JOIN progress_report_details d ON d.progress_report_id=p.id where p.project_id='".$this->project_id."' and p.gender = '0' and d.branch_id=arc_account_report_details.branch_id and p.report_date between '" .$previouse_month_start."' and '".$previouse_month_end."') as objects_count
             ");
        $query->joinWith('report');
        $query->joinWith('report.project');
        Yii::$app->Permission->searchArcAccountReportsFilters($query,$params['controller'],$params['method'],$params['rbac_type']);

        $query->andFilterWhere([
            'id' => $this->id,
            'arc_account_report_id' => $this->arc_account_report_id,
            'division_id' => $this->division_id,
            'arc_account_report_details.region_id' => $this->region_id,
            'arc_account_report_details.area_id' => $this->area_id,
            'arc_account_report_details.branch_id' => $this->branch_id,
            'arc_account_report_details.team_id' => $this->team_id,
            'arc_account_report_details.field_id' => $this->field_id,

            'objects_count' => $this->objects_count,
            'arc_account_reports.code' => $this->code,
            'arc_account_reports.deleted' => 0,
            'arc_account_reports.project_id' => $this->project_id,
            'amount' => $this->amount,
            'rejected_applications' => $this->rejected_applications,
            'disbursed_applications' => $this->disbursed_applications,
        ]);

        $query->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'division', $this->division])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'city', $this->city])
            //->andFilterWhere(['like', 'DATE_FORMAT(progress_reports.report_date,\'%Y-%m-%d\')', $this->report_date])
            //->andFilterWhere(['=', 'progress_reports.id', $this->project_id]);
            ->andFilterWhere(['<>', 'objects_count', 0])
            ->andFilterWhere(['between', 'arc_account_reports.report_date', $start_date,$end_date])
            ->andFilterWhere(['=', 'arc_account_reports.status', 1]);
        $query->groupBy('branch_id');
        $query->orderBy([
            'region_id' => SORT_ASC,
            'area_id'=>SORT_ASC,
            'branch_id'=>SORT_ASC
        ]);
        return $query->asArray()->all();
    }

    public function searchFundrequestReport($params)
    {
        $this->load($params);
        //print_r($params);die();
        $branches_cond = '';
        $awp_cond = '';
        $accounts_cond = '';
        $fund_request_cond = '';
       // $accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');

        if(empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $branches_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','branches');
            $fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests');
            $awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');
            $accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');
        }
        if (isset($params['ArcAccountReportDetailsSearch']['region_id']) && !empty($params['ArcAccountReportDetailsSearch']['region_id'])) {
            $region = $params['ArcAccountReportDetailsSearch']['region_id'];
            $branches_cond .='and branches.region_id= "'.$region.'"';
            $branches_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','branches');
            $fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests');
            $awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');
            $accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');

            // $fund_request_cond .=' and fund_requests.region_id= "'.$region.'"';
           // $fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests');

          //  $awp_cond .=' and awp.region_id= "'.$region.'"';
            //$awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');

           // $accounts_cond .=' and arc_account_report_details.region_id= "'.$region.'"';
            //$accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');

        }
        if(isset($params['ArcAccountReportDetailsSearch']['branch_id']) && !empty($params['ArcAccountReportDetailsSearch']['branch_id'])) {
            $branch = $params['ArcAccountReportDetailsSearch']['branch_id'];

            $branches_cond .=' and branches.id= "'.$branch.'"';
            //$branches_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','branches');

           // $fund_request_cond .=' and fund_requests.branch_id= "'.$branch.'"';
           // $fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests');

            //$awp_cond .=' and awp.branch_id= "'.$branch.'"';
           // $awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');

            //$accounts_cond .=' and arc_account_report_details.branch_id= "'.$branch.'"';
            //$accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');

        }
        if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])) {
            $area = $params['ArcAccountReportDetailsSearch']['area_id'];

            $branches_cond .=' and branches.area_id= "'.$area.'"';
           // $branches_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','branches');

           // $fund_request_cond .=' and fund_requests.area_id= "'.$area.'"';
            //$fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests');

            //$awp_cond .=' and awp.area_id= "'.$area.'"';
            //$awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');

            //$accounts_cond .=' and arc_account_report_details.area_id= "'.$area.'"';
            //$accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_report_details');

        }
        if(isset($params['ArcAccountReportDetailsSearch']['project_id']) && !empty($params['ArcAccountReportDetailsSearch']['project_id'])) {
            $project = $params['ArcAccountReportDetailsSearch']['project_id'];

            $fund_request_cond .=' and fund_requests_details.project_id= "'.$project.'"';
            //$fund_request_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','fund_requests_details');

            $awp_cond .=' and awp.project_id= "'.$project.'"';
            //$awp_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','awp');

            $accounts_cond .=' and arc_account_reports.project_id= "'.$project.'"';
            //$accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_reports');

        } else {
            $accounts_cond .=' and arc_account_reports.project_id = 0';
           // $accounts_cond .= Yii::$app->Permission->searchFundRequestReportsFilters($params['controller'],$params['method'],$params['rbac_type'], '','arc_account_reports');

        }
        if(isset($params['ArcAccountReportDetailsSearch']['to_date']) && isset($params['ArcAccountReportDetailsSearch']['from_date']) &&
          (!empty($params['ArcAccountReportDetailsSearch']['to_date']) && !empty($params['ArcAccountReportDetailsSearch']['from_date']))) {
            $from_date = $params['ArcAccountReportDetailsSearch']['from_date'];
            $to_date = $params['ArcAccountReportDetailsSearch']['to_date'];
            $awp_cond .='and awp.month between "'.$from_date.'" and "'.$to_date.'" ';
            $start_date = strtotime(date('Y-m-01',strtotime($from_date)));
            $end_date = strtotime(date('Y-m-t 23:59:59',strtotime($to_date)));
            $accounts_cond .=' and arc_account_reports.report_date between "'.$start_date.'" and "'.$end_date.'" ';
            $fund_request_cond .=' and fund_requests.created_at between "'.$start_date.'" and "'.$end_date.'" ';

        } else {
            $to_date = date('Y-m');
            $from_date = date('Y-m',strtotime($to_date));
            $awp_cond .='and awp.month between "'.$from_date.'" and "'.$to_date.'" ';
            $start_date = strtotime(date('Y-m-01',strtotime($from_date)));
            $end_date = strtotime(date('Y-m-t 23:59:59',strtotime($to_date)));
            $accounts_cond .=' and arc_account_reports.report_date between "'.$start_date.'" and "'.$end_date.'" ';
            $fund_request_cond .=' and fund_requests.created_at between "'.$start_date.'" and "'.$end_date.'" ';
        }

        //print_r($accounts_cond);die();
        $query = 'select region_id, area_id,id as branch_id ,

                  (select COALESCE(sum(disbursement_amount),0) as disbursement_amount from awp where awp.deleted=0 and awp.branch_id=branches.id
                   and awp.area_id=branches.area_id and awp.region_id=branches.region_id ' . $awp_cond . ') as disbursement_amount,
 
                  (select COALESCE(sum(fund_requests_details.total_approved_amount),0) as approved_amount from fund_requests 
                  inner join fund_requests_details on fund_requests.id=fund_requests_details.fund_request_id
                  where fund_requests.deleted=0 and fund_requests.status="processed" and fund_requests_details.deleted=0 and fund_requests.branch_id=branches.id 
                  and fund_requests.area_id=branches.area_id and fund_requests.region_id=branches.region_id ' . $fund_request_cond . ' 
                  ) as fund_requests_amount_processed,
                  
                  (select COALESCE(sum(arc_account_report_details.amount),0) as account_disbursed_amount from arc_account_report_details 
                  inner join arc_account_reports on arc_account_reports.id=arc_account_report_details.arc_account_report_id
                  where arc_account_reports.report_name = "Disbursement Summary" and arc_account_report_details.branch_id=branches.id
                   and arc_account_report_details.area_id=branches.area_id and arc_account_report_details.region_id=branches.region_id
                   and arc_account_reports.status = 1 and arc_account_reports.deleted = 0 ' . $accounts_cond . ' 
                  ) as account_report_amount_disbursed
                  
                  from branches where  1 ' . $branches_cond . ' group by branch_id order by area_id asc
                  ';
        $result = \Yii::$app->db->createCommand($query)->queryAll();
       // echo '<pre>';
       // print_r($result);die();
        return $result;
    }
}
