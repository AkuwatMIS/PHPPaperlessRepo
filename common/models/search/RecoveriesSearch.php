<?php

namespace common\models\search;

use common\models\CihTransactionsMapping;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Recoveries;

/**
 * RecoveriesSearch represents the model behind the search form about `common\models\Recoveries`.
 */
class RecoveriesSearch extends Recoveries
{
    /**
     * @inheritdoc
     */
    public $sanction_no;
    public $name;
    public $member_name;
    public $member_cnic;
    public $bank_rct_no;

    public $project_ids = array();
    public $export;
    public $crop_type;
    public $parentage;

    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'schedule_id', 'loan_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'project_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['due_date', 'receive_date', 'receipt_no', 'type', 'source', 'is_locked', 'deleted', 'created_at', 'updated_at','member_name','member_cnic','sanction_no'], 'safe'],
            [['amount','charges_amount','credit_tax'], 'number'],
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

        $query = Recoveries::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' =>false,
        ]);
        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('loan');
        //$query->joinWith('area');
        //$query->joinWith('region');
        $query->joinWith('application.member');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [

                'receive_date' => [
                    'asc' => ['receive_date' => SORT_ASC],
                    'desc' => ['receive_date' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'amount' => [
                    'asc' => ['amount' => SORT_ASC],
                    'desc' => ['amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'receipt_no' => [
                    'asc' => ['receipt_no' => SORT_ASC],
                    'desc' => ['receipt_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
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
                'sanction_no' => [
                    'asc' => ['loans.sanction_no' => SORT_ASC],
                    'desc' => ['loans.sanction_no' => SORT_DESC],
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
                    'asc' => ['branches.name' => SORT_ASC],
                    'desc' => ['branches.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],*/
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                /*'project_id' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],*/
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
//            'schedule_id' => $this->schedule_id,
            'loan_id' => $this->loan_id,
            'recoveries.region_id' => $this->region_id,
            'recoveries.area_id' => $this->area_id,
            'recoveries.branch_id' => $this->branch_id,
            'recoveries.team_id' => $this->team_id,
            'recoveries.field_id' => $this->field_id,
//            'due_date' => $this->due_date,
            //'receive_date' => $this->receive_date,
            'amount' => $this->amount,
            'recoveries.project_id' => $this->project_id,
//            'assigned_to' => $this->assigned_to,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'source', $this->source])
            //->andFilterWhere(['like', 'is_locked', $this->is_locked])
            //->andFilterWhere(['like', 'deleted', $this->deleted])

            ->andFilterWhere(['=', 'members.full_name', $this->member_name])
            ->andFilterWhere(['=', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no])
            ->andFilterWhere(['recoveries.deleted' => 0]);


        if (isset($this->receive_date) && !is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {

            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'receive_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchComposite($params,$export=false)
    {
        $query = Recoveries::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);

        $dataProvider->pagination->pageSize=50;
        $query->joinWith('loan');
        $query->joinWith('application.member');
        $dataProvider->setSort([
            'attributes' => [

                'receive_date' => [
                    'asc' => ['receive_date' => SORT_ASC],
                    'desc' => ['receive_date' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'amount' => [
                    'asc' => ['amount' => SORT_ASC],
                    'desc' => ['amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'receipt_no' => [
                    'asc' => ['receipt_no' => SORT_ASC],
                    'desc' => ['receipt_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
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
                'sanction_no' => [
                    'asc' => ['loans.sanction_no' => SORT_ASC],
                    'desc' => ['loans.sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'loan_id' => $this->loan_id,
            'recoveries.region_id' => $this->region_id,
            'recoveries.area_id' => $this->area_id,
            'recoveries.branch_id' => $this->branch_id,
            'recoveries.team_id' => $this->team_id,
            'recoveries.field_id' => $this->field_id,
            'amount' => $this->amount,
            'recoveries.project_id' => $this->project_id
        ]);

        $query->andFilterWhere(['=', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'source', $this->source])
            ->andFilterWhere(['=', 'members.full_name', $this->member_name])
            ->andFilterWhere(['=', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no])
            ->andFilterWhere(['recoveries.deleted' => 0]);

        $query->orderBy('created_at desc');
        return $dataProvider;
    }

    public function searchHousing($params,$export=false)
    {
        $query = Recoveries::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' =>false,
        ]);
        if(empty($params['RecoveriesSearch']['project_id'])){
            $params['RecoveriesSearch']['project_id'] = 52;
        }
        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('loan');
        //$query->joinWith('area');
        //$query->joinWith('region');
        $query->joinWith('application.member');
        //$query->joinWith('branch');
        $query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [

                'receive_date' => [
                    'asc' => ['receive_date' => SORT_ASC],
                    'desc' => ['receive_date' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'amount' => [
                    'asc' => ['amount' => SORT_ASC],
                    'desc' => ['amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'receipt_no' => [
                    'asc' => ['receipt_no' => SORT_ASC],
                    'desc' => ['receipt_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
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
                'sanction_no' => [
                    'asc' => ['loans.sanction_no' => SORT_ASC],
                    'desc' => ['loans.sanction_no' => SORT_DESC],
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
                    'asc' => ['branches.name' => SORT_ASC],
                    'desc' => ['branches.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],*/
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                /*'project_id' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],*/
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'schedule_id' => $this->schedule_id,
            'loan_id' => $this->loan_id,
            'recoveries.region_id' => $this->region_id,
            'recoveries.area_id' => $this->area_id,
            'recoveries.branch_id' => $this->branch_id,
            'recoveries.team_id' => $this->team_id,
            'recoveries.field_id' => $this->field_id,
            'due_date' => $this->due_date,
            //'receive_date' => $this->receive_date,
            'amount' => $this->amount,
//            'recoveries.project_id' => 52,
            'recoveries.project_id' => $this->project_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'source', $this->source])
            //->andFilterWhere(['like', 'is_locked', $this->is_locked])
            //->andFilterWhere(['like', 'deleted', $this->deleted])

            ->andFilterWhere(['=', 'members.full_name', $this->member_name])
            ->andFilterWhere(['=', 'members.cnic', $this->member_cnic])
            ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no])
            ->andFilterWhere(['recoveries.deleted' => 0]);


        if (isset($this->receive_date) && !is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {

            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'receive_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->orderBy('created_at desc');
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchdepositedrecovery($params,$export=false)
    {

        $query = CihTransactionsMapping::find()->select(['cih_transactions_mapping.id','cih_transactions_mapping.cih_type_id','cih_transactions_mapping.type','cih_transactions_mapping.transaction_id','cih_transactions_mapping.amount',
        ])->where(['cih_transactions_mapping.type'=>'recov'])->andWhere(['recoveries.created_by'=>Yii::$app->user->getId()]);

        $query->joinWith('recovery');
        $query->joinWith('transaction');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (isset($params['RecoveriesSearch']['branch_id']) && $params['RecoveriesSearch']['branch_id'] != null) {
            $query->andFilterWhere(['=', 'recoveries.branch_id', $params['RecoveriesSearch']['branch_id']]);
        }
        if (isset($params['RecoveriesSearch']['receipt_no']) && $params['RecoveriesSearch']['receipt_no'] != null) {
            $query->andFilterWhere(['=', 'recoveries.receipt_no', $params['RecoveriesSearch']['receipt_no']]);
        }
        if (isset($params['RecoveriesSearch']['amount']) && $params['RecoveriesSearch']['amount'] != null) {
            $query->andFilterWhere(['=', 'recoveries.amount', $params['RecoveriesSearch']['amount']]);
        }
        if (isset($params['RecoveriesSearch']['receive_date']) && !is_null($params['RecoveriesSearch']['receive_date']) && strpos($params['RecoveriesSearch']['receive_date'], ' - ') !== false) {
            $date = explode(' - ', $params['RecoveriesSearch']['receive_date']);
            $query->andFilterWhere(['between', 'recoveries.receive_date', strtotime(date('Y-m-d 00:00:00',strtotime($date[0]))), strtotime(date('Y-m-d 23:59:59',strtotime($date[1])))]);
        }
        if (isset($params['RecoveriesSearch']['bank_rct_no']) && $params['RecoveriesSearch']['bank_rct_no'] != null) {
            $query->andFilterWhere(['=', 'transactions.bank_rct_no', $params['RecoveriesSearch']['bank_rct_no']]);
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }
}
