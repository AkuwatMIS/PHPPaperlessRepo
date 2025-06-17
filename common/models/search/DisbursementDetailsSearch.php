<?php

namespace common\models\search;

use common\models\DisbursementDetailsLogs;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DisbursementDetails;
use yii\data\ArrayDataProvider;

/**
 * DisbursementDetailsSearch represents the model behind the search form about `common\models\DisbursementDetails`.
 */
class DisbursementDetailsSearch extends DisbursementDetails
{
    /**
     * @inheritdoc
     */
   public $new_value;
   public $old_value;
   public $stamp;
   public $fund_id;
   public $batch_id;
   public $activity_id;
   public $pmt;
   public $province_id;
   public $district_id;
   public $age;
   public $gender;


    public function rules()
    {
        return [
            [['activity_id','id', 'tranche_id', 'disbursement_id', 'response_code', 'created_by', 'updated_by', 'updated_at','payment_method_id'], 'integer'],
            [['bank_name', 'account_no', 'status', 'response_description', 'deleted','sanction_no','region_id','area_id','branch_id','date_disbursed','cnic','title','project_id'], 'safe'],
            [['transferred_amount'], 'number'],
            [['created_at','type','batch_id','bank_disb_date','pmt','gender','age','district_id','province_id'], 'safe'],
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
        $query = DisbursementDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('tranch');
        $query->joinWith('tranch.loan');
        $query->joinWith('tranch.batch');
        //$query->joinWith('tranch.loan.application.member');
        //$query->joinWith('tranch.loan.application.member.verifiedAccount');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tranche_id' => $this->tranche_id,
            'project_fund_detail.batch_no' => $this->batch_id,
            'loans.branch_id' => $this->branch_id,
            'payment_method_id' => $this->payment_method_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'loans.project_id' => $this->project_id,
            'loans.sanction_no' => $this->sanction_no,
            //'members.cnic' => $this->cnic,
            'transferred_amount' => $this->transferred_amount,
            'disbursement_id' => $this->disbursement_id,
            'response_code' => $this->response_code,
            'loan.activity_id' => $this->activity_id,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'disbursement_details.bank_name', $this->bank_name])
            ->andFilterWhere(['=', 'disbursement_details.account_no', $this->account_no])
            ->andFilterWhere(['=', 'disbursement_details.status', $this->status])
            ->andFilterWhere(['like', 'response_description', $this->response_description])
            //->andFilterWhere(['like', 'members_account.title', $this->title])
            ->andFilterWhere(['=', 'deleted', $this->deleted]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            //$query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }
        if(!empty($params['DisbursementDetailsSearch']['created_at'])){
            $dates = explode(' - ', $params['DisbursementDetailsSearch']['created_at']);
            $query->andFilterWhere(['between', 'disbursement_details.created_at', strtotime($dates[0]), strtotime(($dates[1].' 23:59:59'))]);
        }

        if(!empty($params['DisbursementDetailsSearch']['status'])){
            $query->andFilterWhere(['disbursement_details.status' => $params['DisbursementDetailsSearch']['status']]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchPmyp($params,$export=false)
    {
        $query = DisbursementDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('tranch');
        $query->joinWith('tranch.loan');
        $query->joinWith('tranch.batch');
        //$query->joinWith('tranch.loan.application.member');
        //$query->joinWith('tranch.loan.application.member.verifiedAccount');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tranche_id' => $this->tranche_id,
            'project_fund_detail.batch_no' => $this->batch_id,
            'loans.branch_id' => $this->branch_id,
            'payment_method_id' => $this->payment_method_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'loans.project_id' => $this->project_id,
            'loans.sanction_no' => $this->sanction_no,
            //'members.cnic' => $this->cnic,
            'transferred_amount' => $this->transferred_amount,
            'disbursement_id' => $this->disbursement_id,
            'response_code' => $this->response_code,
            'loan.activity_id' => $this->activity_id,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'disbursement_details.bank_name', $this->bank_name])
            ->andFilterWhere(['=', 'disbursement_details.account_no', $this->account_no])
            ->andFilterWhere(['=', 'disbursement_details.status', $this->status])
            ->andFilterWhere(['like', 'response_description', $this->response_description])
            //->andFilterWhere(['like', 'members_account.title', $this->title])
            ->andFilterWhere(['=', 'deleted', $this->deleted]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            //$query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }
        if(!empty($params['DisbursementDetailsSearch']['created_at'])){
            $dates = explode(' - ', $params['DisbursementDetailsSearch']['created_at']);
            $query->andFilterWhere(['between', 'disbursement_details.created_at', strtotime($dates[0]), strtotime(($dates[1].' 23:59:59'))]);
        }

        if(!empty($params['DisbursementDetailsSearch']['status'])){
            $query->andFilterWhere(['disbursement_details.status' => $params['DisbursementDetailsSearch']['status']]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function publish($params,$export=false)
    {
        $query = DisbursementDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('tranch');
        $query->joinWith('tranch.loan');
        //$query->joinWith('tranch.loan.application.member');
        //$query->joinWith('tranch.loan.application.member.verifiedAccount');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tranche_id' => $this->tranche_id,
            'loans.branch_id' => $this->branch_id,
            'payment_method_id' => $this->payment_method_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'loans.project_id' => $this->project_id,
            'loans.sanction_no' => $this->sanction_no,
            //'members.cnic' => $this->cnic,
            'disbursement_details.status' => 0,
            'transferred_amount' => $this->transferred_amount,
            'disbursement_id' => $this->disbursement_id,
            'response_code' => $this->response_code,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'disbursement_details.bank_name', $this->bank_name])
            ->andFilterWhere(['=', 'disbursement_details.account_no', $this->account_no])
            ->andFilterWhere(['=', 'disbursement_details.status', $this->status])
            ->andFilterWhere(['like', 'response_description', $this->response_description])
            //->andFilterWhere(['like', 'members_account.title', $this->title])
            ->andFilterWhere(['=', 'deleted', $this->deleted]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            //$query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchInProcess($params,$export=false)
    {
        $query = DisbursementDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('tranch');
        $query->joinWith('tranch.loan');
        //$query->joinWith('tranch.loan.application.member');
        //$query->joinWith('tranch.loan.application.member.verifiedAccount');
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tranche_id' => $this->tranche_id,
            'loans.branch_id' => $this->branch_id,
            'payment_method_id' => $this->payment_method_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'loans.project_id' => $this->project_id,
            'loans.sanction_no' => $this->sanction_no,
            //'members.cnic' => $this->cnic,
            'transferred_amount' => $this->transferred_amount,
            'disbursement_id' => $this->disbursement_id,
            'response_code' => $this->response_code,
            'disbursement_details.status' => 5,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'disbursement_details.bank_name', $this->bank_name])
            ->andFilterWhere(['=', 'disbursement_details.account_no', $this->account_no])
            ->andFilterWhere(['=', 'disbursement_details.status', $this->status])
            ->andFilterWhere(['like', 'response_description', $this->response_description])
            //->andFilterWhere(['like', 'members_account.title', $this->title])
            ->andFilterWhere(['=', 'deleted', $this->deleted]);
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            //$query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime(($date[1].' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['loans.date_disbursed' => $this->date_disbursed]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchResponseLogs($params,$export=false)
    {


        $this->load($params);
        $query_cond = '';

        if (isset($params['DisbursementDetailsSearch']['sanction_no']) && !empty($params['DisbursementDetailsSearch']['sanction_no'])) {
            $sanction_no = $params['DisbursementDetailsSearch']['sanction_no'];
            $query_cond .=' and loans.sanction_no= "'.$sanction_no.'"';
        }
        if (isset($params['DisbursementDetailsSearch']['date_disbursed']) && !empty($params['DisbursementDetailsSearch']['date_disbursed'])) {
            $date_disbursed = explode(' - ', $params['DisbursementDetailsSearch']['date_disbursed']);
            $query_cond .=' and loans.date_disbursed between "'.$date_disbursed[0].'" and "'.$date_disbursed[1].'"';
        }
        if (isset($params['DisbursementDetailsSearch']['region_id']) && !empty($params['DisbursementDetailsSearch']['region_id'])) {
            $region_id = $params['DisbursementDetailsSearch']['region_id'];
            $query_cond .=' and loans.region_id= "'.$region_id.'"';
        }
        if (isset($params['DisbursementDetailsSearch']['area_id']) && !empty($params['DisbursementDetailsSearch']['area_id'])) {
            $area_id =  $params['DisbursementDetailsSearch']['area_id'];
            $query_cond .=' and loans.area_id= "'.$area_id.'"';
        }
        if (isset($params['DisbursementDetailsSearch']['branch_id']) && !empty($params['DisbursementDetailsSearch']['branch_id'])) {
            $branch_id = $params['DisbursementDetailsSearch']['branch_id'];
            $query_cond .=' and loans.branch_id= "'.$branch_id.'"';
        }
        if (isset($params['DisbursementDetailsSearch']['project_id']) && !empty($params['DisbursementDetailsSearch']['project_id'])) {
            $branch_id = $params['DisbursementDetailsSearch']['project_id'];
            $query_cond .=' and loans.project_id= "'.$branch_id.'"';
        }
        $query = 'SELECT loans.sanction_no as sanction_no, old_value as old_value, new_value as new_value , stamp FROM `disbursement_details_logs` 
                    LEFT JOIN `disbursement_details` ON `disbursement_details_logs`.`id` = `disbursement_details`.`id` 
                    LEFT JOIN `loan_tranches` ON `disbursement_details`.`tranche_id` = `loan_tranches`.`id` 
                    LEFT JOIN `loans` ON `loans`.`id` = `loan_tranches`.`loan_id` 
                    WHERE (disbursement_details_logs.field="response_description") AND (disbursement_details_logs.action="CHANGE") AND (disbursement_details.status="1") ' . $query_cond . ' ';
        $response = \Yii::$app->db->createCommand($query)->queryAll();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $response,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        if($export){
            return $response;
        }else{
            return $dataProvider;
        }
    }

    public function search_fund_allocation($params,$export=false)
    {

        $this->load($params);

        $project_id = isset($params['project_id'])?$params['project_id']:$this->project_id;
        $bank_name = isset($params['bank_name'])?$params['bank_name']:$this->bank_name;
        $region_id = isset($params['region_id'])?$params['region_id']:$this->region_id;
        $area_id = isset($params['area_id'])?$params['area_id']:$this->area_id;
        $branch_id = isset($params['branch_id'])?$params['branch_id']:$this->branch_id;
        $sanction_no = isset($params['sanction_no'])?$params['sanction_no']:$this->sanction_no;
        if((!empty($project_id) && !empty($bank_name))) {

            $conditions_region_area_branch = '';
            $conditions_member_bank = '';
            $conditions_batch = '';

            if (isset($region_id) && !empty($region_id)) {
                $rIds = $region_id;
                $regionIds = '0';
                foreach ($rIds as $r){
                    $regionIds = $regionIds.','.$r;
                }
                $conditions_region_area_branch = $conditions_region_area_branch . ' and region_id IN(' . $regionIds . ')';

            }

            if (isset($sanction_no) && !empty($sanction_no)) {
                $sanction_no = rtrim($sanction_no, ",");
                $conditions_region_area_branch = $conditions_region_area_branch . ' and sanction_no IN(' . $sanction_no . ')';

            }
            if (isset($area_id) && !empty($area_id)) {

                $aIds = $area_id;
                $areaIds = '0';
                foreach ($aIds as $a){
                    $areaIds = $areaIds.','.$a;
                }
                $conditions_region_area_branch = $conditions_region_area_branch . ' and area_id IN(' . $areaIds . ')';

            }
            if (isset($branch_id) && !empty($branch_id)) {
                $bIds = $branch_id;
                $branchIds = '0';
                foreach ($bIds as $b){
                    $branchIds = $branchIds.','.$b;
                }
                $conditions_region_area_branch = $conditions_region_area_branch . ' and branch_id IN(' . $branchIds . ')';
            }
            if (isset($project_id) && !empty($project_id)) {
                $conditions_region_area_branch = $conditions_region_area_branch . ' and project_id =' . $project_id;
            }
            if (isset($bank_name) &&  !empty($bank_name)) {
                $conditions_member_bank = $conditions_member_bank . ' and disbursement_details.bank_name = ' .'"' .$bank_name.'"';

            }


            $list = "SELECT `tranche_id` FROM `disbursement_details`
                    inner join loan_tranches on loan_tranches.id = disbursement_details.tranche_id 
                    inner join loans on loans.id = loan_tranches.loan_id 
                    WHERE disbursement_details.status = 0 $conditions_region_area_branch $conditions_member_bank";
            $list = \Yii::$app->db->createCommand($list)->queryAll();

            foreach ($list as $id) {
                $batch[] = $id['tranche_id'];
            }

            if (isset($batch)) {
                $batch = implode(",", $batch);
                $conditions_batch = $conditions_batch . ' and tranche_id in(' . $batch . ')';
            }
            $list_sum = "SELECT sum(transferred_amount) as transferred_amount  FROM `disbursement_details`
                    inner join loan_tranches on loan_tranches.id = disbursement_details.tranche_id 
                    inner join loans on loans.id = loan_tranches.loan_id 
                    WHERE disbursement_details.status = 0 $conditions_batch $conditions_region_area_branch $conditions_member_bank";
            $list_sum = \Yii::$app->db->createCommand($list_sum)->queryAll();

            $query['project_id'] = $project_id;
            $query['count'] = count($list);
            $query['sum'] = $list_sum[0]['transferred_amount'];
            $query['batch'] = $batch;
            $query['sanction_no'] = $sanction_no;
            $query['region_id'] = $region_id;
            $query['branch_id'] = $branch_id;
            $query['area_id'] = $area_id;


            return $query;

        } else {
            if(!empty($params) ) {
                Yii::$app->session->setFlash('error', "Please Select Project and Disbursement Source!");
                return $query = [];
            }
        }
    }
}
