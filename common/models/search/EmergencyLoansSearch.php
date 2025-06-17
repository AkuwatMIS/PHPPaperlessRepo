<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmergencyLoans;

/**
 * EmergencyLoansSearch represents the model behind the search form of `common\models\EmergencyLoans`.
 */
class EmergencyLoansSearch extends EmergencyLoans
{
    public $member_cnic;
    public $member_parentage;
    public $member_name;
    public $sanction_no;
    public $region_id;
    public $area_id;
    public $branch_id;
    public $date_disbursed;
    public $project_id;
    public $province_id;
    public $city_id;
    public $district_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'loan_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['sanction_no', 'member_cnic', 'member_parentage', 'member_name','region_id', 'province_id','city_id','district_id',
              'area_id','branch_id', 'date_disbursed','project_id','donated_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = EmergencyLoans::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('loan');
        //$query->joinWith('loan.region');
        //$query->joinWith('loan.area');
        //$query->joinWith('loan.branch');
        $query->joinWith('loan.application.member');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'emergency_loans.status' => $this->status,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query
            ->andFilterWhere(['like', 'members.full_name',$this->member_name])
            ->andFilterWhere(['like', 'members.cnic',$this->member_cnic])
            ->andFilterWhere(['like', 'loans.sanction_no',$this->sanction_no])
            ->andFilterWhere(['like', 'loans.region_id',$this->region_id])
            ->andFilterWhere(['like', 'loans.area_id',$this->area_id])
            ->andFilterWhere(['like', 'loans.branch_id',$this->branch_id])
            ->andFilterWhere(['like', 'loans.project_id',$this->project_id])
            ->andFilterWhere(['like', 'members.parentage',$this->member_parentage])
          ;
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }
//print_r($query->asArray()->all());die();
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function searchCityWise($params,$export=false)
    {
        $query = EmergencyLoans::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('loan');
        //$query->joinWith('loan.branch');
        $query->joinWith('loan.branch.province');
        $query->joinWith('loan.branch.district');
        $query->joinWith('loan.branch.city');
        $query->joinWith('loan.application.member');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'emergency_loans.status' => $this->status,
        ]);
        $query
            ->andFilterWhere(['like', 'members.full_name',$this->member_name])
            ->andFilterWhere(['like', 'members.cnic',$this->member_cnic])
            ->andFilterWhere(['like', 'loans.sanction_no',$this->sanction_no])
            ->andFilterWhere(['like', 'branches.province_id',$this->province_id])
            ->andFilterWhere(['like', 'branches.city_id',$this->city_id])
            ->andFilterWhere(['like', 'branches.district_id',$this->district_id])
            ->andFilterWhere(['like', 'loans.project_id',$this->project_id])
            ->andFilterWhere(['like', 'members.parentage',$this->member_parentage])
        ;
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }
        if (!is_null($this->donated_date) && strpos($this->donated_date, ' - ') !== false) {
            $date = explode(' - ', $this->donated_date);
            $query->andFilterWhere(['between', 'donated_date', ($date[0]), ($date[1].' 23:59:59')]);
        } else {
            $query->andFilterWhere(['donated_date' => $this->donated_date]);
        }
//print_r($query->asArray()->all());die();
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

}
