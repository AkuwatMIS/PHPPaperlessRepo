<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BranchRequests;

/**
 * BranchRequestsSearch represents the model behind the search form about `common\models\BranchRequests`.
 */
class BranchRequestsSearch extends BranchRequests
{
    public $region_name;
    public $area_name;
    public $city_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'cr_division_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['type', 'name', 'code', 'uc', 'address', 'description', 'opening_date', 'status', 'remarks',  'created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['region_name', 'area_name', 'city_name'], 'safe'],
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
        $query = BranchRequests::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('city');
        $dataProvider->setSort([
            'attributes' => [
                'region_name' => [
                    'asc' => ['regions.name' => SORT_ASC],
                    'desc' => ['regions.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'area_name' => [
                    'asc' => ['areas.name' => SORT_ASC],
                    'desc' => ['areas.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'city_name' => [
                    'asc' => ['cities.name' => SORT_ASC],
                    'desc' => ['cities.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'code' => [
                    'asc' => ['code' => SORT_ASC],
                    'desc' => ['code' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['status' => SORT_ASC],
                    'desc' => ['status' => SORT_DESC],
                    'default' => SORT_ASC
                ]
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'city_id' => $this->city_id,
            'tehsil_id' => $this->tehsil_id,
            'district_id' => $this->district_id,
            'division_id' => $this->division_id,
            'province_id' => $this->province_id,
            'country_id' => $this->country_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'opening_date' => $this->opening_date,
            'cr_division_id' => $this->cr_division_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'uc', $this->uc])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'branch_requests.status', $this->status])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'regions.name', $this->region_name])
            ->andFilterWhere(['like', 'areas.name', $this->area_name])
            ->andFilterWhere(['like', 'cities.name', $this->city_name]);
        $query->orderBy('created_at desc');
        return $dataProvider;
    }
}
