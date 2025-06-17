<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanWriteOff;

/**
 * LoanWriteOffSearch represents the model behind the search form of `common\models\LoanWriteOff`.
 */
class LoanWriteOffSearch extends LoanWriteOff
{
    /**
     * {@inheritdoc}
     */
    public $project_id;
    public $region_id;
    public $area_id;
    public $branch_id;
    public $from_date;
    public $to_date;
    public $sanction_no;

    public function rules()
    {
        return [
            [['id', 'recovery_id', 'amount', 'type', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['loan_id','sanction_no','status','cheque_no', 'voucher_no', 'bank_name', 'bank_account_no', 'reason', 'deposit_slip_no','borrower_name','who_will_work','other_name','other_cnic','borrower_cnic','from_date','to_date','write_off_date','sanction_no'], 'safe'],

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
    public function search($params)
    {
        $query = LoanWriteOff::find();
//        $query->innerJoinWith('loan');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->joinWith('loan');
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
           'loan_id' => $this->loan_id,
            'recovery_id' => $this->recovery_id,
            'amount' => $this->amount,
            'type' => $this->type,
            'borrower_cnic' => $this->borrower_cnic,
            'other_cnic' => $this->other_cnic,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
			'loan_write_off.recovery_id' => $this->recovery_id,
            'loan_write_off.amount' => $this->amount,
            'loan_write_off.type' => $this->type,
            'loan_write_off.status' => $this->status,
            'loan_write_off.reason' => $this->reason,
            'loan_write_off.created_by' => $this->created_by,
            'loan_write_off.updated_by' => $this->updated_by,
            'loan_write_off.created_at' => $this->created_at,
            'loan_write_off.updated_at' => $this->updated_at,
            'loan_write_off.voucher_no' => $this->voucher_no,
            'loan_write_off.cheque_no' => $this->cheque_no,
            'loan_write_off.bank_account_no' => $this->bank_account_no,
            'loans.sanction_no' => $this->sanction_no,
        ]);

        $query->andFilterWhere(['like', 'bank_name', $this->bank_name]);


        return $dataProvider;
    }

    public function searchWriteOff($params)
    {
        $query = LoanWriteOff::find()->select([
            'branches.name as branch_name',
            'branches.code as branch_code',
            'areas.name as area_name',
            'projects.name as project_name',
            'regions.name as region_name',
            'members.full_name as member_name',
            'members.gender as gender',
            'loans.sanction_no',
            'loan_write_off.bank_name as account_title',
            'loan_write_off.bank_account_no as account_no',
            'loan_write_off.amount as amount',
            'loan_write_off.other_name as other_name',
            'loan_write_off.other_cnic as other_cnic',
            'loan_write_off.type as type',
            'loan_write_off.reason as reason',
            'loan_write_off.who_will_work as relation',
            'loan_write_off.cheque_no as cheque_no',
            'loan_write_off.deposit_slip_no as deposit_slip_no',
            'loan_write_off.voucher_no as voucher_no',
            'loan_write_off.who_will_work as who_will_work',
            'loan_write_off.status as status',
        ]);

        $query->innerJoinWith('loan');
        $query->innerJoinWith('loan.application');
        $query->innerJoinWith('loan.application.project');
        $query->innerJoinWith('loan.application.region');
        $query->innerJoinWith('loan.application.area');
        $query->innerJoinWith('loan.application.branch');
        $query->innerJoinWith('loan.application.member');

        $this->load($params);

        if(isset($params['LoanWriteOffSearch']['region_id'])) {
            $query->andFilterWhere(['applications.region_id' => $params['LoanWriteOffSearch']['region_id']]);
        }
        if(isset($params['LoanWriteOffSearch']['area_id'])) {
            $query->andFilterWhere(['applications.area_id' => $params['LoanWriteOffSearch']['area_id']]);
        }
        if(isset($params['LoanWriteOffSearch']['branch_id'])) {
            $query->andFilterWhere(['applications.branch_id' => $params['LoanWriteOffSearch']['branch_id']]);
        }
        if(isset($params['LoanWriteOffSearch']['project_id'])) {
            $query->andFilterWhere(['applications.project_id' => $params['LoanWriteOffSearch']['project_id']]);
        }
        if(isset($params['LoanWriteOffSearch']['type'])) {
            $query->andFilterWhere(['loan_write_off.type' => $params['LoanWriteOffSearch']['type']]);
        }
        if(isset($params['LoanWriteOffSearch']['reason'])) {
            $query->andFilterWhere(['loan_write_off.reason' => $params['LoanWriteOffSearch']['reason']]);
        }
        if(isset($params['LoanWriteOffSearch']['bank_name'])) {
            $query->andFilterWhere(['loan_write_off.bank_name' => $params['LoanWriteOffSearch']['bank_name']]);
        }
        if(isset($params['LoanWriteOffSearch']['bank_account_no'])) {
            $query->andFilterWhere(['bank_account_no' => $params['LoanWriteOffSearch']['bank_account_no']]);
        }
        if(isset($params['LoanWriteOffSearch']['status'])) {
            $query->andFilterWhere(['loan_write_off.status' => $params['LoanWriteOffSearch']['status']]);
        }

        if((isset($params['LoanWriteOffSearch']['from_date']) && !empty($params['LoanWriteOffSearch']['from_date'])) && (isset($params['LoanWriteOffSearch']['to_date']) && !empty($params['LoanWriteOffSearch']['to_date']))) {
            $from = strtotime($params['LoanWriteOffSearch']['from_date']);
            $to = strtotime($params['LoanWriteOffSearch']['to_date']);
            $query->andFilterWhere(['between', 'loan_write_off.write_off_date', $from, $to]);
        }
        
        return $query->asArray()->all();
    }
}
