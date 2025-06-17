<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProjectCharges;

/**
 * ProjectChargesSearch represents the model behind the search form of `common\models\ProjectCharges`.
 */
class ProjectChargesSearch extends ProjectCharges
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'allocated_funds', 'received_funds', 'total_disbursement', 'due_amount', 'received_amount', 'pending_amount', 'received_date', 'status'], 'integer'],
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
        $query = ProjectCharges::find();

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
            'allocated_funds' => $this->allocated_funds,
            'received_funds' => $this->received_funds,
            'total_disbursement' => $this->total_disbursement,
            'due_amount' => $this->due_amount,
            'received_amount' => $this->received_amount,
            'pending_amount' => $this->pending_amount,
            'received_date' => $this->received_date,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
