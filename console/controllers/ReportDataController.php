<?php

namespace console\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\StructureHelper;
use common\models\Actions;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Awp;
use common\models\AwpOverdue;
use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\Donations;
use common\models\DynamicReports;
use common\models\Loans;
use common\models\MemberInfo;
use common\models\Members;
use common\models\ProgressReports;
use common\models\Recoveries;
use common\models\Users;
use frontend\modules\branch\Branch;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class ReportDataController extends Controller
{
//    php yii report-data/member-portfolio

    public function actionMemberPortfolio()
    {
        ini_set('memory_limit', '1024M');
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT
            lon.sanction_no,
            mem.full_name,
            mem.cnic,
            (
            SELECT
                memPh.phone
            FROM
                members_phone memPh
            WHERE
                memPh.member_id = mem.id AND memPh.is_current = 1
            ORDER BY
                memPh.id
            LIMIT 1
        ) mobile,
        FROM_UNIXTIME(lon.date_disbursed) date_of_disbursment,
        lon.loan_amount,
        lon.inst_amnt
        
        FROM
            loans lon
        INNER JOIN
            applications app
        ON
            app.id = lon.application_id
        INNER JOIN
            members mem
        ON
            mem.id = app.member_id
        WHERE
            lon.date_disbursed BETWEEN 1604232000 AND 1619783999
        ");
        $data = $command->queryAll();
        $filename = "member_loan_portfolio.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("Sanction No","Name", "CNIC", "Mobile Number", "Date of Disbursement", "Loan Amount", "Monthly Instalment");
        fputcsv($fopen, $createColumn);
        foreach ($data as $d) {
            fputcsv($fopen, $d);
        }
        fclose($fopen);
        exit;
    }


//php yii report-data/sanctions-recovery-data

    public function actionSanctionsRecoveryData(){
        $filename = "member_loan_recovery.csv";
        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_recovery.csv');
        $createColumn = array("sanction no", "recovery amount", "recovery date");
        fputcsv($fopenW, $createColumn);

        $recoveryArray = [];
        $resultArray = [];
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT
            id,sanction_no
        FROM
            loans
        WHERE
            date_disbursed BETWEEN 1604232000 AND 1619783999
        ");
        $loans = $command->queryAll();
        if(!empty($loans) && $loans!=null){
            foreach ($loans as $key=>$loan){
                $recovery = Recoveries::find()->where(['loan_id'=>$loan['id']])->all();
                if(!empty($recovery) && $recovery!=null){
                    foreach ($recovery as $rKey=>$r){
                        $recoveryArray[$key][$rKey]['sanction_no'] = $loan['sanction_no'];
                        $recoveryArray[$key][$rKey]['amount'] = $r->amount;
                        $recoveryArray[$key][$rKey]['receive_date'] = date('Y-m-d',$r->receive_date);
                    }
                }
            }
        }
        foreach ($recoveryArray as $recoveries){
            foreach ($recoveries as $rec){
                $resultArray[] = $rec;
            }
        }

        if(!empty($resultArray) && $resultArray!=null){
            foreach ($resultArray as $d){
                fputcsv($fopenW, $d);
                print_r($d['sanction_no']);echo '<--->';
            }
        }
    }

}