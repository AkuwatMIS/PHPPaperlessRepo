<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpOverdue;

/**
 * AwpOverdueSearch represents the model behind the search form of `common\models\AwpOverdue`.
 */
class AwpOverdueSearch extends AwpOverdue
{
    /**
     * @inheritdoc
     */
    public $date_of_opening;

    public function rules()
    {
        return [
            [['id', 'branch_id', 'area_id', 'region_id', 'overdue_numbers', 'overdue_amount', 'awp_active_loans', 'awp_olp', 'active_loans', 'olp', 'diff_active_loans', 'diff_olp'], 'integer'],
            [['month','month_from', 'date_of_opening','def_recovered','write_off_amount_new','write_off_loans_new'], 'safe'],
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
        // $query = AwpOverdue::find();
        $query = AwpOverdue::find()
            ->select(['branch_id as id','region_id',/*'date_of_opening',*/'area_id','branch_id','sum(overdue_numbers) as overdue_numbers','sum(overdue_amount) as overdue_amount','sum(awp_active_loans) as awp_active_loans','month','def_recovered', 'write_off_amount_new','write_off_loans_new',
                'sum(awp_olp) as awp_olp','sum(active_loans) as active_loans','sum(olp) as olp','sum(diff_active_loans) as diff_active_loans','sum(diff_olp) as diff_olp','sum(write_off_amount) as write_off_amount','sum(write_off_recovered) as write_off_recovered',
                //'(select coalesce(count(amount),0) from recoveries r where r.branch_id = awp_overdue.branch_id and r.source = "WROFF" and r.deleted=0) as writeoff_count',
                //'(select coalesce(sum(amount),0) from recoveries r where r.branch_id = awp_overdue.branch_id and r.source = "WROFF" and r.deleted=0) as writeoff_amount'
            ]);
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
        $dataProvider->pagination->pageSize=50;
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
//            'month'=>'2021-12',
            //'date_of_opening' => $this->date_of_opening,
            'overdue_numbers' => $this->overdue_numbers,
            'overdue_amount' => $this->overdue_amount,
            'awp_active_loans' => $this->awp_active_loans,
            'awp_olp' => $this->awp_olp,
            'active_loans' => $this->active_loans,
            'olp' => $this->olp,
            'diff_active_loans' => $this->diff_active_loans,
            'diff_olp' => $this->diff_olp,
            'deleted' => 0,
        ]);

        if(empty($params['AwpOverdueSearch']['month']))
        {
            $query->andFilterWhere(['month'=>'2022-12']);
        } else {
            $date  = $params['AwpOverdueSearch']['month'];
            if($date>=7 && $date<=12){
                $this->month='2022-'.$date;
            }
            else{
                $this->month='2023-'.$date;
            }

            $query->andFilterWhere(['month'=>$this->month]);
        }

        //$query->andFilterWhere(['like', 'month', $this->month]);
        $query->groupBy('branch_id');

        return $dataProvider;
    }
    public function searchMonth($params)
    {
        // $query = AwpOverdue::find();
        $query = AwpOverdue::find()
            ->select(['branch_id as id','region_id',/*'date_of_opening',*/'area_id','branch_id','sum(overdue_numbers) as overdue_numbers','sum(overdue_amount) as overdue_amount','sum(awp_active_loans) as awp_active_loans','month','def_recovered','write_off_amount_new','write_off_loans_new',
                'sum(awp_olp) as awp_olp','sum(active_loans) as active_loans','sum(olp) as olp','sum(diff_active_loans) as diff_active_loans','sum(diff_olp) as diff_olp','sum(write_off_amount) as write_off_amount','sum(write_off_recovered) as write_off_recovered',
                //'(select coalesce(count(amount),0) from recoveries r where r.branch_id = awp_overdue.branch_id and r.source = "WROFF" and r.deleted=0) as writeoff_count',
                //'(select coalesce(sum(amount),0) from recoveries r where r.branch_id = awp_overdue.branch_id and r.source = "WROFF" and r.deleted=0) as writeoff_amount'

            ]);
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
        $dataProvider->pagination->pageSize=50;
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            'month'=>'2020-07',
            //'date_of_opening' => $this->date_of_opening,
            'overdue_numbers' => $this->overdue_numbers,
            'overdue_amount' => $this->overdue_amount,
            'awp_active_loans' => $this->awp_active_loans,
            'awp_olp' => $this->awp_olp,
            'active_loans' => $this->active_loans,
            'olp' => $this->olp,
            'diff_active_loans' => $this->diff_active_loans,
            'diff_olp' => $this->diff_olp,
        ]);

        //$query->andFilterWhere(['like', 'month', $this->month]);
        $query->groupBy('branch_id');

        return $dataProvider;
    }
}
