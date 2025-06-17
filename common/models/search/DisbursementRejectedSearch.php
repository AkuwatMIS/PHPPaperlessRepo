<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DisbursementRejected;

/**
 * DisbursementRejectedSearch represents the model behind the search form of `common\models\DisbursementRejected`.
 */
class DisbursementRejectedSearch extends DisbursementRejected
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id' ,'disbursement_detail_id', 'deposit_amount', 'created_by', 'is_verified', 'verified_by', 'verfied_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['reject_reason', 'deposit_slip_no','deposit_date', 'deposit_bank','file','file_path','borrower_name','borrower_cnic','sanction_no','loan_amount'], 'safe'],
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
        $query = DisbursementRejected::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'disbursement_detail_id' => $this->disbursement_detail_id,
            'deposit_date' => $this->deposit_date,
            'deposit_amount' => $this->deposit_amount,
            'loan_amount' => $this->loan_amount,
            'created_by' => $this->created_by,
            'is_verified' => $this->is_verified,
            'verified_by' => $this->verified_by,
            'verfied_at' => $this->verfied_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => 0
        ]);

        $query->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'borrower_name', $this->borrower_name])
            ->andFilterWhere(['like', 'borrower_cnic', $this->borrower_cnic])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'deposit_slip_no', $this->deposit_slip_no])
            ->andFilterWhere(['like', 'deposit_bank', $this->deposit_bank]);

        return $dataProvider;
    }
}
