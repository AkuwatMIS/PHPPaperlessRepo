<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MemberInfo;

/**
 * MemberInfoSearch represents the model behind the search form about `common\models\MemberInfo`.
 */
class MemberInfoSearch extends MemberInfo
{
    /**
     * @inheritdoc
     */
    public $cnic;
    public function rules()
    {
        return [
            [['id', 'member_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['disability_nature','disability_details','cnic_expiry_date', 'cnic_issue_date', 'mother_name','cnic'], 'safe'],
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
        $query = MemberInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->joinWith('member');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'members.cnic' => $this->cnic,
            'cnic_expiry_date' => $this->cnic_expiry_date,
            'cnic_issue_date' => $this->cnic_issue_date,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'mother_name', $this->mother_name]);

        return $dataProvider;
    }
}
