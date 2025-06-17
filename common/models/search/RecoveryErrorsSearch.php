<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RecoveryErrors;

/**
 * RecoveryErrorsSearch represents the model behind the search form about `common\models\RecoveryErrors`.
 */
class RecoveryErrorsSearch extends RecoveryErrors
{
    public $file_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'recovery_files_id', 'branch_id', 'area_id', 'region_id', 'assigned_to', 'created_by'], 'integer'],
            [['source', 'sanction_no', 'recv_date', 'cnic','receipt_no', 'error_description', 'created_at', 'updated_at', 'status','comments','file_name','bank_branch_name', 'bank_branch_code'], 'safe'],
            [['credit', 'balance'], 'number'],
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
        $date='';
        if (isset($params['RecoveryErrors']['recv_date'])) {
            if ($params['RecoveryErrors']['recv_date'] != null) {
                $date = date('Y-m-d', (strtotime($params['RecoveryErrors']['recv_date'])));

            }
        }

        $query = RecoveryErrors::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('recoveryFile');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'recovery_files_id' => $this->recovery_files_id,
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            'recv_date' => $this->recv_date,
            'credit' => $this->credit,
            'cnic' => $this->cnic,
            'balance' => $this->balance,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'recovery_errors.source', $this->source])
            ->andFilterWhere(['like', 'bank_branch_name', $this->bank_branch_name])
            ->andFilterWhere(['like', 'bank_branch_code', $this->bank_branch_code])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['=', 'recv_date', $date])
            ->andFilterWhere(['like', 'recoveryFile.file_name', $this->file_name])
            ->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['like', 'cnic', $this->cnic])
            ->andFilterWhere(['like', 'error_description', $this->error_description])
            ->andFilterWhere(['like', 'recovery_errors.status', $this->status]);

        return $dataProvider;
    }
}
