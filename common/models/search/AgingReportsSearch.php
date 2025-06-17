<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AgingReports;

/**
 * AgingReportsSearch represents the model behind the search form about `common\models\AgingReports`.
 */
class AgingReportsSearch extends AgingReports
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'start_month', 'created_at', 'updated_at','deleted'], 'integer'],
            [['type', 'status'], 'safe'],
            [['one_month', 'next_three_months', 'next_six_months', 'next_one_year', 'next_two_year', 'next_three_year', 'next_five_year', 'total'], 'number'],
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
        $query = AgingReports::find();

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
            'start_month' => $this->start_month,
            'one_month' => $this->one_month,
            'next_three_months' => $this->next_three_months,
            'next_six_months' => $this->next_six_months,
            'next_one_year' => $this->next_one_year,
            'next_two_year' => $this->next_two_year,
            'next_three_year' => $this->next_three_year,
            'next_five_year' => $this->next_five_year,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['in','type',['due','overdue']]);
        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['=', 'deleted', 0]);
        $query->orderBy('created_at desc');

        return $dataProvider;
    }

    public function searchAgingAccount($params)
    {
        $query = AgingReports::find();

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
            'start_month' => $this->start_month,
            'one_month' => $this->one_month,
            'next_three_months' => $this->next_three_months,
            'next_six_months' => $this->next_six_months,
            'next_one_year' => $this->next_one_year,
            'next_two_year' => $this->next_two_year,
            'next_three_year' => $this->next_three_year,
            'next_five_year' => $this->next_five_year,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['in','type',['due_acc','overdue_acc']]);
        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['=', 'deleted', 0]);
        $query->orderBy('created_at desc');

        return $dataProvider;
    }
}
