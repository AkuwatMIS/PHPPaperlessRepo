<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppraisalsHousing;

/**
 * AppraisalsHousingSearch represents the model behind the search form about `common\models\AppraisalsHousing`.
 */
class AppraisalsHousingSearch extends AppraisalsHousing
{
    /**
     * @inheritdoc
     */
    public $application_no;
    public function rules()
    {
        return [
            [['id', 'application_id', 'living_duration', 'no_of_rooms', 'no_of_kitchens', 'no_of_toilets',/* 'estimated_start_date', 'estimated_completion_time', */'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['property_type', 'ownership', 'duration_type', 'address', /*'estimated_figures',*/ 'housing_appraisal_address', 'description', 'description_image', 'status', 'is_lock', 'deleted', 'platform','application_no'], 'safe'],
            [['land_area', 'residential_area', 'purchase_price', 'current_price', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
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
        $query = AppraisalsHousing::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('application');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'land_area' => $this->land_area,
            'residential_area' => $this->residential_area,
            'living_duration' => $this->living_duration,
            'no_of_rooms' => $this->no_of_rooms,
            'no_of_kitchens' => $this->no_of_kitchens,
            'no_of_toilets' => $this->no_of_toilets,
            'purchase_price' => $this->purchase_price,
            'current_price' => $this->current_price,
            //'estimated_start_date' => $this->estimated_start_date,
            //'estimated_completion_time' => $this->estimated_completion_time,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'bm_verify_latitude' => $this->bm_verify_latitude,
            'bm_verify_longitude' => $this->bm_verify_longitude,
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'property_type', $this->property_type])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'ownership', $this->ownership])
            ->andFilterWhere(['like', 'duration_type', $this->duration_type])
            ->andFilterWhere(['like', 'address', $this->address])
            //->andFilterWhere(['like', 'estimated_figures', $this->estimated_figures])
            ->andFilterWhere(['like', 'housing_appraisal_address', $this->housing_appraisal_address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'description_image', $this->description_image])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'platform', $this->platform]);

        return $dataProvider;
    }
}
