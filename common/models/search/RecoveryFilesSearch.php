<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RecoveryFiles;

/**
 * RecoveryFilesSearch represents the model behind the search form about `common\models\RecoveryFiles`.
 */
class RecoveryFilesSearch extends RecoveryFiles
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'total_records', 'inserted_records', 'error_records', 'updated_by'], 'integer'],
            [['source', 'description', 'file_date', 'file_name', 'status', 'created_at', 'updated_at'], 'safe'],
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
        if (isset($params['RecoveryFiles']['file_date'])) {
            if ($params['RecoveryFiles']['file_date'] != null) {
                $date = date('Y-m-d', (strtotime($params['RecoveryFiles']['file_date'])));

            }
        }
        $query = RecoveryFiles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10, // Set an appropriate page size
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
            'total_records' => $this->total_records,
            'inserted_records' => $this->inserted_records,
            'error_records' => $this->error_records,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['=', 'file_date', $date])
            ->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('created_at desc');
        return $dataProvider;
    }
}
