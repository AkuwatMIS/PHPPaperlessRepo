<?php

namespace common\models\search;

use common\components\Helpers\StructureHelper;
use common\models\Applications;
use common\models\Loans;
use common\models\LoanTranches;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Groups;

/**
 * GroupsSearch represents the model behind the search form about `common\models\Groups`.
 */
class GroupsSearch extends Groups
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
            [['id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'br_serial', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['is_locked', 'grp_no', 'group_name', 'grp_type', 'status', 'reject_reason', 'created_at', 'updated_at'], 'safe'],
            [['region', 'area', 'branch'], 'safe'],
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
        $query = Groups::find()->select([
            'groups.id','groups.region_id','groups.area_id','groups.branch_id','groups.team_id','groups.field_id',
            'groups.grp_no','groups.group_name','groups.grp_type','groups.created_at','groups.deleted','groups.is_locked',
            'groups.status','groups.reject_reason'/*,
            'regions.name','areas.name','branches.name'*/
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
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
                'grp_no' => [
                    'asc' => ['grp_no' => SORT_ASC],
                    'desc' => ['grp_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_name' => [
                    'asc' => ['group_name' => SORT_ASC],
                    'desc' => ['group_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'grp_type' => [
                    'asc' => ['grp_type' => SORT_ASC],
                    'desc' => ['grp_type' => SORT_DESC],
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
            'groups.region_id' => $this->region_id,
            'groups.area_id' => $this->area_id,
            'groups.branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'field_id' => $this->field_id,
            'br_serial' => $this->br_serial,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'groups.deleted' => 0,
        ]);

        $query->andFilterWhere(['like', 'is_locked', $this->is_locked])
            ->andFilterWhere(['like', 'grp_no', $this->grp_no])
            ->andFilterWhere(['like', 'group_name', $this->group_name])
            ->andFilterWhere(['like', 'grp_type', $this->grp_type])
            ->andFilterWhere(['like', 'status', $this->status]);
            //->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            /*->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'branches.name', $this->branch]);*/
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
        $filter = Yii::$app->getRequest()->getQueryParam('filter');

        if(isset($search)){
            $params=array_merge($params,$search);
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Groups::find()
            ->select('groups.*')
            ->where(['groups.deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['filter']))
        {
            $query->joinWith('applications');
            $applications = Applications::find()->joinWith('member')->where(['!=','group_id',0]);
            if(($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter))==1) {
                $applications->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $applications->andFilterWhere(['like', 'members.full_name', $filter]);
            }

            $applications = $applications->all();
            $groups_list = array();
            foreach ($applications as $application){
                if(!in_array($application->group_id,$groups_list)){
                    $groups_list[] = $application->group_id;
                }
            }

            $query->andWhere(['in','groups.id',$groups_list]);
        }


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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
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
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' =>  (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiProcessed($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=array_merge($params,$search);
        }

        $limit = isset($limit) ? $limit : 5;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;
        $loans = Loans::find()
            ->join('inner join','group_actions','group_actions.parent_id=loans.group_id')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['group_actions.action'=>'lac','group_actions.status'=>1])
            ->andWhere(['>=','group_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => 'pending','loan_tranches.fund_request_id'=>0, 'loan_tranches.disbursement_id' => 0, 'loan_tranches.status' => 3])
            ->orderBy('loans.created_at desc,loan_tranches.updated_at desc')
            //->andWhere(['loans.status' => 'pending','loans.fund_request_id'=>0,'loans.disbursement_id'=>0])->orderBy('loans.created_at desc')
            ->all();
        /*$loans = Loans::find()
            ->join('inner join','application_actions','application_actions.parent_id=loans.application_id')
            ->where(['application_actions.action'=>'lac','application_actions.status'=>1])
            ->andWhere(['>=','application_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => 'pending','loans.fund_request_id'=>0,'loans.disbursement_id'=>0])->orderBy('loans.created_at desc')
            ->all();*/
        $groups_list = array();
        foreach ($loans as $loan){
            if(!in_array($loan->group_id,$groups_list)){
                $groups_list[] = $loan->group_id;
            }
        }
        $query = Groups::find()
            ->select('*')
            ->where(['deleted' => 0])
            ->andWhere(['in','id',$groups_list])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        $query->orderBy('groups.created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        $info = [];
        $info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];
        /*$info[] = ['key' => 'lock', 'value' => (int)$query->where(['is_locked' => 1])->count()];
        $info[] = ['key' => 'unlock', 'value' => (int)$query->where(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' => (int)$query->count()
            /*'info' => $info*/
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiProcessedCE($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=array_merge($params,$search);
        }

        $limit = isset($limit) ? $limit : 5;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;
        $loans = Loans::find()
            ->where(['loans.status' => 'collected','loans.deleted'=>0])
            ->andWhere(['in','project_id', StructureHelper::trancheProjects()])
            ->orderBy('loans.updated_at desc')
            //->andWhere(['loans.status' => 'pending','loans.fund_request_id'=>0,'loans.disbursement_id'=>0])->orderBy('loans.created_at desc')
            ->all();
        /*$loans = Loans::find()
            ->join('inner join','application_actions','application_actions.parent_id=loans.application_id')
            ->where(['application_actions.action'=>'lac','application_actions.status'=>1])
            ->andWhere(['>=','application_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => 'pending','loans.fund_request_id'=>0,'loans.disbursement_id'=>0])->orderBy('loans.created_at desc')
            ->all();*/
        $groups_list = array();
        foreach ($loans as $loan){
            if(!in_array($loan->group_id,$groups_list)){
                $groups_list[] = $loan->group_id;
            }
        }
        $query = Groups::find()
            ->select('*')
            ->where(['deleted' => 0])
            ->andWhere(['in','id',$groups_list])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        $query->orderBy('groups.created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        $info = [];
        //$info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];
        /*$info[] = ['key' => 'lock', 'value' => (int)$query->where(['is_locked' => 1])->count()];
        $info[] = ['key' => 'unlock', 'value' => (int)$query->where(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' => (int)$query->count()
            /*'info' => $info*/
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiStatus($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $params = $params['search'];

        /*print_r($params);
        die();*/
        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Groups::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
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

    public function searchApiPending($params)
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

        $applications = Applications::find()
            ->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->join('inner join','group_actions','group_actions.parent_id=applications.group_id')
            ->leftJoin('loans','loans.application_id = applications.id')
            //->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->where(['application_actions.action'=>'group_formation','application_actions.status'=>1])
            ->andWhere(['>=','application_actions.expiry_date', strtotime('now')])
            ->andWhere(['group_actions.action'=>'lac','group_actions.status'=>0])
            //->andWhere(['is', 'loans.application_id', null])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['<>','applications.group_id','0'])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->andWhere(['not in','applications.project_id',StructureHelper::trancheProjects()])
            ->orWhere(['and',['in','applications.project_id', StructureHelper::trancheProjects()],['>','applications.recommended_amount',0],['application_actions.action'=>'group_formation'],
                ['>=','application_actions.expiry_date', strtotime('now')],['group_actions.action'=>'lac','group_actions.status'=>0],['applications.status'=>'approved'],
                ['<>','applications.group_id','0'],['=','applications.branch_id',$params['branch_id']/*,['loan_tranches.status' => 2]*/]
            ])
            /*->orWhere(['and',['applications.project_id'=> 47],['>','applications.recommended_amount',0],['application_actions.action'=>'group_formation'],
                ['>=','application_actions.expiry_date', strtotime('now')],['group_actions.action'=>'lac','group_actions.status'=>0],['applications.status'=>'approved'],
                ['<>','applications.group_id','0'],['=','applications.branch_id',$params['branch_id'],['loan_tranches.status' => 2]]
            ])*/
            ->orderBy('applications.updated_at desc')
            /*->limit(10)*/
            ->all();

        $groups_list = array();
        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }

        $applications = Applications::find()
            ->joinWith('loan')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0, 'loan_tranches.status' =>2])
            ->andWhere(['in','applications.project_id',StructureHelper::trancheProjects()])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->orderBy('loan_tranches.updated_at desc')
            /*->limit(10)*/
            ->all();

        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }

        $query = Groups::find()
            ->where(['in','id',$groups_list])
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        /*if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }*/
        $query->orderBy('created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        $info = [];
        $info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];
       /* $info[] = ['key' => 'lock', 'value' => (int)$query->andWhere(['is_locked' => 1])->count()];
        $info[] = ['key' => 'unlock', 'value' => (int)$query->andwhere(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' =>  (int)$query->count(),
            'info' => $info
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiRecommendHousing($params)
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

        $applications = Applications::find()
            ->joinWith('loan')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0, 'loan_tranches.status' =>2])
            ->andWhere(['in','applications.project_id',StructureHelper::trancheProjects()])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->orderBy('loan_tranches.updated_at desc')
            /*->limit(10)*/
            ->all();

        $groups_list = array();
        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }
        $query = Groups::find()
            ->where(['in','id',$groups_list])
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        /*if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }*/
        $query->orderBy('created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        $info = [];
        $info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];
        /* $info[] = ['key' => 'lock', 'value' => (int)$query->andWhere(['is_locked' => 1])->count()];
         $info[] = ['key' => 'unlock', 'value' => (int)$query->andwhere(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' =>  (int)$query->count(),
            'info' => $info
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiPendingHousing($params)
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

        $applications = Applications::find()
            ->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->join('inner join','group_actions','group_actions.parent_id=applications.group_id')
            ->leftJoin('loans','loans.application_id = applications.id')
            ->where(['application_actions.action'=>'group_formation','application_actions.status'=>1])
            ->andWhere(['>=','application_actions.expiry_date', strtotime('now')])
            ->andWhere(['group_actions.action'=>'lac','group_actions.status'=>0])
            ->andWhere(['is', 'loans.application_id', null])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['in','applications.project_id',StructureHelper::trancheInProjects()])
            ->andWhere(['<>','applications.group_id',0])
            ->andWhere(['applications.recommended_amount' => 0])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->orderBy('applications.updated_at desc')
            /*->limit(10)*/
            ->all();

        $tranch_applications = Applications::find()
            ->leftJoin('loans','loans.application_id = applications.id')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0, 'loan_tranches.status' =>1])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['in','applications.project_id',StructureHelper::trancheInProjects()])
            ->andWhere(['<>','applications.group_id',0])
            ->andWhere(['>','applications.recommended_amount', 0])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->orderBy('applications.updated_at desc')
            /*->limit(10)*/
            ->all();

        $applications = array_merge($applications,$tranch_applications);

        $groups_list = array();
        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }
        $query = Groups::find()
            ->where(['in','id',$groups_list])
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        /*if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }*/
        $query->orderBy('created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        /*$info = [];
        $info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];*/
        /* $info[] = ['key' => 'lock', 'value' => (int)$query->andWhere(['is_locked' => 1])->count()];
         $info[] = ['key' => 'unlock', 'value' => (int)$query->andwhere(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' =>  (int)$query->count(),
            //'info' => $info
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiRecommended($params)
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

        $applications = Applications::find()
            ->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->join('inner join','group_actions','group_actions.parent_id=applications.group_id')
            ->leftJoin('loans','loans.application_id = applications.id')
            ->where(['application_actions.action'=>'group_formation','application_actions.status'=>1])
            //->andWhere(['>=','application_actions.expiry_date', strtotime('now')])
            //->andWhere(['group_actions.action'=>'lac','group_actions.status'=>0])
            //->andWhere(['is', 'loans.application_id', null])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['in','applications.project_id',StructureHelper::trancheProjects()])
            ->andWhere(['<>','applications.group_id',0])
            ->andWhere(['<>','applications.recommended_amount',0])
            ->andWhere(['=','applications.branch_id',$params['branch_id']])
            ->orderBy('applications.updated_at desc')
            /*->limit(10)*/
            ->all();

        $groups_list = array();
        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }

        $query = Groups::find()
            ->where(['in','id',$groups_list])
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        /*if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }*/
        $query->orderBy('created_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        /*$info = [];
        $info[] = ['key' => 'totalRecords', 'value' => (int)$query->count()];*/
        /* $info[] = ['key' => 'lock', 'value' => (int)$query->andWhere(['is_locked' => 1])->count()];
         $info[] = ['key' => 'unlock', 'value' => (int)$query->andwhere(['is_locked' => 0])->count()];*/

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit),
            'totalRecords' =>  (int)$query->count(),
            //'info' => $info
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchApiPendingDisbursement($params)
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

        $loans = Loans::find()
            ->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            ->where(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
            /*->where(['loan_actions.action'=>'takaful','loan_actions.status'=>1])
            ->andWhere(['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')])*/
            ->andWhere(['in','loans.status' ,["pending","collected"]])
            ->andWhere(['loan_tranches.disbursement_id' => 0 ])
            ->andWhere(['in','loan_tranches.status' , [4,5]])
            ->orWhere(['and',['not in' , 'loans.project_id',  StructureHelper::trancheProjects()], ['loan_actions.action'=>'takaful','loan_actions.status'=>1],['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')],['in','loans.status' ,["pending","collected"]],['loan_tranches.disbursement_id' => 0 ],['in','loan_tranches.status' , [4,5]]])
            ->orderBy('loan_tranches.updated_at desc')
            ->all();

        $groups_list = array();
        foreach ($loans as $loan){
            if(!in_array($loan->group_id,$groups_list)){
                $groups_list[] = $loan->group_id;
            }
        }

        $loans_discart = Loans::find()
            ->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loan_actions.action'=>'takaful','loan_actions.status'=>0])
            ->andWhere(['loan_tranches.disbursement_id' => 0 ])
            ->andWhere(['in','loan_tranches.status' , [4,5]])
            ->andWhere(['in','loans.group_id',$groups_list])
            ->andWhere(['in' ,'loans.status' , ["pending","collected"]])
            ->all();

        foreach ($loans_discart as $loan){
            $index = array_search($loan->group_id, $groups_list);
            if($index !== false){
                unset($groups_list[$index]);
            }
        }

        $query = Groups::find()
            ->where(['in','id',$groups_list])
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

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

        if(isset($params['team_id'])) {
            $query->andFilterWhere(['team_id' => $params['team_id']]);
        }

        if(isset($params['field_id'])) {
            $query->andFilterWhere(['field_id' => $params['field_id']]);
        }

        if(isset($params['br_serial'])) {
            $query->andFilterWhere(['br_serial' => $params['br_serial']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['grp_no' => $params['grp_no']]);
        }

        if(isset($params['group_name'])) {
            $query->andFilterWhere(['group_name' => $params['group_name']]);
        }

        if(isset($params['grp_type'])) {
            $query->andFilterWhere(['grp_type' => $params['grp_type']]);
        }


        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        /*if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }*/
        $query->orderBy('updated_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }

        $count = (int)$query->count();
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil($count/$limit),
            'totalRecords' =>  $count,
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
