<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FilesAccounts;

/**
 * FilesAccountsSearch represents the model behind the search form about `common\models\FilesAccounts`.
 */
class FilesAccountsSearch extends FilesAccounts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','project_id', 'total_records', 'updated_records', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['file_path', 'status', 'error_description','type'], 'safe'],
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
        $query = FilesAccounts::find()->orderBy(['id' => SORT_DESC]);

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
            'total_records' => $this->total_records,
            'updated_records' => $this->updated_records,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'project_id' => $this->project_id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'error_description', $this->error_description]);

        return $dataProvider;
    }
}
