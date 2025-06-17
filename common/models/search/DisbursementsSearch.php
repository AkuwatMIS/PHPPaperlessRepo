<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Disbursements;

/**
 * DisbursementsSearch represents the model behind the search form about `common\models\Disbursements`.
 */
class DisbursementsSearch extends Disbursements
{
    public $region;
    public $area;
    public $branch;
    public $disbursement_date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date_disbursed', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','region_id', 'area_id', 'branch_id'], 'integer'],
            [['venue', 'deleted'], 'safe'],
            [['region', 'area', 'branch','disbursement_date'], 'safe'],
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
    public function search($params,$export=false)
    {
        /*echo '<pre>';
        print_r(Disbursements::find()->all());
        die('here');*/
        $query = Disbursements::find()->select(['disbursements.id','disbursements.region_id','disbursements.area_id','disbursements.branch_id','date_disbursed','venue','disbursements.created_at','disbursements.deleted']);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
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
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'venue' => [
                    'asc' => ['venue' => SORT_ASC],
                    'desc' => ['venue' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
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
            //'date_disbursed' => $this->date_disbursed,
            'disbursements.region_id' => $this->region_id,
            'disbursements.area_id' => $this->area_id,
            'disbursements.branch_id' => $this->branch_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        if (isset($this->disbursement_date) && !is_null($this->disbursement_date) && strpos($this->disbursement_date, ' - ') !== false) {

            $date = explode(' - ', $this->disbursement_date);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->andFilterWhere(['like', 'venue', $this->venue])
            ->andFilterWhere(['like', 'disbursements.deleted', 0])
            ->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'branches.name', $this->branch]);
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchApi($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=$search;
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Disbursements::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['date_disbursed'])) {
            $query->andFilterWhere(['date_disbursed' => $params['date_disbursed']]);
        }

        if(isset($params['venue'])) {
            $query->andFilterWhere(['venue' => $params['venue']]);
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

        if(isset($params['deleted'])) {
            $query->andFilterWhere(['deleted' => $params['deleted']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => (int)$query->count()
        ];


        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
