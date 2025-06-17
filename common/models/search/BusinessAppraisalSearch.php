<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppraisalsBusiness;

/**
 * BusinessAppraisalSearch represents the model behind the search form about `common\models\AppraisalsBusiness`.
 */
class BusinessAppraisalSearch extends AppraisalsBusiness
{
    /**
     * @inheritdoc
     */
    public $member_name;
    public $member_cnic;
    public $application_no;
    public $region_id;
    public $area_id;
    public $branch_id;
    public $team_id;
    public $field_id;
    public function rules()
    {
        return [
            [['id', 'application_id', 'approved_by', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['business_type', 'place_of_business',/*'business_details',*/ 'status', 'approved_on', 'created_at', 'updated_at','member_name','member_cnic','application_no','region_id','area_id','branch_id','team_id','field_id','application_no'], 'safe'],
            [[/*'business_income', 'job_income', 'house_rent_income', 'other_income', 'estimated_business_capital', 'business_expenses', 'income_before_business', 'total_business_income',*/'latitude', 'longitude'], 'number'],
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
        $query = AppraisalsBusiness::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('application.member');
        $query->joinWith('application.region');
        $query->joinWith('application.area');
        $query->joinWith('application.branch');
        $query->joinWith('application.team');
        $query->joinWith('application.field');
        $dataProvider->setSort([
            'attributes' => [
                'member_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'application_no' => [
                    'asc' => ['applications.application_no' => SORT_ASC],
                    'desc' => ['applications.application_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],

                'place_of_business' => [
                    'asc' => ['place_of_business' => SORT_ASC],
                    'desc' => ['place_of_business' => SORT_DESC],
                    'default' => SORT_ASC
                ],
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
            'application_id' => $this->application_id,
            // 'business_income' => $this->business_income,
            //'job_income' => $this->job_income,
            //'house_rent_income' => $this->house_rent_income,
            //'other_income' => $this->other_income,
            //'estimated_business_capital' => $this->estimated_business_capital,
            //'business_expenses' => $this->business_expenses,
            //'income_before_business' => $this->income_before_business,
            //'total_business_income' => $this->total_business_income,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'place_of_business', $this->place_of_business])
           // ->andFilterWhere(['like', 'business_details', $this->business_details])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'regions.id', $this->region_id])
            ->andFilterWhere(['like', 'areas.id', $this->area_id])
            ->andFilterWhere(['like', 'branches.id', $this->branch_id])
            ->andFilterWhere(['like', 'teams.id', $this->team_id])
            ->andFilterWhere(['like', 'fields.id', $this->field_id]);
        if (!Yii::$app->request->get('sort')) {
            $query->orderBy('created_at desc');
        }

        return $dataProvider;
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

        $query = AppraisalsBusiness::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        /*$query->joinWith('baBusinessExpenses');
        $query->joinWith('baExistingInvestments');
        $query->joinWith('baFixedBusinessAssets');
        $query->joinWith('baRequiredAssets');*/

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['application_id'])) {
            $query->andFilterWhere(['application_id' => $params['application_id']]);
        }

        if(isset($params['place_of_business'])) {
            $query->andFilterWhere(['place_of_business' => $params['place_of_business']]);
        }

        if(isset($params['business_type'])) {
            $query->andFilterWhere(['business_type' => $params['business_type']]);
        }

        if(isset($params['business_details'])) {
            $query->andFilterWhere(['like', 'business_details' , $params['business_details']]);
        }

        if(isset($params['business_income'])) {
            $query->andFilterWhere(['business_income' => $params['business_income']]);
        }

        if(isset($params['job_income'])) {
            $query->andFilterWhere(['job_income' => $params['job_income']]);
        }

        if(isset($params['house_rent_income'])) {
            $query->andFilterWhere(['house_rent_income' => $params['house_rent_income']]);
        }

        if(isset($params['other_income'])) {
            $query->andFilterWhere(['other_income' => $params['other_income']]);
        }

        if(isset($params['estimated_business_capital'])) {
            $query->andFilterWhere(['estimated_business_capital' => $params['estimated_business_capital']]);
        }

        if(isset($params['business_expenses'])) {
            $query->andFilterWhere(['business_expenses' => $params['business_expenses']]);
        }

        if(isset($params['income_before_business'])) {
            $query->andFilterWhere(['income_before_business' => $params['income_before_business']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['approved_by'])) {
            $query->andFilterWhere(['approved_by' => $params['approved_by']]);
        }

        if(isset($params['approved_on'])) {
            $query->andFilterWhere(['approved_on' => $params['approved_on']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        if(isset($params['latitude'])) {
            $query->andFilterWhere(['latitude' => $params['latitude']]);
        }

        if(isset($params['longitude'])) {
            $query->andFilterWhere(['longitude' => $params['longitude']]);
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
