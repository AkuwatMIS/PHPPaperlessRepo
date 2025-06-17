<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FundRequests;

/**
 * FundRequestsSearch represents the model behind the search form about `common\models\FundRequests`.
 */
class FundRequestsSearch extends FundRequests
{
    public $region;
    public $area;
    public $branch;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'branch_id', 'approved_by', 'approved_on', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['requested_amount','approved_amount'], 'number'],
            [['deleted'], 'safe'],
            [['region', 'area', 'branch','status'], 'safe'],
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
        $query = FundRequests::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $dataProvider->setSort([
            'attributes' => [
                'region' => [
                    'asc' => ['regions.name' => SORT_ASC],
                    'desc' => ['regions.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'area' => [
                    'asc' => ['areas.name' => SORT_ASC],
                    'desc' => ['areas.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'branch' => [
                    'asc' => ['branch.name' => SORT_ASC],
                    'desc' => ['branch.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'requested_amount' => [
                    'asc' => ['requested_amount' => SORT_ASC],
                    'desc' => ['requested_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'approved_amount' => [
                    'asc' => ['approved_amount' => SORT_ASC],
                    'desc' => ['approved_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['status' => SORT_ASC],
                    'desc' => ['status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'created_at' => SORT_DESC
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
            'fund_requests.region_id' => $this->region_id,
            'fund_requests.area_id' => $this->area_id,
            'fund_requests.branch_id' => $this->branch_id,
            'requested_amount' => $this->requested_amount,
            'approved_amount' => $this->approved_amount,
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'fund_requests.status'=>$this->status,
            'fund_requests.deleted'=>0
        ]);

        $query->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'branches.name', $this->branch]);
        $query->orderBy('fund_requests.created_at desc');
        return $dataProvider;
    }
    public function searchCount($params)
    {
        $query = FundRequests::find()->select('count(fund_requests.id),fund_requests.status');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fund_requests.region_id' => $this->region_id,
            'fund_requests.area_id' => $this->area_id,
            'fund_requests.branch_id' => $this->branch_id,
            'requested_amount' => $this->requested_amount,
            'approved_amount' => $this->approved_amount,
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
           // 'fund_requests.status'=>$this->status,
            'fund_requests.deleted'=>0
        ]);

        $query->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'branches.name', $this->branch]);
        $query->groupBy('fund_requests.status');
        //print_r($query->asArray()->all());die();
        //$result= $query->asArray()->all();
        return $query;
    }
    public function searchApi($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=array_merge($params,$search);
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = FundRequests::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['region_id'])) {
            $query->andFilterWhere(['region_id' => $params['region_id']]);
        }

        if(isset($params['area_id'])) {
            $query->andFilterWhere(['area_id' => $params['area_id']]);
        }

        if(isset($params['branch_id'])) {
            $query->andFilterWhere(['branch_id' => $params['branch_id']]);
        }

        if(isset($params['requested_amount'])) {
            $query->andFilterWhere(['requested_amount' => $params['requested_amount']]);
        }

        if(isset($params['approved_amount'])) {
            $query->andFilterWhere(['approved_amount' => $params['approved_amount']]);
        }

        if(isset($params['approved_by'])) {
            $query->andFilterWhere(['approved_by' => $params['approved_by']]);
        }

        if(isset($params['approved_on'])) {
            $query->andFilterWhere(['approved_on' => $params['approved_on']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        $query->orderBy('created_at desc');
        if(isset($order)){
            $query->orderBy($order);
        }

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil((int)$query->count()/$limit),
            'totalRecords' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
