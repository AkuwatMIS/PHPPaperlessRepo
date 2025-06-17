<?php

namespace common\models\search\analytics;

use common\models\Users;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Analytics;

/**
 * AnalyticsSearch represents the model behind the search form about `common\models\Analytics`.
 */
class AnalyticsReportSearch extends Analytics
{
    public $email;
    public $fullname;
    public $designation;
    public $region;
    public $area;
    public $branch;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'count', 'deleted'], 'integer'],
            [['api', 'description', 'created_at', 'updated_at','email','fullname','designation','region','area','branch','users_count'], 'safe'],
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
    public function search_analytics($params)
    {
        $query = Analytics::find()
            ->select(['analytics.id','api','sum(count) as count',
                'count(api) as users_count'
            ]);
        $query->joinWith('user');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'api' => [
                    'asc' => ['api' => SORT_ASC],
                    'desc' => ['api' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'count' => [
                    'asc' => ['count' => SORT_ASC],
                    'desc' => ['count' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'description' => [
                    'asc' => ['description' => SORT_ASC],
                    'desc' => ['description' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'updated_at' => [
                    'asc' => ['updated_at' => SORT_ASC],
                    'desc' => ['updated_at' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'deleted' => [
                    'asc' => ['deleted' => SORT_ASC],
                    'desc' => ['deleted' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'dt_applied' => [
                    'asc' => ['dt_applied' => SORT_ASC],
                    'desc' => ['dt_applied' => SORT_DESC],
                    'default' => SORT_ASC
                ]
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
            'user_id' => $this->user_id,
            'count' => $this->count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
            'type'=>'reports',
        ]);

        $query->andFilterWhere(['like', 'api', $this->api])
            ->andFilterWhere(['like', 'description', $this->description]);
        $query->groupBy('api');
        /*echo '<pre>';
        print_r($query->all());*/

        return $dataProvider;
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
        $query = Users::find();
        $query->joinWith('designation');
        $query->joinWith('analytics');
        $query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination = ['pageSize' => 50];
        $dataProvider->setSort([
            'attributes' => [
                'email' => [
                    'asc' => ['email' => SORT_ASC],
                    'desc' => ['email' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'fullname' => [
                    'asc' => ['fullname' => SORT_ASC],
                    'desc' => ['fullname' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'designation' => [
                    'asc' => ['designation.name' => SORT_ASC],
                    'desc' => ['designation.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'region' => [
                    'asc' => ['region.name' => SORT_ASC],
                    'desc' => ['region.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'area' => [
                    'asc' => ['area.name' => SORT_ASC],
                    'desc' => ['area.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'branch' => [
                    'asc' => ['branch.name' => SORT_ASC],
                    'desc' => ['branch.name' => SORT_DESC],
                    'default' => SORT_ASC
                ]

            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //$query->where(['IS NOT', 'analytics.user_id', null]);
        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query
            //->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'designations.desig_label', $this->designation])
            //->andFilterWhere(['like', 'areas.name', $this->area])
            //->andFilterWhere(['like', 'branches.name', $this->branch])
            ->andFilterWhere(['=', 'users.status', 1])
            ->andFilterWhere(['=', 'users.is_block', 0])
            ->andFilterWhere(['=', 'analytics.type','reports'])
            ->andFilterWhere(['not in', 'designations.code', array('LO','DEO','AA','BM','RC','RA','ITE','DA')]);

        //$query->groupBy('user_id');
        return $dataProvider;
    }
}
