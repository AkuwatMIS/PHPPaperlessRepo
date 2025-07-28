<?php


namespace console\controllers;


use common\components\Helpers\AcagHelper;
use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\KamyabPakistanHelper;
use common\components\Helpers\MemberHelper;
use common\models\Accounts;
use common\models\DisbursementDetails;
use common\models\DynamicReports;
use common\models\FilesAccounts;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\MembersAccount;
use common\models\MembersPhone;
use common\models\PaymentPins;
use Yii;
use yii\console\Controller;

class BankAccountsController extends Controller
{

//php /var/www/paperless_web/yii bank-accounts/verify

    public function actionVerify(){
        $accounts_files = FilesAccounts::find()->where(['status'=>3,'type'=>0])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/verified_accounts/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $account_no = str_replace("'", "",  $row[1]);
                    $member = Members::find()->where(['cnic' => $row[0]])->one();
                    if(isset($member) && !empty($member)) {
                        $member_account = MembersAccount::find()->where(['account_no' => $account_no, 'member_id' => $member->id,'is_current' => 1])->one();
                        if (isset($member_account)) {
                            $member_account->status = 1;
                            $member_account->verified_by = $accounts_file->created_by;
                            $member_account->acc_file_id = $accounts_file->id;
                            if(empty($member_account->account_type)){
                                $member_account->account_type = 'bank_accounts';
                            }
                            if ($member_account->save()) {
                                MemberHelper::getVerficationAction($member_account->member_id,$accounts_file->created_by);
                                $updated_records++;
                            } else {
                                $errors[] = [$row[1] => $member_account->getErrors()];
                            }
                        } else {
                            $errors[] = [$row[1] => 'account not found'];
                        }
                    } else {
                        $errors[]= [$row[0] => 'member not found'];
                    }
                }
            }
            $accounts_file->total_records = $total_records - 1;
            $accounts_file->updated_records = $updated_records;
            $accounts_file->status = 1;
            if(isset($errors) && !empty($errors) ) {
                $accounts_file->error_description = json_encode($errors);
            }
            $accounts_file->save();
        }
    }

//php /var/www/paperless_web/yii bank-accounts/rejected

    public function actionRejected(){

        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>6])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/verified_accounts/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $account_no = str_replace("'", "",  $row[1]);
                    $member = Members::find()->where(['cnic' => $row[0]])->one();
                    if(isset($member) && !empty($member)) {
                        $member_account = MembersAccount::find()->where(['account_no' => $account_no, 'member_id' => $member->id,'is_current' => 1])->one();
                        if (isset($member_account)) {
                            $member_account->status = 2;
                            $member_account->verified_by = $accounts_file->created_by;
                            if(empty($member_account->account_type)){
                                $member_account->account_type = 'bank_accounts';
                            }
                            if ($member_account->save()) {
                                $updated_records++;
                            } else {
                                $errors[] = [$row[1] => $member_account->getErrors()];
                            }
                        } else {
                            $errors[] = [$row[1] => 'account not found'];
                        }
                    } else {
                        $errors[]= [$row[0] => 'member not found'];
                    }
                }
            }
            $accounts_file->total_records = $total_records - 1;
            $accounts_file->updated_records = $updated_records;
            $accounts_file->status = 1;
            if(isset($errors) && !empty($errors) ) {
                $accounts_file->error_description = json_encode($errors);
            }
            $accounts_file->save();
        }
    }

//   php /var/www/paperless_web/yii bank-accounts/publish

    public function actionPublish(){
        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>1])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/verified_accounts/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $account_no = str_replace("'", "",  $row[1]);
                    $cnic = str_replace("'", "",  $row[0]);
                    $date_disbursement = str_replace("'", "",  $row[3]);
//                    ['in','loan_tranches.status',[6,8]]
                    $tranch = LoanTranches::find()->where(['loan_tranches.status' => 8])
                        ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                        ->join('inner join', 'applications', 'applications.id=loans.application_id')
                        ->join('inner join', 'members', 'members.id=applications.member_id')
                        ->join('inner join', 'members_account', 'members_account.member_id=members.id')
                        ->andWhere(['=','members_account.is_current','1'])
                        ->andWhere(['=','members_account.account_no',$account_no])
                        ->andWhere(['=','loans.sanction_no',$row[0]])
                        ->andWhere(['loans.deleted' => 0])
//                        ->orderBy(['id' => SORT_DESC])
//                        ->limit(1)->one();
//                        ->createCommand()->getRawSql();
                        ->one();
                    $disb_detail=DisbursementDetails::find()->where(['tranche_id'=>$tranch->id])->andWhere(['in','status',[0,4,5,6]])->andWhere(['deleted'=>0])->one();
                    if(!empty($disb_detail)){
                        $disb_detail->status=1;
                        $disb_detail->response_description=$row[2];
                        $disb_detail->updated_by=$accounts_file->created_by;
                        if($disb_detail->save()){
                            $tranch->cheque_no=$row[2];
                            if($tranch->save()){
                                $updated_records++;
                                if($tranch->tranch_no==1){
                                    $loan = Loans::find()->where(['id'=>$tranch->loan_id])->one();
                                    if(!empty($loan) && $loan!=null){
                                        $loan->date_disbursed = strtotime($date_disbursement);
                                        if($loan->save(false)){
                                            $tranch->date_disbursed = strtotime($date_disbursement);
                                            if(!$tranch->save(false)){
                                                $errors[]= [$row[0] => 'tranche disbursed date not updated'];
                                            }
                                        }else{
                                            $errors[]= [$row[0] => 'Loan disbursed date not updated'];
                                        }
                                    }
                                }else{
                                    $tranch->date_disbursed = strtotime($date_disbursement);
                                    if(!$tranch->save(false)){
                                        $errors[]= [$row[0] => 'tranche disbursed date not updated'];
                                    }else{
                                        $loan = Loans::find()->where(['id'=>$tranch->loan_id])->one();
                                        if(!empty($loan) && $loan!=null && $loan->project_id == 132){
                                            $cnic_without_hyphens = str_replace('-', '', $loan->application->member->cnic);
                                            $obj = [
                                                "CNIC"=> $cnic_without_hyphens,
                                                "FirstDisbursementDate"=> null,
                                                "NoOfInstallments"=>null,
                                                "MonthlyInstallmentAmount"=> null,
                                                "FirstDueDate"=>null,
                                                "SecondDisbursementDate"=> $date_disbursement,
                                            ];
                                            AcagHelper::actionPushDisbursement($obj);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!empty($tranch)){
                            $errors[]= [$row[0] => 'not published'];
                        }else{
                            $mem = Members::find()->where(['cnic' => $cnic])->one();
                            if (empty($mem)) {
                                $errors[] = [$row[0] => 'member not found'];
                            } else {
                                $errors[] = [$row[0] => 'account not found'];
                            }
                        }
                    }
                }
            }
            $accounts_file->total_records = $total_records - 1;
            $accounts_file->updated_records = $updated_records;
            $accounts_file->status = 1;
            if(isset($errors) && !empty($errors) ) {
                $accounts_file->error_description = json_encode($errors);
            }
            $accounts_file->save();
        }
    }

// php /var/www/paperless_web/yii bank-accounts/publish-out
    public function actionPublishOut(){
        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>5])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/verified_accounts/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $account_no = str_replace("'", "",  $row[1]);
                    $cnic = str_replace("'", "",  $row[0]);
//                    ['in','loan_tranches.status',[6,8]]
                    $tranch = LoanTranches::find()->where(['loan_tranches.status' => 6])
                        ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                        ->join('inner join', 'applications', 'applications.id=loans.application_id')
                        ->join('inner join', 'members', 'members.id=applications.member_id')
                        ->join('inner join', 'members_account', 'members_account.member_id=members.id')
                        ->andWhere(['=','members_account.is_current','1'])
                        ->andWhere(['=','members_account.account_no',$account_no])
                        ->andWhere(['=','loans.sanction_no',$row[0]])
                        ->andWhere(['loans.deleted' => 0])
                        ->orderBy(['id' => SORT_DESC])
//                        ->limit(1)->one();
//                        ->createCommand()->getRawSql();
                        ->one();
                    $disb_detail=DisbursementDetails::find()->where(['tranche_id'=>$tranch->id])->andWhere(['in','status',[4]])->andWhere(['deleted'=>0])->one();
                    if(!empty($disb_detail)){
                        $disb_detail->status=3;
                        $disb_detail->response_description=$row[2];
                        $disb_detail->updated_by=$accounts_file->created_by;
                        if($disb_detail->save()){
                            $tranch->cheque_no=$row[2];
                            $tranch->save();
                            $updated_records++;
                        }
                    }else{
                        if(!empty($tranch)){
                            $errors[]= [$row[0] => 'not published'];
                        }else{
                            $mem = Members::find()->where(['cnic' => $cnic])->one();
                            if (empty($mem)) {
                                $errors[] = [$row[0] => 'member not found'];
                            } else {
                                $errors[] = [$row[0] => 'account not found'];
                            }
                        }
                    }
                }
            }
            $accounts_file->total_records = $total_records - 1;
            $accounts_file->updated_records = $updated_records;
            $accounts_file->status = 1;
            if(isset($errors) && !empty($errors) ) {
                $accounts_file->error_description = json_encode($errors);
            }
            $accounts_file->save();
        }
    }

    public function actionAblBankTransaction() {
        $env = 'sandbox';
        $disb_details = DisbursementDetails::find()->where(['bank_name' => 'ABL','status' => 0,'tranche_id' => 4723605 ])->all();
        $access_token = BankaccountsHelper::getAccessToken($env);
        foreach ($disb_details as $disb)
        {
            $response = BankaccountsHelper::ablEcommereceApi($access_token,$disb->tranche_id,$disb->account_no,$disb->transferred_amount,$env);
            if($response['StatusCode'] == 0) {
                $disb->status = 1;
            } else {
                $disb->status = 2;
            }
            $disb->response_code = $response['StatusCode'];
            $disb->response_description = $response['StatusDescription'];
            $disb->save();
        }

    }

    public function actionAblBankTransactionNew() {
        $env = 'sandbox';
        $disb_details = DisbursementDetails::find()->where(['bank_name' => 'ABL','status' => 0,'tranche_id'=>4723605])->all();
        $access_token = BankaccountsHelper::getAccessToken($env);
        foreach ($disb_details as $disb)
        {
            $response_account = BankaccountsHelper::ablCheckAccountNo($access_token,$disb->tranche_id,$disb->account_no,$disb->transferred_amount,$env);
            if($response_account['StatusCode'] == 0) {
                $response = BankaccountsHelper::ablEcommereceApiNew($access_token,$disb->tranche_id,$disb->account_no,$disb->transferred_amount,$env);
                if($response['StatusCode'] == 0) {
                    $disb->status = 1;
                } else {
                    $disb->status = 2;
                    $disb->response_code = $response['StatusCode'];
                    $disb->response_description = 'Account No not exist in ABL';
                }
            } else {
                $disb->status = 2;
                $disb->response_code = $response_account['StatusCode'];
                $disb->response_description = $response_account['StatusDescription'];
            }
            $disb->save();
        }

    }
    public function actionAblBankPayAnyOne() {

        $total_records = DisbursementDetails::find()->where(['status' => 0])->count();
        while($total_records > 0) {
            $disb_details = DisbursementDetails::find()->where(['status' => 0])->limit(10)->all();
            //$disb_details = DisbursementDetails::find()->where(['tranche_id' => 4861023])->all();
            $batch = [];
            foreach ($disb_details as $id) {
                $batch[] = $id['id'];
            }
            $batch = implode(",", $batch);
            Yii::$app->db->createCommand('UPDATE disbursement_details SET status = 10 WHERE id IN(' . $batch . ')')->execute();
            // rr -- $disb_details = DisbursementDetails::find()->where(['status' => 10])->limit(10)->all();
            $access_token = BankaccountsHelper::getAccessToken();
            foreach ($disb_details as $disb) {
                $project =  $disb->tranch->loan;
                $key = Accounts::find()->select('third_party_key')->join('inner join', 'branch_projects_mapping', 'branch_projects_mapping.account_id=accounts.id')
                    ->where(['project_id' => $project->project_id])->andWhere(['branch_id' => $project->branch_id])->one();
                $request = [
                    'access_token'   => $access_token,
                    'request_id'     => $disb->tranche_id,
                    'credit_account' => $disb->account_no,
                    'amount'         => $disb->transferred_amount,
                   // 'member'         => $disb->tranch->loan->application->member,
                    'thirdpartykey'  => $key->third_party_key
                ];
               // $response = BankaccountsHelper::ablPayAnyOne($access_token, $disb->tranche_id, $disb->account_no, $disb->transferred_amount, $disb->tranch->loan->application->member,$third_party_key);
                $response = BankaccountsHelper::ablPayAnyOne($request , $disb->tranch->loan->application->member);
                print_r($request);
                if(!empty($response)) {
                    if ($response['Responsecode'] == 00) {
                        $disb->status = 1;
                    } else {
                        $disb->status = 2;
                    }
                    $disb->response_code = $response['Responsecode'];
                    $disb->response_description = $response['Responsedescription'] . '-pin-' . $response['PIN'] . '-txn_no-' . $response['TransactionId'];
                    $disb->save();
                    $pin = new PaymentPins();
                    $pin->disbursement_details_id = $disb->id;
                    $pin->pin = $response['PIN'];
                    $pin->created_at = strtotime(date('d-m-Y'));
                    $pin->updated_at = strtotime(date('d-m-Y'));
                    $pin->created_by = 1;
                    $pin->save();
                    $tranch = LoanTranches::find()->where(['loan_id' => $disb->tranch->loan->id])->one();
                    $tranch->cheque_no = $response['TransactionId'];
                    $tranch->save();
                    //$total_records = $total_records-1;
                }
            }
            $total_records = $total_records-10;
        }
    }

// php yii bank-accounts/generate-ledger
    public function actionGenerateLedger() {
        $disb_tranches = DisbursementDetails::find()->where(['status' => 1])->all();
        foreach ($disb_tranches as $disb_tranch)
        {
            $loan_tranche = LoanTranches::find()->where(['id' => $disb_tranch->tranche_id])->one();
            $loan_tranche->status = 6;
            $loan_tranche->updated_by = 1;
            $loan_tranche->save();
            $loan = Loans::find()->where(['id' => $loan_tranche->loan_id])->one();
            if(!empty($loan) && $loan!=null){
                $tranchesAmountSum = LoanTranches::find()
                    ->where(['loan_id' => $loan_tranche->loan_id])
                    ->andWhere(['status' => 6])
                    ->sum('tranch_amount');
            }
            $loan->status = 'collected';
            $loan->disbursed_amount = $tranchesAmountSum;
            $loan->updated_by=1;
            $loan->save();

            if ($loan->project_id == 77){
                if($loan_tranche->tranch_no == "1") {
                    KamyabPakistanHelper::kppLedgerGenerate($loan);
                }else{
                    KamyabPakistanHelper::ledgerReGeneratesKppSingle($loan);
                }
            }else{
                $projects = [52,61,62,64,67,76,90,103];
                if (in_array($loan->project_id, $projects)){
                    if($loan_tranche->tranch_no == "1") {
                        DisbursementHelper::GenerateSchedule($loan,true);
                        FixesHelper::update_loan_expiry($loan);
                    } elseif ($loan_tranche->tranch_no == "2") {
                        FixesHelper::ledger_regenerate($loan);
                    }else{
                        if (in_array($loan->project_id, $projects)){
                            FixesHelper::LedgerGeneratesExtra($loan->id);
                        }
                    }
                }else{
                    if($loan_tranche->tranch_no == "1") {
                        DisbursementHelper::GenerateSchedule($loan,true);
                        FixesHelper::update_loan_expiry($loan);
                    } elseif ($loan_tranche->tranch_no == "2") {
                        FixesHelper::ledger_regenerate($loan);
                    }
                }
            }

            $disb_tranch->status = 3;
            $disb_tranch->updated_by = 1;
            $disb_tranch->save();
        }

    }

    // php yii bank-accounts/pm-ledger-regenerate
    public function actionPmLedgerRegenerate($id){
        FixesHelper::LedgerGeneratesExtra($id);
    }
    public function actionUpdateAccountNo()
    {
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [16]])->all();
        foreach ($dynamic_reports as $report) {
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'account' . '/' . $report->uploaded_file;
            $myfile = fopen($file_path, "r");
            $flag = false;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $member=Members::find()->where(['cnic'=>$fileop[1]])->one();
                    if(!empty($member)){
                        $mobile=MembersPhone::find()->where(['member_id'=>$member->id,'is_current'=>1,'phone_type'=>'mobile'])->one();
                        ///if(!empty($mobile)){
                        /*$mobiles=MembersPhone::find()->where(['member_id'=>$member->id,'is_current'=>1,'phone_type'=>'mobile'])->all();
                        foreach($mobiles as $m){
                            $m->is_current=0;
                            $m->updated_by=1;
                            $m->save();
                        }*/
                        /*$new_mob=new MembersPhone();
                        $new_mob->member_id=$member->id;
                        $new_mob->phone=str_replace("'", "",  $fileop[3]);
                        $new_mob->phone_type='mobile';
                        $new_mob->is_current=1;
                        $new_mob->created_by=1;
                        $new_mob->assigned_to=1;
                        if(!$new_mob->save()){
                            print_r($new_mob->phone);
                            print_r($new_mob->getErrors());
                            die();
                        }*/

                        $accounts=MembersAccount::find()->where(['member_id'=>$member->id])->all();
                        foreach($accounts as $acc){
                            $acc->is_current=0;
                            $acc->updated_by=1;
                            if(!$acc->save(false)){
                                print_r($acc->getErrors());
                                die('here');
                            }
                        }

                        $account=new MembersAccount();
                        $account->member_id=$member->id;
                        //$account->bank_name='Mobile';
                        $account->account_type=trim($fileop[4]);
                        $account->bank_name=trim($fileop[5]);
                        $account->title=trim($fileop[2]);
                        $account->account_no=str_replace("'", "",  $fileop[3]);
                        $account->is_current=1;
                        $account->created_by=1;
                        $account->assigned_to=1;
                        if(!$account->save()){
                            print_r($account->getErrors());
                            die('saved');
                        }
                        //}
                        print_r($account);
                    }
                }
                $flag = true;
            }
            $report->status=1;
            $report->save();
            die('i done.');
        }
    }
}