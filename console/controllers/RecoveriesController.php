<?php

namespace console\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\RecoveriesHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\StructureHelper;
use common\models\Actions;
use common\models\ApplicationActions;
use common\models\Areas;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Blacklist;
use common\models\Branches;
use common\models\CreditDivisions;
use common\models\Disbursements;
use common\models\Donations;
use common\models\FundRequests;
use common\models\Funds;
use common\models\GroupActions;
use common\models\Groups;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\Operations;
use common\models\ProgressReports;
use common\models\ProjectFundDetail;
use common\models\Recoveries;
use common\models\Applications;
use common\models\RecoveryErrors;
use common\models\RecoveryFiles;
use common\models\Regions;
use common\models\Schedules;
use common\models\Users;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\console\Controller;

class RecoveriesController extends Controller
{



    public $cnic;
    public function actionOldFieldsChange($region_id = 0, $area_id = 0, $branch_id = 0)
    {
        ini_set('memory_limit', '16128M');

        $connection = Yii::$app->db;

        ////Credit Division
        /*$vr_divisions_query = "UPDATE credit_divisions SET created_at = UNIX_TIMESTAMP(created_on_old),updated_at = UNIX_TIMESTAMP(updated_on_old) WHERE 1";
        $connection->createCommand($vr_divisions_query)->execute();

        ////Regions
        $regions_query = "UPDATE region SET opening_date = UNIX_TIMESTAMP(opening_date_old),created_at = UNIX_TIMESTAMP(created_on_old),updated_at = UNIX_TIMESTAMP(updated_on_old) WHERE 1";
        $connection->createCommand($regions_query)->execute();

        ////Areas
        $areas_query = "UPDATE areas SET opening_date = UNIX_TIMESTAMP(opening_date_old),created_at = UNIX_TIMESTAMP(created_on_old),updated_at = UNIX_TIMESTAMP(updated_on_old) WHERE 1";
        $connection->createCommand($areas_query)->execute();

        ////Branches
        $branches_query = "UPDATE branches SET opening_date = UNIX_TIMESTAMP(opening_date_old),created_at = UNIX_TIMESTAMP(created_on_old),updated_at = UNIX_TIMESTAMP(updated_on_old) WHERE 1";
        $connection->createCommand($branches_query)->execute();*/

        //get all regions
        $regions = Regions::find()->all();
        foreach ($regions as $region){

            ////disbursements
           /* $disbursements_query = "UPDATE disbursements SET date_disbursed = UNIX_TIMESTAMP(date_disburse_old) WHERE date_disburse_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($disbursements_query)->execute();


            ////groups
            $groups_query = "UPDATE groups SET created_at = UNIX_TIMESTAMP(dt_entry_old) WHERE dt_entry_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($groups_query)->execute();*/

            ////applications
            /*$applications_query = "UPDATE applications SET application_date = UNIX_TIMESTAMP(dt_applied_old),created_at = UNIX_TIMESTAMP(dt_applied_old) WHERE dt_applied_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($applications_query)->execute();*/

            ////loans
            /*$loans_query = "UPDATE loans SET date_approved = UNIX_TIMESTAMP(date_approved_old),date_disbursed = UNIX_TIMESTAMP(date_disburse_old),cheque_dt = UNIX_TIMESTAMP(cheque_dt_old),loan_expiry = UNIX_TIMESTAMP(loan_expiry_old),loan_completed_date = UNIX_TIMESTAMP(loan_completed_dt_old) WHERE date_approved_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($loans_query)->execute();*/

            ////members

            ////donations
            /*$donations_query = "UPDATE donations SET receive_date = UNIX_TIMESTAMP(recv_date_old) WHERE recv_date_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($donations_query)->execute();*/

            ////operations
            /*$operations_query = "UPDATE operations SET receive_date = UNIX_TIMESTAMP(recv_date_old) WHERE recv_date_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($operations_query)->execute();

            ////Recoveries
            $recoveries_query = "UPDATE recoveries SET receive_date = UNIX_TIMESTAMP(recv_date_old),due_date = UNIX_TIMESTAMP(due_date_old) WHERE recv_date_old is not NULL and region_id = ".$region->id;
            $connection->createCommand($recoveries_query)->execute();*/

            ////schedules
            $branches = $region->branches;
            foreach ($branches as $branch){
                $schedules_query = "UPDATE schedules SET due_date = UNIX_TIMESTAMP(due_date_old),updated_at = UNIX_TIMESTAMP(updated_on_old) WHERE due_date_old is not NULL and due_date = 0 and branch_id = ".$branch->id;
                $connection->createCommand($schedules_query)->execute();
            }

        }
    }

    public function actionPost()
    {
        ini_set('memory_limit', '404894M');
        ini_set('max_execution_time', 10000);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $recovery_file_records = RecoveryFiles::find()->where(['status' => '2'])->all();
        $flag = false;
        $currect_date = strtotime("now");
        $last_months = strtotime('now first day of last month');
        foreach ($recovery_file_records as $recovery_file_record) {
            $file_name = $recovery_file_record['file_name'];
            $file_source = $recovery_file_record['source'];
            $file_path = ImageHelper::getAttachmentPath() . /*'/frontend/web'. */'/recoveries/' . $file_source . '/' . $file_name;
            $inserted_records = 0;
            $error_records = 0;
            $total_records = 1;
            $branch_name = '';
            $branch_code = '';
            $recovery_file_model = $this->findModel($recovery_file_record['id']);
            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $model = new Recoveries();

                    foreach ($row as $key => $value) {

                        $column_name = $header[$key];
                        /*if (isset($model->$column_name)) {
                            $model->$column_name = $value;
                        }*/
                        if ($model->hasAttribute($column_name)) {
                            $model->$column_name = $value;
                        }
                        if ($column_name == 'receipt_no') {
                            $model->receipt_no = trim($recovery_file_record['source'] . $value);
                        }
                        if ($column_name == 'bank_branch_name') {
                            $branch_name = $value;
                        }
                        if ($column_name == 'bank_branch_code') {
                            $branch_code = $value;
                        }
                        if ($column_name == 'recv_date') {
                            $model->receive_date = strtotime($value);
                        }
                        if ($column_name == 'credit') {
                            $model->amount = $value ;
                        }
                        if (isset($recovery_file_model->$column_name)) {
                            $recovery_file_model->$column_name = $value;
                        }
                        //$model->mdp = '0';
                        $model->created_by = $recovery_file_record['updated_by'];
                        //$model->mdp_acc_no = $branch_code . '_' . $branch_name;
                        $model->source = $recovery_file_record['source'];

                        if ($column_name == 'cnic/sanction_no') {

                            if (preg_match('/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/', $value)) {
                                $flag = true;
                                $model->sanction_no = RecoveriesHelper::searchSanctionnoByCnic($value);
                                $this->cnic = $value;
                                if($model->source=='WROFF'){
                                    $black_list=Blacklist::find()->where(['cnic'=>$this->cnic,'deleted'=>0])->one();
                                    if(empty($black_list)){
                                        $member=Members::find()->where(['cnic'=>$this->cnic,'deleted'=>0])->one();
                                        if(!empty($member)){
	                                        $b_model=new Blacklist();
	                                        $b_model->cnic=$this->cnic;
	                                        $b_model->name=$member->full_name;
	                                        $b_model->type='soft';
	                                        $b_model->reason='write-off';
	                                        $b_model->created_by=recovery_file_record['updated_by'];
	                                        $b_model->save();
                                        }
                                    }
                                }
                            } else {
                                $model->sanction_no = $value;
                            }
                        }
                    }
                    if(($model->receive_date > $currect_date) || ($model->receive_date <= $last_months)){
                        if (empty($model->sanction_no)) {
                            $model->addError('sanction_no', 'This CNIC No not Exists.');
                        }

                        if ($model->receive_date > $currect_date) {
                            $model->addError('receive_date','recovery date is in future date.');
                        }
                        if($model->receive_date <= $last_months){
                            $model->addError('receive_date','recovery date is in previous month.');
                        }

                        $error_records++;
                        $error_model = new RecoveryErrors();
                        if ($flag) {
                            $error_model->cnic = $this->cnic;
                        }
                        $error_model->sanction_no = $model->sanction_no;
                        $error_model->recv_date = $model->receive_date;
                        if($recovery_file_record['source'] == 'cih'){
                            $error_model->receipt_no = 'abc';
                        }else{
                            $error_model->receipt_no = trim($model->receipt_no);
                        }

                        $error_model->credit = isset($model->amount) ? $model->amount : 0;
                        $error_model->bank_branch_name = $branch_name;
                        $error_model->bank_branch_code = $branch_code;
                        if($recovery_file_record['source'] == 'cih'){
                            $error_model->source = '1';
                        }else{
                            $error_model->source = $recovery_file_record['source'];
                        }

                        if (empty($model->sanction_no)) {

                        }
                        if (isset($model->loan->balance)) {
                            $error_model->balance = $model->loan->balance;
                        }
                        if (isset($model->branch_id)) {
                            $error_model->branch_id = $model->branch_id;
                        }
                        if (isset($model->area_id)) {
                            $error_model->area_id = $model->area_id;
                        }
                        if (isset($model->region_id)) {
                            $error_model->region_id = $model->region_id;
                        }
                        $error_model->recovery_files_id = $recovery_file_record['id'];
                        $error_model->status = '0';
                        $error_model->error_description = json_encode($model->getErrors());
                        $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                        $error_model->created_by = '1';
                        $error_model->assigned_to = '1';
                        $error_model->save();
                    }else{
                        if (!$model->save()) {
                            if (empty($model->sanction_no)) {
                                $model->addError('sanction_no', 'This CNIC No not Exists.');
                            }
                            $error_records++;
                            $error_model = new RecoveryErrors();
                            if ($flag) {
                                $error_model->cnic = $this->cnic;
                            }
                            $error_model->sanction_no = $model->sanction_no;
                            $error_model->recv_date = $model->receive_date;
                            if($recovery_file_record['source'] == 'cih'){
                                $error_model->receipt_no = 'abc';
                            }else{
                                $error_model->receipt_no = trim($model->receipt_no);
                            }
                            $error_model->credit = isset($model->amount) ? $model->amount : 0;
                            $error_model->bank_branch_name = $branch_name;
                            $error_model->bank_branch_code = $branch_code;

                            if($recovery_file_record['source'] == 'cih'){
                                $error_model->source = '1';
                            }else{
                                $error_model->source = $recovery_file_record['source'];
                            }


                            if (empty($model->sanction_no)) {

                            }
                            if (isset($model->loan->balance)) {
                                $error_model->balance = $model->loan->balance;
                            }
                            if (isset($model->branch_id)) {
                                $error_model->branch_id = $model->branch_id;
                            }
                            if (isset($model->area_id)) {
                                $error_model->area_id = $model->area_id;
                            }
                            if (isset($model->region_id)) {
                                $error_model->region_id = $model->region_id;
                            }
                            $error_model->recovery_files_id = $recovery_file_record['id'];
                            $error_model->status = '0';
                            $error_model->error_description = json_encode($model->getErrors());
                            $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                            $error_model->created_by = '1';
                            $error_model->assigned_to = '1';
                            $error_model->save();
                        } else {
//                        if(in_array($model->project_id,[77,78,79])){
//                            self::actionUpdateRecoveryForFund($model->id);
//                        }
                            if(in_array($model->project_id,[77])){
                                $controller = new FixesController(Yii::$app->controller->id, Yii::$app);
                                $controller->actionLedgerReGeneratesKppSingle($model->loan_id);
                            }

                            if(in_array($model->project_id,[52,61,62,64,67,76,77,83,90])){
                                SmsHelper::SmsLogs('recovery', $model);
                            }
                            $inserted_records++;
                        }
                    }

                }
            }
            $recovery_file_model->total_records = $total_records - 1;
            $recovery_file_model->inserted_records = $inserted_records;
            $recovery_file_model->error_records = $error_records;
            if ($recovery_file_model->total_records == $recovery_file_model->inserted_records) {
                $recovery_file_model->status = '4';
            } else {
                $recovery_file_model->status = '3';
            }
            $recovery_file_model->file = 'abc';
            if($recovery_file_model->save()){

            }else{
                var_dump($recovery_file_model->getErrors());
                die();
            }
        }

    }
    
    public function actionUpdateFundRequest()
    {
        $fund_requests=FundRequests::find()->all();
        foreach ($fund_requests as $model){
            $branch = StructureHelper::getBranch($model->branch_id);
            $model->region_id = $branch->region_id;
            $model->area_id =   $branch->area_id;
            $model->updated_by =   0;
            $model->save();
        }

    }
    public function actionCreateGroupActions()
    {
        $ids=array(961308,961315,961316,963177);

        $groups=Groups::find()->where(['in','id',$ids])->all();
        foreach ($groups as $model){
            $grp_action=GroupActions::find()->where(['parent_id'=>$model->id])->all();
            if(empty($grp_action)){
                //fund_request
                $action_model = new GroupActions();
                $action_model->parent_id = $model->id;
                $action_model->user_id = $model->created_by;
                $action_model->created_by = $model->created_by;
                $action_model->action = "fund_request";
                $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                $action_model->save();
                //lac
                $action_model = new GroupActions();
                $action_model->parent_id = $model->id;
                $action_model->user_id = $model->created_by;
                $action_model->created_by = $model->created_by;
                $action_model->action = "lac";
                $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                $action_model->save();
            }
        }

    }
    public function actionCreateLoanActions()
    {
        /*$ids=array(14321);
        $loans=Loans::find()->where(['in','id',$ids])->all();*/

        $loans=Loans::find()->where(['status'=>'pending','deleted'=>0,'disbursement_id'=>0])->all();
        foreach ($loans as $model){
            $group_action=GroupActions::find()->where(['parent_id'=>$model->group_id])->all();
            if(empty($group_action)){
                ///create group actions

                //fund_request
                $action_model_group = new GroupActions();
                //die('a');
                $action_model_group->setValues($model->group_id, "fund_request", $model->created_by, $status = 0);
                $action_model_group->created_by = $model->created_by;
                $action_model_group->save();

                //lac
                $action_flag=true;
                $application=Applications::find()->where(['group_id'=>$model->group_id])->all();
                foreach($application as $app){
                    if(!isset($app->loan)){
                        $action_flag=false;
                    }
                }
                if($action_flag==true){
                    $action_model = new GroupActions();
                    $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 1);
                    $action_model->created_by = $model->created_by;
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                }
                else{
                    $action_model = new GroupActions();
                    $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 0);
                    $action_model->created_by = $model->created_by;
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                }
            }
            $loan_action=LoanActions::find()->where(['parent_id'=>$model->id])->all();
            if(empty($loan_action)){
                ///create loan actions

                //lac
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "lac", $model->created_by, $status = 1);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                $action_model_loan->save();

                //cheque_printing
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "cheque_printing", $model->created_by, $status = 0);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->save();

                //disbursement
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "disbursement", $model->created_by, $status = 0);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->save();

                //loan_approved
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "loan_approved", $model->created_by, $status = 1);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");;
                $action_model_loan->save();

                //takaful
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "takaful", $model->created_by, $status = 0);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->save();
            }
        }

    }
    public function actionChequePrintLoanActions()
    {
        /*$ids=array(14321);
        $loans=Loans::find()->where(['in','id',$ids])->all();*/

        $loans=Loans::find()->where(['status'=>'approved','deleted'=>0,'disbursement_id'=>0])->all();
        foreach ($loans as $model){
            $group_action=GroupActions::find()->where(['parent_id'=>$model->group_id])->all();
            if(empty($group_action)){
                ///create group actions

                //fund_request
                $action_model_group = new GroupActions();
                //die('a');
                $action_model_group->setValues($model->group_id, "fund_request", $model->created_by, $status = 0);
                $action_model_group->created_by = $model->created_by;
                $action_model_group->save();

                //lac
                $action_flag=true;
                $application=Applications::find()->where(['group_id'=>$model->group_id])->all();
                foreach($application as $app){
                    if(!isset($app->loan)){
                        $action_flag=false;
                    }
                }
                if($action_flag==true){
                    $action_model = new GroupActions();
                    $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 1);
                    $action_model->created_by = $model->created_by;
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                }
                else{
                    $action_model = new GroupActions();
                    $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 0);
                    $action_model->created_by = $model->created_by;
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                }
            }
            $loan_action=LoanActions::find()->where(['parent_id'=>$model->id])->all();
            if(empty($loan_action)){
                ///create loan actions

                //lac
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "lac", $model->created_by, $status = 1);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                $action_model_loan->save();

                //cheque_printing
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "cheque_printing", $model->created_by, $status = 1);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");;
                $action_model_loan->save();

                //disbursement
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "disbursement", $model->created_by, $status = 0);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->save();

                //loan_approved
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "loan_approved", $model->created_by, $status = 1);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");;
                $action_model_loan->save();

                //takaful
                $action_model_loan = new LoanActions();
                $action_model_loan->setValues($model->id, "takaful", $model->created_by, $status = 0);
                $action_model_loan->created_by = $model->created_by;
                $action_model_loan->save();
            }
        }

    }
    protected function findModel($id)
    {
        if (($model = RecoveryFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateRecoveryForFund($recovery_id){
        $projects = [77,78,79];
        $model = Recoveries::find()->where(['id',$recovery_id])->one();
        if(in_array($model->project_id,$projects)){
            $loanTranches = LoanTranches::find()->where(['loan_id'=>$model->loan_id])->all();
            if(count($loanTranches) == 1){
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[0]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }else{
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[1]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }
        }
        return true;
    }
    public function actionUpdateRecoveryFund(){
        $sql = "SELECT
                project_fund_detail.id as pfd_id,funds.id as fund_id,sum(recoveries.amount) recovery_amount, project_fund_detail.batch_no
            FROM
                recoveries
            INNER JOIN
                loan_tranches
            ON
                loan_tranches.loan_id = recoveries.loan_id
            INNER JOIN
                project_fund_detail
            ON
                project_fund_detail.id = loan_tranches.batch_id
            INNER JOIN
                funds
            ON
                funds.id = project_fund_detail.fund_id
            WHERE
                recoveries.receive_date > funds.recovery_last_update AND recoveries.project_id = funds.project_id AND recoveries.deleted = 0 GROUP BY project_fund_detail.id";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        if(!empty($data) && $data!=null){
            foreach ($data as $d){
                $pfd = ProjectFundDetail::find()->where(['id'=>$d['pfd_id']])->one();
                $fund = Funds::find()->where(['id'=>$d['fund_id']])->one();
                if(!empty($fund) && $fund!=null){
                    $fund->recovery_last_update = Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'));
                    $fund->recovery = $fund->recovery+$d['recovery_amount'];
                    $fund->save();
                }
            }
        }
    }
}