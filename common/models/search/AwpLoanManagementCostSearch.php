<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpLoanManagementCost;

/**
 * AwpLoanManagementCostSearch represents the model behind the search form of `common\models\AwpLoanManagementCost`.
 */
class AwpLoanManagementCostSearch extends AwpLoanManagementCost
{
    /**
     * @inheritdoc
     */
    public $date_of_opening;
    public function rules()
    {
        return [
            [['id', 'branch_id', 'area_id', 'region_id', 'opening_active_loans', 'closing_active_loans', 'average', 'amount', 'lmc'], 'integer'],
            [['date_of_opening'], 'safe'],
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
        $query = AwpLoanManagementCost::find();

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
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            //'date_of_opening' => $this->date_of_opening,
            'opening_active_loans' => $this->opening_active_loans,
            'closing_active_loans' => $this->closing_active_loans,
            'average' => $this->average,
            'amount' => $this->amount,
            'lmc' => $this->lmc,
        ]);

        return $dataProvider;
    }
}
