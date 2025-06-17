<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lists;

/**
 * ListsSearch represents the model behind the search form about `common\models\Lists`.
 */
class ListsSearch extends Lists
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['list_name', 'value', 'label', 'sort_order'], 'safe'],
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
        $query = Lists::find();

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
        ]);

        $query->andFilterWhere(['like', 'list_name', $this->list_name])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'sort_order', $this->sort_order]);

        return $dataProvider;
    }

    public function searchList($params)
    {
        $query = Lists::find()->select(['list_name'])->distinct('list_name')->orderBy('list_name');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /*$query->andFilterWhere([
            'id' => $this->id,
        ]);*/

        /*$query->andFilterWhere(['like', 'list_name', $this->list_name])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'sort_order', $this->sort_order]);*/

        return $dataProvider;
    }
}
