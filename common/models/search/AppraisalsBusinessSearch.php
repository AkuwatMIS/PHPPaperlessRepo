<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppraisalsBusiness;

/**
 * AppraisalsBusinessSearch represents the model behind the search form about `common\models\AppraisalsBusiness`.
 */
class AppraisalsBusinessSearch extends AppraisalsBusiness
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'application_id', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['place_of_business', 'fixed_business_assets', 'running_capital', 'business_expenses', 'new_required_assets', 'business_appraisal_address', 'status', 'is_lock', 'deleted', 'platform'], 'safe'],
            [['fixed_business_assets_amount', 'running_capital_amount', 'business_expenses_amount', 'new_required_assets_amount', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
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
        $query = AppraisalsBusiness::find();

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
            'application_id' => $this->application_id,
            'fixed_business_assets_amount' => $this->fixed_business_assets_amount,
            'running_capital_amount' => $this->running_capital_amount,
            'business_expenses_amount' => $this->business_expenses_amount,
            'new_required_assets_amount' => $this->new_required_assets_amount,
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

        $query->andFilterWhere(['like', 'place_of_business', $this->place_of_business])
            ->andFilterWhere(['like', 'fixed_business_assets', $this->fixed_business_assets])
            ->andFilterWhere(['like', 'running_capital', $this->running_capital])
            ->andFilterWhere(['like', 'business_expenses', $this->business_expenses])
            ->andFilterWhere(['like', 'new_required_assets', $this->new_required_assets])
            ->andFilterWhere(['like', 'business_appraisal_address', $this->business_appraisal_address])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'platform', $this->platform]);

        return $dataProvider;
    }
}
