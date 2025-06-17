<?php

namespace common\models\search;

use common\models\CihTransactionsMapping;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Donations;

/**
 * DonationsSearch represents the model behind the search form about `common\models\Donations`.
 */
class DonationsSearch extends Donations
{
    public $sanction_no;
    public $member_cnic;
    public $member_name;
    public $project_ids = array();
    public $crop_type;
    public $bank_rct_no;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'loan_id', 'schedule_id', 'branch_id','area_id','region_id','team_id','field_id', 'project_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['receive_date', 'receipt_no', 'deleted', 'created_at', 'updated_at','sanction_no','member_cnic','member_name'], 'safe'],
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
        $query = Donations::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
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
                'region_id' => [
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
                ],
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project_id' => [
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
        $query->joinWith('loan');
        $query->joinWith('application.member');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'donations.application_id' => $this->application_id,
            'loan_id' => $this->loan_id,
            'schedule_id' => $this->schedule_id,
            'donations.branch_id' => $this->branch_id,
            'donations.project_id' => $this->project_id,
            'donations.region_id' => $this->region_id,
            'donations.area_id' => $this->area_id,
            'donations.team_id' => $this->team_id,
            'donations.field_id' => $this->field_id,

            'donations.amount' => $this->amount,
            //'receive_date' => $this->receive_date,
            //'donations.assigned_to' => $this->assigned_to,
            //'donations.created_by' => $this->created_by,
            'donations.updated_by' => $this->updated_by,
            'donations.created_at' => $this->created_at,
            'donations.updated_at' => $this->updated_at,

        ]);

        $query->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'donations.deleted', 0])
        ->andFilterWhere(['like', 'members.full_name', $this->member_name])
        ->andFilterWhere(['like', 'members.cnic', $this->member_cnic])
        ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no]);
        if (isset($this->receive_date) && !is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {
            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'donations.receive_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->orderBy('donations.created_at desc');
        return $dataProvider;
    }

    public function searchComposite($params)
    {
        $query = Donations::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
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
                'region_id' => [
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
                ],
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project_id' => [
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
        $query->joinWith('loan');
        $query->joinWith('application.member');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'donations.application_id' => $this->application_id,
            'loan_id' => $this->loan_id,
            'donations.amount' => $this->amount,

        ]);

        $query->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'donations.deleted', 0])
            ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no]);

        if (isset($this->receive_date) && !is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {
            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'donations.receive_date', strtotime($date[0]), strtotime($date[1])]);
        }
        $query->orderBy('donations.created_at desc');
        return $dataProvider;
    }

    public function mdpreportsearch($params,$export=false)
    {
        $query = Donations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        $this->load($params);
        $query->joinWith('application.member');
        $query->joinWith('loan');
        $query->joinWith('project');
        $query->joinWith('region');
        $query->joinWith('area');
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
                'region_id' => [
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
                ],
                'project_name' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'project_id' => [
                    'asc' => ['projects.name' => SORT_ASC],
                    'desc' => ['projects.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ]
        ]);
        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'loan_id' => $this->loan_id,
//            'schedule_id' => $this->schedule_id,
            'donations.branch_id' => $this->branch_id,
            'donations.project_id' => $this->project_id,
            'donations.region_id' => $this->region_id,
            'donations.area_id' => $this->area_id,
            'donations.team_id' => $this->team_id,
            'donations.field_id' => $this->field_id,
            'amount' => $this->amount,
            //'receive_date' => $this->receive_date,
//            'assigned_to' => $this->assigned_to,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,

        ]);


        $query->andFilterWhere(['=', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'donations.deleted', 0])

            ->andFilterWhere(['=', 'loans.sanction_no', $this->sanction_no])
            ->andFilterWhere(['=', 'members.full_name', $this->member_name])
            ->andFilterWhere(['=', 'members.cnic', $this->member_cnic]);

        if (isset($this->receive_date) && !is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {
            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'receive_date', strtotime($date[0]), strtotime($date[1])]);
        }
        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchdepositeddonation($params,$export=false)
    {
        $query = CihTransactionsMapping::find()->select(['cih_transactions_mapping.id','cih_transactions_mapping.cih_type_id','cih_transactions_mapping.type','cih_transactions_mapping.transaction_id','cih_transactions_mapping.amount',
        ])->where(['cih_transactions_mapping.type'=>'donat'])->andWhere(['donations.created_by'=>Yii::$app->user->getId()]);

        $query->joinWith('donation');
        $query->joinWith('transaction');


        /*$query->joinWith('loan');
        $query->joinWith('borrower');*/
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);

        if (isset($params['DonationsSearch']['branch_id']) && $params['DonationsSearch']['branch_id'] != null) {
            $query->andFilterWhere(['=', 'donations.branch_id', $params['DonationsSearch']['branch_id']]);
        }
        if (isset($params['DonationsSearch']['receipt_no']) && $params['DonationsSearch']['receipt_no'] != null) {
            $query->andFilterWhere(['=', 'donations.receipt_no', $params['DonationsSearch']['receipt_no']]);
        }
        if (isset($params['DonationsSearch']['amount']) && $params['DonationsSearch']['amount'] != null) {
            $query->andFilterWhere(['=', 'donations.amount', $params['DonationsSearch']['amount']]);
        }
        if (isset($params['DonationsSearch']['receive_date']) && !is_null($params['DonationsSearch']['receive_date']) && strpos($params['DonationsSearch']['receive_date'], ' - ') !== false) {
            $date = explode(' - ', $params['DonationsSearch']['receive_date']);
            $query->andFilterWhere(['between', 'donations.receive_date',strtotime(date('Y-m-d 00:00:00',strtotime($date[0]))), strtotime(date('Y-m-d 23:59:59',strtotime($date[1])))]);
        }
        if (isset($params['DonationsSearch']['bank_rct_no']) && $params['DonationsSearch']['bank_rct_no'] != null) {
            $query->andFilterWhere(['=', 'transactions.bank_rct_no', $params['DonationsSearch']['bank_rct_no']]);
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
