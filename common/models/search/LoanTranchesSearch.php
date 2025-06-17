<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanTranches;

/**
 * LoanTranchesSarch represents the model behind the search form about `common\models\LoanTranches`.
 */
class LoanTranchesSearch extends LoanTranches
{
    /**
     * @inheritdoc
     */
    public $sanction_no;

    public function rules()
    {
        return [
            [['id', 'loan_id', 'tranch_no', 'date_disbursed', 'disbursement_id', 'cheque_date', 'fund_request_id', 'tranch_date', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['tranch_amount'], 'number'],
            [['cheque_no', 'status', 'deleted', 'platform','sanction_no'], 'safe'],
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
        $query = LoanTranches::find();

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
            'loan_id' => $this->loan_id,
            'tranch_no' => $this->tranch_no,
            'tranch_amount' => $this->tranch_amount,
            'date_disbursed' => $this->date_disbursed,
            'disbursement_id' => $this->disbursement_id,
            'cheque_date' => $this->cheque_date,
            'fund_request_id' => $this->fund_request_id,
            'tranch_date' => $this->tranch_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'platform', $this->platform]);

        return $dataProvider;
    }

    public function searchTranche($params)
    {
        $query = LoanTranches::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('loan');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'tranch_no' => $this->tranch_no,
            'tranch_amount' => $this->tranch_amount
        ]);
        $query->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no]);

        return $dataProvider;
    }
}
