<?php

namespace common\models\search;

use common\models\AppraisalsLivestock;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AppraisalsAgricultureSearch represents the model behind the search form about `common\models\AppraisalsAgriculture`.
 */
class AppraisalsLivestockSearch extends AppraisalsLivestock
{
    /**
     * @inheritdoc
     */
    public $application_no;
    public function rules()
    {
        return [
            [['id','used_land_size','required_amount','monthly_income','expected_income','deleted','assigned_to','approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['description','animal_type','business_type','business_condition','business_place','business_address','running_capital','new_assets','status','used_land_type'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = AppraisalsLivestock::find();

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
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'business_address', $this->business_address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'deleted', $this->deleted]);

        return $dataProvider;
    }
}
