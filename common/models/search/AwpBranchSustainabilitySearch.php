<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpBranchSustainability;

/**
 * AwpBranchSustainabilitySearch represents the model behind the search form of `common\models\AwpBranchSustainability`.
 */
class AwpBranchSustainabilitySearch extends AwpBranchSustainability
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'branch_id', 'branch_code', 'region_id', 'area_id', 'amount_disbursed'], 'integer'],
            [['month','month_from'], 'safe'],
            [['percentage', 'income', 'actual_expense', 'surplus_deficit'], 'number'],
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
       // $query = AwpBranchSustainability::find();
        $query = AwpBranchSustainability::find()
            ->select(['region_id','area_id','branch_id','sum(amount_disbursed) as amount_disbursed','sum(income) as income','sum(actual_expense) as actual_expense'
                ,'sum(surplus_deficit) as surplus_deficit_total']);
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
            'branch_code' => $this->branch_code,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'amount_disbursed' => $this->amount_disbursed,
            'percentage' => $this->percentage,
            'income' => $this->income,
            'actual_expense' => $this->actual_expense,
            'deleted'=>0
            //'surplus_deficit' => $this->surplus_deficit,
        ]);
        if(!isset($this->surplus_deficit) || empty($this->surplus_deficit)){
            //die('a');
            $query->having('surplus_deficit_total < 0');
        }
        //die('b');

        if(empty($params['AwpBranchSustainabilitySearch']['month']) && empty($params['AwpBranchSustainabilitySearch']['month_from']))
        {
            $query->andFilterWhere(['between', 'awp_branch_sustainability.month','2022-06', '2023-06']);
        } else {
            if($this->month_from>=1 && $this->month_from<=6){
                $this->month_from='2023-'.$this->month_from;
            }
            else{
                $this->month_from='2022-'.$this->month_from;
            }
            if($this->month>=1 && $this->month<=6){
                $this->month='2023-'.$this->month;
            }
            else{
                $this->month='2022-'.$this->month;
            }
            $query->andFilterWhere(['between', 'awp_branch_sustainability.month', $this->month_from, $this->month]);
        }
        //$query->andFilterWhere(['like', 'month', $this->month]);
        $query->groupBy('branch_id');
        return $dataProvider;
    }
    public function search_index($params)
    {
        // $query = AwpBranchSustainability::find();
        $query = AwpBranchSustainability::find();
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
            'branch_code' => $this->branch_code,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'amount_disbursed' => $this->amount_disbursed,
            'percentage' => $this->percentage,
            'income' => $this->income,
            'actual_expense' => $this->actual_expense,
            'month'=>$this->month,
            'deleted'=>0
            //'surplus_deficit' => $this->surplus_deficit,
        ]);
        return $dataProvider;
    }
}
