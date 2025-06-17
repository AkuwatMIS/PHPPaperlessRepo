<?php

namespace common\models\search;

use common\components\Helpers\UsersHelper;
use common\models\UserStructureMapping;
use common\models\UserTransferHierarchy;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Users;
use yii\data\SqlDataProvider;
use yii\db\Query;

/**
 * UsersSearch represents the model behind the search form about `common\models\UsersCopy`.
 */
class UsersSearch extends \common\models\Users
{
    public $no_of_members;
    public $no_of_applications;
    public $no_of_social_appraisals;
    public $no_of_business_appraisals;
    public $no_of_verifications;
    public $no_of_groups;
    public $no_of_loans;
    public $no_of_fund_requests;
    public $no_of_disbursements;
    public $no_of_recoveries;
    public $region_id;
    public $area_id;
    public $branch_id;
    public $team_id;
    public $field_id;
    public $report_date;
    public $role;
    public $designation_name;
    public $designation_code;
    public $branch_name;
    public $area_name;
    public $region_name;
    public $branches = array();
    public $type;
    public $table_name;
    public $platform;
    public $role_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['username', 'fullname', 'father_name', 'email','cnic', 'alternate_email', 'password', 'auth_key', 'password_hash', 'password_reset_token', 'last_login_at', 'last_login_token', 'image', 'mobile', 'joining_date', 'emp_code', 'is_block', 'reason', 'block_date', 'team_name', 'status', 'created_at', 'updated_at','role_name'], 'safe'],
            [['no_of_members','no_of_applications','no_of_social_appraisals','no_of_business_appraisals','no_of_verifications','no_of_groups','no_of_loans','no_of_fund_requests','no_of_disbursements','no_of_recoveries','region_id','area_id','branch_id','team_id','field_id','report_date','role','platform',
                'designation_name','designation_code','branch_name','$area_name','region_name','branches','type','table_name'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = \common\models\Users::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('region');


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'cnic' => $this->cnic,
            'last_login_at' => $this->last_login_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'joining_date' => $this->joining_date,
            'block_date' => $this->block_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'father_name', $this->father_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'alternate_email', $this->alternate_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_login_token', $this->last_login_token])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'emp_code', $this->emp_code])
            ->andFilterWhere(['like', 'is_block', $this->is_block])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchUsers($params,$export=false)
    {
        $query = \common\models\Users::find();

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
            'id' => $this->id,
            'city_id' => $this->city_id,
            'cnic' => $this->cnic,
            'last_login_at' => $this->last_login_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'joining_date' => $this->joining_date,
            'block_date' => $this->block_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => Yii::$app->user->id,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'father_name', $this->father_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'alternate_email', $this->alternate_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_login_token', $this->last_login_token])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'emp_code', $this->emp_code])
            ->andFilterWhere(['like', 'is_block', $this->is_block])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchManagement($params,$export=false)
    {
        $query = \common\models\Users::find();

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
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $sub_user_roles = UserTransferHierarchy::find()->select('value,type')->where(['role' => $user_role])->andWhere(['!=','type','promotion_level'])->asArray()->all();

        $roles = [];
        foreach ($sub_user_roles as $role)
        {

            $role_name = explode( ',',$role['value']);
            $roles = array_unique(array_merge($roles,$role_name));
        }
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $str=[];

        if(in_array($user_role,['RM','RA','RC','DM'])){
            $type='region';
            $region = UserStructureMapping::find()->select(['obj_id'])->where(['user_id' => Yii::$app->user->getId(),'obj_type'=>$type])/*->asArray()*/->all();
            foreach ($region as $r){
                $str[]=$r->obj_id;
            }
        }else if(in_array($user_role,['AM','AA','AAA','DEO'])){
            $type='area';
            $area = UserStructureMapping::find()->select(['obj_id'])->where(['user_id' => Yii::$app->user->getId(),'obj_type'=>$type])/*->asArray()*/->all();
            foreach ($area as $r){
                $str[]=$r->obj_id;
            }
        }else if(in_array($user_role,['BM','LO'])){
            $type='branch';
            $branch = UserStructureMapping::find()->select(['obj_id'])->where(['user_id' => Yii::$app->user->getId(),'obj_type'=>$type])/*->asArray()*/->all();
            foreach ($branch as $r){
                $str[]=$r->obj_id;
            }
        }

        $query->joinWith('role')->where(['in','item_name' , $roles])->one();
        if(in_array($user_role,['RM','RA','RC','DM','AM','AA','AAA','DEO','BM','LO'])){
            $query->joinWith('structure')->where(['in','user_structure_mapping.obj_type',$type])->andWhere(['in','obj_id',$str]);
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'cnic' => $this->cnic,
            'last_login_at' => $this->last_login_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'joining_date' => $this->joining_date,
            'block_date' => $this->block_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'father_name', $this->father_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'alternate_email', $this->alternate_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_login_token', $this->last_login_token])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'emp_code', $this->emp_code])
            ->andFilterWhere(['like', 'is_block', $this->is_block])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchTransfer($params,$export=false)
    {
        $query = \common\models\Users::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;

        $user_role = UsersHelper::getRole(Yii::$app->user->getId());

        if($user_role == 'BM')
        {
            $parent_type = 'branch';
        }
        if($user_role == 'AM')
        {
            $parent_type = 'area';
        }
        if($user_role == 'RM')
        {
            $parent_type = 'region';
        }
        $parent_value =Yii::$app->user->identity->branch->obj_id;
        /*print_r($user_role);
        die();*/
        $sub_user_roles = UserTransferHierarchy::find()->select('value,type')->where(['role' => $user_role])->asArray()->all();

        $roles = [];
        foreach ($sub_user_roles as $role)
        {

            $role_name = explode( ',',$role['value']);
            $roles = array_unique(array_merge($roles,$role_name));
        }
        $query->joinWith($parent_type)->joinWith('role')->where(['in','item_name' , $roles,'obj_id' =>$parent_value])->one();
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'cnic' => $this->cnic,
            'last_login_at' => $this->last_login_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'joining_date' => $this->joining_date,
            'block_date' => $this->block_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'father_name', $this->father_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'alternate_email', $this->alternate_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_login_token', $this->last_login_token])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'emp_code', $this->emp_code])
            ->andFilterWhere(['like', 'is_block', $this->is_block])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('created_at desc');

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchuserreport($params,$export=false)
    {
        $this->load($params);
        $member='';
        $application='';
        $social_appraisal='';
        $business_appraisal='';
        $verification='';
        $group='';
        $loans='';
        $fund_request='';
        $disbursement='';
        $recovery='';

        if(!empty($this->report_date)){
            if (!is_null($this->report_date) && strpos($this->report_date, ' - ') !== false) {
                $date = explode(' - ', $this->report_date);
                $date[0]=strtotime($date[0]);
                $date[1]=strtotime($date[1]);
                $member='and members.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $application='and applications.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $social_appraisal='and appraisals_social.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $business_appraisal='and appraisals_business.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $verification='and applications.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $group='and groups.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $loans='and loans.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $fund_request='and loans.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $disbursement='and loans.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $recovery='and recoveries.created_at between "'.$date[0].'" and "'.$date[1].'"';

            }
        }
        if(!empty($this->platform)){

                $member.=' and members.platform ="'.$this->platform.'"';
                $application.=' and applications.platform ="'.$this->platform.'"';
                $social_appraisal.=' and appraisals_social.platform="'.$this->platform.'"';
                $business_appraisal.=' and appraisals_business.platform ="'.$this->platform.'"';
                $verification.=' and applications.platform= "'.$this->platform.'"';
                $group.=' and groups.platform="'.$this->platform.'"';
                $loans.=' and loans.platform="'.$this->platform.'"';
                $fund_request.=' and loans.platform="'.$this->platform.'"';
                $disbursement.='  and loans.platform="'.$this->platform.'"';
                $recovery.=' and recoveries.platform="'.$this->platform.'"';

        }

        $query = \common\models\Users::find()->select(['users.id', 'users.username', 'users.fullname','users.cnic', 'users.emp_code',
            '(select count(members.id) from members where members.created_by=users.id '.$member.') as no_of_members',
            '(select count(applications.id) from applications where applications.created_by=users.id '.$application.')as no_of_applications',
            '(select count(appraisals_social.id) from appraisals_social where appraisals_social.created_by=users.id '.$social_appraisal.')as no_of_social_appraisals',
            '(select count(appraisals_business.id) from appraisals_business where appraisals_business.created_by=users.id '.$business_appraisal.')as no_of_business_appraisals',
            '(select count(applications.id) from applications where applications.created_by=users.id and applications.status="approved" '.$verification.')as no_of_verifications',
            '(select count(groups.id) from groups where groups.created_by=users.id '.$group.')as no_of_groups',
            '(select count(loans.id) from loans where loans.created_by=users.id '.$loans.')as no_of_loans',
            '(select count(loans.id) from loans where loans.created_by=users.id and loans.fund_request_id!="0" '.$fund_request.')as no_of_fund_requests',
            '(select count(loans.id) from loans where loans.created_by=users.id and loans.disbursement_id!="0" '.$disbursement.')as no_of_disbursements',
            '(select count(recoveries.id) from recoveries where recoveries.created_by=users.id '.$recovery.')as no_of_recoveries',
            '(select (user_structure_mapping.obj_id) from user_structure_mapping where user_structure_mapping.user_id=users.id and user_structure_mapping.obj_type="region" LIMIT 1)as region_id',
            '(select (user_structure_mapping.obj_id) from user_structure_mapping where user_structure_mapping.user_id=users.id and user_structure_mapping.obj_type="area" LIMIT 1)as area_id',
            '(select (user_structure_mapping.obj_id) from user_structure_mapping where user_structure_mapping.user_id=users.id and user_structure_mapping.obj_type="branch" LIMIT 1)as branch_id',
            '(select (user_structure_mapping.obj_id) from user_structure_mapping where user_structure_mapping.user_id=users.id and user_structure_mapping.obj_type="team" LIMIT 1)as team_id',
            '(select (user_structure_mapping.obj_id) from user_structure_mapping where user_structure_mapping.user_id=users.id and user_structure_mapping.obj_type="field" LIMIT 1)as field_id',
            ])
            //->join('join','auth_assignment','auth_assignment.user_id=users.id')
        ;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        

        $dataProvider->pagination->pageSize=50;
        $query->joinWith('role');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([

            'id' => $this->id,
            'users.cnic' => $this->cnic,
            'users.emp_code'=>$this->emp_code,
            'users.username'=>$this->username,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'auth_assignment.item_name'=>$this->role,

        ]);

        $query->andFilterHaving([

            'no_of_members'=>$this->no_of_members,
            'no_of_applications'=>$this->no_of_applications,
            'no_of_social_appraisals'=>$this->no_of_social_appraisals,
            'no_of_business_appraisals'=>$this->no_of_business_appraisals,
            'no_of_groups'=>$this->no_of_groups,
            'no_of_verifications'=>$this->no_of_verifications,
            'no_of_loans'=>$this->no_of_loans,
            'no_of_fund_requests'=>$this->no_of_fund_requests,
            'no_of_disbursements'=>$this->no_of_disbursements,
            'no_of_recoveries'=>$this->no_of_recoveries,
            'region_id'=>$this->region_id,
            'area_id'=>$this->area_id,
            'branch_id'=>$this->branch_id,
            'team_id'=>$this->team_id,
            'field_id'=>$this->field_id,
        ]);

        //$query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function search_cih_by_user($user_id)
    {
        $query = Users::find()
            ->select([
                'id', 'username', 'fullname', 'email',
                '@cih_amount := ( select coalesce(sum( ' . $this->table_name . '.amount), 0) as amount from  ' . $this->table_name . '
                 where  ' . $this->table_name . '.id not in ( select cih_type_id 
                            from cih_transactions_mapping 
                            where cih_transactions_mapping.cih_type_id = ' . $this->table_name . '.id 
                            and cih_transactions_mapping.type = "' . $this->type . '")
                 and ' . $this->table_name . '.created_by = users.id and  ' . $this->table_name . '.source = "cc" and ' . $this->table_name . '.transaction_id= 0) as cih_amount,
        
                 @cih_partial := (select coalesce(sum(' . $this->table_name . '.amount), 0) as amount from ' . $this->table_name . '
                 where ' . $this->table_name . '.id in ( select cih_type_id 
                            from cih_transactions_mapping 
                            where cih_transactions_mapping.cih_type_id = ' . $this->table_name . '.id 
                            and cih_transactions_mapping.type = "' . $this->type . '")
                 and ' . $this->table_name . '.created_by = users.id and  ' . $this->table_name . '.source = "cc" and ' . $this->table_name . '.transaction_id= 0) as cih_partial,
         
                 @cih_partial_sum := ( select coalesce(sum(cih_transactions_mapping.amount), 0) as amount from ' . $this->table_name . '
                    inner join cih_transactions_mapping on cih_transactions_mapping.cih_type_id = ' . $this->table_name . '.id
                    where  ' . $this->table_name . '.created_by = users.id and  ' . $this->table_name . '.source = "cc" and ' . $this->table_name . '.transaction_id= 0 and cih_transactions_mapping.type = "' . $this->type . '") as cih_partial_sum ,
                @cih := (@cih_amount + (@cih_partial - @cih_partial_sum)) as cih,
                @deposited := @cih_partial_sum as deposited',


            ])
            ->where(['status' => 1,'id'=>$user_id])->asArray()->one();
        return $query;
    }

    public function searchcih($params,$export=false)
    {
        $cond = '';
        if (isset($params['UsersSearch']['emp_code']) && !empty($params['UsersSearch']['emp_code'])) {
            $cond .= " && users.emp_code = '" . $params['UsersSearch']['emp_code']. "'";
        }

        if (isset($params['UsersSearch']['username']) && !empty($params['UsersSearch']['username'])) {
            $cond .= " && users.username  LIKE '". '%' . $params['UsersSearch']['username'].'%' ."'";
        }

        if (isset($params['UsersSearch']['email']) && !empty($params['UsersSearch']['email'])) {
            $cond .= " && users.email  LIKE '". '%' . $params['UsersSearch']['email'].'%' ."'";
        }
        $sql ="select users.id, users.username,users.email,users.emp_code,
         @total_amount := ( select coalesce(sum(r.amount), 0) as credit from  $this->table_name  r
          where  r.user_id = users.id  and r.source = 'cc' ) as total_amount,
         
        @cih_amount := ( select coalesce(sum(r.amount), 0) as credit from  $this->table_name  r
           where r.id not in (select cih_type_id from cih_transactions_mapping where cih_transactions_mapping.cih_type_id = r.id  and cih_transactions_mapping.type = '$this->type')
            and r.user_id = users.id and r.source = 'cc' and r.transaction_id= 0) as cih_amount,
        @cih_partial := (select coalesce(sum(r.amount), 0) as credit from $this->table_name r 
            where r.id in (select cih_type_id from cih_transactions_mapping where cih_transactions_mapping.cih_type_id = r.id  and cih_transactions_mapping.type = '$this->type')
            and r.user_id = users.id and r.source = 'cc' and r.transaction_id = 0 ) as cih_partial,
         @cih_partial_sum := ( select coalesce(sum(cih_transactions_mapping.amount), 0) as credit from $this->table_name r 
            inner join cih_transactions_mapping on cih_transactions_mapping.cih_type_id = r.id
            where r.user_id = users.id and r.source = 'cc' and r.transaction_id= 0  and cih_transactions_mapping.type = '$this->type' ) as cih_partial_sum ,
        @cih := (@cih_amount + (@cih_partial - @cih_partial_sum)) as cih,
        @deposited := @cih_partial_sum as deposited
        FROM users left join auth_assignment on auth_assignment.user_id = users.id
                where auth_assignment.item_name = 'COLLECTOR'  " . $cond . "
        ";

        $query = Yii::$app->db->createCommand($sql)->queryAll();

        /* print_r($query);
         die();*/
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
        ]);
        $this->load($params);
        $dataProvider->setSort([
            'attributes' => [
                'cih' => [
                    'asc' => ['cih' => SORT_ASC],
                    'desc' => ['cih' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'username' => [
                    'asc' => ['username' => SORT_ASC],
                    'desc' => ['username' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'emp_code' => [
                    'asc' => ['emp_code' => SORT_ASC],
                    'desc' => ['emp_code' => SORT_DESC],
                    'default' => SORT_ASC
                ],

            ]        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }

    }

    public function searchStaff($params)
    {
        /*$user=Yii::$app->user->getId();
        $role=AuthAssignment::find()->where(['user_id'=>$user])->one();

        if($role->item_name=='RM'){
           $descrptn=UserStructureMapping::find()->where(['user_id'=>$user,'obj_type'=>'region'])->one();
        $query = Users::find()->select('username,users.mobile,image');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
             $query->joinWith(['role'])
             ->joinWith(['structure'])
            ->andWhere(['=','users.status', 1])
            ->andWhere(['=','auth_assignment.item_name','AM'])
            ->andWhere(['=','user_structure_mapping.obj_type',$descrptn->obj_type])
            ->andWhere(['=','user_structure_mapping.obj_id',$descrptn->obj_id])
                       ->all();
        return $dataProvider;
    }
        else if($role->item_name=='RC'){
            $descrptn=UserStructureMapping::find()->where(['user_id'=>$user,'obj_type'=>'region'])->one();
            $query = Users::find()->select('username,users.mobile,image');
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $query->joinWith(['role'])
                ->joinWith(['structure'])
                ->andWhere(['=','users.status', 1])
                ->andWhere(['=','auth_assignment.item_name','AM'])
                ->andWhere(['=','user_structure_mapping.obj_type',$descrptn->obj_type])
                ->andWhere(['=','user_structure_mapping.obj_id',$descrptn->obj_id])
                ->all();
            return $dataProvider;
        }*/
        $query=Users::find();
        $this->load($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith(['role']);
        $query->join('LEFT JOIN','user_structure_mapping as user_area','users.id=user_area.user_id and user_area.obj_type="area"');
        //$query->joinWith(['area']);
        $query->joinWith(['region']);
        $query->andFilterWhere([
            'id' => $this->id,
            //'city_id' => $this->city_id,
            'cnic' => $this->cnic,
            /*'last_login_at' => $this->last_login_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'joining_date' => $this->joining_date,
            'block_date' => $this->block_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,*/
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'father_name', $this->father_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'alternate_email', $this->alternate_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_login_token', $this->last_login_token])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'emp_code', $this->emp_code])
            ->andFilterWhere(['like', 'is_block', $this->is_block])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['=', 'status', 1])
            ->andFilterWhere(['=', 'is_block', 0])
            ->andFilterWhere(['in', 'auth_assignment.item_name', ['RM','AM']])
            ->andFilterWhere(['=', 'auth_assignment.item_name', $this->role_name])
            ->andFilterWhere(['=', 'user_area.obj_id', $this->area_id])
            ->andFilterWhere(['=', 'user_structure_mapping.obj_id', $this->region_id]);
        $query->orderBy('created_at desc');
        //echo '<pre>';
       // print_r($query->all());die();
        return $dataProvider;
        //return $query;

    }
}
