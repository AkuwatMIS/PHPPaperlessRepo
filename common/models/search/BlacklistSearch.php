<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Blacklist;

/**
 * BlacklistSearch represents the model behind the search form about `common\models\Blacklist`.
 */
class BlacklistSearch extends Blacklist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['province','cnic','name','parentage','cnic_invalid', 'reason', 'description', 'location', 'type', 'deleted'], 'safe'],
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
    public function search($params,$export=false)
    {
        $query = Blacklist::find();

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
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted'=>0,
        ]);

        $query->andFilterWhere(['like', 'cnic', $this->cnic])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'parentage', $this->parentage])
            ->andFilterWhere(['like', 'cnic', $this->cnic])
            ->andFilterWhere(['like', 'cnic_invalid', $this->cnic_invalid])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['!=', 'reason', 'write-off']);
        $query->andFilterWhere(['or', ['like', 'name', 'Ali'], ['like', 'name', 'Ahmed']]);
//        if (!empty($this->name)) {
//            // Remove number-dot prefixes like "1.John", "2.Jane"
//            $cleaned = preg_replace('/\d+\./', '', $this->name);
//
//            // Split into words based on space or comma
//            $names = preg_split('/[\s,]+/', $cleaned, -1, PREG_SPLIT_NO_EMPTY);
//
//            // Build OR conditions
//            $orConditions = ['or'];
//            foreach ($names as $n) {
//                $orConditions[] = ['like', 'name', $n];
//            }
//
//            // Apply to query
//            if (count($orConditions) > 1) {
//                $query->andFilterWhere($orConditions);
//            }
//        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }

    }
}
