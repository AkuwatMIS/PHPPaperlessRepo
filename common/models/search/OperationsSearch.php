<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Operations;

/**
 * OperationsSearch represents the model behind the search form about `common\models\Operations`.
 */
class OperationsSearch extends Operations
{
    public $project_ids;
    public $crop_type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'application_id', 'loan_id', 'operation_type_id', 'branch_id', 'team_id', 'field_id', 'project_id', 'region_id', 'area_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['credit'], 'number'],
            [['form_no', 'receipt_no', 'receive_date', 'deleted', 'created_at', 'updated_at'], 'safe'],
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
        $query = Operations::find();

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
            'loan_id' => $this->loan_id,
            'operation_type_id' => $this->operation_type_id,
            'credit' => $this->credit,
            'receive_date' => $this->receive_date,
            'branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'field_id' => $this->field_id,
            //'transaction_id' => $this->transaction_id,
            'project_id' => $this->project_id,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'receipt_no', $this->receipt_no]);

        return $dataProvider;
    }
}
