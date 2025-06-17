<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Branches;

/**
 * BranchesSearch represents the model behind the search form about `common\models\Branches`.
 */
class BranchesSearch extends Branches
{
    /**
     * @inheritdoc
     */
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
    public $report_date;
    public $platform;

    public $date;
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'cr_division_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['type', 'name', 'code', 'uc', 'village', 'address', 'mobile', 'description', 'opening_date', 'status', 'created_at', 'updated_at','date'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['no_of_members','no_of_applications','no_of_social_appraisals','no_of_business_appraisals','no_of_verifications','no_of_groups','no_of_loans','no_of_fund_requests','no_of_disbursements','no_of_recoveries','report_date','platform'], 'safe'],
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
        $query = Branches::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'city_id' => $this->city_id,
            'tehsil_id' => $this->tehsil_id,
            'district_id' => $this->district_id,
            'division_id' => $this->division_id,
            'province_id' => $this->province_id,
            'country_id' => $this->country_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'opening_date' => $this->opening_date,
            'cr_division_id' => $this->cr_division_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'uc', $this->uc])
            ->andFilterWhere(['like', 'village', $this->village])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status]);
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

        $query = Branches::find()
            ->select('*')
            ->where(['deleted' => 0])
            //->join('inner join','application_actions','applications.id=application_actions.parent_id')
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

        if(isset($params['type'])) {
            $query->andFilterWhere(['like','type' , $params['type']]);
        }

        if(isset($params['name'])) {
            $query->andFilterWhere(['name' => $params['name']]);
        }

        if(isset($params['code'])) {
            $query->andFilterWhere(['code' => $params['code']]);
        }

        if(isset($params['uc'])) {
            $query->andFilterWhere(['uc' => $params['uc']]);
        }

        if(isset($params['village'])) {
            $query->andFilterWhere(['village' => $params['village']]);
        }

        if(isset($params['address'])) {
            $query->andFilterWhere(['address' => $params['address']]);
        }

        if(isset($params['mobile'])) {
            $query->andFilterWhere(['mobile' => $params['mobile']]);
        }

        if(isset($params['city_id'])) {
            $query->andFilterWhere(['city_id' => $params['city_id']]);
        }

        if(isset($params['tehsil_id'])) {
            $query->andFilterWhere(['tehsil_id' => $params['tehsil_id']]);
        }

        if(isset($params['district_id'])) {
            $query->andFilterWhere(['district_id' => $params['district_id']]);
        }

        if(isset($params['division_id'])) {
            $query->andFilterWhere(['division_id' => $params['division_id']]);
        }

        if(isset($params['province_id'])) {
            $query->andFilterWhere(['province_id' => $params['province_id']]);
        }

        if(isset($params['country_id'])) {
            $query->andFilterWhere(['country_id' => $params['country_id']]);
        }

        if(isset($params['latitude'])) {
            $query->andFilterWhere(['latitude' => $params['latitude']]);
        }

        if(isset($params['longitude'])) {
            $query->andFilterWhere(['longitude'=> $params['longitude']]);
        }

        if(isset($params['opening_date'])) {
            $query->andFilterWhere(['opening_date'=> $params['opening_date']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['status' => $params['status']]);
        }

        if(isset($params['cr_division_id'])) {
            $query->andFilterWhere(['cr_division_id' => $params['cr_division_id']]);
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
    public function searchprogress($params)
    {
        if (isset($params['BranchesSearch']['date'])) {
            $date = $params['BranchesSearch']['date'];
        } else {
            $date = date('Y-m-d');
        }

        $connection = Yii::$app->db;
        $select_query = "SELECT b.id,b.name,b.region_id,
                            b.area_id,b.code,b.mobile,b.latitude,b.longitude,b.address,
                            (SELECT r.name FROM  regions r where(r.id=b.region_id)) region_name,
                            (SELECT a.name FROM  areas a where(a.id=b.area_id)) area_name,
                            (SELECT d.name FROM  districts d where(d.id=b.district_id)) district_name,
                            (SELECT prov.name FROM  provinces prov where(prov.id=b.province_id)) province_name,
                            (SELECT c.name FROM cities c where(c.id=b.city_id)) city_name,
                            (SELECT pr.id FROM progress_reports pr where(pr.report_date = '" . strtotime($date) . "' and pr.project_id=0 and pr.status=1 and pr.deleted=0)) pr_id,
                            (SELECT pd.no_of_loans FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) no_of_loans,
                            (SELECT pd.active_loans FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) active_loans,
                            (SELECT pd.female_loans FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) female_loans,
                            (SELECT pd.family_loans FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) male_loans,
                            (SELECT pd.cum_disb FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) amount_disbursed,
                            (SELECT pd.recovery_percentage FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) percentage_recovery,
                            (SELECT pd.olp_amount FROM progress_report_details pd WHERE (pd.progress_report_id =pr_id AND pd.branch_id= b.id)) olp
                         FROM branches b WHERE b.status = 1
                         ORDER BY b.region_id, b.code";
        $dataProvider = $connection->createCommand($select_query)->queryAll();
        return $dataProvider;
    }
    public function searchusagereport($params,$export=false)
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
                //$date[1]=strtotime(date('Y-m-d-h:i:sa',strtotime($date[1])));

                $member='and members.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $application='and applications.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $social_appraisal='and sa.created_at between "'.$date[0].'" and "'.$date[1].'"';
                $business_appraisal='and ba.created_at between "'.$date[0].'" and "'.$date[1].'"';
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
            $social_appraisal.=' and sa.platform="'.$this->platform.'"';
            $business_appraisal.=' and ba.platform ="'.$this->platform.'"';
            $verification.=' and applications.platform= "'.$this->platform.'"';
            $group.=' and groups.platform="'.$this->platform.'"';
            $loans.=' and loans.platform="'.$this->platform.'"';
            $fund_request.=' and loans.platform="'.$this->platform.'"';
            $disbursement.='  and loans.platform="'.$this->platform.'"';
            $recovery.=' and recoveries.platform="'.$this->platform.'"';

        }

        $query = \common\models\Branches::find()->select(['branches.id', 'branches.name', 'branches.code','branches.region_id', 'branches.area_id',
            '(select count(members.id) from members where members.branch_id=branches.id '.$member.') as no_of_members',
            '(select count(applications.id) from applications where applications.branch_id=branches.id '.$application.')as no_of_applications',

            '(select count(sa.id) from appraisals_social sa inner join applications app on app.id=sa.application_id where app.branch_id=branches.id '.$social_appraisal.')as no_of_social_appraisals',
            '(select count(ba.id) from appraisals_business ba inner join applications app on app.id=ba.application_id where app.branch_id=branches.id '.$business_appraisal.')as no_of_business_appraisals',

            '(select count(applications.id) from applications where applications.branch_id=branches.id and applications.status="approved" '.$verification.')as no_of_verifications',
            '(select count(groups.id) from groups where groups.branch_id=branches.id '.$group.')as no_of_groups',
            '(select count(loans.id) from loans where loans.branch_id=branches.id '.$loans.')as no_of_loans',
            '(select count(loans.id) from loans where loans.branch_id=branches.id and loans.fund_request_id!="0" '.$fund_request.')as no_of_fund_requests',
            '(select count(loans.id) from loans where loans.branch_id=branches.id and loans.disbursement_id!="0" '.$disbursement.')as no_of_disbursements',
            '(select count(recoveries.id) from recoveries where recoveries.branch_id=branches.id '.$recovery.')as no_of_recoveries',
            ])
            //->join('join','auth_assignment','auth_assignment.user_id=users.id')
        ;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->pagination->pageSize=50;
        //$query->joinWith('role');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([

            'branches.id' => $this->id,
            'branches.name' => $this->name,
            'branches.region_id'=>$this->region_id,
            'branches.area_id'=>$this->area_id,
            'branches.code' => $this->code,
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
        ]);

        //$query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
}
