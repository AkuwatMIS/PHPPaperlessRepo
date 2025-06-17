<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmailsListDetails;

/**
 * EmailsListDetailsSearch represents the model behind the search form about `common\models\EmailsListDetails`.
 */
class EmailsListDetailsSearch extends EmailsListDetails
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'email_list_id', 'status', 'deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['receiver_email'], 'safe'],
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
        $query = EmailsListDetails::find();

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
            'email_list_id' => $this->email_list_id,
            'status' => $this->status,
            'deleted' => $this->deleted,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'receiver_email', $this->receiver_email]);

        return $dataProvider;
    }
}
