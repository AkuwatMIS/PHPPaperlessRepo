<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;

use common\models\Accounts;
use common\models\Applications;
use common\models\ArcAccountReportDetails;
use common\models\ArcAccountReports;
use common\models\Awp;
use common\models\AwpBranchSustainability;
use common\models\AwpFinal;
use common\models\AwpLoanManagementCost;
use common\models\AwpLoansUm;
use common\models\AwpOverdue;
use common\models\AwpProjectMapping;
use common\models\AwpRecoveryPercentage;
use common\models\AwpTargetVsAchievement;
use common\models\BankAccounts;
use common\models\BranchAccount;
use common\models\Branches;
use common\models\BranchProjects;
use common\models\BranchProjectsMapping;
use common\models\BranchSustainability;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\ProgressReportDetails;
use common\models\ProgressReports;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\search\AwpLoansUmSearch;
use common\models\TargetVsAchievement;
use frontend\controllers\AwpLoanManagementCostController;
use yii\console\Controller;
use Yii;
use yii\db\Exception;

class DataSettingController extends Controller
{

    public function actionMemberVerify()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_in = 'member_cnic_status_check.csv';
        $file_out = 'member_cnic_status.csv';

        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/' . $file_in;
        $myfile = fopen($file_path, "r");
        $flag = true;
        $i = 0;
        $dataArray=[];
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {
                $i++;
                $member = Members::find()->select(['id', 'cnic'])->where(['cnic'=>$fileop[0]])->one();
                if (!empty($member) && $member!=null) {
                    $application = Applications::find()
                        ->select(['applications.id','applications.application_no'])
                        ->where(['applications.member_id'=>$member->id])
                        ->innerJoin('loans', 'loans.application_id = applications.id')
                        ->orderBy('loans.id DESC')
                        ->one();

                    if(!empty($application) && $application!=null){
                        $loan = Loans::find()->where(['application_id'=>$application->id])->one();
                        if(!empty($loan) && $loan!=null){
                            $dataArray[$i]['cnic']        = $member->cnic;
                            $dataArray[$i]['sanction_no'] = $loan->sanction_no;
                            $dataArray[$i]['loan_amount'] = $loan->loan_amount;
                            $dataArray[$i]['status']      = $loan->status;
                            $dataArray[$i]['date_of_disbursement']      = $loan->date_disbursed;
                            echo '------';
                            echo 'data';
                            echo '------';
                            $flag = true;
                        }else{
                            echo 'loan------';
                            print_r($application->application_no);
                            echo 'end_app------';
                            print_r($fileop[0]);
                            echo '------';
                            $dataArray[$i]['cnic']   = $fileop[0];
                            $dataArray[$i]['sanction_no'] = 'NA';
                            $dataArray[$i]['loan_amount'] = 'NA';
                            $dataArray[$i]['status']      = 'NA';
                            $dataArray[$i]['date_of_disbursement']      = 'NA';
                            $flag = false;
                        }

                    }
                }
            }

        }

        $file_path_out = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/' . $file_out;
        $fopen = fopen($file_path_out, "w");
        fputcsv($fopen,['CNIC','Sanction No','Loan Amount','Status','Date Of Disbursement']);
        foreach ($dataArray as $row)
        {
            fputcsv($fopen,$row);
        }
        fclose($fopen);

    }


}


