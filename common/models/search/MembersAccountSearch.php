<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MembersAccount;

/**
 * MembersAccountSearch represents the model behind the search form about `common\models\MembersAccount`.
 */
class MembersAccountSearch extends MembersAccount
{
    /**
     * @inheritdoc
     */
    public $member_cnic;
    public function rules()
    {
        return [
            [['id', 'member_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'status', 'verified_at', 'verified_by', 'deleted','acc_file_id'], 'integer'],
            [['bank_name', 'title', 'account_no', 'is_current','member_cnic'], 'safe'],
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
        $query = MembersAccount::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('member');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'verified_at' => $this->verified_at,
            'verified_by' => $this->verified_by,
            'deleted' => $this->deleted,
            'members.cnic' => $this->member_cnic,
            'is_current' => 1,
        ]);

        $query->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'account_no', $this->account_no])
            ->andFilterWhere(['like', 'is_current', $this->is_current]);

        return $dataProvider;
    }
}
