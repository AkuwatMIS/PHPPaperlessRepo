<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppraisalsAgriculture;

/**
 * AppraisalsAgricultureSearch represents the model behind the search form about `common\models\AppraisalsAgriculture`.
 */
class AppraisalsAgricultureSearch extends AppraisalsAgriculture
{
    /**
     * @inheritdoc
     */
    public $application_no;
    public function rules()
    {
        return [
            [['id', 'application_id', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['water_analysis', 'soil_analysis', 'laser_level', 'irrigation_source', 'other_source', 'crop_year', 'resources', 'agriculture_appraisal_address', 'description', 'status', 'deleted', 'platform','application_no'], 'safe'],
            [['crop_production', 'expenses', 'available_resources', 'required_resources', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
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
        $query = AppraisalsAgriculture::find();

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
            'crop_production' => $this->crop_production,
            'expenses' => $this->expenses,
            'available_resources' => $this->available_resources,
            'required_resources' => $this->required_resources,
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

        $query->andFilterWhere(['like', 'water_analysis', $this->water_analysis])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'soil_analysis', $this->soil_analysis])
            ->andFilterWhere(['like', 'laser_level', $this->laser_level])
            ->andFilterWhere(['like', 'irrigation_source', $this->irrigation_source])
            ->andFilterWhere(['like', 'other_source', $this->other_source])
            ->andFilterWhere(['like', 'crop_year', $this->crop_year])
            ->andFilterWhere(['like', 'resources', $this->resources])
            ->andFilterWhere(['like', 'agriculture_appraisal_address', $this->agriculture_appraisal_address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'platform', $this->platform]);

        return $dataProvider;
    }
}
