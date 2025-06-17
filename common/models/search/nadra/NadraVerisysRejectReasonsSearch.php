<?php

namespace common\models\search\nadra;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NadraVerisysRejectReasons;

/**
 * AnalyticsSearch represents the model behind the search form about `common\models\Analytics`.
 */
class NadraVerisysRejectReasonsSearch extends NadraVerisysRejectReasons
{
    public $fullname;
    public $cnic;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_info_id', 'rejected_date'], 'required'],
            [['status', 'reject_reason', 'remarks'], 'safe'],
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
        $query = NadraVerisysRejectReasons::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination = ['pageSize' => 50];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere(['=', 'users.status', 1]);

       return $dataProvider;
    }

}
