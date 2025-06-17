<?php

namespace common\models\search;

use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\MemberInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Members;
use yii\db\Expression;
/**
 * MembersSearch represents the model behind the search form about `common\models\Members`.
 */
class MembersSearch extends Members
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', /*'dob', */'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'parentage', 'parentage_type', 'cnic', 'dob','gender', 'education', 'marital_status', 'family_no', 'family_member_name', 'family_member_cnic', 'religion', 'profile_pic', 'status', 'is_lock', 'deleted'], 'safe'],
            [['family_member_left_thumb','family_member_right_thumb'], 'safe'],
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
        $query = Members::find()->select(['id','full_name','parentage','parentage_type','cnic',
            'gender','dob','education','marital_status','status','is_lock','religion','created_at',
            'region_id','area_id','branch_id','team_id','field_id','deleted'
        ])->where(['members.deleted' => 0]);
        //$this->deleted = 0;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $dataProvider->setSort([
            'attributes' => [
                'full_name' => [
                    'asc' => ['full_name' => SORT_ASC],
                    'desc' => ['full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage_type' => [
                    'asc' => ['parentage_type' => SORT_ASC],
                    'desc' => ['parentage_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['cnic' => SORT_ASC],
                    'desc' => ['cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'gender' => [
                    'asc' => ['gender' => SORT_ASC],
                    'desc' => ['gender' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'dob' => [
                    'asc' => ['dob' => SORT_ASC],
                    'desc' => ['dob' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'education' => [
                    'asc' => ['education' => SORT_ASC],
                    'desc' => ['education' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'marital_status' => [
                    'asc' => ['marital_status' => SORT_ASC],
                    'desc' => ['marital_status' => SORT_DESC],
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
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'field_id' => $this->field_id,
            //'dob' => $this->dob,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'full_name', $this->full_name])
            ->andFilterWhere(['=', 'parentage', $this->parentage])
            ->andFilterWhere(['=', 'parentage_type', $this->parentage_type])
            ->andFilterWhere(['=', 'cnic', $this->cnic])
            ->andFilterWhere(['=', 'gender', $this->gender])
            ->andFilterWhere(['=', 'education', $this->education])
            ->andFilterWhere(['=', 'marital_status', $this->marital_status])
            ->andFilterWhere(['=', 'family_no', $this->family_no])
            ->andFilterWhere(['=', 'family_member_name', $this->family_member_name])
            ->andFilterWhere(['=', 'family_member_cnic', $this->family_member_cnic])
            ->andFilterWhere(['=', 'religion', $this->religion])
            ->andFilterWhere(['=', 'profile_pic', $this->profile_pic])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'is_lock', $this->is_lock])
            ->andFilterWhere(['deleted' => 0]);
        if (!is_null($this->dob) && strpos($this->dob, ' - ') !== false) {
            $date = explode(' - ', $this->dob);
            $query->andFilterWhere(['between', 'dob', strtotime($date[0]), strtotime($date[1])]);
        } else {
           // $query->andFilterWhere(['=','dob',strtotime($this->dob)]);
        }
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchExpireCNIC($params,$export=false)
    {
        $status='collected';
        $date=date("Y-m-d",strtotime("+1 month"));

        //die($date);
        $query = Members::find()->select(['members.id','members.full_name','members.parentage','members.parentage_type','members.cnic',
            'members.gender','members.dob','members.education','members.marital_status','members.status','members.is_lock','members.religion','members.created_at',
            'members.region_id','members.area_id','members.branch_id','members.team_id','members.field_id'
        ])
        ->innerJoin('member_info', 'member_info.member_id=members.id')
        ->innerJoin('applications', 'applications.member_id=members.id')
        ->innerJoin('loans', 'loans.application_id=applications.id')
        //->where(['<=', 'member_info.cnic_expiry_date', new Expression('NOW()')])
        ->where('DATE(member_info.cnic_expiry_date) <= DATE("'.$date.'")')
        ->andWhere(['=','loans.status', $status]);

        // echo $query->createCommand()->rawSql;

        //  die();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize=50;
        $dataProvider->setSort([
            'attributes' => [
                'full_name' => [
                    'asc' => ['full_name' => SORT_ASC],
                    'desc' => ['full_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage_type' => [
                    'asc' => ['parentage_type' => SORT_ASC],
                    'desc' => ['parentage_type' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['cnic' => SORT_ASC],
                    'desc' => ['cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'gender' => [
                    'asc' => ['gender' => SORT_ASC],
                    'desc' => ['gender' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'dob' => [
                    'asc' => ['dob' => SORT_ASC],
                    'desc' => ['dob' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'education' => [
                    'asc' => ['education' => SORT_ASC],
                    'desc' => ['education' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'marital_status' => [
                    'asc' => ['marital_status' => SORT_ASC],
                    'desc' => ['marital_status' => SORT_DESC],
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
            'members.region_id' => $this->region_id,
            'members.area_id' => $this->area_id,
            'members.branch_id' => $this->branch_id,
            'members.team_id' => $this->team_id,
            'members.field_id' => $this->field_id,
            //'dob' => $this->dob,
            'members.assigned_to' => $this->assigned_to,
            'members.created_by' => $this->created_by,
            'members.updated_by' => $this->updated_by,
            'members.created_at' => $this->created_at,
            'members.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'full_name', $this->full_name])
            ->andFilterWhere(['=', 'parentage', $this->parentage])
            ->andFilterWhere(['=', 'parentage_type', $this->parentage_type])
            ->andFilterWhere(['=', 'cnic', $this->cnic])
            ->andFilterWhere(['=', 'gender', $this->gender])
            ->andFilterWhere(['=', 'education', $this->education])
            ->andFilterWhere(['=', 'marital_status', $this->marital_status])
            ->andFilterWhere(['=', 'family_no', $this->family_no])
            ->andFilterWhere(['=', 'family_member_name', $this->family_member_name])
            ->andFilterWhere(['=', 'family_member_cnic', $this->family_member_cnic])
            ->andFilterWhere(['=', 'religion', $this->religion])
            ->andFilterWhere(['=', 'profile_pic', $this->profile_pic])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'is_lock', $this->is_lock]);
        if (!is_null($this->dob) && strpos($this->dob, ' - ') !== false) {
            $date = explode(' - ', $this->dob);
            $query->andFilterWhere(['between', 'dob', strtotime($date[0]), strtotime($date[1])]);
        } else {
           // $query->andFilterWhere(['=','dob',strtotime($this->dob)]);
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

        $query = Members::find()
            ->select('members.*, members_address.address, members_address.address_type,members_email.email,members_phone.phone as mobile,members_phone.phone')
            ->where(['members.deleted' => 0])
            //->asArray(true)
            ->limit($limit)
            ->offset($offset);

        $query->joinWith('membersAddress');
        $query->joinWith('membersEmail');
        $query->joinWith('membersPhone');


        if(isset($params['id'])) {
            $query->andFilterWhere(['members.id' => $params['id']]);
        }

        if(isset($params['full_name'])) {
            $query->andFilterWhere(['like', 'full_name', $params['full_name']]);
        }

        if(isset($params['parentage'])) {
            $query->andFilterWhere(['like', 'parentage', $params['parentage']]);
        }

        if(isset($params['parentage_type'])) {
            $query->andFilterWhere(['parentage_type'=> $params['parentage_type']]);
        }

        if(isset($params['cnic'])){
            $query->andFilterWhere(['cnic' => $params['cnic']]);
        }

        if(isset($params['gender'])) {
            $query->andFilterWhere(['gender' => $params['gender']]);
        }

        if(isset($params['dob'])) {
            $query->andFilterWhere(['dob' => $params['dob']]);
        }

        if(isset($params['education'])) {
            $query->andFilterWhere(['education' => $params['education']]);
        }

        if(isset($params['marital_status'])) {
            $query->andFilterWhere(['marital_status' => $params['marital_status']]);
        }

        if(isset($params['family_no'])) {
            $query->andFilterWhere(['like', 'family_no', $params['family_no']]);
        }

        if(isset($params['family_member_name'])) {
            $query->andFilterWhere(['like', 'family_member_name', $params['family_member_name']]);
        }

        if(isset($params['family_member_cnic'])) {
            $query->andFilterWhere(['family_member_cnic' => $params['family_member_cnic']]);
        }

        if(isset($params['religion'])) {
            $query->andFilterWhere(['religion' => $params['religion']]);
        }

        if(isset($params['status'])) {
            $query->andFilterWhere(['members.status' => $params['status']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['members.created_at' => $params['created_at']]);
        }

        if(isset($params['mobile'])) {
            $query->andFilterWhere(['members_phone.phone' => $params['phone']]);
        }

        if(isset($params['phone'])) {
            $query->andFilterWhere(['members_phone.phone' => $params['phone']]);
        }

        if(isset($params['email'])) {
            $query->andFilterWhere(['members_email.email' => $params['email']]);
        }

        if(isset($params['address'])) {
            $query->andFilterWhere(['like','members_address.address' , $params['address']]);
        }

        if(isset($params['address_type'])) {
            $query->andFilterWhere(['like','members_address.address_type' , $params['address_type']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }

        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => Members::find()->where(['members.deleted' => 0])->count('id')
            //'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
