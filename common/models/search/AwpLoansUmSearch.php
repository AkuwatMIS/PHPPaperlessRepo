<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpLoansUm;

/**
 * AwpLoansUmSearch represents the model behind the search form of `common\models\AwpLoansUm`.
 */
class AwpLoansUmSearch extends AwpLoansUm
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'branch_id', 'active_loans', 'no_of_um', 'active_loans_per_um'], 'integer'],
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
        $query = AwpLoansUm::find();

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
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'branch_id' => $this->branch_id,
            'active_loans' => $this->active_loans,
            'no_of_um' => $this->no_of_um,
            'active_loans_per_um' => $this->active_loans_per_um,
        ]);

        return $dataProvider;
    }
}
