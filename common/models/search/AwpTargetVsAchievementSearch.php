<?php

namespace common\models\search;

use common\models\Awp;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpTargetVsAchievement;

/**
 * AwpTargetVsAchievementSearch represents the model behind the search form of `common\models\AwpTargetVsAchievement`.
 */
class AwpTargetVsAchievementSearch extends AwpTargetVsAchievement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'branch_id', 'project_id', 'target_loans', 'target_amount', 'achieved_loans', 'achieved_amount', 'loans_dif', 'amount_dif'], 'integer'],
            [['month','month_from'], 'safe'],
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


    public function search($params,$export=false)
    {


        $cond = $awp_cond = '';
        $project_cond = '';
        $awp_project = '';
        if(empty($params['AwpTargetVsAchievementSearch']['month']) && empty($params['AwpTargetVsAchievementSearch']['month_from']))
        {
            //$cond .= ' and l.datedisburse between "2019-01-01" and "'.date('Y-m-t').'"';
            $awp_cond .= ' and m.month between "2021-07" and "'."2022-05".'"';
        } else {
            $date1  = $params['AwpTargetVsAchievementSearch']['month_from'];
            $date2  = $params['AwpTargetVsAchievementSearch']['month'];

            if($date1>=7 && $date1<=12){
                $date1='2022-'.$date1;
            }
            else{
                $date1='2023-'.$date1;
            }

            if($date2>=7 && $date2<=12){
                $date2='2022-'.$date2;
            }
            else{
                $date2='2023-'.$date2;
            }


            $output = [];
            $time   = strtotime($date1);
            $last   = date('Y-m', strtotime($date2));
            $months = '';
            do {
                $month = date('Y-m', $time);
                $months .= "'".$month."',";
                $output[] = $month;
                $time = strtotime('+1 month', $time);
            } while ($month != $last);

            //$cond .= ' and p.report_date in ('.trim($months,',').')';
            $cond .= ' and l.date_disbursed between '.strtotime(date('Y-m-01',strtotime($date1))).' and '.strtotime(date('Y-m-t',strtotime($date2)));
            $awp_cond .= ' and m.month between "'.date('Y-m',strtotime($date1)).'" and "'.date('Y-m',strtotime($date2)).'"';
        }
        if(!empty($params['AwpTargetVsAchievementSearch']['project_id']) && !empty($params['AwpTargetVsAchievementSearch']['project_id']))
        {
            $project_cond .= ' and m.project_id = '.$params['AwpTargetVsAchievementSearch']['project_id'];
            $awp_project .= ' and m.project_id = '.$params['AwpTargetVsAchievementSearch']['project_id'];
        }
        $query = AwpTargetVsAchievement::find()
            ->select(['awp_target_vs_achievement.region_id',
                'awp_target_vs_achievement.area_id',
                'awp_target_vs_achievement.branch_id',
                '@target_loans:=sum(awp_target_vs_achievement.target_loans) as target_loans',
                '@target_amount:=sum(awp_target_vs_achievement.target_amount) as target_amount',
                //'sum(awp.no_of_loans) as target_loans',
                //'sum(awp.disbursement_amount) as target_amount',
                //'(select coalesce(sum(m.no_of_loans),0) from awp m where m.branch_id = awp_target_vs_achievement.branch_id '.$awp_project.$awp_project.' group by branch_id) as target_loans',
                //'(select coalesce(sum(m.disbursement_amount),0) from awp m where m.branch_id = awp_target_vs_achievement.branch_id '.$awp_project.$awp_cond.' group by branch_id) as target_amount',
                //'(select coalesce(count(l.loan_amount),0) from loans l where l.branch_id = awp.branch_id '.$cond.$project_cond.') as achieved_loans',
                //'(select coalesce(sum(l.loan_amount),0) from loans l where l.branch_id = awp.branch_id '.$cond.$project_cond.') as achieved_amount',
                '@achieved_loans:=sum(achieved_loans) as achieved_loans',
                '@achieved_amount:=sum(achieved_amount) as achieved_amount',
                //'(sum(achieved_loans) - sum(awp.no_of_loans)) as loans_dif',
                //'(sum(achieved_amount) - sum(awp.disbursement_amount)) as amount_dif']),
                '(@achieved_loans - @target_loans) as amount_dif',
                '(@achieved_amount - @target_amount) as loans_dif'
                //'(sum(achieved_amount) - (select coalesce(sum(m.disbursement_amount),0) from awp m where m.branch_id = awp_target_vs_achievement.branch_id '.$awp_project.$awp_cond.' group by branch_id)) as amount_dif',
                //'(sum(achieved_loans) - (select coalesce(sum(m.no_of_loans),0) from awp m where m.branch_id = awp_target_vs_achievement.branch_id '.$awp_project.$awp_cond.' group by branch_id)) as loans_dif',
            ]);

        //$query->joinWith('awp');

       // $query = AwpTargetVsAchievement::find()->select('(sum(target_loans)) as target_loans');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/
        /*echo'<pre>';
        print_r($dataProvider->getModels());
        die();*/
        $dataProvider->pagination->pageSize=50;
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'awp_target_vs_achievement.region_id' => $this->region_id,
            'awp_target_vs_achievement.area_id' => $this->area_id,
            'awp_target_vs_achievement.branch_id' => $this->branch_id,
            'awp_target_vs_achievement.project_id' => $this->project_id,
            'target_loans' => $this->target_loans,
            'target_amount' => $this->target_amount,
            'achieved_loans' => $this->achieved_loans,
            'achieved_amount' => $this->achieved_amount,
            'loans_dif' => $this->loans_dif,
            'amount_dif' => $this->amount_dif,
            'deleted' => 0,
        ]);


        if(empty($params['AwpTargetVsAchievementSearch']['month']) && empty($params['AwpTargetVsAchievementSearch']['month_from']))
        {
            $query->andFilterWhere(['between', 'awp_target_vs_achievement.month','2022-07', '2023-06']);
        } else {
            $query->andFilterWhere(['between', 'awp_target_vs_achievement.month', $date1, $date2]);
        }
        $query->groupBy('awp_target_vs_achievement.branch_id');

//        print_r($query->createCommand()->getRawSql());
//        die();

        if($export){
            return $query->all();
        }else{
            return $dataProvider;
        }
    }

    public function search_($params)
    {
        $query = Awp::find()
            ->select(['region_id','area_id','branch_id','sum(no_of_loans) as target_loans','sum(amount_disbursed) as target_amount','sum(achieved_loans) as achieved_loans'
                ,'@month := month as month',
                '@achieved_amount := (SELECT COUNT(loans.*) from loans where loans.branch_id = awp.branch_id and loans.dsb_status != "Not Collected" and loans.datedisburse BETWEEN '. date("Y-m-01", strtotime('@month')) .' and '. date("Y-m-t", strtotime('month')).') as achieved_amount',
                '@achieved_loans := (coalesce(sum(loans.amountapproved), 0) from loans where loans.branch_id = awp.branch_id and loans.dsb_status != "Not Collected" and loans.datedisburse BETWEEN '. date("Y-m-01", strtotime('month')) .' and '. date("Y-m-t", strtotime('month')).') as achieved_loans'
            ]);

        // $query = AwpTargetVsAchievement::find()->select('(sum(target_loans)) as target_loans');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        /*echo'<pre>';
        print_r($dataProvider->getModels());
        die();*/
        $dataProvider->pagination->pageSize=50;
        // grid filtering conditions
        /* $query->andFilterWhere([
             'id' => $this->id,
             'region_id' => $this->region_id,
             'area_id' => $this->area_id,
             'branch_id' => $this->branch_id,
             'project_id' => $this->project_id,
             'target_loans' => $this->target_loans,
             'target_amount' => $this->target_amount,
             'achieved_loans' => $this->achieved_loans,
             'achieved_amount' => $this->achieved_amount,
             'loans_dif' => $this->loans_dif,
             'amount_dif' => $this->amount_dif,
         ]);*/

        $query->andFilterWhere(['like', 'month', $this->month]);
        //$query->groupBy('branch_id');
        print_r($query->asArray()->all());
        die();
        // $query->limit(3);
        return $dataProvider;
    }
    
    public function search_index($params)
    {
        $query = AwpTargetVsAchievement::find();
        // $query = AwpTargetVsAchievement::find()->select('(sum(target_loans)) as target_loans');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        /*echo'<pre>';
        print_r($dataProvider->getModels());
        die();*/
        $dataProvider->pagination->pageSize=50;
        // grid filtering conditions
         $query->andFilterWhere([
             'id' => $this->id,
             'region_id' => $this->region_id,
             'area_id' => $this->area_id,
             'branch_id' => $this->branch_id,
             'project_id' => $this->project_id,
             'target_loans' => $this->target_loans,
             'month' => $this->month,
             'target_amount' => $this->target_amount,
             'achieved_loans' => $this->achieved_loans,
             'achieved_amount' => $this->achieved_amount,
             'loans_dif' => $this->loans_dif,
             'amount_dif' => $this->amount_dif,
         ]);
        return $dataProvider;
    }
}
