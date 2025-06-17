<?php

namespace common\models\search;

use common\components\RbacHelper;
use common\models\Projects;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Awp;
use yii\data\SqlDataProvider;

/**
 * AwpSearch represents the model behind the search form about `common\models\Awp`.
 */
class AwpSearch extends Awp
{
    /**
     * @inheritdoc
     */
    public $monthly_recovery;

    public $active_loans_last;
    public function rules()
    {
        return [
           // [['month', 'region_id', 'area_id', 'branch_id', 'project_id',/* 'created_at', 'updated_at'*/], 'required'],
            [['region_id', 'area_id', 'branch_id', 'project_id', 'awp_id', 'no_of_loans', 'avg_loan_size', 'disbursement_amount', 'monthly_olp', 'active_loans', 'monthly_closed_loans', 'monthly_recovery', 'avg_recovery', 'funds_required', 'actual_recovery', 'actual_disbursement', 'actual_no_of_loans', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['month'], 'string', 'max' => 15],
            [['month_from'], 'safe'],
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
        //$query = Awp::find();
        /*$query = Awp::find()
            ->select(['region_id','area_id','branch_id',
                'branch_id as b,(select sum(active_loans) from awp where(month=\'2018-06\' and branch_id=b)) as active_loans_last',
                '(select sum(monthly_olp) from awp where(month="2018-06" and branch_id=b)) as olp_last',
                '(select sum(active_loans) from awp where(month="2019-05" and branch_id=b)) as active_loans_current',
                '(select sum(monthly_olp) from awp where(month="2019-05" and branch_id=b)) as olp_current',

                'sum(no_of_loans) as no_of_loans','sum(active_loans) as active_loans','sum(monthly_olp) as monthly_olp',
                'sum(avg_recovery) as avg_recovery','sum(avg_loan_size) as avg_loan_size',
                'sum(monthly_closed_loans) as monthly_closed_loans',
                'sum(monthly_recovery) as monthly_recovery',
                'sum(funds_required) as funds_required',
                'sum(amount_disbursed) as amount_disbursed']);*/
        $this->load($params);

        /*if (isset($this->project_id) && $this->project_id != null && isset($this->month) && $this->month != null) {
            $cond = 'and a.project_id="' . $this->project_id . '" and a.month="' . $this->month . '"';
        } else if (isset($this->project_id) && $this->project_id != null) {
            $cond = 'and a.project_id="' . $this->project_id . '"';

        } else if (isset($this->month) && $this->month != null) {
            $cond = 'and a.month="' . $this->month . '"';
        } else {
            $cond = '';
        }*/
        /*$query = Awp::find()
            ->select(['region_id', 'area_id', 'branch_id',
                'branch_id as b,(select active_loans from awp where(month="2018-07" and branch_id=b)) as active_loans_last',
                '(select monthly_olp from awp where(month="2018-07" and branch_id=b)) as olp_last',
                '(select active_loans from awp where(month="2019-06" and branch_id=b)) as active_loans_current',
                '(select monthly_closed_loans from awp where(month="2019-06" and branch_id=b)) as monthly_closed_loans_last',
                '(select no_of_loans from awp where(month="2019-06" and branch_id=b)) as no_of_loans_last',
                '(select monthly_olp from awp where(month="2019-06" and branch_id=b)) as olp_current',
                '(select monthly_recovery from awp where(month="2019-06" and branch_id=b)) as monthly_recovery_last',
                '(select amount_disbursed from awp where(month="2019-06" and branch_id=b)) as amount_disbursed_last',
                'sum(no_of_loans) as no_of_loans',
                'sum(monthly_closed_loans) as monthly_closed_loans',
                '(select sum(b.monthly_recovery) from awp a inner join awp_project_mapping as b on a.id = b.awp_id where a.branch_id = b '.$cond.') as monthly_recovery',
                '(select sum(b.funds_required) from awp a inner join awp_project_mapping as b on a.id = b.awp_id where a.branch_id = b '.$cond.') as funds_required',
                '(select sum(b.disbursement_amount) from awp a inner join awp_project_mapping as b on a.id = b.awp_id where a.branch_id = b '.$cond.') as amount_disbursed',]);*/
        /*$query = Awp::find()
            ->select(['region_id', 'area_id', 'branch_id',
                'branch_id as b',
                '(select sum(active_loans) from awp where(month="2020-08" and branch_id=b)) as active_loans_last',
                '(select sum(monthly_olp) from awp where(month="2020-08" and branch_id=b)) as olp_last',
                '(select sum(active_loans) from awp where(month="2021-06" and branch_id=b)) as active_loans_current',
                '(select sum(monthly_closed_loans) from awp where(month="2021-06" and branch_id=b)) as monthly_closed_loans_last',
                '(select sum(no_of_loans) from awp where(month="2021-06" and branch_id=b)) as no_of_loans_last',
                '(select sum(monthly_olp) from awp where(month="2021-06" and branch_id=b)) as olp_current',
                '(select sum(monthly_recovery) from awp where(month="2021-06" and branch_id=b)) as monthly_recovery_last',
                '(select sum(disbursement_amount) from awp where(month="2021-06" and branch_id=b)) as amount_disbursed_last',
                'sum(no_of_loans) as no_of_loans',
                'sum(monthly_closed_loans) as monthly_closed_loans',
                'sum(monthly_recovery) as monthly_recovery',
                'sum(funds_required) as funds_required',
                'sum(disbursement_amount) as disbursement_amount',]);*/
        if (isset($this->project_id) && $this->project_id != null) {

            $cond1 = 'AND project_id = p';
        } else {
            
            $cond1 = '';
        }
        $query = Awp::find()
            ->select(['region_id', 'area_id', 'branch_id',
                'branch_id as b' ,'project_id as p',
                '(select sum(active_loans) from awp where(month="2023-07" and branch_id=b '.$cond1.')) as active_loans_last',
                '(select sum(monthly_olp) from awp where(month="2023-07" and branch_id=b '.$cond1.')) as olp_last',
                '(select sum(active_loans) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as active_loans_current',
                '(select sum(monthly_closed_loans) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as monthly_closed_loans_last',
                '(select sum(no_of_loans) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as no_of_loans_last',
                '(select sum(monthly_olp) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as olp_current',
                '(select sum(monthly_recovery) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as monthly_recovery_last',
                '(select sum(disbursement_amount) from awp where(month="2024-06" and branch_id=b '.$cond1.')) as amount_disbursed_last',
                'sum(no_of_loans) as no_of_loans',
                'sum(monthly_closed_loans) as monthly_closed_loans',
                'sum(monthly_recovery) as monthly_recovery',
                'sum(funds_required) as funds_required',
                'sum(disbursement_amount) as disbursement_amount',]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        /*if(empty($params['AwpSearch']['month']) && empty($params['AwpSearch']['month_from']))
        {
            $query->andFilterWhere(['between', 'awp.month','2019-07', '2020-06']);
        } else {
            if($this->month_from>=1 && $this->month_from<=6){
                $this->month_from='2021-'.$this->month_from;
            }
            else{
                $this->month_from='2020-'.$this->month_from;
            }
            if($this->month>=1 && $this->month<=6){
                $this->month='2021-'.$this->month;
            }
            else{
                $this->month='2020-'.$this->month;
            }
            $query->andFilterWhere(['between', 'awp.month', $this->month_from, $this->month]);
        }*/
        if(empty($params['AwpSearch']['month']) && empty($params['AwpSearch']['month_from']))
        {
            $query->andFilterWhere(['between', 'awp.month','2022-06', '2023-06']);
        } else {
            if($this->month_from>=1 && $this->month_from<=6) {
                $this->month_from='2023-'.$this->month_from;
            }
            else{
                $this->month_from='2022-'.$this->month_from;
            }
            if($this->month>=1 && $this->month<=6) {
                $this->month='2023-'.$this->month;
            }
            else{
                $this->month='2022-'.$this->month;
            }
            $query->andFilterWhere(['between', 'awp.month', $this->month_from, $this->month]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            'project_id' => $this->project_id,
            'no_of_loans' => $this->no_of_loans,
            'active_loans' => $this->active_loans,
            'monthly_olp' => $this->monthly_olp,
            'avg_loan_size' => $this->avg_loan_size,
            'monthly_closed_loans' => $this->monthly_closed_loans,
            'monthly_recovery' => $this->monthly_recovery,
            'funds_required' => $this->funds_required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => 0,
        ]);

        //$query->andFilterWhere(['like', 'month', $this->month]);
        $query->groupBy('branch_id');
        return $dataProvider;
    }

    public function searchprojectwise($params)
    {
        $this->load($params);
        $date1 = $this->month;
        $date2 = date('Y-m-t', (strtotime('-1 months', strtotime($this->month))));
        if($this->month==null){
            $date1 = date('Y-m');
            $date2 = date('Y-m-t', (strtotime('-1 months', strtotime($date1))));
        }
        $connection = Yii::$app->db;
        $sql = "SELECT pr.id,pr.name,COALESCE((pr.total_fund),0) as total_fund,
               (select  COALESCE (sum(d.olp_amount),0) from progress_reports p INNER JOIN progress_report_details d ON d.progress_report_id=p.id where p.project_id=pr.id and p.report_date ='" . $date2 ."') as olp,
               (select  COALESCE(sum(m.actual_no_of_loans),0) from awp a INNER JOIN awp_project_mapping m ON a.id=m.awp_id where a.month='".$date1."' and m.project_id=pr.id) as actual_no_of_loans,
               (select  COALESCE(sum(m.actual_recovery),0) from awp a INNER JOIN awp_project_mapping m ON a.id=m.awp_id where a.month='".$date1."' and m.project_id=pr.id) as actual_recovery,
               (select  COALESCE(sum(m.actual_disbursement),0) from awp a INNER JOIN awp_project_mapping m ON a.id=m.awp_id where a.month='".$date1."' and m.project_id=pr.id) as actual_disbursement
          from projects pr";
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            //'totalCount' => 22,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $dataProvider;
    }
    public function searchawpreport($params)
    {

        $this->load($params);


        /*if (isset($this->project_id) && $this->project_id != null && isset($this->month) && $this->month != null) {
            $cond = 'and a.project_id="' . $this->project_id . '" and a.month="' . $this->month . '"';
        } else if (isset($this->project_id) && $this->project_id != null) {
            $cond = 'and a.project_id="' . $this->project_id . '"';

        } else if (isset($this->month) && $this->month != null) {
            $cond = 'and a.month="' . $this->month . '"';
        } else {
            $cond = 'and a.month between"' . '2019-07' . '" and"' . '2020-06' . '" ';
        }*/
        // 'coalesce(sum(funds_required),0) as funds_required',
        $query = AwpSearch::find()->select(['awp.region_id', 'awp.area_id', 'awp.branch_id','awp.project_id','branch_id as b',
            'coalesce(sum(monthly_recovery),0) as monthly_recovery',
            'coalesce(sum(no_of_loans),0) as no_of_loans',
            'coalesce(sum(disbursement_amount),0) as amount_disbursed',
            '(coalesce(sum(disbursement_amount),0)-coalesce(sum(monthly_recovery),0)) as funds_required',
            'coalesce(sum(actual_recovery),0) as actual_recovery',
            'coalesce(sum(actual_disbursement),0) as actual_disbursement',

        ]);
        //$this->load($params);

        RbacHelper::searchAwpRegionWiseFilters($query);
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        //$this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/
        /*echo '<pre>';
        print_r($query->asArray()->all());
        die();*/
        $query->andFilterWhere([
            'id' => $this->id,
            'awp.branch_id' => $this->branch_id,
            'awp.area_id' => $this->area_id,
            'awp.region_id' => $this->region_id,
            'project_id' => $this->project_id,
            'no_of_loans' => $this->no_of_loans,
            'active_loans' => $this->active_loans,
            'monthly_olp' => $this->monthly_olp,
            'avg_loan_size' => $this->avg_loan_size,
            'monthly_closed_loans' => $this->monthly_closed_loans,
            'monthly_recovery' => $this->monthly_recovery,
            'funds_required' => $this->funds_required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        if (isset($this->month) && $this->month != null && isset($this->month_from) && $this->month_from != null ) {
            $query->andFilterWhere(['between', 'awp.month',$this->month_from, $this->month]);
        } else {
        }
        //$query->andFilterWhere(['like', 'month', $this->month]);
         $query->groupBy('branch_id');
        $query->orderBy([
            'region_id' => SORT_ASC,
            'area_id'=>SORT_ASC,
            'branch_id'=>SORT_ASC
        ]);
        /*echo'<pre>';
        print_r($query->asArray()->all());
        die();*/
        return $query->asArray()->all();
    }
    public function searchprojectwisebudget($params, $export = false)
    {
        $this->load($params);
        $date1 = $this->month;
        $date2 = date('Y-m-t', (strtotime('-1 months', strtotime($this->month))));
        if($this->month==null){
            $date1 = date('Y-m');
            $date2 = date('Y-m-t', (strtotime('-1 months', strtotime($date1))));
        }
        //$date1='2018-08';
        $connection = Yii::$app->db;
        $sql = "SELECT pr.id,pr.name,COALESCE((pr.total_fund),0) as total_fund,fund_received,fund_source,
               (select  COALESCE (sum(d.olp_amount),0) from progress_reports p INNER JOIN progress_report_details d ON d.progress_report_id=p.id where p.do_delete=0 and p.project_id=pr.id and p.gender = '0' and from_unixtime(p.report_date , '%Y-%m-%d') = '" . ($date2) ."') as olp,
               (select  COALESCE(sum(a.actual_no_of_loans),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as actual_no_of_loans,
               (select  COALESCE(sum(a.monthly_recovery),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as expected_recovery,
               (select  COALESCE(sum(a.disbursement_amount),0) from awp a where a.month='".$date1."' and a.project_id=pr.id) as disbursement_amount
          from projects pr where pr.total_fund>0";
        /*where pr.total_fund>0*/
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            //'totalCount' => 22,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        if ($export) {
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $result = $command->queryAll();
            return $result;
        } else {
            return $dataProvider;
        }
    }
    public function search_index($params)
    {
       $query = Awp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            'project_id' => $this->project_id,
            'no_of_loans' => $this->no_of_loans,
            'active_loans' => $this->active_loans,
            'monthly_olp' => $this->monthly_olp,
            'avg_loan_size' => $this->avg_loan_size,
            'monthly_closed_loans' => $this->monthly_closed_loans,
            'monthly_recovery' => $this->monthly_recovery,
            'funds_required' => $this->funds_required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'month'=>$this->month
        ]);

        return $dataProvider;
    }
}
