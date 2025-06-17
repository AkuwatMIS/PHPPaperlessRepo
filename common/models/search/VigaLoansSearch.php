<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VigaLoans;

/**
 * VigaLoansSearch represents the model behind the search form about `common\models\VigaLoansSearch`.
 */
class VigaLoansSearch extends VigaLoans
{
    public $sanction_no;
    public $group_no;
    public $member_cnic;
    public $member_parentage;
    public $member_name;
    public $date_disbursed;
    //public $date_disbursed;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'loan_id'], 'integer'],
            [['status','member_name','member_parentage','member_cnic','date_disbursed','group_no','sanction_no','created_by', 'updated_by', 'created_at', 'updated_at','is_sync'], 'safe'],
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
        $query = VigaLoans::find();
        if(!isset($params['VigaLoansSearch']['is_sync'])){
            $this->is_sync = 0;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('loan');
        $query->joinWith('loan.group');
        $query->joinWith('loan.application.member');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'viga_loans.id' => $this->id,
            'viga_loans.loan_id' => $this->loan_id,
            'viga_loans.created_by' => $this->created_by,
            'viga_loans.updated_by' => $this->updated_by,
            'viga_loans.created_at' => $this->created_at,
            'viga_loans.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'viga_loans.status', $this->status])
              ->andFilterWhere(['like', 'is_sync', $this->is_sync])
              ->andFilterWhere(['like', 'groups.grp_no', $this->group_no])
              ->andFilterWhere(['like', 'loans.sanction_no', $this->sanction_no])
              ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
              ->andFilterWhere(['like', 'members.full_name', $this->member_name])
              ->andFilterWhere(['like', 'members.parentage', $this->member_parentage]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['like','loans.date_disbursed' , strtotime($this->date_disbursed)]);
        }

        return $dataProvider;
    }
}
