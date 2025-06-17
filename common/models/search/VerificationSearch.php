<?php

namespace common\models\search;

use common\models\Applications;
use common\models\Verification;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Members;

/**
 * MembersSearch represents the model behind the search form about `common\models\Members`.
 */
class VerificationSearch extends Verification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'assigned_to'], 'required'],
            [['application_id', 'assigned_to', 'verified_at'], 'integer'],
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
        $query = Members::find();
        $this->deleted = 0;
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

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'parentage', $this->parentage])
            ->andFilterWhere(['like', 'parentage_type', $this->parentage_type])
            ->andFilterWhere(['like', 'cnic', $this->cnic])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'education', $this->education])
            ->andFilterWhere(['like', 'marital_status', $this->marital_status])
            ->andFilterWhere(['like', 'family_no', $this->family_no])
            ->andFilterWhere(['like', 'family_member_name', $this->family_member_name])
            ->andFilterWhere(['like', 'family_member_cnic', $this->family_member_cnic])
            ->andFilterWhere(['like', 'religion', $this->religion])
            ->andFilterWhere(['like', 'profile_pic', $this->profile_pic])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['like', 'deleted', $this->deleted]);
        if (!is_null($this->dob) && strpos($this->dob, ' - ') !== false) {
            $date = explode(' - ', $this->dob);
            $query->andFilterWhere(['between', 'dob', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['like','dob',strtotime($this->dob)]);
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
        $filter = Yii::$app->getRequest()->getQueryParam('filter');


        if(isset($search)){
            $params=$search;
        }
        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Applications::find()
            ->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->joinWith('member')
            //->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'approved/rejected'])
            ->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'business_appraisal','application_actions.status'=>1])
            ->andWhere(['>=','application_actions.expiry_date', date('Y-m-d H:i:s')])
            //->asArray(true)

            ->limit($limit)
            ->offset($offset);

        if(isset($filter)){
            if(($result = preg_match("/[0-9]{5}\-[0-9]{7}\-[0-9]{1}|[0-9]|-/", $filter))==1) {
                $query->andFilterWhere(['like', 'members.cnic', $filter]);
            } else {
                $query->andFilterWhere(['like', 'members.full_name', $filter]);
            }
        }

        if(isset($order)){
            $query->orderBy($order);
        }
        $query->orderBy('applications.created_at desc');
        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' =>  ceil((int)$query->count()/$limit)
            //'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
