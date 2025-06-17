<?php

namespace console\controllers;
use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\models\DisbursementDetails;
use common\models\DisbursementDetailsLogs;
use common\models\DynamicReports;
use common\models\FilesAccounts;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\MembersAccount;
use common\models\PaymentPins;
use common\models\search\PaymentPinsSearch;
use common\models\MembersPhone;
use yii\console\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * PaymentPinsController implements the CRUD actions for PaymentPins model.
 */
class PaymentPinsController extends Controller
{


    public function actionPin(){
        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>2])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/update_pin_files/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $sanction_no           = trim($row[0]);
                    $account_no            = trim(str_replace("'", "", $row[1]));
                    $response_description  = trim($row[2]);

                    $disbursement_detail=DisbursementDetails::find()
                        ->join('inner join', 'loan_tranches', 'loan_tranches.id=disbursement_details.tranche_id')
                        ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                        ->where(['loans.sanction_no'=>$sanction_no])
                        ->andWhere(['=','disbursement_details.account_no',$account_no])
                        ->andWhere(['=','disbursement_details.response_description',$response_description])
                        ->one();
                    if(!empty($disbursement_detail)){
                        $check_pin = PaymentPins::find()->where(['pin'=>trim($row[3])])
                            ->andWhere(['=','disbursement_details_id',$disbursement_detail->id])
                            ->one();
                        if($check_pin){
                            $check_pin->pin = trim($row[3]);
                            $check_pin->created_by = 1;
                            if($check_pin->save()){
                                $updated_records++;
                            }else{
                                $errors[] = [$row[0] => 'failed to update payment pin'];
                            }
                        }else{
                            $pin = new PaymentPins();
                            $pin->disbursement_details_id = $disbursement_detail->id;
                            $pin->pin = trim($row[3]);
                            $pin->created_by = 1;
                            if($pin->save()){
                                $updated_records++;
                            }else{
                                $errors[] = [$row[0] => 'failed to add payment pin'];
                            }
                        }

                    }else{
                        $errors[] = [$row[0] => 'Loan not found'];
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

    // php /var/www/paperless_web/yii payment-pins/rejected

    public function actionRejected(){
        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>3])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/rejected_transaction/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $sanction_no            = trim($row[0]);
                    $account_num            = trim(str_replace("'", "", $row[1]));
                    $response_description   = trim(str_replace("'", "",$row[2]));

                    $disbursement_detail=DisbursementDetails::find()
                        ->join('inner join', 'loan_tranches', 'loan_tranches.id=disbursement_details.tranche_id')
                        ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                        ->where(['loans.sanction_no'=>$sanction_no])
                        ->andWhere(['=','disbursement_details.account_no',$account_num])
                        ->andWhere(['=','disbursement_details.response_description',$response_description])
                        ->andWhere(['=','disbursement_details.deleted',0])
                        ->one();
                    $tranche = LoanTranches::find()->where(['id'=>$disbursement_detail->tranche_id])->one();
//                    if($tranche){
//                        $tranche->status = 8;
//                        $tranche->updated_by = 1;
//                        $tranche->save();
//                    }
//                    createCommand()->getRawSql();

                    if(!empty($disbursement_detail)){
                        $disbursement_detail->status = 4;
                        $disbursement_detail->updated_by = 1;
                        if($disbursement_detail->save()){
                            $updated_records++;
                        }else{
                            print_r($disbursement_detail->getErrors());
                            die();
                            $errors[] = [$row[0] => 'Error while saving record.'];
                        }
                    }else{
                        $errors[] = [$row[0] => 'Loan not found against this account'.$account_num];
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


    public function actionChequePresent(){
        $accounts_files = FilesAccounts::find()->where(['status'=>0,'type'=>4])->all();
        foreach ($accounts_files as $accounts_file)
        {
            $file_name = $accounts_file->file_path;
            $path =ImageHelper::getAttachmentPath().'/cheque_presented/' . $file_name;
            $total_records = 1;
            $errors=[];
            $updated_records =0 ;
            if (($handle = fopen($path, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $total_records++;
                    $sanction_no           = trim($row[0]);
                    $cheque_no            = trim($row[1]);

                    $loan_tranches=LoanTranches::find()
                        ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                        ->where(['loans.sanction_no'=>$sanction_no])
                        ->andWhere(['=','loan_tranches.cheque_no',$cheque_no])
                        ->andWhere(['=','loan_tranches.deleted',0])
                        ->one();

                    if(!empty($loan_tranches) && $loan_tranches->payment_status !=1){
                        $loan_tranches->payment_status = 1;
                        $loan_tranches->updated_by = 1;
                        if($loan_tranches->save()){
                            $updated_records++;
                        }else{
                            $errors[] = [$row[0] => 'cheque status not updated'];
                        }
                    }else{
                        $errors[] = [$row[0] => 'cheque already presented'];
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
}
