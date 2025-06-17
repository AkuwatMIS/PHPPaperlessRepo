<?php

namespace common\models\search;

use common\models\TemporaryDisbursementRejected;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DisbursementRejectedSearch represents the model behind the search form of `common\models\DisbursementRejected`.
 */
class TemporaryDisbursementRejectedSearch extends TemporaryDisbursementRejected
{
    public $file;
    public $borrower_account_no;
    public $tranch_amount;
    public $project_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'disbursement_detail_id', 'created_by', 'is_verified', 'verified_by', 'verfied_at', 'status', 'created_at', 'updated_at','tranche_no'], 'integer'],
            [['reject_reason','file','file_path','tranch_amount','borrower_account_no','project_name'], 'safe'],
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
        $query = TemporaryDisbursementRejected::find();

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
            'disbursement_detail_id' => $this->disbursement_detail_id,
            'tranche_no' => $this->tranche_no,
            'created_by' => $this->created_by,
            'is_verified' => $this->is_verified,
            'verified_by' => $this->verified_by,
            'verfied_at' => $this->verfied_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => 0
        ]);

        $query->andFilterWhere(['like', 'reject_reason', $this->reject_reason]);

        return $dataProvider;
    }
}
