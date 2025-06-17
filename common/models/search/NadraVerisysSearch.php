<?php

namespace common\models\search;

use common\models\NadraVerisys;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountTypesSearch represents the model behind the search form about `common\models\AccountTypes`.
 */
class NadraVerisysSearch extends NadraVerisys
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'application_id', 'created_at', 'updated_at'], 'integer'],
            [['document_type', 'document_name', 'status', 'deleted', 'deleted_by'], 'safe'],
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
        $query = NadraVerisys::find();

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
            'member_id' => $this->member_id,
            'application_id' => $this->application_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'document_type', $this->document_type])
            ->andFilterWhere(['like', 'document_name', $this->document_name]);

        return $dataProvider;
    }
}
