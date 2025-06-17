<?php

namespace common\models\search;

use common\models\AppraisalsSocial;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SocialAppraisal;

/**
 * SocialAppraisalSearch represents the model behind the search form about `common\models\SocialAppraisal`.
 */
class SocialAppraisalSearch extends AppraisalsSocial
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
            [['id', 'application_id', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', 'date_of_maturity', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['poverty_index', 'house_ownership', 'source_of_income', 'monthly_savings', 'economic_dealings', 'social_behaviour', 'fatal_disease', 'child', 'disease_type', 'status', 'deleted','member_name','member_cnic','application_no','region_id','area_id','branch_id','team_id','field_id'], 'safe'],
            [['land_size', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'amount', 'other_expenses', 'total_expenses', 'loan_amount', 'latitude', 'longitude'], 'number'],
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
        $query = SocialAppraisal::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
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
                'house_ownership' => [
                    'asc' => ['house_ownership' => SORT_ASC],
                    'desc' => ['house_ownership' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'no_of_earning_hands' => [
                    'asc' => ['no_of_earning_hands' => SORT_ASC],
                    'desc' => ['no_of_earning_hands' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'source_of_income' => [
                    'asc' => ['source_of_income' => SORT_ASC],
                    'desc' => ['source_of_income' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'monthly_savings' => [
                    'asc' => ['monthly_savings' => SORT_ASC],
                    'desc' => ['monthly_savings' => SORT_DESC],
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
            'land_size' => $this->land_size,
            'total_family_members' => $this->total_family_members,
            'no_of_earning_hands' => $this->no_of_earning_hands,
            'ladies' => $this->ladies,
            'gents' => $this->gents,
            'source_of_income' => $this->source_of_income,
            'total_household_income' => $this->total_household_income,
            'utility_bills' => $this->utility_bills,
            'educational_expenses' => $this->educational_expenses,
            'medical_expenses' => $this->medical_expenses,
            'kitchen_expenses' => $this->kitchen_expenses,
            'amount' => $this->amount,
            'date_of_maturity' => $this->date_of_maturity,
            'other_expenses' => $this->other_expenses,
            'total_expenses' => $this->total_expenses,
            'loan_amount' => $this->loan_amount,
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

        $query->andFilterWhere(['like', 'poverty_index', $this->poverty_index])
            ->andFilterWhere(['like', 'house_ownership', $this->house_ownership])
            ->andFilterWhere(['like', 'source_of_income', $this->source_of_income])
            ->andFilterWhere(['like', 'monthly_savings', $this->monthly_savings])
            ->andFilterWhere(['like', 'economic_dealings', $this->economic_dealings])
            ->andFilterWhere(['like', 'social_behaviour', $this->social_behaviour])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
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

        $query = SocialAppraisal::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['application_id'])) {
            $query->andFilterWhere(['application_id' => $params['application_id']]);
        }

        if(isset($params['poverty_index'])) {
            $query->andFilterWhere(['poverty_index' => $params['poverty_index']]);
        }

        if(isset($params['land_size'])) {
            $query->andFilterWhere(['land_size' => $params['land_size']]);
        }

        if(isset($params['house_ownership'])) {
            $query->andFilterWhere(['house_ownership' => $params['house_ownership']]);
        }

        if(isset($params['total_family_members'])) {
            $query->andFilterWhere(['total_family_members' => $params['total_family_members']]);
        }

        if(isset($params['no_of_earning_hands'])) {
            $query->andFilterWhere(['no_of_earning_hands' => $params['no_of_earning_hands']]);
        }

        if(isset($params['ladies'])) {
            $query->andFilterWhere(['ladies' => $params['ladies']]);
        }

        if(isset($params['gents'])) {
            $query->andFilterWhere(['gents' => $params['gents']]);
        }

        if(isset($params['source_of_income'])) {
            $query->andFilterWhere(['source_of_income' => $params['source_of_income']]);
        }

        if(isset($params['total_household_income'])) {
            $query->andFilterWhere(['total_household_income' => $params['total_household_income']]);
        }

        if(isset($params['utility_bills'])) {
            $query->andFilterWhere(['utility_bills' => $params['utility_bills']]);
        }

        if(isset($params['educational_expenses'])) {
            $query->andFilterWhere(['educational_expenses' => $params['educational_expenses']]);
        }

        if(isset($params['medical_expenses'])) {
            $query->andFilterWhere(['medical_expenses' => $params['medical_expenses']]);
        }

        if(isset($params['kitchen_expenses'])) {
            $query->andFilterWhere(['kitchen_expenses' => $params['kitchen_expenses']]);
        }

        if(isset($params['amount'])) {
            $query->andFilterWhere(['amount' => $params['amount']]);
        }

        if(isset($params['date_of_maturity'])) {
            $query->andFilterWhere(['date_of_maturity' => $params['date_of_maturity']]);
        }

        if(isset($params['other_expenses'])) {
            $query->andFilterWhere(['other_expenses' => $params['other_expenses']]);
        }

        if(isset($params['total_expenses'])) {
            $query->andFilterWhere(['total_expenses' => $params['total_expenses']]);
        }

        if(isset($params['loan_amount'])) {
            $query->andFilterWhere(['loan_amount' => $params['loan_amount']]);
        }

        if(isset($params['monthly_savings'])) {
            $query->andFilterWhere(['monthly_savings' => $params['monthly_savings']]);
        }

        if(isset($params['economic_dealings'])) {
            $query->andFilterWhere(['like', 'economic_dealings', $params['economic_dealings']]);
        }

        if(isset($params['social_behaviour'])) {
            $query->andFilterWhere(['like', 'social_behaviour', $params['social_behaviour']]);
        }

        if(isset($params['fatal_disease'])) {
            $query->andFilterWhere(['fatal_disease' => $params['fatal_disease']]);
        }

        if(isset($params['child'])) {
            $query->andFilterWhere(['child' => $params['child']]);
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

        if(isset($params['disease_type'])) {
            $query->andFilterWhere(['disease_type' => $params['disease_type']]);
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
