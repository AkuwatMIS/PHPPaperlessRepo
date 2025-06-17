<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Designations as DesignationsModel;

/**
 * Designations represents the model behind the search form about `common\models\Designations`.
 */
class DesignationsSearch extends DesignationsModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sorting', 'network', 'progress_report', 'projects', 'districts', 'products', 'analysis', 'search_loan', 'news', 'maps', 'staff', 'links'], 'integer'],
            [['name', 'desig_label', 'code'], 'safe'],
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
        $query = DesignationsModel::find();

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
            'sorting' => $this->sorting,
            'network' => $this->network,
            'progress_report' => $this->progress_report,
            'projects' => $this->projects,
            'districts' => $this->districts,
            'products' => $this->products,
            'analysis' => $this->analysis,
            'search_loan' => $this->search_loan,
            'news' => $this->news,
            'maps' => $this->maps,
            'staff' => $this->staff,
            'links' => $this->links,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'desig_label', $this->desig_label])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
