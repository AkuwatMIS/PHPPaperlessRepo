<?php


namespace console\controllers;


use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\models\DisbursementDetails;
use common\models\DynamicReports;
use common\models\FilesAccounts;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\MembersAccount;
use common\models\MembersPhone;
use Yii;
use yii\console\Controller;

class DisbursementDetailsController extends Controller
{
    public function actionUpdateDisbursementStatus(){
        $disbursement_file = DynamicReports::find()->where(['status'=>0,'report_defination_id'=>19 , 'deleted' => 0 ,'is_approved' => 1])->all();
        foreach ($disbursement_file as $file) {
            $file_open = ImageHelper::getAttachmentPath().'/dynamic_reports/response_description/' . $file->uploaded_file;
           // $file_open = 'E:/wamp64/www/paperless_web/frontend/web/uploads/dynamic_reports/response_description/' . $file->uploaded_file;
            $myfile = fopen($file_open, "r");
            $flag = false;
            $i = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $tranch=LoanTranches::find()
                        ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                        ->andWhere(['loans.sanction_no'=>$fileop[0]])
                        ->andWhere(['loan_tranches.tranch_no' => 1])
                        ->one();
                    $tranch->cheque_no=$fileop[1];
                    $tranch->updated_by=1;
                    $tranch->save();
                    $description = DisbursementDetails::find()
                        ->joinWith('tranch')
                        ->joinWith('tranch.loan')
                        ->where(['sanction_no' => $fileop[0]])
                        ->andWhere(['in','disbursement_details.status' ,[3]])
                        ->andWhere(['loan_tranches.tranch_no' => 1])
                        ->one();
                    if (isset($description) && !empty($description)) {
                        $description->response_description = $fileop[1];
                        if(!$description->save()){
                            print_r($description->getErrors());
                        }
                    }
                }
                $flag = true;
                $i++;
            }
            $file->status = 1;
            $file->save();
        }
    }


    public function actionUpdateResponse(){
        $disbursement_file = DynamicReports::find()->where(['status'=>0,'report_defination_id'=>19 , 'deleted' => 0 ,'is_approved' => 1])->all();
        foreach ($disbursement_file as $file) {
            $file_open = ImageHelper::getAttachmentPath().'/dynamic_reports/response_description/' . $file->uploaded_file;
            // $file_open = 'E:/wamp64/www/paperless_web/frontend/web/uploads/dynamic_reports/response_description/' . $file->uploaded_file;
            $myfile = fopen($file_open, "r");
            $flag = false;
            $i = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $sanction = trim($fileop[0]);
                    $res_desc = trim($fileop[1]);
                    $cheque = trim($fileop[2]);
                    $tranch=LoanTranches::find()
                        ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                        ->andWhere(['loans.sanction_no'=>$sanction])
                        ->andWhere(['loan_tranches.tranch_no' => 1])
                        ->one();
                    $tranch->cheque_no=$cheque;
                    $tranch->updated_by=1;
                    $tranch->save();
                    $description = DisbursementDetails::find()
                        ->joinWith('tranch')
                        ->joinWith('tranch.loan')
                        ->where(['sanction_no' => $sanction])
                        ->andWhere(['disbursement_details.response_description' =>$res_desc])
                        ->andWhere(['in','disbursement_details.status' ,[3]])
                        ->one();
                    if (isset($description) && !empty($description)) {
                        $description->status = $fileop[1];
                        if($description->save()){
                            echo 'disbursement detail status updated'. PHP_EOL;
                        }else{
                            var_dump($description->getErrors());
                        }
                    }
                }
                $flag = true;
                $i++;
            }
            $file->status = 1;
            $file->save();
        }
    }


}