<?php

namespace common\models\search;

use common\components\Helpers\CacheHelper;
use common\components\Helpers\StructureHelper;
use common\models\FundRequests;
use common\models\Groups;
use common\models\LoanTranches;
use common\models\UserStructureMapping;
use common\models\VegaLoan;
use common\models\Writeoff;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Loans;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class LoansSearch extends Loans
{
    public $project_ids;
    public $crop_type;
    public $application_no;
    public $member_name;
    public $member_parentage;
    public $family_member_cnic;
    public $member_cnic;
    public $group_no;
    public $project;
    public $region;
    public $area;
    public $branch;
    public $branch_name;
    public $project_name;
    public $loan_amnt_frm;
    public $loan_amnt_to;
    public $report_date;
    public $cheque_date;
    public $tranch_amount;
    public $tranch_no;
    public $bank;
    public $referral_id;
    public $mobile;
    public $write_off_amount;
    public $write_off_date;
    public $write_off_by;
    public $province_id;
    public $city_id;
    public $bank_name;
    public $account_no;
    public $account_title;
    public $is_pledged;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'application_id', 'project_id', /*'date_approved', */'cheque_dt', 'disbursement_id', 'activity_id', 'product_id', 'group_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'loan_expiry', 'loan_completed_date', 'br_serial', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['account_title','account_no','bank_name','project_table','date_approved','project','application_no', 'cheque_no', 'inst_type', 'old_sanc_no', 'remarks', 'sanction_no', 'status', 'reject_reason', 'is_lock', 'deleted','date_disbursed','member_name','member_cnic','report_date','family_member_cnic','member_parentage','group_no','branch_name','project_name','project_ids','bank','referral_id','city_id','province_id'], 'safe'],
            [['loan_amount','inst_amnt', 'inst_months', 'due', 'overdue','fund_request_id', 'balance','loan_amnt_frm','loan_amnt_to','tranch_no','tranch_amount'], 'number'],
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
        //$cur_date=strtotime((date("Y-m-d")));
        $cur_date=strtotime(date("Y-m-d H:i:s", strtotime('+5 hours')));
        $six_month_back_date=strtotime(date("Y-m-d",strtotime("-4 Months")));
        $query = Loans::find()->select([
            'loans.id','loans.loan_amount','loans.inst_amnt','loans.inst_months','loans.inst_type','loans.sanction_no','loans.date_disbursed','loans.loan_expiry',
            'loans.activity_id','loans.product_id','loans.region_id','loans.area_id','loans.branch_id','loans.team_id','loans.field_id','loans.project_id','loans.group_id','loans.application_id',
            'loans.created_at','loans.status','loans.is_lock','loans.reject_reason','loans.disbursement_id','loans.fund_request_id',
            'applications.id as application_id','applications.member_id','applications.application_no','members.id as member_id','members.full_name','members.cnic',
            /*'regions.name','areas.name','branches.name','projects.name',*/'groups.grp_no','groups.grp_type',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('application');
        $query->joinWith('application.member');
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        $query->joinWith('group');
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
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project' => [
                    'asc' => ['project.name' => SORT_ASC],
                    'desc' => ['project.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_months' => [
                    'asc' => ['inst_months' => SORT_ASC],
                    'desc' => ['inst_months' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_amnt' => [
                    'asc' => ['inst_amnt' => SORT_ASC],
                    'desc' => ['inst_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_type' => [
                    'asc' => ['inst_type' => SORT_ASC],
                    'desc' => ['inst_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
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
            'application_id' => $this->application_id,
            'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            //'date_disbursed' => $this->date_disbursed,
            'cheque_dt' => $this->cheque_dt,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'loans.team_id' => $this->team_id,
            'loans.field_id' => $this->field_id,
            'loan_expiry' => $this->loan_expiry,
            'loan_completed_date' => $this->loan_completed_date,
            'br_serial' => $this->br_serial,
            'due' => $this->due,
            'loans.status' => $this->status,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'loans.fund_request_id'=>$this->fund_request_id
        ]);

        $query->andFilterWhere(['like', 'project_table', $this->project_table])
            ->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'loans.inst_type', $this->inst_type])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            /*->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'projects.name', $this->project])
            ->andFilterWhere(['like', 'branches.name', $this->branch])*/
            //->andFilterWhere(['between','loans.created_at',$six_month_back_date,$cur_date])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);
        if(!isset($params['LoansSearch']['disb_date']))
        {
            $query->andFilterWhere(['between','loans.created_at',$six_month_back_date,$cur_date]);
        }
        if(isset($params['LoansSearch']['disb_date']) && is_null($this->date_disbursed))
        {
            $query->andFilterWhere(['>=', 'loans.created_at',$params['LoansSearch']['disb_date']]);
        }
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['like','date_disbursed' , strtotime($this->date_disbursed)]);
        }
        if(!empty($this->loan_amnt_frm) && !empty($this->loan_amnt_to)){
            $query->andFilterWhere(['between', 'loan_amount', $this->loan_amnt_frm,$this->loan_amnt_to]);
        }

        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchPledgeLoan($params,$export=false)
    {
        $cur_date=strtotime(date("Y-m-d H:i:s", strtotime('+5 hours')));
        $six_month_back_date=strtotime(date("Y-m-d",strtotime("-4 Months")));

        $query = Loans::find()
            ->select([
                'loans.id', 'loans.loan_amount', 'loans.inst_amnt', 'loans.inst_months', 'loans.inst_type', 'loans.sanction_no',
                'loans.date_disbursed', 'loans.loan_expiry', 'loans.activity_id', 'loans.product_id', 'loans.region_id',
                'loans.area_id', 'loans.branch_id', 'loans.team_id', 'loans.field_id', 'loans.project_id', 'loans.group_id',
                'loans.application_id', 'loans.created_at', 'loans.status', 'loans.is_lock', 'loans.reject_reason',
                'loans.disbursement_id', 'loans.fund_request_id',
                'applications.id as application_id', 'applications.member_id', 'applications.application_no',
                'members.id as member_id', 'members.full_name', 'members.cnic',
                'groups.grp_no', 'groups.grp_type', 'applications.is_pledged',
            ])
            ->joinWith(['application', 'application.member', 'group']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

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
            'application_id' => $this->application_id,
            'loans.project_id' => 132,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'loans.team_id' => $this->team_id,
            'due' => $this->due,
            'loans.status' => $this->status,
            'loans.fund_request_id'=>$this->fund_request_id
        ]);

        $query->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['like', 'members.name', $this->region])
            ->andFilterWhere(['like', 'applications.is_pledged', 0]);
        if(!isset($params['LoansSearch']['disb_date']))
        {
            $query->andFilterWhere(['between','loans.created_at',$six_month_back_date,$cur_date]);
        }
        if(isset($params['LoansSearch']['disb_date']) && is_null($this->date_disbursed))
        {
            $query->andFilterWhere(['>=', 'loans.created_at',$params['LoansSearch']['disb_date']]);
        }
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['like','date_disbursed' , strtotime($this->date_disbursed)]);
        }

        $query->orderBy('created_at desc');

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchLoan($params,$export=false)
    {
        $loginUser =  \Yii::$app->user->identity->getId();

        //$cur_date=strtotime((date("Y-m-d")));
//        $cur_date=strtotime(date("Y-m-d H:i:s", strtotime('+5 hours')));
//        $six_month_back_date=strtotime(date("Y-m-d",strtotime("-4 Months")));
        $query = Loans::find()->select([
            'loans.id','loans.loan_amount','loans.inst_amnt','loans.inst_months','loans.inst_type','loans.sanction_no','loans.date_disbursed','loans.loan_expiry',
            'loans.activity_id','loans.product_id','loans.region_id','loans.area_id','loans.branch_id','loans.team_id','loans.field_id','loans.project_id','loans.group_id','loans.application_id',
            'loans.created_at','loans.status','loans.is_lock','loans.reject_reason','loans.disbursement_id','loans.fund_request_id',
            'applications.id as application_id','applications.member_id','applications.application_no','members.id as member_id','members.full_name','members.cnic',
            /*'regions.name','areas.name','branches.name','projects.name',*/'groups.grp_no','groups.grp_type',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('application');
        $query->joinWith('application.member');
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        $query->joinWith('group');
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
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project' => [
                    'asc' => ['project.name' => SORT_ASC],
                    'desc' => ['project.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_months' => [
                    'asc' => ['inst_months' => SORT_ASC],
                    'desc' => ['inst_months' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_amnt' => [
                    'asc' => ['inst_amnt' => SORT_ASC],
                    'desc' => ['inst_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_type' => [
                    'asc' => ['inst_type' => SORT_ASC],
                    'desc' => ['inst_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
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
            'application_id' => $this->application_id,
            'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            //'date_disbursed' => $this->date_disbursed,
            'cheque_dt' => $this->cheque_dt,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'loans.team_id' => $this->team_id,
            'loans.field_id' => $this->field_id,
            'loan_expiry' => $this->loan_expiry,
            'loan_completed_date' => $this->loan_completed_date,
            'br_serial' => $this->br_serial,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'loans.fund_request_id'=>$this->fund_request_id
        ]);

        $query->andFilterWhere(['like', 'project_table', $this->project_table])
            ->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'loans.inst_type', $this->inst_type])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'loans.status', $this->status])
            ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            /*->andFilterWhere(['like', 'regions.name', $this->region])
            ->andFilterWhere(['like', 'areas.name', $this->area])
            ->andFilterWhere(['like', 'projects.name', $this->project])
            ->andFilterWhere(['like', 'branches.name', $this->branch])*/
            //->andFilterWhere(['between','loans.created_at',$six_month_back_date,$cur_date])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);
//        if(!isset($params['LoansSearch']['disb_date']))
//        {
//            $query->andFilterWhere(['between','loans.created_at',$six_month_back_date,$cur_date]);
//        }


        if(\Yii::$app->user->identity->designation_id == 8){
            $userMappings = UserStructureMapping::find()
                ->where(['user_id'=>$loginUser])
                ->andWhere(['obj_type'=>'area'])
                ->select(['user_id','obj_id'])
                ->one();
            if (isset($userMappings) && !empty($userMappings)){
                $query->andFilterWhere(['=', 'loans.area_id',$userMappings->obj_id]);
            }
        }

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

        $query = Loans::find()
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

        if(isset($params['project_id'])) {
            $query->andFilterWhere(['project_id' => $params['project_id']]);
        }

        if(isset($params['project_table'])) {
            $query->andFilterWhere(['project_table' => $params['project_table']]);
        }

        if(isset($params['activity_id'])) {
            $query->andFilterWhere(['activity_id' => $params['activity_id']]);
        }

        if(isset($params['product_id'])) {
            $query->andFilterWhere(['product_id' => $params['product_id']]);
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

        if(isset($params['group_id'])) {
            $query->andFilterWhere(['group_id' => $params['group_id']]);
        }

        if(isset($params['date_approved'])) {
            $query->andFilterWhere(['date_approved' => $params['date_approved']]);
        }

        if(isset($params['loan_amount'])) {
            $query->andFilterWhere(['loan_amount' => $params['loan_amount']]);
        }

        if(isset($params['cheque_no'])) {
            $query->andFilterWhere(['cheque_no' => $params['cheque_no']]);
        }

        if(isset($params['inst_amnt'])) {
            $query->andFilterWhere(['inst_amnt' => $params['inst_amnt']]);
        }

        if(isset($params['inst_months'])) {
            $query->andFilterWhere(['inst_months' => $params['inst_months']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['deleted'])) {
            $query->andFilterWhere(['deleted' => $params['deleted']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        if(isset($params['is_lock'])) {
            $query->andFilterWhere(['is_lock' => $params['is_lock']]);
        }

        if(isset($params['inst_type'])) {
            $query->andFilterWhere(['inst_type' => $params['inst_type']]);
        }

        if(isset($params['date_disbursed'])) {
            $query->andFilterWhere(['date_disbursed' => $params['date_disbursed']]);
        }

        if(isset($params['cheque_dt'])) {
            $query->andFilterWhere(['cheque_dt' => $params['cheque_dt']]);
        }

        if(isset($params['disbursement_id'])) {
            $query->andFilterWhere(['disbursement_id' => $params['disbursement_id']]);
        }

        if(isset($params['loan_expiry'])) {
            $query->andFilterWhere(['loan_expiry' => $params['loan_expiry']]);
        }

        if(isset($params['loan_completed_date'])) {
            $query->andFilterWhere(['loan_completed_date' => $params['loan_completed_date']]);
        }

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['due'])) {
            $query->andFilterWhere(['due' => $params['due']]);
        }

        if(isset($params['overdue'])) {
            $query->andFilterWhere(['overdue' => $params['overdue']]);
        }

        if(isset($params['balance'])) {
            $query->andFilterWhere(['balance' => $params['balance']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        if(!isset($order)) {
            $query->orderBy('created_at desc');
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

    public function searchApiProcessedHousing($params)
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
        $query = Loans::find()
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0, 'loan_tranches.status' =>1])
            ->andWhere(['in','project_id',StructureHelper::trancheProjects()]);


        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);
        if(isset($params['branch_id'])) {
            $query->andFilterWhere(['branch_id' => $params['branch_id']]);
        }

        $query->orderBy('loans.updated_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }


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

    public function searchApiPendingHousing($params)
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
        $tranches = LoanTranches::find()->select(['loan_id'])->joinWith('loan')->where(['in','loan_tranches.status',[1,2,3,4,5]])
            ->andWhere(['in','loans.project_id',StructureHelper::trancheProjects()])
            ->groupBy('loan_tranches.loan_id')->all();
        $loan_ids = [];
        foreach ($tranches as $tranche)
        {
            $loan_ids[] = $tranche->loan_id;
        }
        $query = Loans::find()
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0,'loan_tranches.status' => 0])
            ->andWhere(['in','project_id',StructureHelper::trancheProjects()])
            ->andFilterWhere(['not in','loans.id',$loan_ids]);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        $query->orderBy('loans.updated_at desc');

        if(isset($order)){
            $query->orderBy($order);
        }


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

    public function searchForTakaful($params)
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

        $query = Loans::find()
            ->select('loans.*')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->join('left join','fund_requests','fund_requests.id = loan_tranches.fund_request_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loans.deleted' => 0])
            ->andWhere(['loan_actions.action'=>'takaful','loan_actions.status'=>0])
            ->andWhere(['loans.status' => 'pending'])
            ->andWhere("loan_tranches.status = 4 and loan_tranches.date_disbursed = 0 and loan_tranches.cheque_no is not null and loan_tranches.fund_request_id != 0 and loan_tranches.cheque_no <> '' and loan_tranches.cheque_no != 0 and fund_requests.status = 'processed' ")
            // ->asArray(true)
            ->orderBy('fund_requests.updated_at DESC')
            ->limit($limit)
            ->offset($offset);

        //$query->joinWith('fundRequest');
        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['value'])) {
            $cnic = substr_replace($params['cnic'], '-', 5, 0);
            $cnic = substr_replace($cnic, '-', 13, 0);
            $query->joinWith('application');
            $query->joinWith('application.member');
            $query->andFilterWhere(['members.cnic' => $cnic]);
        }

        /*$query->andFilterWhere(['fund_requests.status' => 'processed']);
        $query->orderBy('fund_requests.updated_at desc');*/
        if(isset($order)){
            $query->orderBy($order);
        }
        $total = (int)$query->count('loans.id');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => ceil($total/$limit),
            'totalRecords' => $total
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function searchForApprovedTakaful($params)
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

        $query = Loans::find()
            ->select('loans.*')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->join('left join','fund_requests','fund_requests.id = loan_tranches.fund_request_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loans.deleted' => 0])
            ->andWhere(['loan_actions.action'=>'takaful','loan_actions.status'=>1])
            ->andWhere(['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => 'pending'])
            ->andWhere("loan_tranches.status = 4 and loan_tranches.date_disbursed = 0 and loan_tranches.cheque_no is not null and loan_tranches.fund_request_id != 0 and loan_tranches.cheque_no <> '' and loan_tranches.cheque_no != 0 and fund_requests.status = 'processed'")
            ->orderBy('fund_requests.updated_at DESC')
            // ->asArray(true)
            ->limit($limit)
            ->offset($offset);

        //$query->joinWith('fundRequest');
        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['cnic'])) {
            $query->joinWith('application');
            $query->joinWith('application.member');
            $query->andFilterWhere(['members.cnic' => $params['cnic']]);
        }

        /*$query->andFilterWhere(['fund_requests.status' => 'processed']);
        $query->orderBy('fund_requests.updated_at desc');*/
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

    public function searchGlobal($params)
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

        $query = Loans::find()
            ->select('loans.*')
            ->where(['loans.deleted' => 0])
            // ->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query->joinWith('application.member');
        $query->joinWith('group');
        // $query->joinWith('project');

        //$query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['cnic'])) {
            $query->andFilterWhere(['members.cnic' => $params['cnic']]);
        }

        if(isset($params['grp_no'])) {
            $query->andFilterWhere(['groups.grp_no' => $params['grp_no']]);
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

    public function searchApiCEProcessed($params)
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

        $query = Loans::find()
            ->select('loans.*')
            ->joinWith('tranch')
            ->innerJoin('loan_tranches','loans.id=(SELECT id
                        FROM loan_tranches                     
                        WHERE loan_tranches.loan_id=loans.id and loan_tranches.status = 1 and loan_tranches.date_disbursed != 0 and loan_tranches.disbursement_id != 0
                        and loan_tranches.fund_request_id != 0
                        ORDER BY loan_tranches.created_at
                        LIMIT 1)')
            ->join('inner join','group_actions','group_actions.parent_id=loans.group_id')
            ->where(['group_actions.action'=>'lac','group_actions.status'=>1])
            ->andWhere(['>=','group_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.deleted' => 0, 'loans.status' => 'collected','loan_tranches.status' => 1])
            ->andWhere(['in','loans.project_id',StructureHelper::trancheProjects()])
            ->andWhere(['!=','loan_tranches.date_disbursed',0 ])
            ->andWhere(['!=','loan_tranches.disbursement_id',0])
            ->andWhere(['!=','loan_tranches.fund_request_id',0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['application_id'])) {
            $query->andFilterWhere(['application_id' => $params['application_id']]);
        }

        if(isset($params['project_id'])) {
            $query->andFilterWhere(['project_id' => $params['project_id']]);
        }

        if(isset($params['project_table'])) {
            $query->andFilterWhere(['project_table' => $params['project_table']]);
        }

        if(isset($params['activity_id'])) {
            $query->andFilterWhere(['activity_id' => $params['activity_id']]);
        }

        if(isset($params['product_id'])) {
            $query->andFilterWhere(['product_id' => $params['product_id']]);
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

        if(isset($params['group_id'])) {
            $query->andFilterWhere(['group_id' => $params['group_id']]);
        }

        if(isset($params['date_approved'])) {
            $query->andFilterWhere(['date_approved' => $params['date_approved']]);
        }

        if(isset($params['loan_amount'])) {
            $query->andFilterWhere(['loan_amount' => $params['loan_amount']]);
        }

        if(isset($params['cheque_no'])) {
            $query->andFilterWhere(['cheque_no' => $params['cheque_no']]);
        }

        if(isset($params['inst_amnt'])) {
            $query->andFilterWhere(['inst_amnt' => $params['inst_amnt']]);
        }

        if(isset($params['inst_months'])) {
            $query->andFilterWhere(['inst_months' => $params['inst_months']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['loans.status' => $params['status']]);
        }

        if(isset($params['deleted'])) {
            $query->andFilterWhere(['deleted' => $params['deleted']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        if(isset($params['is_lock'])) {
            $query->andFilterWhere(['is_lock' => $params['is_lock']]);
        }

        if(isset($params['inst_type'])) {
            $query->andFilterWhere(['inst_type' => $params['inst_type']]);
        }

        if(isset($params['date_disbursed'])) {
            $query->andFilterWhere(['date_disbursed' => $params['date_disbursed']]);
        }

        if(isset($params['cheque_dt'])) {
            $query->andFilterWhere(['cheque_dt' => $params['cheque_dt']]);
        }

        if(isset($params['disbursement_id'])) {
            $query->andFilterWhere(['disbursement_id' => $params['disbursement_id']]);
        }

        if(isset($params['loan_expiry'])) {
            $query->andFilterWhere(['loan_expiry' => $params['loan_expiry']]);
        }

        if(isset($params['loan_completed_date'])) {
            $query->andFilterWhere(['loan_completed_date' => $params['loan_completed_date']]);
        }

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['due'])) {
            $query->andFilterWhere(['due' => $params['due']]);
        }

        if(isset($params['overdue'])) {
            $query->andFilterWhere(['overdue' => $params['overdue']]);
        }

        if(isset($params['balance'])) {
            $query->andFilterWhere(['balance' => $params['balance']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        if(!isset($order)) {
            $query->orderBy('loans.updated_at desc');
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

    public function searchApiProcessed($params)
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

        $query = Loans::find()
            ->select('loans.*')
            /*->where(['loan_actions.action'=>'takaful','loan_actions.status'=>1])
            ->andWhere(['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')])*/
            ->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->where(['loan_tranches.disbursement_id' => 0])
            ->andWhere(['loans.deleted' => 0, 'loan_tranches.status' => 7])
            ->andWhere(['loans.branch_id' => $params['branch_id']])
            ->andWhere(['not in','loans.status',['rejected']])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['application_id'])) {
            $query->andFilterWhere(['application_id' => $params['application_id']]);
        }

        if(isset($params['project_id'])) {
            $query->andFilterWhere(['project_id' => $params['project_id']]);
        }

        if(isset($params['project_table'])) {
            $query->andFilterWhere(['project_table' => $params['project_table']]);
        }

        if(isset($params['activity_id'])) {
            $query->andFilterWhere(['activity_id' => $params['activity_id']]);
        }

        if(isset($params['product_id'])) {
            $query->andFilterWhere(['product_id' => $params['product_id']]);
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

        if(isset($params['group_id'])) {
            $query->andFilterWhere(['group_id' => $params['group_id']]);
        }

        if(isset($params['date_approved'])) {
            $query->andFilterWhere(['date_approved' => $params['date_approved']]);
        }

        if(isset($params['loan_amount'])) {
            $query->andFilterWhere(['loan_amount' => $params['loan_amount']]);
        }

        if(isset($params['cheque_no'])) {
            $query->andFilterWhere(['cheque_no' => $params['cheque_no']]);
        }

        if(isset($params['inst_amnt'])) {
            $query->andFilterWhere(['inst_amnt' => $params['inst_amnt']]);
        }

        if(isset($params['inst_months'])) {
            $query->andFilterWhere(['inst_months' => $params['inst_months']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['loans.status' => $params['status']]);
        }

        if(isset($params['deleted'])) {
            $query->andFilterWhere(['deleted' => $params['deleted']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        if(isset($params['is_lock'])) {
            $query->andFilterWhere(['is_lock' => $params['is_lock']]);
        }

        if(isset($params['inst_type'])) {
            $query->andFilterWhere(['inst_type' => $params['inst_type']]);
        }

        if(isset($params['date_disbursed'])) {
            $query->andFilterWhere(['date_disbursed' => $params['date_disbursed']]);
        }

        if(isset($params['cheque_dt'])) {
            $query->andFilterWhere(['cheque_dt' => $params['cheque_dt']]);
        }

        if(isset($params['disbursement_id'])) {
            $query->andFilterWhere(['disbursement_id' => $params['disbursement_id']]);
        }

        if(isset($params['loan_expiry'])) {
            $query->andFilterWhere(['loan_expiry' => $params['loan_expiry']]);
        }

        if(isset($params['loan_completed_date'])) {
            $query->andFilterWhere(['loan_completed_date' => $params['loan_completed_date']]);
        }

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['due'])) {
            $query->andFilterWhere(['due' => $params['due']]);
        }

        if(isset($params['overdue'])) {
            $query->andFilterWhere(['overdue' => $params['overdue']]);
        }

        if(isset($params['balance'])) {
            $query->andFilterWhere(['balance' => $params['balance']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        if(!isset($order)) {
            $query->orderBy('loans.updated_at desc');
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

    public function searchApiDisbursed($params)
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

        $query = Loans::find()
            ->select('loans.*')
            ->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            ->where(['loan_tranches_actions.action'=>'disbursement','loan_tranches_actions.status'=>1])
            ->andWhere(['>=','loan_tranches_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.deleted' => 0, 'loans.status' => 'collected'])
            ->andWhere(['loan_tranches.status' => 6])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query = Yii::$app->Permission->getSearchFilterQuery($query,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['application_id'])) {
            $query->andFilterWhere(['application_id' => $params['application_id']]);
        }

        if(isset($params['project_id'])) {
            $query->andFilterWhere(['project_id' => $params['project_id']]);
        }

        if(isset($params['project_table'])) {
            $query->andFilterWhere(['project_table' => $params['project_table']]);
        }

        if(isset($params['activity_id'])) {
            $query->andFilterWhere(['activity_id' => $params['activity_id']]);
        }

        if(isset($params['product_id'])) {
            $query->andFilterWhere(['product_id' => $params['product_id']]);
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

        if(isset($params['group_id'])) {
            $query->andFilterWhere(['group_id' => $params['group_id']]);
        }

        if(isset($params['date_approved'])) {
            $query->andFilterWhere(['date_approved' => $params['date_approved']]);
        }

        if(isset($params['loan_amount'])) {
            $query->andFilterWhere(['loan_amount' => $params['loan_amount']]);
        }

        if(isset($params['cheque_no'])) {
            $query->andFilterWhere(['cheque_no' => $params['cheque_no']]);
        }

        if(isset($params['inst_amnt'])) {
            $query->andFilterWhere(['inst_amnt' => $params['inst_amnt']]);
        }

        if(isset($params['inst_months'])) {
            $query->andFilterWhere(['inst_months' => $params['inst_months']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['loans.status' => $params['status']]);
        }

        if(isset($params['deleted'])) {
            $query->andFilterWhere(['deleted' => $params['deleted']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }

        if(isset($params['is_lock'])) {
            $query->andFilterWhere(['is_lock' => $params['is_lock']]);
        }

        if(isset($params['inst_type'])) {
            $query->andFilterWhere(['inst_type' => $params['inst_type']]);
        }

        if(isset($params['date_disbursed'])) {
            $query->andFilterWhere(['date_disbursed' => $params['date_disbursed']]);
        }

        if(isset($params['cheque_dt'])) {
            $query->andFilterWhere(['cheque_dt' => $params['cheque_dt']]);
        }

        if(isset($params['disbursement_id'])) {
            $query->andFilterWhere(['disbursement_id' => $params['disbursement_id']]);
        }

        if(isset($params['loan_expiry'])) {
            $query->andFilterWhere(['loan_expiry' => $params['loan_expiry']]);
        }

        if(isset($params['loan_completed_date'])) {
            $query->andFilterWhere(['loan_completed_date' => $params['loan_completed_date']]);
        }

        if(isset($params['sanction_no'])) {
            $query->andFilterWhere(['sanction_no' => $params['sanction_no']]);
        }

        if(isset($params['due'])) {
            $query->andFilterWhere(['due' => $params['due']]);
        }

        if(isset($params['overdue'])) {
            $query->andFilterWhere(['overdue' => $params['overdue']]);
        }

        if(isset($params['balance'])) {
            $query->andFilterWhere(['balance' => $params['balance']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        if(!isset($order)) {
            $query->orderBy('loans.updated_at desc');
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

    public function searchchequewise($params,$export=false)
    {
        $query = LoanTranches::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100 // in case you want a default pagesize
            ]
        ]);
        // $dataProvider->pagination->pageSize = 500;
        $query->joinWith('loan');
        $query->joinWith('loan.application');
        $query->joinWith('loan.application.member');
        $query->joinWith('loan.application.group');
        /*$query->joinWith('branch');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('project');*/
        $dataProvider->setSort([
            'attributes' => [
                'member_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_parentage' => [
                    'asc' => ['members.parentage' => SORT_ASC],
                    'desc' => ['members.parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_tranches.cheque_no' => [
                    'asc' => ['loan_tranches.cheque_no' => SORT_ASC],
                    'desc' => ['loan_tranches.cheque_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_amnt' => [
                    'asc' => ['inst_amnt' => SORT_ASC],
                    'desc' => ['inst_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_months' => [
                    'asc' => ['inst_months' => SORT_ASC],
                    'desc' => ['inst_months' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_type' => [
                    'asc' => ['inst_type' => SORT_ASC],
                    'desc' => ['inst_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'loans.region_id' => [
                    'asc' => ['loans.region_id' => SORT_ASC],
                    'desc' => ['loans.region_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'area_id' => [
                    'asc' => ['area_id' => SORT_ASC],
                    'desc' => ['area_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'branch_id' => [
                    'asc' => ['branch_id' => SORT_ASC],
                    'desc' => ['branch_id' => SORT_DESC],
                    'default' => SORT_DESC
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
            'loan_amount' => $this->loan_amount,
            'tranch_amount' => $this->tranch_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'created_by' => $this->created_by,
            'is_lock' => $this->is_lock,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'loan_expiry' => $this->loan_expiry,
            'loans.project_id' => $this->project_id,
            'loans.deleted' => 0,
        ]);

        $query->andFilterWhere(['like', 'loan_tranches.cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['!=', 'loans.status', 'not collected'])
            ->andFilterWhere(['!=', 'loans.status', 'rejected'])
            ->andFilterWhere(['!=', 'loans.status', 'grant'])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'members.parentage', $this->member_parentage])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere([ 'tranch_no'=> $this->tranch_no])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);

        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {

            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loan_tranches.date_disbursed' => $this->date_disbursed]);
        }

        if (!is_null($this->date_approved) && strpos($this->date_approved, ' - ') !== false) {
            $date = explode(' - ', $this->date_approved);
            $query->andFilterWhere(['between', 'date_approved', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['date_approved' => $this->date_approved]);
        }

        if (!is_null($this->loan_expiry) && strpos($this->loan_expiry, ' - ') !== false) {
            $date = explode(' - ', $this->loan_expiry);
            $query->andFilterWhere(['between', 'loan_expiry', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['loan_expiry' => $this->loan_expiry]);
        }

        if (!is_null($this->cheque_date) && strpos($this->cheque_date, ' - ') !== false) {
            $date = explode(' - ', $this->cheque_dt);
            $query->andFilterWhere(['between', 'loan_tranches.cheque_date', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['loan_tranches.cheque_date' => $this->cheque_date]);
        }
        $query->orderBy('loan_tranches.cheque_no');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchchequewise_($params,$export=false)
    {
        $query = Loans::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100 // in case you want a default pagesize
            ]
        ]);
        // $dataProvider->pagination->pageSize = 500;
        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        /*$query->joinWith('branch');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('project');*/
        $dataProvider->setSort([
            'attributes' => [
                'member_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_parentage' => [
                    'asc' => ['members.parentage' => SORT_ASC],
                    'desc' => ['members.parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cheque_no' => [
                    'asc' => ['cheque_no' => SORT_ASC],
                    'desc' => ['cheque_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_amnt' => [
                    'asc' => ['inst_amnt' => SORT_ASC],
                    'desc' => ['inst_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_months' => [
                    'asc' => ['inst_months' => SORT_ASC],
                    'desc' => ['inst_months' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_type' => [
                    'asc' => ['inst_type' => SORT_ASC],
                    'desc' => ['inst_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'loans.region_id' => [
                    'asc' => ['loans.region_id' => SORT_ASC],
                    'desc' => ['loans.region_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'area_id' => [
                    'asc' => ['area_id' => SORT_ASC],
                    'desc' => ['area_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'branch_id' => [
                    'asc' => ['branch_id' => SORT_ASC],
                    'desc' => ['branch_id' => SORT_DESC],
                    'default' => SORT_DESC
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
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'created_by' => $this->created_by,
            'is_lock' => $this->is_lock,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'loan_expiry' => $this->loan_expiry,
            'loans.project_id' => $this->project_id,
            'loans.deleted' => 0,
        ]);

        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['!=', 'loans.status', 'not collected'])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'members.parentage', $this->member_parentage])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);

        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {

            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['date_disbursed' => $this->date_disbursed]);
        }

        if (!is_null($this->date_approved) && strpos($this->date_approved, ' - ') !== false) {
            $date = explode(' - ', $this->date_approved);
            $query->andFilterWhere(['between', 'date_approved', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['date_approved' => $this->date_approved]);
        }

        if (!is_null($this->loan_expiry) && strpos($this->loan_expiry, ' - ') !== false) {
            $date = explode(' - ', $this->loan_expiry);
            $query->andFilterWhere(['between', 'loan_expiry', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['loan_expiry' => $this->loan_expiry]);
        }

        if (!is_null($this->cheque_dt) && strpos($this->cheque_dt, ' - ') !== false) {
            $date = explode(' - ', $this->cheque_dt);
            $query->andFilterWhere(['between', 'cheque_dt', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['cheque_dt' => $this->cheque_dt]);
        }
        $query->orderBy('loans.cheque_no');
        print_r($query->all());
        die();
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchLedger($params)
    {
        $query = Loans::find();

        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('recoveries');
        $query->joinWith('branch');
        $query->joinWith('project');

        $this->load($params);

        $query->andFilterWhere([
            'loans.id' => $this->id,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'created_by' => $this->created_by,
            'is_lock' => $this->is_lock,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'loan_expiry' => $this->loan_expiry,
            'loans.project_id' => $this->project_id,
        ]);

        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'branches.name', $this->branch_name])
            ->andFilterWhere(['like', 'projects.name', $this->project_name])
            ->andFilterWhere(['like', 'application.member.full_name', $this->member_name])
            ->andFilterWhere(['like', 'application.member.cnic', $this->member_cnic]);

        $query->orderBy('recoveries.receive_date desc');

        return $query->one();
    }

    public function searchRecoveriesLedger($params)
    {
        //print_r($params);
        //die();
        $query = Loans::find();

        $query->joinWith('application');
        $query->joinWith('branch');
        $query->joinWith('application.member');
        $query->joinWith('project');
        $query->joinWith('recoveries');

        $this->load($params);

        $query->andFilterWhere([
            'loans.id' => $this->id,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'created_by' => $this->created_by,
            'is_lock' => $this->is_lock,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'loan_expiry' => $this->loan_expiry,
            'loans.project_id' => $this->project_id,
        ]);

        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'branches.name', $this->branch_name])
            ->andFilterWhere(['like', 'projects.name', $this->project_name])
            ->andFilterWhere(['like', 'application.member.full_name', $this->member_name])
            ->andFilterWhere(['like', 'application.member.cnic', $this->member_cnic]);

        $query->orderBy('recoveries.receive_date desc');
        //print_r($query->all());
        //die();

        return $query->all();
    }
    public function search_family_member_report($params,$export=false)
    {
        //print_r($params);
        //die();
        $query = Loans::find()->select(
            'loans.id,loans.application_id,loans.sanction_no,loans.loan_amount,loans.project_id,loans.date_disbursed,loans.region_id,loans.area_id,loans.branch_id,
            '
        );
        $query->joinWith('application.member');

        $this->load($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100 // in case you want a default pagesize
            ]
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'loans.id' => $this->id,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'created_by' => $this->created_by,
            'is_lock' => $this->is_lock,
            'loans.project_id' => $this->project_id,
        ]);
        if (!is_null($this->report_date) && strpos($this->report_date, ' - ') !== false) {
            $date = explode(' - ', $this->report_date);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['date_disbursed' => $this->date_disbursed]);
        }
        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            //->andFilterWhere(['like', 'branches.name', $this->branch_name])
            //->andFilterWhere(['like', 'projects.name', $this->project_name])
            ->andFilterWhere(['like', 'full_name', $this->member_name])
            ->andFilterWhere(['like', 'parentage', $this->member_parentage])
            ->andFilterWhere(['like', 'family_member_cnic', $this->family_member_cnic])
            ->andFilterWhere(['like', 'cnic', $this->member_cnic]);

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }
    public function search_dibursement_list_attendence($params,$branch_id,$export=false)
    {
        //$query=Loans::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);
        //$query=LoanTranchesSearch::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);
        $loans = Loans::find()->select(['loans.id','group_id','project_id'])
            ->join('inner join','loan_tranches','loans.id=loan_tranches.loan_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->join('inner join','applications_cib','loans.application_id=applications_cib.application_id') //cib newly added for secp check
//            ->join('inner join','nadra_verisys','loans.application_id=nadra_verisys.application_id') //nadra newly added for secp check
            ->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            ->where(['loan_actions.action'=>'account_verification','loan_actions.status'=>1])
            ->andWhere(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
            ->andWhere(['in','loans.status' ,["pending","collected"]])
            ->andWhere(['loan_tranches.disbursement_id' => 0 ])
            ->andWhere(['in', 'loans.project_id',  StructureHelper::trancheProjects()])
            ->andWhere(['in','loan_tranches.status' , [4,5]])
            ->orWhere(['and',['not in' , 'loans.project_id',  StructureHelper::trancheProjectsNotIn()], ['loan_actions.action'=>'takaful','loan_actions.status'=>1],['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')],['in','loans.status' ,["pending","collected"]],['loan_tranches.disbursement_id' => 0 ],['in','loan_tranches.status' , [4,5]]])
            ->andWhere(['applications_cib.status' => 1])    //cib newly added for secp check
//            ->andWhere(['nadra_verisys.status' => 1])    //nadra newly added for secp check
            ->orderBy('loan_tranches.updated_at desc')
            ->all();

        $groups_list = array();
        foreach ($loans as $loan){
            if (!in_array($loan->project_id, StructureHelper::kamyaabPakitanKarobarKistan())) {
                if(!in_array($loan->group_id,$groups_list)){
                    $groups_list[] = $loan->group_id;
                }
            }
            $accVerify_actions = \common\models\LoanActions::find()->where(['parent_id' => $loan->id, 'action' => 'account_verification'])->one();
            if(in_array($loan->project_id, StructureHelper::accountkamyaabPakitanProjects()) && (!empty($accVerify_actions) && $accVerify_actions->status == 1)){
                if(!in_array($loan->group_id,$groups_list)){
                    $groups_list[] = $loan->group_id;
                }
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
            ->where(['in','id',$groups_list]);

        //$query->joinWith('loanactions');
        //enable fund request step
        //$query->joinWith('fundRequest');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'groups.region_id'=>$this->region_id,
            'groups.area_id'=>$this->area_id,
            'groups.branch_id'=>$branch_id,
        ]);

        Yii::$app->Permission->getSearchFilterQuery($query,'groups', 'index','frontend');

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function search_dibursement_list($params,$export=false)
    {
        //$query=Loans::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);
        //$query=LoanTranchesSearch::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);

        $query = LoanTranches::find()/*->select(['loan_id, tranch_amount,loan_tranches.id, loans.project_id,loans.branch_id'])*/
        ->joinWith('loan')
            ->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            ->where(['loan_tranches_actions.action'=>'fund_request','loan_tranches_actions.status'=>1])
            ->andWhere(['>=','loan_tranches_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['in','loans.status' ,["pending","collected"]])
            ->andWhere(['in','loan_tranches.status' ,[4,5]])
            ->andWhere(['loan_tranches.disbursement_id' => 0])
            ->andWhere(['loan_tranches.date_disbursed' => 0]);


        //$query->joinWith('loanactions');
        //enable fund request step
        //$query->joinWith('fundRequest');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'loans.region_id'=>$this->region_id,
            'loans.area_id'=>$this->area_id,
            'loans.branch_id'=>$this->branch_id,
        ]);
        Yii::$app->Permission->getSearchFilterQuery($query,'loans', 'index','frontend');
        $query->orderBy('loans.sanction_no asc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function search_chequeprintloans($params)
    {
        $this->load($params);
        $query = Loans::find()->where(['loans.status'=>'pending'])
            ->join('inner join','groups','loans.group_id=groups.id')
            ->join('inner join','group_actions','group_actions.parent_id=groups.id')
            ->andWhere(['=','group_actions.action','lac'])
            ->andWhere(['=','group_actions.status','1'])
            ->andWhere(['is not','sanction_no',NULL])
            ->andWhere(['!=','sanction_no',''])
            ->andWhere(['loans.deleted'=>0]);
        if(!empty($this->branch_id)){
            $query ->andWhere(['=', 'loans.branch_id', $this->branch_id]);
        }
        return $query;
    }
    public function search_mega_disb_list($params,$export=false)
    {
        $query = Loans::find()->where(['disbursement_id'=>0,'date_disbursed'=>0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100 // in case you want a default pagesize
            ]
        ]);
        // $dataProvider->pagination->pageSize = 500;
        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        /*$query->joinWith('branch');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('project');*/
        $dataProvider->setSort([
            'attributes' => [
                'member_name' => [
                    'asc' => ['members.full_name' => SORT_ASC],
                    'desc' => ['members.full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_parentage' => [
                    'asc' => ['members.parentage' => SORT_ASC],
                    'desc' => ['members.parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'member_cnic' => [
                    'asc' => ['members.cnic' => SORT_ASC],
                    'desc' => ['members.cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cheque_no' => [
                    'asc' => ['cheque_no' => SORT_ASC],
                    'desc' => ['cheque_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_amnt' => [
                    'asc' => ['inst_amnt' => SORT_ASC],
                    'desc' => ['inst_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_months' => [
                    'asc' => ['inst_months' => SORT_ASC],
                    'desc' => ['inst_months' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'inst_type' => [
                    'asc' => ['inst_type' => SORT_ASC],
                    'desc' => ['inst_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'group_no' => [
                    'asc' => ['groups.grp_no' => SORT_ASC],
                    'desc' => ['groups.grp_no' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'project_id' => [
                    'asc' => ['project_id' => SORT_ASC],
                    'desc' => ['project_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'region_id' => [
                    'asc' => ['region_id' => SORT_ASC],
                    'desc' => ['region_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'area_id' => [
                    'asc' => ['area_id' => SORT_ASC],
                    'desc' => ['area_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'branch_id' => [
                    'asc' => ['branch_id' => SORT_ASC],
                    'desc' => ['branch_id' => SORT_DESC],
                    'default' => SORT_DESC
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
            'loan_amount' => $this->loan_amount,
            'sanction_no' => $this->sanction_no,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'loans.project_id' => $this->project_id,
            'loans.deleted' => 0,
        ]);

        $query
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'members.parentage', $this->member_parentage])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);


        if (!is_null($this->date_approved) && strpos($this->date_approved, ' - ') !== false) {
            $date = explode(' - ', $this->date_approved);
            $query->andFilterWhere(['between', 'date_approved', strtotime($date[0]), strtotime($date[1])]);
        } else {

            $query->andFilterWhere(['date_approved' => $this->date_approved]);
        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }
    public function search_takaf_pending_list($params,$export=false)
    {
        //$query=Loans::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);
        //$query=LoanTranchesSearch::find()->where(['or',['disbursement_id'=> null],['disbursement_id'=>0]])->andWhere(['or',['date_disbursed'=>0],['date_disbursed'=>null]]);

        /*$query = Loans::find()
            ->select('loans.*')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->join('left join','fund_requests','fund_requests.id = loan_tranches.fund_request_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loans.deleted' => 0])
            ->andWhere(['loan_actions.action'=>'takaful','loan_actions.status'=>0])
            ->andWhere(['loans.status' => 'pending'])
            ->andWhere("loan_tranches.status = 4 and loan_tranches.date_disbursed = 0 and loan_tranches.cheque_no is not null and loan_tranches.fund_request_id != 0 and loan_tranches.cheque_no <> '' and loan_tranches.cheque_no != 0 and fund_requests.status = 'processed' ")
            ->orderBy('fund_requests.updated_at DESC');*/
        $query = Loans::find()
            ->select('loans.*')
            ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loans.deleted' => 0])
            ->andWhere(['loan_actions.action'=>'takaful','loan_actions.status'=>0])
            ->andWhere(['in','loans.status' , ['pending']])
            ->andWhere("loan_tranches.status = 4 and loan_tranches.date_disbursed = 0 and loan_tranches.cheque_no is not null and loan_tranches.cheque_no <> '' and loan_tranches.cheque_no != 0 ");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'loans.region_id'=>$this->region_id,
            'loans.area_id'=>$this->area_id,
            'loans.branch_id'=>$this->branch_id,
        ]);
        Yii::$app->Permission->getSearchFilterQuery($query,'loans', 'add-takaf','frontend');
        //$query->orderBy('loans.sanction_no asc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function searchPendingTakaf($params,$export=false)
    {

        $query = Loans::find()
            ->select('loans.*')
            ->where(['loans.deleted' => 0])
            ->andWhere(['in','loans.status' , ['collected']])
            ->andWhere(['in','loans.project_id',[77,78]]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $this->load($params);
        if (!$this->validate()) {

            return $dataProvider;
        }
        $query->andFilterWhere([
            'loans.region_id'=>$this->region_id,
            'loans.area_id'=>$this->area_id,
            'loans.branch_id'=>$this->branch_id,
        ]);

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function search_bm_list($params,$export=false)
    {
        $this->load($params);
        $tranches = LoanTranches::find()->select(['loan_id'])->joinWith('loan')->where(['in', 'loan_tranches.status', [1, 2, 3, 4]])
            ->andWhere(['in','loans.project_id',StructureHelper::trancheProjects()])
            ->groupBy('loan_tranches.loan_id')->all();
        $loan_ids = [];
        foreach ($tranches as $tranche) {
            $loan_ids[] = $tranche->loan_id;
        }
        $query = Loans::find()
            ->join('inner join', 'loan_tranches', 'loans.id= loan_tranches.loan_id')
            ->where(['loans.status' => 'collected','loans.deleted'=>0,'loan_tranches.status' => 0])
            ->andWhere(['in','loans.project_id',StructureHelper::trancheProjects()])
            ->andFilterWhere(['not in','loans.id',$loan_ids]);
        if(!empty($this->branch_id)){
            $query->andFilterWhere(['loans.branch_id' => $this->branch_id]);
        }
        return $query;
    }
    public function search_ready_for_disbursement_list($params,$export=false){

        $query = LoanTranches::find()->where(['loan_tranches.status' => 3])
            ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
            //->join('inner join','groups','loans.group_id=groups.id')
            ->join('inner join','group_actions','group_actions.parent_id=loans.group_id')
            ->andWhere(['=','group_actions.action','lac'])
            ->andWhere(['=','group_actions.status','1'])
            ->andWhere(['in','loans.status',['not collected','pending','collected']])
            ->andWhere(['loans.deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['=', 'project_id', $this->project_id])
             //->andFilterWhere(['=', 'branch_id', $this->branch_id])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['=', 'loans.region_id', $this->region_id])
            ->andFilterWhere(['=', 'loans.area_id',  $this->area_id])
            ->andFilterWhere(['=', 'loans.branch_id',  $this->branch_id])
            ->andFilterWhere(['=', 'loans.status',  $this->status]);
            $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchRemoveFundRequest($params,$export=false){

        $query = LoanTranches::find()
            ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
            ->where(['loan_tranches.status' => 4])
            ->andWhere(['in','loans.status',['not collected','pending','collected']])
            ->andWhere(['loan_tranches.fund_request_id' => 0])
            ->andWhere(['loans.deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['=', 'project_id', $this->project_id])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['=', 'loans.region_id', $this->region_id])
            ->andFilterWhere(['=', 'loans.area_id',  $this->area_id])
            ->andFilterWhere(['=', 'loans.branch_id',  $this->branch_id]);
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function search_publish_loan_list($params,$export=false){

        $query = LoanTranches::find()->where(['loan_tranches.status' => 8])
            ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
            ->join('left join', 'disbursement_details', 'disbursement_details.tranche_id=loan_tranches.id and disbursement_details.status in (0,1,3,4,5,6) and disbursement_details.deleted=0')
            ->join('inner join', 'applications', 'applications.id=loans.application_id')
            ->join('inner join', 'members_account', 'members_account.member_id=applications.member_id')
            ->andWhere(['=','members_account.is_current','1'])
            ->andWhere(['!=','loans.status',"rejected"])
            ->andWhere(['is','disbursement_details.tranche_id',null])
            ->andWhere(['loans.deleted' => 0])
            ->andWhere([
                'or',
                [
                    'and',
                    ['applications.project_id' => 132],
                    ['applications.is_pledged' => 1]
                ],
                [
                    'and',
                    ['!=', 'applications.project_id', 132],
                    ['applications.is_pledged' => 0]
                ]
            ]);

//        echo $query->createCommand()->getRawSql();
//        die();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['=', 'loans.project_id', $this->project_id])
            //->andFilterWhere(['=', 'branch_id', $this->branch_id])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['=', 'loans.region_id', $this->region_id])
            ->andFilterWhere(['=', 'loans.area_id',  $this->area_id])
            ->andFilterWhere(['=', 'loans.branch_id',  $this->branch_id])
            ->andFilterWhere(['=', 'members_account.bank_name',  $this->bank]);

//        if($this->project_id == 132){
//            $query->andFilterWhere(['=', 'applications.is_pledged', 1]);
//        }

        $query->orderBy('created_at desc');
        Yii::$app->Permission->getSearchFilterQuery($query,'loans', 'index','frontend');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
    public function searchReferralReport($params,$export=false){

        $date1= strtotime(date('d M y'));
        $date2=strtotime(date('d M y',strtotime('-3 months')));

        $query = Loans::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(!isset($params['LoansSearch']['date_disbursed'])){
            $disb_date=array(date('Y-m-d',$date2),date('Y-m-d',$date1));
            $this->date_disbursed = implode('-' ,$disb_date);
        }

        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.referral');
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'loans.region_id'=>$this->region_id,
            'loans.area_id'=>$this->area_id,
            'loans.branch_id'=>$this->branch_id,
            'loans.loan_amount'=>$this->loan_amount,
            'loans.project_id'=>$this->project_id,
            'applications.application_no'=>$this->application_no,
            'loans.sanction_no'=>$this->sanction_no,
            'applications.referral_id'=>$this->referral_id,
        ]);
        $query->andFilterWhere(['>', 'loans.date_disbursed', 0])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['in', 'loans.status', ['collected','loan completed']])
            ->andFilterWhere(['like', 'members.full_name',  $this->member_name])
            ->andFilterWhere(['like', 'members.cnic',  $this->member_cnic])
            ->andFilterWhere(['=', 'members.parentage',  $this->member_parentage])
            ->andFilterWhere(['=', 'applications.status',  'approved'])
            ->andFilterWhere(['>', 'applications.referral_id',  0]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['between','loans.date_disbursed' ,$date2,$date1]);
        }
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchwriteoff($params,$export=false) {

        $date1= strtotime(date('d M y'));
        $date2=strtotime(date('d M y',strtotime('-3 months')));

        $query = Writeoff::find()->select(['loans.id as loanId,applications.id as appid,activities.name as activity_name,branches.name as branch_name, areas.name as area_name,projects.name as project_name, regions.name as region_name, members.full_name as member_name,loans.sanction_no, loans.loan_amount, members.parentage as member_parentage, members.cnic as member_cnic,loans.date_disbursed,
                        (SELECT phone from members_phone WHERE members_phone.phone_type="mobile" and members_phone.member_id=applications.member_id) as mobile,
                        (select amount from recoveries WHERE recoveries.loan_id=loanId and recoveries.deleted=0 and source ="WR_OFF") as write_off_amount ,
                        (select recoveries.receive_date from recoveries WHERE recoveries.loan_id=loanId and recoveries.deleted=0 and source ="WR_OFF" ) as write_off_date,
                        (select users.fullname from users INNER JOIN recoveries on users.id=recoveries.created_by WHERE recoveries.loan_id=loanId and recoveries.deleted=0 and source ="WR_OFF" )as write_off_by

        ']);

        if(!isset($params['LoansSearch']['date_disbursed'])){
            $disb_date=array(date('Y-m-d',$date2),date('Y-m-d',$date1));
            $this->date_disbursed = implode('-' ,$disb_date);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('application');
        $query->joinWith('project');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('branch');
        $query->joinWith('activity');
        $query->joinWith('application.member');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'members.cnic' => $this->member_cnic,
            'loan_amount' => $this->loan_amount,
            'loans.sanction_no' => $this->sanction_no,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'loans.project_id' => $this->project_id,
            'loans.activity_id' => $this->activity_id,
            'loans.deleted' => 0,
            'loans.status' => 'loan completed'
        ]);
        $query->andFilterWhere(['>', 'loans.date_disbursed', 0])
            ->andFilterWhere(['like', 'members.parentage', $this->member_parentage])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['between','loans.date_disbursed' ,$date2,$date1]);
        }
        $query->having('write_off_amount>0');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchVigaLoan($params)
    {
        $query = Loans::find()->andwhere(['is','viga_loans.loan_id',NULL]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('branch');
        $query->joinWith('branch.city');
        $query->joinWith('branch.province');
        $query->joinWith('group');
        $query->joinWith('vigaLoan');
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'application_id' => $this->application_id,
            'loans.branch_id' => $this->branch_id,
            'branches.city_id' => $this->city_id,
            'branches.province_id' => $this->province_id,
            'loans.product_id' => $this->product_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['=', 'loans.status', "collected"])
            ->andFilterWhere(['>', 'loans.date_disbursed',0 ])
            ->andFilterWhere(['=', 'loans.deleted', 0])
            ->andFilterWhere(['like', 'members.full_name', $this->member_name])
            ->andFilterWhere(['like', 'applications.application_no', $this->application_no])
            ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['like', 'groups.grp_no', $this->group_no]);

        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['like','date_disbursed' , strtotime($this->date_disbursed)]);
        }
            return $dataProvider;
    }

}
