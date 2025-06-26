<?php

namespace common\models\search;

use common\models\Loans;
use common\models\Referrals;
use common\models\Users;
use common\models\UserStructureMapping;
use common\models\Visits;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Applications;

/**
 * ApplicationsSearch represents the model behind the search form about `common\models\Applications`.
 */
class ApplicationsSearch extends Applications
{
    public $full_name;
    public $cnic;
    public $region;
    public $area;
    public $branch;
    public $project;
    public $loan_amnt_frm;
    public $loan_amnt_to;
    public $created_by_name;
    public $visit_count;
    public $image_count;
    public $disb_status;
    public $image_status;
    public $referral_name;
    public $nadra_verisys;
    public $visit_id;
    public $is_shifted;
    public $account_file_id;
    public $account_file_very_at;
    public $last_action_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'referral_id', 'member_id', 'project_id', 'activity_id', 'product_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'group_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['fee', 'req_amount','account_file_id'], 'number'],
            [['application_no', 'application_date', 'project_table', 'no_of_times', 'bzns_cond', 'who_will_work', 'name_of_other', 'other_cnic', 'status', 'is_urban', 'reject_reason', 'is_lock', 'deleted', 'created_at', 'updated_at', 'bank_name', 'parentage', 'title', 'account_no', 'status', 'visit_count', 'image_count', 'disb_status', 'image_status','account_file_very_at','account_file_id','last_action_at'], 'safe'],
            [['referral_name', 'full_name', 'cnic', 'region', 'area', 'branch', 'project', 'loan_amnt_frm', 'loan_amnt_to', 'created_by_name', 'is_biometric', 'nadra_verisys', 'visit_id','is_shifted'], 'safe']
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
    public function search($params, $export = false)
    {
        //$cur_date=strtotime(date("Y-m-d-m-s",strtotime("+5 hours")));
        $cur_date = strtotime(date("Y-m-d H:i:s", strtotime('+5 hours')));
        $six_month_back_date = strtotime(date("Y-m-d", strtotime("-8 Months")));
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('member');
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        //$query->joinWith('user');

        $dataProvider->setSort([
            'attributes' => [
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'application_no' => [
                    'asc' => ['application_no' => SORT_ASC],
                    'desc' => ['application_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                /*'region_id' => [
                    'asc' => ['regions.name' => SORT_ASC],
                    'desc' => ['regions.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'area_id' => [
                    'asc' => ['areas.name' => SORT_ASC],
                    'desc' => ['areas.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'branch_id' => [
                    'asc' => ['branch.name' => SORT_ASC],
                    'desc' => ['branch.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],*/
                'req_amount' => [
                    'asc' => ['req_amount' => SORT_ASC],
                    'desc' => ['req_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project' => [
                    'asc' => ['project.name' => SORT_ASC],
                    'desc' => ['project.name' => SORT_DESC],
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
            'applications.id' => $this->member_id,
            'member_id' => $this->member_id,
            //'fee' => $this->fee,
            'applications.project_id' => $this->project_id,
            //'activity_id' => $this->activity_id,
            //'product_id' => $this->product_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            // 'applications.team_id' => $this->team_id,
            //'applications.field_id' => $this->field_id,
            //'group_id' => $this->group_id,
            'applications.referral_id' => $this->referral_id,
            //'req_amount' => $this->req_amount,
            //'assigned_to' => $this->assigned_to,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            'applications.deleted' => 0,
        ]);

        $query->andFilterWhere(['=', 'application_no', $this->application_no])
            //->andFilterWhere(['like', 'project_table', $this->project_table])
            //->andFilterWhere(['like', 'no_of_times', $this->no_of_times])
            //->andFilterWhere(['like', 'bzns_cond', $this->bzns_cond])
            //->andFilterWhere(['like', 'who_will_work', $this->who_will_work])
            //->andFilterWhere(['like', 'name_of_other', $this->name_of_other])
            //->andFilterWhere(['like', 'other_cnic', $this->other_cnic])
            ->andFilterWhere(['=', 'applications.status', $this->status])
            //->andFilterWhere(['like', 'is_urban', $this->is_urban])
            // ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            // ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            //->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['=', 'members.full_name', $this->full_name])
            ->andFilterWhere(['=', 'members.cnic', $this->cnic]);
        //->andFilterWhere(['like', 'regions.name', $this->region])
        //->andFilterWhere(['like', 'areas.name', $this->area])
        //->andFilterWhere(['like', 'branches.name', $this->branch])
        //->andFilterWhere(['like', 'users.username', $this->created_by_name]);


        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'application_date', strtotime($date[0]), strtotime($date[1])]);
        } else if (isset($params['ApplicationsSearch']['app_date'])) {
            $query->andFilterWhere(['between', 'applications.application_date', $params['ApplicationsSearch']['app_date'], $cur_date]);

        } else {
            $this->application_date = date("Y-m-d", strtotime("-8 Months")) . ' - ' . date("Y-m-d");
            $query->andFilterWhere(['between', 'applications.application_date', $six_month_back_date, $cur_date]);
            //$query->andFilterWhere(['between','applications.application_date',$six_month_back_date,$cur_date]);
            //$query->andFilterWhere(['like','application_date' , strtotime($this->date_disbursed)]);
        }
        if (!empty($params['ApplicationsSearch']['created_at'])) {
            $m = strtotime($params['ApplicationsSearch']['created_at']);
            $n = strtotime('+1 day', $m);


            $query->andFilterWhere(['between', 'applications.created_at', $m, $n]);

        }
        //if(!empty($this->loan_amnt_frm) && !empty($this->loan_amnt_to)){
        //$query->andFilterWhere(['between', 'req_amount', $this->loan_amnt_frm,$this->loan_amnt_to]);
        // }

        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchApp($params)
    {
        $loginUser = \Yii::$app->user->identity->getId();

        $query = Applications::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $query->joinWith('member');
        $this->load($params);
        $query->andWhere(['application_no' => $params['Applications']['application_no']]);

        if (\Yii::$app->user->identity->designation_id == 8) {
            $userMappings = UserStructureMapping::find()
                ->where(['user_id' => $loginUser])
                ->andWhere(['obj_type' => 'area'])
                ->select(['user_id', 'obj_id'])
                ->one();
            if (isset($userMappings) && !empty($userMappings)) {
                $query->andFilterWhere(['=', 'applications.area_id', $userMappings->obj_id]);
            }
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function search_rejected_pending($params, $export = false)
    {
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('member');
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        //$query->joinWith('user');

        $dataProvider->setSort([
            'attributes' => [
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'application_no' => [
                    'asc' => ['application_no' => SORT_ASC],
                    'desc' => ['application_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'req_amount' => [
                    'asc' => ['req_amount' => SORT_ASC],
                    'desc' => ['req_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project' => [
                    'asc' => ['project.name' => SORT_ASC],
                    'desc' => ['project.name' => SORT_DESC],
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
        if (!isset($this->status) || empty($this->status)) {
            $this->status = 'pending';
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'applications.id' => $this->member_id,
            'member_id' => $this->member_id,
            'members.cnic' => $this->cnic,
            //'fee' => $this->fee,
            'applications.project_id' => $this->project_id,
            //'activity_id' => $this->activity_id,
            //'product_id' => $this->product_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            //'applications.team_id' => $this->team_id,
            //'applications.field_id' => $this->field_id,
            //'group_id' => $this->group_id,
            //'req_amount' => $this->req_amount,
            //'assigned_to' => $this->assigned_to,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            'applications.deleted' => 0,
        ]);
        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'application_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->andFilterWhere(['=', 'application_no', $this->application_no])
            ->andFilterWhere(['=', 'applications.status', $this->status])
            ->andFilterWhere(['=', 'members.full_name', $this->full_name])
            ->andFilterWhere(['=', 'members.cnic', $this->cnic]);
        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchRejectedApplications($params, $export = false)
    {
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('member');
        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'application.id' => $this->id,
            'application_no' => $this->application_no,
            'project_id' => $this->project_id,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.deleted' => 0,

        ]);
        $query->andFilterWhere(['=', 'applications.status', "rejected"])
            ->andFilterWhere(['like', 'applications.reject_reason', 'black list'])
            ->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage]);
        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'application_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchApi($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $filter = Yii::$app->getRequest()->getQueryParam('filter');
        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = array_merge($params, $search);
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;
        /*$branch_ids = [];
        $user = Users::findOne([Yii::$app->user->identity->getId()]);
        if(isset($user->branches))
        {
            foreach ($user->branches as $branch)
            {
                $branch_ids[] = $branch->obj_id;
            }
        }*/

        $query = Applications::find()
            ->select('applications.*,members.full_name,members.cnic')
            //->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->where(['applications.deleted' => 0])
            ->andWhere(['or',
                ['applications.status' => 'approved'],
                ['applications.status' => 'pending']
            ])
            //->andWhere(['application_actions.action'=>'social_appraisal'])
            //->andWhere(['in','applications.branch_id', $branch_ids])
            //->join('inner join','application_actions','applications.id=application_actions.parent_id')
            ->limit($limit)
            ->offset($offset);

        $query->joinWith('member');

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if (isset($filter)) {
            if (($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter)) == 1) {
                $query->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $query->andFilterWhere(['like', 'members.full_name', $filter]);
            }
        }

        if (isset($params['id'])) {
            $query->andFilterWhere(['applications.id' => $params['id']]);
        }

        if (isset($params['member_id'])) {
            $query->andFilterWhere(['member_id' => $params['member_id']]);
        }

        if (isset($params['cnic'])) {
            $query->andFilterWhere(['cnic' => $params['cnic']]);
        }

        if (isset($params['full_name'])) {
            $query->andFilterWhere(['full_name' => $params['full_name']]);
        }

        if (isset($params['application_no'])) {
            $query->andFilterWhere(['application_no' => $params['application_no']]);
        }

        if (isset($params['fee'])) {
            $query->andFilterWhere(['like', 'application_no', $params['application_no']]);
        }

        if (isset($params['bzns_cond'])) {
            $query->andFilterWhere(['bzns_cond' => $params['bzns_cond']]);
        }

        if (isset($params['project_id']) && !empty($params['project_id'])) {
            $query->andFilterWhere(['project_id' => $params['project_id']]);
        }

        if (isset($params['project_table'])) {
            $query->andFilterWhere(['project_table' => $params['project_table']]);
        }

        if (isset($params['activity_id'])) {
            $query->andFilterWhere(['activity_id' => $params['activity_id']]);
        }

        if (isset($params['product_id'])) {
            $query->andFilterWhere(['product_id' => $params['product_id']]);
        }

        if (isset($params['region_id']) && !empty($params['region_id'])) {
            $query->andFilterWhere(['applications.region_id' => $params['region_id']]);
        }

        if (isset($params['area_id']) && !empty($params['area_id'])) {
            $query->andFilterWhere(['applications.area_id' => $params['area_id']]);
        }

        if (isset($params['branch_id']) && !empty($params['branch_id'])) {
            $query->andFilterWhere(['applications.branch_id' => $params['branch_id']]);
        }

        if (isset($params['team_id']) && !empty($params['team_id'])) {
            $query->andFilterWhere(['applications.team_id' => $params['team_id']]);
        }

        if (isset($params['field_id']) && !empty($params['field_id'])) {
            $query->andFilterWhere(['applications.field_id' => $params['field_id']]);
        }

        if (isset($params['group_id'])) {
            $query->andFilterWhere(['group_id' => $params['group_idn']]);
        }

        if (isset($params['no_of_times'])) {
            $query->andFilterWhere(['no_of_times' => $params['no_of_times']]);
        }

        if (isset($params['other_cnic'])) {
            $query->andFilterWhere(['other_cnic' => $params['other_cnic']]);
        }

        if (isset($params['is_urban'])) {
            $query->andFilterWhere(['is_urban' => $params['is_urban']]);
        }

        if (isset($params['name_of_other'])) {
            $query->andFilterWhere(['like', 'name_of_other', $params['name_of_other']]);
        }

        if (isset($params['who_will_work'])) {
            $query->andFilterWhere(['like', 'who_will_work', $params['who_will_work']]);
        }

        if (isset($params['status']) && !empty($params['status'])) {
            $query->andFilterWhere(['applications.status' => $params['status']]);
        }

        if (isset($params['deleted'])) {
            $query->andFilterWhere(['applications.deleted' => $params['deleted']]);
        }

        if (isset($params['created_at'])) {
            $query->andFilterWhere(['applications.created_at' => $params['created_at']]);
        }

        if (isset($params['is_lock'])) {
            $query->andFilterWhere(['applications.is_lock' => $params['is_lock']]);
        }

        if (isset($params['req_amount'])) {
            $query->andFilterWhere(['req_amount' => $params['req_amount']]);
        }

        if (isset($params['date']) && !empty($params['date'])) {
            $date = explode(' - ', $params['date']);
            $query->andFilterWhere(['between', 'applications.created_at', strtotime($date[0]), strtotime(date('Y-m-d 23:59:59', strtotime($date[1])))]);
        }

        if (isset($order) && !empty($order)) {
            $query->orderBy($order);
        }

        if (!isset($order) || empty($order)) {
            $query->orderBy('applications.created_at desc');
        }

        $count = (int)$query->count('applications.id');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil($count / $limit),
            'totalRecords' => $count
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchMemberApi($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->select('applications.*, members.cnic')
            ->where(['applications.deleted' => 0])
            //->asArray(true)
            ->limit(1)
            ->offset($offset);

        $query->joinWith('member');

        if (isset($params['cnic'])) {
            $query->andFilterWhere(['members.cnic' => $params['cnic']]);
        }

        if (isset($params['application_no'])) {
            $query->andFilterWhere(['application_no' => $params['application_no']]);
        }

        if (isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if (isset($order)) {
            $query->orderBy($order);
        }

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => Applications::find()->where(['applications.deleted' => 0])->count('applications.id')
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiVerification($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $search = Yii::$app->getRequest()->getQueryParam('search');
        $filter = Yii::$app->getRequest()->getQueryParam('filter');


        if (isset($search)) {
            $params = array_merge($params, $search);
        }
        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->join('inner join', 'application_actions as f_action', 'f_action.parent_id=applications.id')
            ->joinWith('member')
            //->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'approved/rejected'])
            ->where(['applications.deleted' => 0, 'applications.status' => 'pending', 'application_actions.action' => 'approved/rejected', 'application_actions.status' => 0])
            ->andWhere(['f_action.action' => 'family_member_info', 'f_action.status' => 1])
            ->andWhere(['>=', 'f_action.expiry_date', date('Y-m-d H:i:s')])
            //->asArray(true)

            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if (isset($filter)) {
            if (($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter)) == 1) {
                $query->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $query->andFilterWhere(['like', 'members.full_name', $filter]);
            }
        }

        if (isset($order)) {
            $query->orderBy($order);
        }
        $query->orderBy('applications.created_at desc');
        $count = (int)$query->count('applications.id');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil($count / $limit),
            'totalRecords' => $count
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiVerificationNew($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $search = Yii::$app->getRequest()->getQueryParam('search');
        $filter = Yii::$app->getRequest()->getQueryParam('filter');


        if (isset($search)) {
            $params = array_merge($params, $search);
        }
        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->join('inner join', 'application_actions as f_action', 'f_action.parent_id=applications.id')
            ->joinWith('member')
            //->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'approved/rejected'])
            ->where(['applications.deleted' => 0, 'applications.status' => 'pending', 'application_actions.action' => 'approved/rejected', 'application_actions.status' => 0])
            ->andWhere(['f_action.action' => 'family_member_info', 'f_action.status' => 1])
            ->andWhere(['>=', 'application_actions.expiry_date', date('Y-m-d H:i:s')])
            //->asArray(true)

            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if (isset($filter)) {
            if (($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter)) == 1) {
                $query->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $query->andFilterWhere(['like', 'members.full_name', $filter]);
            }
        }

        if (isset($order)) {
            $query->orderBy($order);
        }
        $query->orderBy('applications.created_at desc');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil((int)$query->count() / $limit),
            'totalRecords' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiVerified($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $search = Yii::$app->getRequest()->getQueryParam('search');
        $filter = Yii::$app->getRequest()->getQueryParam('filter');


        if (isset($search)) {
            $params = array_merge($params, $search);
        }
        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->joinWith('member')
            ->leftJoin('loans', 'loans.application_id = applications.id')
            //->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'approved/rejected'])
            ->where(['is', 'loans.application_id', null])
            ->andWhere(['applications.deleted' => 0, 'applications.status' => 'approved', 'application_actions.action' => 'approved/rejected', 'application_actions.status' => 1])
            ->andWhere(['>=', 'application_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['=', 'applications.group_id', 0])
            //->asArray(true)

            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if (isset($filter)) {
            if (($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter)) == 1) {
                $query->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $query->andFilterWhere(['like', 'members.full_name', $filter]);
            }
        }

        if (isset($order)) {
            $query->orderBy($order);
        }
        $query->orderBy('applications.updated_at desc');
        $count = (int)$query->count('applications.id');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil($count / $limit),
            'totalRecords' => $count
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiMemberInfo($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $search = Yii::$app->getRequest()->getQueryParam('search');
        $filter = Yii::$app->getRequest()->getQueryParam('filter');


        if (isset($search)) {
            $params = array_merge($params, $search);
        }

        $limit = isset($limit) ? $limit : 10;
        $page = 1;
        //$page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->join('inner join', 'members', 'members.id=applications.member_id')
            ->leftJoin('loans', 'loans.application_id = applications.id')
            ->where(['is', 'loans.application_id', null])
            ->andWhere(['applications.status' => 'approved', 'application_actions.action' => 'approved/rejected', 'application_actions.status' => 1])
            ->andWhere(['>=', 'application_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['members.cnic' => $params['cnic']])
            ->andWhere(['=', 'applications.group_id', 0])
            ->andWhere(['applications.deleted' => 0])
            //->asArray(true)

            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query, $params['controller'], $params['method'], $params['rbac_type'], $params['user_id']);

        if (isset($order)) {
            $query->orderBy($order);
        }
        $count = (int)$query->count('applications.id');

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil($count / $limit)
            //'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];

    }

    public function searchunverifiedlist($params, $export = false)
    {


        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('member');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $query->joinWith('project');
        $query->joinWith('user');
        $query->joinWith('actions');

        $dataProvider->setSort([
            'attributes' => [
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'application_no' => [
                    'asc' => ['application_no' => SORT_ASC],
                    'desc' => ['application_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
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
                'req_amount' => [
                    'asc' => ['req_amount' => SORT_ASC],
                    'desc' => ['req_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project' => [
                    'asc' => ['project.name' => SORT_ASC],
                    'desc' => ['project.name' => SORT_DESC],
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
            'applications.id' => $this->member_id,
            'member_id' => $this->member_id,
            'fee' => $this->fee,
            'applications.project_id' => $this->project_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.team_id' => $this->team_id,
            'applications.field_id' => $this->field_id,
            'group_id' => $this->group_id,
            'req_amount' => $this->req_amount,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'application_no', $this->application_no])
            ->andFilterWhere(['like', 'project_table', $this->project_table])
            ->andFilterWhere(['like', 'no_of_times', $this->no_of_times])
            ->andFilterWhere(['like', 'bzns_cond', $this->bzns_cond])
            ->andFilterWhere(['like', 'who_will_work', $this->who_will_work])
            ->andFilterWhere(['like', 'name_of_other', $this->name_of_other])
            ->andFilterWhere(['like', 'other_cnic', $this->other_cnic])
            ->andFilterWhere(['like', 'applications.status', 'pending'])
            ->andFilterWhere(['like', 'is_urban', $this->is_urban])
            ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.cnic', $this->cnic])
            ->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'branches.name', $this->branch])
            ->andFilterWhere(['like', 'users.username', $this->created_by_name])
            ->andFilterWhere(['=', 'application_actions.action', 'approved/rejected'])
            ->andFilterWhere(['=', 'application_actions.status', 0])
            ->andFilterWhere(['like', 'users.username', $this->created_by_name]);

        if (!empty($this->loan_amnt_frm) && !empty($this->loan_amnt_to)) {
            $query->andFilterWhere(['between', 'req_amount', $this->loan_amnt_frm, $this->loan_amnt_to]);
        }

        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function search_pending_applications($params, $export = false)
    {
        $query = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->joinWith('member')
            ->where(['applications.status' => 'pending'])
            ->andWhere(['applications.deleted' => 0, 'application_actions.action' => 'approved/rejected', 'application_actions.status' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
        }
        $query->andFilterWhere([
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
        ]);
        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchBankReport($params, $export = false)
    {

//         print_r($params);
//         die();
        $query = Applications::find();
        if (!isset($params['ApplicationsSearch']['status'])) {
            $this->status = 0;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('loan');
        $query->joinWith('member');
        $query->joinWith('member.memberAccount');
        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'application.id' => $this->id,
            'application_no' => $this->application_no,
            'bank_name' => $this->bank_name,
            'applications.project_id' => $this->project_id,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.deleted' => 0,
            'members_account.status' => $this->status,
            'members_account.title' => $this->title,
            'members_account.account_no' => $this->account_no,
            'members_account.is_current' => 1,
            'members_account.acc_file_id' => $this->account_file_id,
            'members_account.verified_at' => $this->account_file_very_at,
            'loans.created_at' => $this->last_action_at

        ]);
        if (isset($this->project_id) && empty($this->project_id)) {
            $query->andFilterWhere(['in', 'applications.project_id', [52, 61, 62, 67, 64, 76, 77, 78, 79, 83, 90, 97, 103, 109, 105, 106, 36,
                74, 85, 86, 88, 94, 96, 99, 100, 110, 113, 11, 56, 118, 114, 119, 124, 126, 127, 132, 87, 134, 136]]);
        }

        $query->andFilterWhere(['=', 'applications.status', "approved"])
            ->andFilterWhere(['in', 'loans.status', ["Pending","collected"]])
            ->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage])
            ->andFilterWhere(['!=', 'members_account.account_no', 'NULL']);
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchOwnHouseModel($params, $export = false)
    {

        /* print_r($params);
         die();*/
        $query = Applications::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $query->joinWith('member');
        $query->joinWith('member.memberInfo');
        $query->joinWith('member.membersAccount');
        $query->joinWith('member.membersAddress');
        $query->joinWith('member.membersPhone');
        $query->joinWith('socialAppraisal');


        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ]
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'application.id' => $this->id,
            'application_no' => $this->application_no,
            'applications.project_id' => $this->project_id,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.deleted' => 0,
            'members_account.is_current' => 1,
            'members_address.is_current' => 1,
            'members_phone.is_current' => 1
        ]);

        $query->andFilterWhere(['in', 'applications.project_id', [132]]);

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchVisitsReport($params, $export = false)
    {
        $query = Applications::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->join('inner join', 'visits', 'visits.parent_id=applications.id');
        $query->joinWith('member');
        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['applications.project_id' => SORT_ASC],
                    'desc' => ['applications.project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
            'applications.deleted' => 0
        ]);
        $query->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage])
            ->andFilterWhere(['>', 'applications.recommended_amount', 0]);
        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'applications.application_date', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['applications.application_date' => $this->application_date]);
        }
        $query->andFilterWhere(['not in', 'applications.status', ["rejected"]]);

//        $query->groupBy('members.cnic');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function visitsShiftedApprovalList($params, $export = false)
    {
        $query = Applications::find()
            ->alias('applications')
            ->select(['applications.*', 'visits.id AS visit_id' , 'visits.is_shifted AS is_shifted'])
            ->innerJoin(
                '(SELECT parent_id, MAX(id) AS last_visit_id 
                  FROM visits 
                  WHERE is_shifted = 1 AND percent = 100 AND shifted_verified_by = 0
                  GROUP BY parent_id) AS latest_visits',
                'latest_visits.parent_id = applications.id'
            )
            ->innerJoin('visits', 'visits.id = latest_visits.last_visit_id')// Join on the last visit
            ->joinWith('member');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['applications.project_id' => SORT_ASC],
                    'desc' => ['applications.project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
            'applications.project_id' => 132,
            'applications.deleted' => 0,
            'visits.percent' => 100,
            'visits.is_shifted' => 1,
            'visits.shifted_verified_by' => 0,
            'visits.deleted' => 0
        ]);
        $query->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage])
            ->andFilterWhere(['>', 'applications.recommended_amount', 0]);

        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'applications.application_date', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['applications.application_date' => $this->application_date]);
        }
        $query->andFilterWhere(['not in', 'applications.status', ["rejected"]]);

//        echo $query->createCommand()->getRawSql();
//        die();

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function oldvisitsConstructionCompletedApprovalList($params, $export = false)
    {
        $query = Applications::find()
            ->alias('applications')
            ->select(['applications.*', 'visits.id AS visit_id' , 'visits.is_shifted AS is_shifted'])
            ->innerJoin(
                '(SELECT parent_id, MAX(id) AS last_visit_id 
                  FROM visits 
                  WHERE percent = 100 AND construction_verified_by = 0
                  GROUP BY parent_id) AS latest_visits',
                'latest_visits.parent_id = applications.id'
            )
            ->innerJoin('visits', 'visits.id = latest_visits.last_visit_id')// Join on the last visit
            ->joinWith('member');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['applications.project_id' => SORT_ASC],
                    'desc' => ['applications.project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
            'applications.project_id' => 132,
            'applications.deleted' => 0,
            'visits.percent' => 100,
            'visits.construction_verified_by' => 0,
            'visits.created_by' => 6174,
            'visits.deleted' => 0
        ]);
        $query->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage])
            ->andFilterWhere(['>', 'applications.recommended_amount', 0]);

        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $query->andFilterWhere(['between', 'applications.application_date', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['applications.application_date' => $this->application_date]);
        }
        $query->andFilterWhere(['not in', 'applications.status', ["rejected"]]);

//        echo $query->createCommand()->getRawSql();
//        die();

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function visitsConstructionCompletedApprovalList($params, $export = false)
    {
        $subQuery = (new \yii\db\Query())
            ->select(['parent_id', 'MAX(id) AS last_visit_id'])
            ->from('visits')
            ->where([
                'percent' => 100,
                'construction_verified_by' => 0,
                'deleted' => 0,
                'created_by' => 6174,
            ])
            ->groupBy('parent_id');

        $query = Applications::find()
            ->alias('applications')
            ->select(['applications.*', 'visits.id AS visit_id', 'visits.is_shifted AS is_shifted'])
            ->innerJoin(['latest_visits' => $subQuery], 'latest_visits.parent_id = applications.id')
            ->innerJoin('visits', 'visits.id = latest_visits.last_visit_id')
            ->joinWith(['member', 'memberAccount']) // assuming these relations exist
            ->where([
                'applications.deleted' => 0,
                'applications.project_id' => 132,
            ])
            ->andWhere(['>', 'applications.recommended_amount', 0])
            ->andWhere(['not in', 'applications.status', ['rejected']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'project_id' => [
                    'asc' => ['applications.project_id' => SORT_ASC],
                    'desc' => ['applications.project_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'full_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'status' => [
                    'asc' => ['members_account.status' => SORT_ASC],
                    'desc' => ['members_account.status' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
        ]);

        $query->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage]);

        if (!is_null($this->application_date) && strpos($this->application_date, ' - ') !== false) {
            $date = explode(' - ', $this->application_date);
            $start = strtotime($date[0]);
            $end = strtotime($date[1] . ' 23:59:59');
            $query->andFilterWhere(['between', 'applications.application_date', $start, $end]);
        } else {
            $query->andFilterWhere(['applications.application_date' => $this->application_date]);
        }

        return $export ? $query : $dataProvider;
    }


    public function searchVisitsImages($params)
    {
        $query = Applications::find()->distinct();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 10;

        $query->join('inner join', 'visits', 'visits.parent_id=applications.id');
        $query->joinWith('member');
        $query->joinWith('loan');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
            'applications.deleted' => 0,
            'visits.deleted' => 0,
        ]);
        $query->andFilterWhere(['=', 'applications.status', "approved"])
            ->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage]);

        if (isset($this->disb_status) && !empty($this->disb_status)) {
            if ($this->disb_status == 'disbursed') {
                $query->andWhere('loan_amount = disbursed_amount');
            } else if ($this->disb_status == 'partial') {
                $query->andWhere('loan_amount > disbursed_amount')
                    ->andFilterWhere(['!=', 'disbursed_amount', 0]);
            } else if ($this->disb_status == 'null') {
                $query->andWhere(['=', 'disbursed_amount', 0]);
            }
        }

        if (isset($params['ApplicationsSearch']['visit_count']) && !empty($params['ApplicationsSearch']['visit_count'])) {
            if ($this->visit_count == '>3') {
                $query->having(['>', 'Count(visits.id)', 3]);
            } else {
                $query->having(['=', 'Count(visits.id)', $this->visit_count]);

            }
        }

        if ((isset($params['ApplicationsSearch']['image_count']) && !empty($params['ApplicationsSearch']['image_count'])) || (isset($params['ApplicationsSearch']['image_status']))) {
            $query->join('inner join', 'images', 'images.parent_id=visits.id and images.parent_type = "visits"');
            //die('xvsde');
            if (isset($params['ApplicationsSearch']['image_count']) && !empty($params['ApplicationsSearch']['image_count'])) {
                if ($this->image_count == '>3') {
                    $query->having(['>', 'Count(images.id)', 3]);
                } else {
                    $query->having(['=', 'Count(images.id)', $this->image_count]);
                }
                $query->groupBy('applications.id, images.parent_id');
            }
            if (isset($params['ApplicationsSearch']['image_status'])) {
                $query->andFilterWhere(['=', 'images.is_published', $this->image_status]);
            }
        } else {
            $query->groupBy('applications.id');
        }
        return $dataProvider;
    }

    public function searchVisitsFloodImages($params)
    {
        $query = Applications::find()->where(['applications.project_id' => 98])->distinct();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 10;

        $query->join('inner join', 'visits', 'visits.parent_id=applications.id');
        $query->joinWith('member');
        $query->joinWith('loan');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if (isset($params['ApplicationsSearch']['referral_name'])) {

            if ($params['ApplicationsSearch']['referral_name'] == 'not-referred') {
                $this->referral_id = 0;
            } else {
                $referralModel = Referrals::find()->where(['name' => $this->referral_name])->select(['id'])->one();
                $this->referral_id = (!empty($referralModel) && $referralModel != null) ? $referralModel->id : 0;
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'applications.id' => $this->id,
            'application_no' => $this->application_no,
            'members.cnic' => $this->cnic,
            'applications.branch_id' => $this->branch_id,
            'applications.area_id' => $this->area_id,
            'applications.region_id' => $this->region_id,
            'applications.referral_id' => $this->referral_id,
            'applications.deleted' => 0,
            'visits.deleted' => 0,
        ]);
        $query->andFilterWhere(['=', 'applications.status', "approved"])
            ->andFilterWhere(['like', 'members.full_name', $this->full_name])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage]);

        if (isset($this->disb_status) && !empty($this->disb_status)) {
            if ($this->disb_status == 'disbursed') {
                $query->andWhere('loan_amount = disbursed_amount');
            } else if ($this->disb_status == 'partial') {
                $query->andWhere('loan_amount > disbursed_amount')
                    ->andFilterWhere(['!=', 'disbursed_amount', 0]);
            } else if ($this->disb_status == 'null') {
                $query->andWhere(['=', 'disbursed_amount', 0]);
            }
        }

        if (isset($params['ApplicationsSearch']['visit_count']) && !empty($params['ApplicationsSearch']['visit_count'])) {
            if ($this->visit_count == '>3') {
                $query->having(['>', 'Count(visits.id)', 3]);
            } else {
                $query->having(['=', 'Count(visits.id)', $this->visit_count]);

            }
        }

        if ((isset($params['ApplicationsSearch']['image_count']) && !empty($params['ApplicationsSearch']['image_count'])) || (isset($params['ApplicationsSearch']['image_status']))) {
            $query->join('inner join', 'images', 'images.parent_id=visits.id and images.parent_type = "visits"');
            //die('xvsde');
            if (isset($params['ApplicationsSearch']['image_count']) && !empty($params['ApplicationsSearch']['image_count'])) {
                if ($this->image_count == '>3') {
                    $query->having(['>', 'Count(images.id)', 3]);
                } else {
                    $query->having(['=', 'Count(images.id)', $this->image_count]);
                }
                $query->groupBy('applications.id, images.parent_id');
            }
            if (isset($params['ApplicationsSearch']['image_status'])) {
                $query->andFilterWhere(['=', 'images.is_published', $this->image_status]);
            }
        } else {
            $query->groupBy('applications.id');
        }
        return $dataProvider;
    }


}
