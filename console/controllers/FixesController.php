<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;


use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\KamyabPakistanHelper;
use common\components\Helpers\MemberHelper;
use common\models\Branches;
use common\models\DisbursementDetails;
use common\models\LedgerRegenerateLogs;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Members;
use common\models\MembersPhone;
use common\models\ProjectsDisabled;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\SchedulesHousingPre;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;

class FixesController extends Controller
{
    public function actionUpdatePhone()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/phone.csv';
        $myfile = fopen($file_path, "r");


        while (($fileop = fgetcsv($myfile)) !== false) {

            $member = Members::find()->where(['cnic' => $fileop[0]])->one();
            if (isset($member)) {
                $memberPhone = new MembersPhone();
                $memberPhone->phone_type = 'mobile';
                $memberPhone->phone = $fileop[1];
                $memberPhone->member_id = $member->id;
                $memberPhone->is_current = 1;
                $memberPhone->assigned_to = 1;
                $memberPhone->created_by = 1;
                $memberPhone->updated_by = 1;
                $phone_save = MemberHelper::saveMemberPhone($memberPhone);
                if (!$phone_save) {
                    echo "not updated cnic";
                    print_r($member->cnic);
                }
            } else {
                echo "not saved cnic";
                print_r($fileop[0]);
            }
        }
    }

    public function actionRecovery()
    {
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        $date = strtotime(date('2018-06-30 23:59:59'));
        $loans = Loans::find()
            ->select(['loans.id', 'loans.inst_amnt', 'loans.expiry_date', 'members.full_name as name', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
            //->where(['!=','loans.status','not collected'])
            ->join('inner join', 'applications', 'applications.id=loans.application_id')
            ->join('inner join', 'members', 'members.id=applications.member_id')
            ->where(['=', 'loans.status', 'collected'])
            ->andWhere(['<=', 'loans.date_disbursed', $date])
            //->andWhere(['<=', 'loans.loan_completed_date', $date])
            ->andWhere(['>', 'loans.date_disbursed', 0])
            ->andWhere(['<=', 'loans.deleted', 0])
            //->andWhere(['=','loans.sanction_no','3007-D002-05579'])
            //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
            ->asArray()
            ->all();
        $loans1 = Loans::find()
            ->select(['loans.id', 'loans.inst_amnt', 'loans.expiry_date', 'members.full_name as name', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
            //->where(['!=','loans.status','not collected'])
            ->join('inner join', 'applications', 'applications.id=loans.application_id')
            ->join('inner join', 'members', 'members.id=applications.member_id')
            ->where(['=', 'loans.status', 'loan completed'])
            ->andWhere(['<=', 'loans.date_disbursed', $date])
            ->andWhere(['>', 'loans.loan_completed_date', $date])
            ->andWhere(['>', 'loans.date_disbursed', 0])
            ->andWhere(['<=', 'loans.deleted', 0])
            //->andWhere(['=','loans.sanction_no','51801-D015-0011'])
            ->asArray()
            ->all();

        $loans = array_merge($loans, $loans1);
        $data = [];
        foreach ($loans as $loan) {
            $aging_report = array(
                'sanction_no' => $loan['sanction_no'],
                'name' => $loan['name'],
                'date_disbursed' => date('Y-m-d', $loan['date_disbursed']),
                'inst_months' => $loan['inst_months'],
                'inst_amnt' => $loan['inst_amnt'],
                'expiry_date' => $loan['expiry_date'],
                'loan_amount' => $loan['loan_amount'],
                'balance' => $loan['balance'],
            );
            $data[] = $aging_report;
        }
        /* $sql = "SELECT loans.sanction_no,members.full_name,FROM_UNIXTIME(loans.date_disbursed) as disbursement_date, loans.inst_months,inst_amnt,
 FROM_UNIXTIME(loans.loan_expiry) as expiry_date,loan_amount,
 (SELECT  COALESCE(SUM(`recoveries`.`amount`),0) FROM recoveries WHERE recoveries.loan_id = loans.id AND recoveries.deleted = 0
 AND recoveries.receive_date > 0 AND recoveries.receive_date <= 1530403199)  AS recovery_amount,

 from loans
 INNER JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id`
 where (loans.deleted = 0) AND date_disbursed > 0 and loans.status != 'not collected' HAVING disbursement_date <= '2018-06-30 23:59:59'";
         $query = Yii::$app->db->createCommand($sql);
         $data = $query->queryAll();*/
        $file_name = 'olp_report_2018_updated' . '.csv';
        $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/uploads/' . $file_name;
        $fopen = fopen($file_path, 'w');
        $header = ['Sanction No', 'Name', 'Disbursement Date', 'Installments', 'Installment Amount', 'Expiry Date', 'Loan Amount', 'Recovery'];
        fputcsv($fopen, $header);
        foreach ($data as $g) {
            fputcsv($fopen, $g);
        }
    }

    public function actionLedgerReGenerate()
    {
        $logs_models = LedgerRegenerateLogs::find()->where(['status' => 0])->all();
        foreach ($logs_models as $logs_model) {
            $loan = Loans::findOne(['id' => $logs_model->loan_id]);
            FixesHelper::ledger_regenerate($loan);
            $logs_model->status = 1;
            $logs_model->save();
        }
    }

    public function actionActiveLoansLedgerReGenerate()
    {
        $branches = Branches::find()->where(['deleted' => 0])->andWhere(['<=', 'id', 1])/*->andWhere(['>','id',1])*/
        ->all();
        foreach ($branches as $branch) {
            //$loans_array = [];
            $loans = Loans::find()/*->select(['id'])*/
            ->where(['status' => 'collected'])->andWhere(['branch_id' => $branch->id])->andWhere(['deleted' => 0])->andWhere(['>', 'date_disbursed', 0])->all();
            /*foreach ($loans as $loan)
            {
                array_push($loans_array, $loan->id);
            }
            if(isset($loans_array) && !empty($loans_array)) {
                self::actionLedgerGenerateById($loans_array);
            }*/
            foreach ($loans as $loan) {
                FixesHelper::fix_schedules_update($loan);
            }
            echo $branch->id . '<br>';
        }

    }

    public function actionLedgerGeneratesExtra($loan_id)
    {
        $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])
            ->andWhere(['deleted' => 0])->sum('tranch_amount');
        $ledger = Schedules::find()->where(['loan_id' => $loan_id])
            ->all();

        SchedulesHousingPre::deleteAll(['loan_id' => $loan_id]);
        foreach ($ledger as $d) {
            if(($d->credit !=0) || ($d->charges_credit !=0)){
                $preScheduleNew = new SchedulesHousingPre();
                $preScheduleNew->setAttributes($d->attributes);
                $preScheduleNew->save();
            }else{
                $checkDate = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", $d->due_date))));
                $CheckLedger = Schedules::find()
                    ->where(['loan_id' => $loan_id])
                    ->andWhere(['due_date' => strtotime($checkDate)])
                    ->one();
                if(!empty($CheckLedger) && $CheckLedger!=null && ($CheckLedger->credit !=0) || ($CheckLedger->charges_credit !=0)){
                    $preScheduleNew = new SchedulesHousingPre();
                    $preScheduleNew->setAttributes($d->attributes);
                    $preScheduleNew->save();
                }
            }
        }

        Schedules::deleteAll(['loan_id' => $loan_id]);
        $tranches_updated = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])->andWhere(['deleted' => 0])->all();
        foreach ($tranches_updated as $tranche) {
            $tranche->status = 6;
            $tranche->updated_by = 1;
            $tranche->save();
            $d_tranches = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->one();
            if (!empty($d_tranches) && $d_tranches != null) {
                $d_tranches->status = 3;
                $d_tranches->save();
            }
        }
        $loan = Loans::find()->where(['id' => $loan_id])->one();
        $loan->status = 'collected';
        $loan->disbursed_amount = $loan_tranche;
        $loan->updated_by = 1;
        if ($loan->save()) {
            $date = date("Y-m-t 00:00:00");
            DisbursementHelper::GenerateScheduleHousingExtraTwo($loan, true);
            $ledgerpre = SchedulesHousingPre::find()->where(['loan_id' => $loan->id])->all();
            foreach ($ledgerpre as $data) {
                $ledgerold = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['due_date' => $data->due_date])->one();
                if ($ledgerold) {
                    $ledgerold->schdl_amnt = $data->schdl_amnt;
                    $ledgerold->charges_schdl_amount = $data->charges_schdl_amount;
                    $ledgerold->charges_schdl_amnt_tax = $data->charges_schdl_amnt_tax;
                    $ledgerold->charges_due_amount = $data->charges_due_amount;
                    $ledgerold->charges_credit = $data->charges_credit;
                    $ledgerold->overdue_log = $data->overdue_log;
//                    $ledgerold->advance_log = $data->advance_log;
                    $ledgerold->credit_tax = $data->credit_tax;
                    $ledgerold->overdue = $data->overdue;
                    $ledgerold->advance = $data->advance;
                    $ledgerold->due_amnt = $data->due_amnt;
//                    $ledgerold->credit = $data->credit;
                    if ($ledgerold->save()) {
                        $sync_rec = Recoveries::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['due_date' => $data->due_date])->andWhere(['deleted' => 0])->all();
                        foreach ($sync_rec as $rec) {
                            $rec->schedule_id = $ledgerold->id;
                            $rec->save(false);
                        }

                    }
                }

            }

            $last_recovery = Recoveries::find()->where(['loan_id' => $loan_id])->andWhere(['deleted' => 0])->orderBy(['due_date' => SORT_DESC])->one();
            $preLedger = Schedules::find()->where(['loan_id' => $loan_id])->andWhere(['>','due_date' , $last_recovery->due_date])->all();
            $loanModel = Loans::find()->where(['id'=>$loan_id])->one();
            $ledgerCount = count($preLedger);
            $balance = $loanModel->balance;
            $chargesPercent = 5;
            $newFixRentInit = ((($balance*$chargesPercent)/100)/12);
            $newFixRentTotal = round($newFixRentInit*$ledgerCount);
            $newFixRent = round($newFixRentTotal/$ledgerCount);
            $lastScheduleRentDifference = $newFixRentTotal-($newFixRent*($ledgerCount-1));

            foreach ($preLedger as $ledger){
                $ledger->charges_schdl_amount = $newFixRent;
                $ledger->save(false);
            }

            $preLedgerLast = Schedules::find()->where(['loan_id' => $loan_id])->orderBy(['due_date' => SORT_DESC])->one();
            if ($preLedgerLast){
                $preLedgerLast->charges_schdl_amount = $lastScheduleRentDifference;
                $preLedgerLast->save(false);
            }
        }

    }

    public function actionLedgerUpdateExtra()
    {
//        php yii fixes/ledger-update-extra
        $sanction_nos = ['111000-D001-00001'];
        foreach ($sanction_nos as $loan_id) {
            $loan = Loans::findOne(['sanction_no' => $loan_id]);
            $pre_ledger = SchedulesHousingPre::find()->where(['loan_id' => $loan->id])->all();
            foreach ($pre_ledger as $data) {
                $ledger_count = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['due_date' => $data->due_date])->count();
                $ledger = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['due_date' => $data->due_date])->one();
                echo $ledger_count;
                echo 'Old data Id';
                print_r($ledger->id);
                echo 'Loan id';
                print_r($ledger->loan_id);
                echo 'due_date';
                print_r($ledger->due_date);

                echo 'New Data Id';
                print_r($data->id);
                echo 'Loan id';
                print_r($data->loan_id);
                echo 'due_date';
                print_r($data->due_date);
                die();
            }
            echo '(' . $loan_id . ')' . '<br>';
        }
    }

    public function actionLedgerGenerateBySanctionNo()
    {
        $sanction_nos = ['908-D003-00001'];
        foreach ($sanction_nos as $loan_id) {
            $loan = Loans::findOne(['sanction_no' => $loan_id]);
            FixesHelper::ledger_regenerate($loan);
            echo '(' . $loan_id . ')' . '<br>';
        }
    }


    public function actionLedgerGenerateById(array $loan_ids)
    {
        foreach ($loan_ids as $loan_id) {
            $loan = Loans::findOne(['id' => $loan_id]);
            FixesHelper::ledger_regenerate($loan);
        }
    }

    public function actionUpdateExpiryDate()
    {
        ini_set('memory_limit', '102423M');
        ini_set('max_execution_time', 400);
        $loans = Loans::find()->where(['in', 'status', ['collected']])->andWhere(['deleted' => 0])->andWhere(['>', 'date_disbursed', 0])->andWhere(['>', 'id', 3200000])->andWhere(['<=', 'id', 3400000])->all();
        foreach ($loans as $loan) {
            FixesHelper::update_loan_expiry($loan);
            echo '(' . $loan->id . ')' . '<br>';
        }

    }

    public function actionExpiryDate($id)
    {
        ini_set('memory_limit', '102423M');
        ini_set('max_execution_time', 400);
        $loans = Loans::find()->where(['in', 'status', ['loan completed']])->andWhere(['deleted' => 0])->andWhere(['>', 'date_disbursed', 0])->andWhere(['<=', 'loan_completed_date', 1561852800])->andWhere(['>', 'id', $id])->limit(50000)->all();
        foreach ($loans as $loan) {
            FixesHelper::update_loan_expiry($loan);
            echo '(' . $loan->id . ')' . '<br>';
        }

    }

    public function actionExpiryDateLoans()
    {
        ini_set('memory_limit', '102423M');
        ini_set('max_execution_time', 400);
        $sanction_list = ['1728-D002-05590',
            '1728-D002-05591',
            '1728-D002-05592',
        ];

        $loans = Loans::find()->where(['in', 'sanction_no', $sanction_list])->all();
        foreach ($loans as $loan) {
            FixesHelper::update_loan_expiry($loan);
            echo '(' . $loan->id . ')' . '<br>';
        }

    }

    public function actionExpiry()
    {
        ini_set('memory_limit', '102423M');
        ini_set('max_execution_time', 400);
        $loans = Loans::find()->where(['in', 'status', ['collected']])->andWhere(['deleted' => 0])->andWhere(['>', 'date_disbursed', 0])->andWhere(['>', 'id', 3400000])->all();
        foreach ($loans as $loan) {
            FixesHelper::update_loan_expiry($loan);
            echo '(' . $loan->id . ')' . '<br>';
        }

    }

    public function actionUpdateCnic()
    {
        $members = Members::find()->where(['branch_id' => 814, 'deleted' => 0])->all();
        foreach ($members as $member) {
            $cnic = "00000-" . mt_rand(1000000, 9999999) . "-" . rand(0, 9);
            while (true) {
                $mem = Members::find()->select(['id'])->where(['cnic' => $cnic])->one();
                if ($mem) {
                    $cnic = "00000-" . mt_rand(1000000, 9999999) . "-" . rand(0, 9);
                } else {
                    $member->cnic = $cnic;
                    break;
                }
            }
            $member->updated_by = 1;
            $member->save();
        }
    }

    public function actionFixesById(array $loan_ids)
    {
        foreach ($loan_ids as $loan_id) {
            $loan = Loans::findOne(['id' => $loan_id]);
            FixesHelper::fix_schedules_update($loan);
        }

    }

    public function actionLoanFixes()
    {
        $loan_ids = [];
        foreach ($loan_ids as $loan_id) {
            $loan = Loans::findOne(['id' => $loan_id]);
            FixesHelper::fix_schedules_update($loan);
        }

    }

    public function actionFixes()
    {
        $loan_ids = array('206-D003-00009',
            '3204-D003-00010',
            '2304-D003-00008',
            '3210-D003-00010',
            '3209-D003-00010',
            '1903-D003-00008',
            '115-D003-00008',
            '608-D003-00005',
            '34202-D003-00039',
            '34202-D003-00046',
            '32301-D003-00039',
            '32201-D003-00001',
            '34203-D003-00014',
            '35110-D003-00021',
            '6406-D003-00006',
            '2107-D003-00008',
            '2814-D003-00013',
            '702-D003-00014',
            '2405-D003-00011',
            '8106-D003-00024',
            '609-D003-00015',
            '3201-D003-00013',
            '2832-D003-00011',
            '11702-D003-00004',
            '2816-D003-00007',
            '3136-D003-00010',
            '3205-D003-00044',
            '605-D003-00009',
            '2913-D003-00019',
            '1713-D003-00016',
            '2404-D003-00025',
            '2401-D003-00029',
            '608-D003-00019',
            '615-D003-00022',
            '615-D003-00023',
            '3013-D003-00010',
            '2914-D003-00002',
            '208-D003-00027',
            '1726-D003-00005',
            '2904-D003-00053',
            '2930-D003-00012',
            '2828-D003-00031',
            '2828-D003-00032',
            '2824-D003-00031',
            '1811-D003-00014',
            '1106-D003-00020',
            '1114-D003-00017',
            '3209-D003-00031',
            '2218-D003-00015',
            '835-D003-00026',
            '1735-D003-00002',
            '4802-D003-00001',
            '2902-D003-00081',
            '3118-D001-00315',
            '2810-D001-00275',
            '2828-D001-00163',
            '615-D003-00024',
            '835-D003-00030',
            '3115-D003-00008',
            '1821-D003-00026',
            '34203-D003-00026',
            '32201-D003-00019',
            '909-D003-00036',
            '1721-D003-00042',
            '35104-D003-00043',
            '14301-D024-00011',
            '212-D003-00024',
            '4802-D003-00006',
            '17203-D003-00047',
            '50601-D003-00019',
            '819-D003-00054',
            '34203-D003-00033',
            '3117-D003-00053',
            '3136-D003-00017',
            '1109-D025-00024',
            '32301-D003-00088',
            '14301-D024-00029',
            '11301-D024-00007',
            '32201-D003-00023',
            '604-D003-00016',
            '8103-D003-00019',
            '34401-D003-00020',
            '310-D003-00050',
            '2215-D003-00004',
            '5001-D001-12899',
            '34201-D001-05893',
            '34202-D001-04026',
            '34202-D001-04027',
            '32301-D001-00089',
            '32301-D001-00093',
            '34202-D001-04061',
            '32301-D001-00101',
            '11404-D003-00009',
            '209-D003-00050',
            '32202-D001-00871',
            '33401-D001-06058',
            '1901-D003-00030',
            '34203-D001-02622',
            '5001-D001-12994',
            '13104-D003-00007',
            '32201-D001-01653',
            '32201-D001-01657',
            '32301-D001-00115',
            '32301-D001-00116',
            '32301-D001-00117',
            '32301-D001-00118',
            '34203-D001-02676',
            '4802-D001-00382',
            '835-D003-00038',
            '13201-D003-00018',
            '34203-D001-02688',
            '34401-D001-05074',
            '32301-D001-00124',
            '32301-D001-00125',
            '32301-D001-00126',
            '32301-D001-00131',
            '32301-D001-00133',
            '32301-D001-00136',
            '34202-D001-04141',
            '609-D003-00046',
            '33401-D001-06244',
            '33401-D001-06247',
            '32201-D001-01702',
            '32201-D001-01703',
            '32201-D001-01707',
            '2833-D003-00023',
            '2816-D003-00014',
            '32301-D001-00165',
            '34202-D001-04210',
            '5001-D001-13138',
            '34203-D001-02752',
            '4802-D001-00402',
            '608-D003-00034',
            '34203-D001-02766',
            '34203-D001-02767',
            '5001-D001-13160',
            '5001-D001-13165',
            '32301-D001-00186',
            '32301-D001-00192',
            '34302-D001-00562',
            '604-D003-00020',
            '3206-D003-00053',
            '32201-D001-01795',
            '34401-D001-05155',
            '34202-D001-04284',
            '34203-D003-00035',
            '34203-D001-02834',
            '34203-D001-02839',
            '4802-D001-00429',
            '2401-D003-00047',
            '32201-D003-00025',
            '32301-D001-00243',
            '32301-D001-00244',
            '34203-D001-02843',
            '34203-D001-02845',
            '34202-D001-04345',
            '32304-D001-00128',
            '32304-D001-00132',
            '2911-D003-00028',
            '32301-D001-00252',
            '615-D003-00036',
            '32201-D003-00026',
            '2932-D003-00018',
            '2404-D003-00038',
            '2833-D003-00025',
            '803-D003-00056',
            '11404-D003-00012',
            '2904-D003-00080',
            '1102-D003-00029',
            '2703-D027-00058',
            '1106-D003-00022',
            '13205-D003-00014',
            '32304-D001-00460',
            '2910-D003-00011',
            '2301-D003-00050',
            '32202-D001-01471',
            '32201-D001-02214',
            '33401-D001-06892',
            '34203-D001-03158',
            '34203-D001-03160',
            '34203-D001-03164',
            '34202-D001-04583',
            '34201-D001-06668',
            '33301-D001-07924',
            '2302-D003-00058',
            '34203-D001-03217',
            '50504-D003-00023',
            '2928-D003-00036',
            '2810-D003-00056',
            '2911-D003-00031',
            '32301-D001-00298',
            '4802-D001-00635',
            '2206-D002-10834',
            '15301-D027-00055',
            '11501-D003-00011',
            '2302-D002-13162',
            '2215-D002-07687',
            '2206-D002-10861',
            '2210-D002-07661',
            '2210-D002-07686',
            '2210-D002-07720',
            '2210-D002-07729',
            '2215-D002-07727',
            '815-D002-10763',
            '101-D002-12738',
            '101-D002-12740',
            '1903-D002-08935',
            '1903-D002-08965',
            '2301-D002-13297',
            '2502-D002-10765',
            '2502-D002-10768',
            '2301-D002-13341',
            '15301-D027-00071',
            '15301-D027-00073',
            '2302-D002-13193',
            '2708-D002-04216',
            '3016-D002-04881',
            '2810-D002-08544',
            '2401-D002-12090',
            '2401-D002-12098',
            '2104-D002-11307',
            '2104-D002-11326',
            '2104-D002-11328',
            '1901-D002-11982',
            '1901-D002-11985',
            '1901-D002-12001',
            '1901-D002-12032',
            '3209-D002-10687',
            '2705-D002-08924',
            '2702-D002-04193',
            '2702-D002-04194',
            '2702-D002-04195',
            '2702-D002-04204',
            '2705-D002-08943',
            '51503-D015-00470',
            '51503-D015-00472',
            '2710-D002-04443',
            '108-D002-09913',
            '3306-D002-08220',
            '2304-D002-11176',
            '2833-D002-04654',
            '2833-D002-04655',
            '2306-D002-08141',
            '1710-D002-14206',
            '3102-D002-12371',
            '3102-D002-12382',
            '2926-D002-05437',
            '2112-D002-07910',
            '2112-D002-07945',
            '710-D002-06713',
            '710-D002-06721',
            '710-D002-06726',
            '710-D002-06731',
            '710-D002-06734',
            '1106-D002-10102',
            '1106-D002-10103',
            '3404-D002-06657',
            '3504-D002-07562',
            '3404-D002-06680',
            '3402-D002-09662',
            '3519-D002-04417',
            '2812-D002-08699',
            '2812-D002-08742',
            '3119-D002-08447',
            '3207-D002-10618',
            '3207-D002-10619',
            '3207-D002-10632',
            '2931-D002-05155',
            '3228-D002-04325',
            '3228-D002-04357',
            '827-D002-06829',
            '827-D002-06830',
            '1703-D002-19381',
            '2928-D002-05514',
            '2111-D002-08219',
            '2111-D002-08222',
            '2932-D002-04388',
            '2932-D002-04420',
            '2929-D002-05238',
            '2816-D002-06728',
            '3516-D002-05688',
            '3516-D002-05708',
            '3516-D002-05738',
            '2816-D002-06740',
            '2816-D002-06741',
            '3304-D002-05554',
            '2809-D002-07950',
            '2809-D002-07977',
            '3210-D002-09307',
            '3210-D002-09310',
            '3212-D002-07046',
            '2802-D002-10242',
            '2911-D002-09908',
            '2911-D002-09953',
            '3136-D002-04683',
            '2914-D002-09349',
            '34202-D001-04730',
            '2924-D002-05546',
            '2924-D002-05582',
            '2902-D002-02224',
            '15301-D027-00088',
            '15301-D027-00102',
            '2401-D002-12120',
            '2401-D002-12122',
            '2401-D002-12155',
            '2102-D002-08345',
            '2102-D002-08360',
            '2203-D002-10648',
            '2203-D002-10654',
            '2210-D002-07759',
            '2210-D002-07789',
            '2208-D002-07835',
            '2208-D002-07836',
            '2208-D002-07850',
            '2209-D002-07568',
            '3102-D002-12456',
            '101-D002-12818',
            '3402-D002-09709',
            '3402-D002-09715',
            '2301-D002-13391',
            '2301-D002-13416',
            '2824-D002-05054',
            '2302-D002-13261',
            '1703-D002-19398',
            '2215-D002-07730',
            '2215-D002-07737',
            '2215-D002-07747',
            '2215-D002-07774',
            '710-D002-06735',
            '710-D002-06737',
            '2833-D002-04709',
            '2708-D002-04278',
            '2708-D002-04279',
            '2402-D002-12139',
            '2402-D002-12140',
            '511-D002-03466',
            '511-D002-03467',
            '2928-D002-05572',
            '2810-D002-08597',
            '2810-D002-08599',
            '2810-D002-08600',
            '2905-D002-09854',
            '2905-D002-09866',
            '3516-D002-05751',
            '1903-D002-09024',
            '2702-D002-04227',
            '2702-D002-04228',
            '2106-D002-10474',
            '2904-D002-11109',
            '608-D003-00050',
            '2108-D002-08634',
            '210-D002-07730',
            '210-D002-07732',
            '210-D002-07749',
            '210-D002-07754',
            '3203-D002-13407',
            '3228-D002-04376',
            '3228-D002-04379',
            '3228-D002-04381',
            '2501-D002-11692',
            '3404-D002-06708',
            '3404-D002-06709',
            '3404-D002-06710',
            '1901-D002-12064',
            '1901-D002-12065',
            '1103-D002-12064',
            '2401-D002-12169',
            '2202-D002-11634',
            '2202-D002-11639',
            '2404-D002-11971',
            '2404-D002-11972',
            '710-D002-06764',
            '710-D002-06766',
            '502-D002-08836',
            '707-D002-12251',
            '707-D002-12253',
            '2833-D002-04772',
            '2833-D002-04773',
            '2810-D002-08665',
            '2810-D002-08671',
            '2810-D002-08672',
            '2810-D002-08676',
            '2810-D002-08677',
            '2810-D002-08678',
            '2902-D002-02243',
            '2809-D002-08044',
            '2809-D002-08050',
            '2809-D002-08072',
            '51503-D015-00510',
            '3016-D002-04918',
            '3405-D002-04084',
            '2810-D002-08688',
            '2810-D002-08689',
            '2833-D002-04785',
            '2833-D002-04787',
            '2833-D002-04788',
            '2833-D002-04796',
            '2833-D002-04797',
            '2833-D002-04798',
            '3011-D002-08426',
            '3011-D002-08427',
            '2302-D003-00063',
            '2824-D002-05130',
            '2824-D002-05131',
            '2824-D002-05132',
            '3402-D002-09735',
            '32304-D027-00051',
            '2928-D002-05588',
            '2928-D002-05591',
            '2928-D002-05611',
            '2928-D002-05612',
            '3214-D002-07235',
            '2932-D002-04478',
            '3102-D002-12495',
            '3102-D002-12496',
            '2601-D002-11831',
            '2601-D002-11832',
            '2306-D002-08148',
            '15301-D027-00129',
            '2306-D002-08160',
            '1807-D002-13423',
            '3210-D002-09365',
            '3206-D002-11141',
            '2816-D002-06768',
            '2816-D002-06795',
            '2816-D002-06840',
            '2902-D002-02259',
            '2902-D002-02260',
            '2902-D002-02281',
            '2902-D002-02282',
            '2112-D002-07990',
            '2112-D002-08009',
            '3516-D002-05787',
            '3516-D002-05788',
            '3516-D002-05789',
            '2911-D002-10008',
            '2911-D002-10021',
            '2402-D002-12160',
            '34203-D001-03397',
            '602-D002-11518',
            '602-D002-11519',
            '602-D002-11520',
            '2302-D002-13287',
            '2302-D002-13293',
            '2810-D002-08730',
            '2810-D002-08752',
            '2810-D002-08753',
            '2108-D002-08672',
            '2401-D002-12201',
            '2401-D002-12209',
            '2833-D002-04839',
            '2833-D002-04841',
            '2833-D002-04848',
            '710-D002-06784',
            '2215-D002-07805',
            '2215-D002-07811',
            '2215-D002-07843',
            '2215-D002-07853',
            '2215-D001-00031',
            '2210-D002-07875',
            '710-D002-06795',
            '2824-D002-05134',
            '2824-D002-05135',
            '2708-D002-04330',
            '710-D002-06800',
            '2708-D002-04342',
            '2402-D002-12196',
            '2402-D002-12198',
            '2404-D002-11997',
            '1711-D002-13219',
            '1711-D002-13237',
            '1711-D002-13238',
            '1711-D002-13247',
            '1711-D002-13248',
            '2402-D002-12225',
            '2708-D002-04362',
            '2208-D002-07895',
            '2208-D002-07896',
            '2208-D002-07902',
            '2208-D002-07909',
            '2224-D002-09084',
            '2118-D002-05767',
            '3213-D002-07096',
            '1106-D002-10243',
            '2812-D002-08830',
            '2812-D002-08831',
            '2301-D002-13453',
            '2812-D002-08877',
            '1903-D002-09106',
            '1903-D002-09108',
            '2812-D002-08884',
            '1903-D002-09135',
            '3136-D002-04786',
            '2302-D002-13309',
            '3136-D002-04790',
            '2302-D002-13337',
            '3136-D002-04818',
            '2107-D002-08956',
            '2102-D002-08449',
            '2711-D002-03672',
            '1901-D002-12141',
            '1901-D002-12148',
            '2816-D002-06846',
            '2816-D002-06893',
            '2816-D002-06920',
            '3301-D002-09821',
            '3301-D002-09823',
            '1402-D002-10995',
            '210-D002-07797',
            '3016-D002-04976',
            '3016-D002-04982',
            '3016-D002-04983',
            '2501-D002-11797',
            '2104-D002-11448',
            '2112-D002-08019',
            '2112-D002-08042',
            '2708-D002-04370',
            '2112-D002-08072',
            '3402-D002-09778',
            '3404-D002-06746',
            '2401-D002-12257',
            '3214-D002-07302',
            '3405-D002-04137',
            '3228-D002-04444',
            '3228-D002-04446',
            '2932-D002-04525',
            '51503-D015-00535',
            '51503-D015-00543',
            '2928-D002-05635',
            '2928-D002-05636',
            '2928-D002-05637',
            '51503-D015-00556',
            '51503-D015-00557',
            '51503-D015-00558',
            '51503-D015-00562',
            '51503-D015-00566',
            '2928-D002-05648',
            '1503-D001-00147',
            '2210-D002-07900',
            '2210-D002-07902',
            '2708-D002-04371',
            '2708-D002-04372',
            '2911-D002-10113',
            '2302-D003-00068',
            '1103-D002-12124',
            '1103-D002-12128',
            '1103-D002-12129',
            '1103-D002-12175',
            '3516-D002-05812',
            '3516-D002-05817',
            '3516-D002-05844',
            '3516-D002-05877',
            '2914-D002-09433',
            '2924-D002-05691',
            '2914-D002-09450',
            '2306-D002-08227',
            '2306-D002-08228',
            '1818-D001-00102',
            '2304-D002-11295',
            '2902-D002-02297',
            '17205-D003-00010',
            '32201-D001-02485',
            '2902-D002-02340',
            '2902-D002-02341',
            '2302-D002-13341',
            '2305-D002-12009',
            '2305-D002-12011',
            '2806-D002-09341',
            '2806-D002-09342',
            '2806-D002-09346',
            '2806-D002-09347',
            '2104-D002-11472',
            '2402-D002-12235',
            '2810-D002-08785',
            '1820-D002-09120',
            '2107-D002-09021',
            '3102-D002-12622',
            '3102-D002-12658',
            '2404-D002-12088',
            '2404-D002-12089',
            '2404-D002-12090',
            '2833-D002-04911',
            '2833-D002-04915',
            '2833-D002-04916',
            '2833-D002-04917',
            '3401-D002-09926',
            '2833-D002-04967',
            '2112-D002-08111',
            '2401-D002-12258',
            '3207-D002-10783',
            '3207-D002-10784',
            '3207-D002-10785',
            '2301-D002-13520',
            '3102-D002-12676',
            '511-D002-03574',
            '3228-D002-04517',
            '3228-D002-04518',
            '1903-D002-09200',
            '2710-D002-04589',
            '2302-D002-13363',
            '2302-D002-13367',
            '2215-D002-07920',
            '602-D002-11555',
            '3119-D002-08685',
            '3119-D002-08695',
            '112-D002-08696',
            '2812-D002-08936',
            '1733-D002-11789',
            '3301-D002-09883',
            '2210-D002-07923',
            '2210-D002-07948',
            '2303-D002-04470',
            '2303-D002-04471',
            '2303-D002-04474',
            '3206-D002-11216',
            '2111-D002-08409',
            '1402-D002-11026',
            '2708-D002-04375',
            '2914-D002-09467',
            '2816-D002-06930',
            '3504-D002-07700',
            '826-D001-00198',
            '2709-D002-04448',
            '2709-D002-04449',
            '2709-D001-00141',
            '2702-D002-04363',
            '3404-D002-06794',
            '1106-D002-10314',
            '1106-D002-10316',
            '2306-D002-08239',
            '2306-D002-08275',
            '3405-D002-04171',
            '3405-D002-04177',
            '2816-D002-06937',
            '2816-D002-06946',
            '2816-D002-06972',
            '2816-D002-06974',
            '2816-D002-06986',
            '3202-D002-06424',
            '3202-D002-06425',
            '3202-D002-06426',
            '2605-D002-08604',
            '2605-D002-08606',
            '2932-D002-04543',
            '2932-D002-04546',
            '2932-D002-04549',
            '2932-D002-04577',
            '2932-D002-04578',
            '2932-D002-04582',
            '2932-D002-04584',
            '2932-D002-04585',
            '3516-D002-05909',
            '3210-D002-09425',
            '3210-D002-09431',
            '3210-D002-09436',
            '3210-D002-09446',
            '2208-D002-07952',
            '2208-D002-07966',
            '2208-D002-07967',
            '2208-D002-07968',
            '1811-D001-00102',
            '3306-D002-08346',
            '2928-D002-05676',
            '51503-D015-00579',
            '51503-D015-00580',
            '51503-D015-00581',
            '1601-D003-00020',
            '2709-D002-04462',
            '2304-D002-11336',
            '2304-D002-11337',
            '2304-D002-11338',
            '212-D001-00260',
            '2708-D001-00121',
            '2902-D002-02376',
            '2902-D002-02378',
            '2932-D001-00073',
            '2806-D003-00056',
            '34203-D001-03481',
            '1903-D001-00213',
            '3203-D002-13540',
            '3101-D002-10873',
            '3207-D002-10842',
            '3207-D002-10844',
            '2833-D002-05001',
            '2833-D002-05011',
            '1901-D002-12245',
            '1901-D002-12276',
            '1901-D002-12282',
            '1901-D002-12285',
            '2208-D001-00069',
            '2208-D002-08022',
            '3209-D002-10874',
            '2209-D002-07788',
            '2809-D002-08264',
            '2215-D002-07967',
            '2217-D002-07620',
            '1805-D002-14766',
            '1007-D002-05829',
            '2108-D002-08800',
            '710-D002-06864',
            '710-D002-06869',
            '2104-D002-11497',
            '2210-D002-08034',
            '2210-D002-08035',
            '3402-D002-09866',
            '3402-D002-09868',
            '2822-D002-05611',
            '2113-D002-08269',
            '2301-D002-13563',
            '2301-D002-13584',
            '3404-D002-06858',
            '3102-D002-12693',
            '3102-D002-12708',
            '3102-D002-12709',
            '2928-D002-05707',
            '2301-D002-13603',
            '602-D002-11562',
            '602-D002-11563',
            '2931-D002-05345',
            '2202-D002-11823',
            '406-D002-09276',
            '608-D001-00234',
            '3228-D002-04525',
            '3228-D002-04538',
            '3228-D002-04551',
            '3228-D002-04553',
            '3228-D002-04560',
            '3228-D002-04561',
            '812-D002-08521',
            '2932-D002-04589',
            '2932-D002-04591',
            '2932-D002-04603',
            '2932-D002-04605',
            '2932-D002-04606',
            '2932-D002-04607',
            '2402-D002-12302',
            '2402-D002-12341',
            '2816-D002-07030',
            '2816-D002-07032',
            '3304-D002-05789',
            '3213-D002-07202',
            '2816-D002-07060',
            '2816-D002-07061',
            '210-D002-07887',
            '1402-D002-11062',
            '1711-D002-13434',
            '2810-D002-08866',
            '828-D001-00212',
            '2810-D002-08888',
            '1003-D001-00523',
            '2824-D002-05321',
            '3016-D002-05105',
            '3205-D002-12749',
            '3205-D002-12756',
            '2404-D002-12117',
            '2404-D002-12144',
            '2404-D002-12148',
            '1733-D002-11829',
            '1733-D002-11858',
            '2708-D002-04453',
            '2708-D002-04454',
            '2708-D002-04461',
            '2708-D002-04462',
            '2931-D002-05374',
            '2812-D002-09003',
            '2708-D002-04475',
            '2812-D002-09035',
            '2812-D002-09036',
            '2812-D002-09037',
            '2812-D002-09050',
            '2711-D002-03775',
            '2401-D002-12342',
            '2401-D002-12375',
            '2702-D002-04380',
            '3136-D002-04912',
            '2929-D002-05450',
            '2932-D002-04629',
            '1807-D002-13664',
            '1807-D002-13665',
            '1807-D002-13666',
            '2304-D002-11358',
            '2304-D002-11365',
            '3210-D002-09482',
            '3210-D002-09496',
            '3206-D002-11249',
            '1727-D001-09033',
            '2902-D002-02411',
            '2902-D002-02421',
            '3401-D002-09960',
            '2306-D002-08281',
            '3306-D002-08380',
            '3306-D002-08383',
            '3306-D002-08393',
            '3306-D002-08395',
            '3228-D002-04581',
            '3228-D002-04593',
            '3304-D002-05800',
            '3304-D002-05801',
            '3011-D002-08571',
            '3516-D002-05995',
            '502-D001-00368',
            '2401-D002-12389',
            '2401-D002-12391',
            '1702-D002-13970',
            '51503-D015-00596',
            '51503-D015-00597',
            '51503-D015-00598',
            '51503-D015-00608',
            '51503-D015-00614',
            '51503-D015-00620',
            '1103-D002-12265',
            '604-D003-00032',
            '2926-D003-00045',
            '811-D003-00072',
            '2215-D002-08003',
            '34202-D001-04919',
            '34203-D001-03546',
            '12402-D001-00381',
            '2217-D002-07709',
            '614-D001-00122',
            '2104-D002-11586',
            '2104-D002-11604',
            '2816-D002-07103',
            '2816-D002-07148',
            '2810-D001-00330',
            '2301-D002-13624',
            '2301-D002-13665',
            '2301-D002-13666',
            '2301-D002-13667',
            '3404-D002-06891',
            '3404-D002-06892',
            '3404-D001-00241',
            '2926-D002-05710',
            '2929-D002-05481',
            '2931-D002-05415',
            '2208-D002-08031',
            '2208-D002-08034',
            '2208-D002-08035',
            '2208-D002-08042',
            '2208-D002-08060',
            '2208-D002-08062',
            '2208-D002-08064',
            '602-D002-11593',
            '2833-D002-05067',
            '2833-D002-05068',
            '2833-D002-05069',
            '2833-D002-05082',
            '2702-D002-04418',
            '2210-D002-08110',
            '2702-D002-04420',
            '2702-D002-04422',
            '2702-D002-04427',
            '2404-D002-12187',
            '2404-D002-12198',
            '2404-D002-12199',
            '3102-D002-12729',
            '3102-D002-12737',
            '3102-D002-12738',
            '1733-D002-11887',
            '2927-D002-05429',
            '202-D002-13562',
            '3214-D002-07410',
            '3214-D002-07412',
            '2809-D002-08343',
            '2809-D002-08344',
            '2809-D002-08395',
            '3301-D002-09965',
            '3301-D002-09967',
            '3301-D002-09968',
            '3301-D002-09972',
            '1711-D002-13481',
            '1711-D002-13491',
            '2402-D002-12373',
            '2107-D002-09158',
            '206-D001-00224',
            '206-D001-00226',
            '3516-D002-06019',
            '3516-D002-06021',
            '3516-D002-06037',
            '3516-D002-06082',
            '1903-D002-09285',
            '1903-D002-09313',
            '1903-D002-09344',
            '2914-D002-09594',
            '3016-D002-05167',
            '3016-D002-05189',
            '3016-D002-05190',
            '3016-D002-05191',
            '2111-D002-08503',
            '1103-D002-12309',
            '1103-D002-12349',
            '2501-D002-11924',
            '2501-D002-11926',
            '3011-D002-08595',
            '3011-D002-08596',
            '3205-D002-12809',
            '2911-D001-00076',
            '2708-D002-04495',
            '2708-D002-04496',
            '2708-D002-04499',
            '2708-D002-04517',
            '2708-D002-04525',
            '2711-D002-03811',
            '2506-D002-04510',
            '2711-D002-03841',
            '15301-D027-00145',
            '2303-D002-04582',
            '2215-D001-00037',
            '316-D001-00107',
            '2909-D002-09685',
            '1106-D002-10387',
            '1106-D002-10389',
            '2812-D002-09081',
            '1204-D001-00277',
            '1702-D002-14032',
            '710-D001-00148',
            '2401-D003-00067',
            '5001-D001-14269',
            '5001-D001-14270',
            '5001-D001-14273',
            '5001-D001-14275',
            '5001-D001-14289',
            '5001-D001-14317',
            '51503-D015-00630',
            '51503-D015-00632',
            '51503-D015-00633',
            '51503-D015-00638',
            '51503-D015-00640',
            '35104-D001-08595',
            '35104-D001-08622',
            '3128-D002-05610',
            '32304-D001-00930',
            '32304-D001-00931',
            '32304-D001-00932',
            '33301-D001-08489',
            '33301-D001-08491',
            '33301-D001-08492',
            '32201-D001-02650',
            '32201-D001-02656',
            '2401-D001-02302',
            '33401-D001-07390',
            '34203-D001-03547',
            '34203-D001-03584',
            '702-D001-01655',
            '33701-D003-00078',
            '12402-D001-00424',
            '1804-D002-14548',
            '33801-D001-06651',
            '401-D002-11872',
            '2932-D002-04702',
            '2932-D002-04705',
            '2932-D002-04724',
            '1903-D002-09405',
            '2833-D002-05196',
            '2810-D002-09073',
            '2810-D002-09075',
            '1007-D002-05912',
            '3403-D002-07691',
            '3403-D002-07695',
            '3403-D002-07702',
            '2208-D002-08081',
            '2208-D002-08085',
            '2208-D002-08123',
            '2208-D002-08138',
            '2208-D002-08144',
            '2208-D002-08146',
            '2202-D002-11973',
            '2928-D002-05803',
            '710-D002-06950',
            '710-D002-06951',
            '710-D002-06953',
            '710-D002-06955',
            '2809-D002-08398',
            '3203-D002-13637',
            '2215-D002-08048',
            '2215-D002-08085',
            '2215-D002-08087',
            '1103-D002-12388',
            '1103-D002-12390',
            '1103-D002-12399',
            '1711-D002-13543',
            '3209-D002-10942',
            '3209-D002-10965',
            '3209-D002-10981',
            '2811-D002-08888',
            '2811-D002-08889',
            '2811-D002-08890',
            '2911-D002-10309',
            '2108-D002-08912',
            '2302-D002-13517',
            '3408-D002-06108',
            '3408-D002-06109',
            '2302-D002-13550',
            '2302-D002-13575',
            '2209-D002-07890',
            '2404-D002-12252',
            '2404-D002-12253',
            '3206-D002-11317',
            '3016-D002-05204',
            '3016-D002-05214',
            '3102-D002-12772',
            '3102-D002-12773',
            '3102-D002-12802',
            '3102-D002-12805',
            '3213-D002-07280',
            '2304-D002-11435',
            '104-D002-12004',
            '2304-D002-11437',
            '2914-D002-09627',
            '2914-D002-09640',
            '2301-D002-13690',
            '2301-D002-13693',
            '2812-D002-09143',
            '2301-D002-13735',
            '2105-D002-12766',
            '35104-D001-08656',
            '602-D002-11636',
            '602-D002-11658',
            '107-D001-00307',
            '2711-D002-03890',
            '2402-D002-12408',
            '2401-D002-12456',
            '2402-D002-12419',
            '2401-D002-12474',
            '2708-D002-04540',
            '2401-D002-12492',
            '2932-D002-04728',
            '2602-D002-11715',
            '3011-D002-08616',
            '3011-D002-08618',
            '1807-D002-13905',
            '6403-D001-00325',
            '2902-D002-02509',
            '811-D001-00578',
            '1901-D002-12388',
            '3516-D001-00110',
            '3516-D002-06136',
            '47001-D001-00801',
            '47001-D001-00802',
            '47001-D001-00803',
            '3103-D002-12925',
            '3103-D002-12929',
            '3210-D002-09574',
            '3136-D002-05067',
            '2911-D002-10343',
            '3306-D002-08499',
            '51503-D015-00660',
            '51503-D015-00671',
            '51503-D015-00678',
            '15301-D027-00163',
            '32202-D001-01896',
            '32201-D001-02697',
            '828-D001-00218',
            '15301-D001-00262',
            '5001-D001-14348',
            '5001-D001-14353',
            '34203-D001-03597',
            '35107-D001-06124',
            '34203-D001-03617',
            '33702-D001-03973',
            '33702-D001-03975',
            '34302-D001-01414',
            '34302-D001-01448',
            '3404-D002-06972',
            '2809-D002-08486',
            '2809-D002-08487',
            '2809-D002-08488',
            '2809-D002-08516',
            '2809-D002-08517',
            '2215-D002-08090',
            '2215-D002-08131',
            '3136-D002-05109',
            '2824-D002-05507',
            '2824-D002-05540',
            '3405-D002-04325',
            '2210-D001-00084',
            '2932-D002-04751',
            '2932-D002-04767',
            '2210-D002-08199',
            '3408-D001-00038',
            '1007-D002-05985',
            '1007-D002-05986',
            '1007-D002-06002',
            '1007-D002-06003',
            '1007-D002-06004',
            '1007-D002-06005',
            '2306-D002-08426',
            '2104-D002-11695',
            '2104-D002-11696',
            '602-D002-11700',
            '2107-D002-09288',
            '2302-D002-13591',
            '2302-D002-13593',
            '2302-D002-13599',
            '1903-D002-09452',
            '2404-D002-12305',
            '2302-D002-13630',
            '2302-D002-13632',
            '2404-D002-12342',
            '710-D002-06963',
            '710-D002-06979',
            '710-D002-06981',
            '710-D001-00152',
            '1402-D002-11209',
            '2806-D002-09541',
            '2810-D002-09106',
            '2810-D002-09108',
            '2810-D002-09160',
            '2911-D002-10403',
            '2911-D002-10404',
            '2301-D002-13777',
            '217-D001-00123',
            '2208-D002-08157',
            '2904-D002-11366',
            '2602-D002-11744',
            '1901-D002-12485',
            '2401-D002-12515',
            '2401-D002-12525',
            '2902-D002-02539',
            '2902-D002-02560',
            '2708-D002-04599',
            '2402-D002-12480',
            '2402-D002-12489',
            '3207-D002-10948',
            '1731-D002-11067',
            '2118-D002-06091',
            '2711-D002-03904',
            '2816-D002-07216',
            '3306-D002-08528',
            '3306-D002-08548',
            '317-D001-00250',
            '3218-D002-02731',
            '3218-D002-02765',
            '206-D001-00230',
            '3516-D002-06195',
            '3516-D002-06196',
            '707-D002-12445',
            '32202-D001-01920',
            '32202-D001-01921',
            '51503-D015-00689',
            '51503-D015-00697',
            '51503-D015-00698',
            '51503-D015-00699',
            '51503-D015-00701',
            '51503-D015-00702',
            '51503-D015-00704',
            '51503-D015-00709',
            '51503-D015-00714',
            '32201-D001-02707',
            '32201-D001-02713',
            '1702-D002-14123',
            '1733-D003-00129',
            '608-D001-00241',
            '47001-D001-00835',
            '47001-D001-00836',
            '47001-D001-00837',
            '47001-D001-00874',
            '47001-D001-00875',
            '47001-D001-00876',
            '2904-D001-00442',
            '32304-D001-01048',
            '32304-D001-01050',
            '32304-D001-01051',
            '1729-D003-00049',
            '33301-D001-08621',
            '33301-D001-08642',
            '33301-D001-08643',
            '33401-D001-07537',
            '34302-D001-01456',
            '34302-D001-01497',
            '34302-D001-01498',
            '34203-D001-03630',
            '5001-D001-14382',
            '5001-D001-14384',
            '5001-D001-14411',
            '34203-D001-03648',
            '33702-D001-04040',
            '34202-D001-05015',
            '811-D001-00583',
            '3102-D003-00082',
            '2215-D002-08143',
            '35108-D001-06348',
            '34401-D001-05957',
            '12402-D001-00468',
            '34202-D001-05021',
            '33801-D001-06742',
            '2215-D002-08171',
            '3214-D002-07571',
            '3214-D002-07582',
            '3403-D002-07789',
            '2106-D002-10812',
            '2108-D002-09014',
            '821-D002-08098',
            '4802-D003-00010',
            '1902-D001-00096',
            '405-D001-00031',
            '2816-D002-07242',
            '2816-D002-07281',
            '3516-D002-06207',
            '3516-D002-06208',
            '3516-D002-06209',
            '3516-D002-06219',
            '2208-D002-08191',
            '2208-D002-08194',
            '2302-D002-13697',
            '2204-D002-11136',
            '2301-D002-13807',
            '2301-D002-13816',
            '210-D001-00155',
            '2301-D002-13827',
            '2208-D002-08225',
            '710-D002-07009',
            '2111-D002-08630',
            '2111-D002-08633',
            '212-D001-00275',
            '1103-D002-12531',
            '2931-D002-05564',
            '2931-D002-05575',
            '2928-D002-05921',
            '2928-D002-05928',
            '2928-D002-05937',
            '710-D002-07029',
            '2914-D002-09746',
            '2914-D002-09777',
            '2924-D002-05947',
            '2924-D002-05973',
            '3016-D002-05348',
            '2927-D002-05604',
            '2926-D002-05857',
            '1818-D001-00105',
            '3209-D002-11041',
            '3011-D002-08737',
            '3011-D002-08751',
            '3405-D002-04369',
            '3402-D002-10041',
            '2710-D002-04774',
            '2902-D002-02604',
            '2910-D002-04459',
            '3402-D001-00081',
            '2929-D002-05595',
            '2929-D002-05605',
            '2929-D002-05611',
            '2929-D002-05613',
            '2506-D001-00221',
            '3228-D002-04765',
            '3228-D002-04785',
            '2304-D002-11556',
            '2401-D002-12597',
            '2401-D002-12611',
            '2902-D002-02630',
            '2806-D002-09599',
            '2806-D002-09626',
            '2501-D002-12094',
            '2501-D002-12096',
            '1402-D002-11229',
            '2811-D002-09034',
            '2811-D002-09035',
            '3205-D002-12909',
            '6403-D002-09475',
            '6403-D001-00333',
            '2705-D002-09319',
            '1122-D001-00101',
            '2404-D002-12372',
            '2812-D002-09243',
            '2812-D002-09276',
            '2812-D002-09285',
            '2812-D002-09286',
            '2812-D002-09300',
            '2812-D002-09301',
            '3206-D002-11413',
            '3206-D002-11424',
            '3206-D002-11426',
            '1203-D027-00064',
            '2601-D002-12159',
            '15301-D027-00172',
            '46501-D023-01076',
            '17209-D023-01727',
            '17209-D023-01728',
            '17209-D023-01729',
            '17205-D023-02153',
            '17205-D023-02170',
            '47001-D001-00877',
            '47001-D001-00878',
            '47001-D001-00879',
            '47001-D001-00895',
            '47001-D001-00896',
            '47001-D001-00897',
            '47001-D001-00898',
            '51503-D015-00723',
            '51503-D015-00724',
            '51503-D015-00725',
            '51503-D015-00741',
            '51503-D015-00744',
            '51503-D015-00752',
            '47001-D001-00918',
            '47001-D001-00919',
            '47001-D001-00920',
            '47001-D001-00921',
            '2603-D027-00272',
            '5001-D001-14462',
            '5001-D001-14463',
            '5001-D001-14465',
            '17207-D023-02303',
            '1003-D003-00100',
            '35111-D001-03303',
            '32202-D001-01989',
            '5001-D001-14481',
            '33401-D001-07555',
            '32304-D001-01090',
            '32201-D001-02762',
            '34203-D001-03660',
            '34203-D001-03661',
            '34203-D001-03662',
            '34203-D001-03677',
            '35105-D001-09121',
            '2810-D001-00343',
            '604-D001-00306',
            '34302-D001-01555',
            '33702-D001-04057',
            '34302-D001-01579',
            '35204-D023-01687',
            '35204-D023-01716',
            '2215-D002-08205',
            '505-D001-00126',
            '2810-D002-09294',
            '2302-D002-13714',
            '2809-D002-08606',
            '2302-D002-13743',
            '2932-D002-04869',
            '2911-D002-10463',
            '2911-D002-10464',
            '105-D002-10830',
            '105-D002-10831',
            '1711-D002-13728',
            '1711-D002-13763',
            '2914-D002-09827',
            '2202-D002-12127',
            '602-D002-11741',
            '2112-D002-08441',
            '1903-D002-09534',
            '1903-D002-09539',
            '2911-D002-10490',
            '1903-D002-09573',
            '2301-D002-13855',
            '2708-D002-04667',
            '710-D002-07048',
            '2306-D002-08521',
            '2306-D002-08522',
            '1103-D002-12580',
            '2931-D001-00132',
            '2303-D002-04719',
            '3011-D002-08779',
            '2702-D002-04566',
            '3214-D002-07615',
            '3214-D002-07616',
            '3404-D002-07059',
            '2208-D002-08245',
            '2929-D002-05648',
            '2929-D002-05650',
            '2304-D002-11587',
            '1106-D001-00210',
            '3228-D002-04802',
            '2401-D002-12628',
            '2401-D002-12653',
            '2401-D002-12655',
            '3516-D002-06265',
            '3516-D002-06267',
            '3516-D002-06273',
            '3516-D002-06284',
            '803-D001-03319',
            '803-D001-03320',
            '3218-D002-02825',
            '3403-D002-07827',
            '3403-D002-07829',
            '2833-D002-05316',
            '2833-D002-05319',
            '2833-D002-05321',
            '1501-D001-00197',
            '2810-D002-09332',
            '3405-D002-04390',
            '2905-D002-10327',
            '2905-D002-10329',
            '2905-D002-10334',
            '1716-D002-12700',
            '3210-D002-09683',
            '801-D001-03124',
            '2404-D002-12415',
            '308-D001-00086',
            '3212-D002-07464',
            '3306-D002-08622',
            '3306-D002-08623',
            '3306-D002-08624',
            '2711-D002-04018',
            '2107-D002-09405',
            '213-D001-01758',
            '3102-D002-12916',
            '3102-D002-12917',
            '3102-D002-12926',
            '3102-D002-12937',
            '2824-D002-05641',
            '2824-D002-05643',
            '3209-D002-11065',
            '316-D001-00119',
            '2812-D002-09325',
            '2812-D002-09331',
            '3504-D002-07983',
            '1901-D002-12547',
            '1901-D002-12557',
            '1901-D002-12565',
            '1901-D002-12571',
            '1901-D002-12581',
            '2811-D002-09083',
            '51503-D015-00754',
            '51503-D015-00758',
            '51503-D015-00761',
            '51503-D015-00775',
            '1714-D002-12643',
            '34203-D001-03688',
            '15301-D001-00322',
            '15301-D001-00324',
            '17207-D023-02433',
            '205-D003-00095',
            '17205-D023-02234',
            '32201-D001-02793',
            '47001-D001-00939',
            '47001-D001-00940',
            '47001-D001-00941',
            '11501-D023-02351',
            '33401-D001-07607',
            '12301-D023-02068',
            '2208-D001-00087',
            '32302-D001-05447',
            '803-D003-00069',
            '8404-D003-00005',
            '33702-D001-04112',
            '35105-D001-09203',
            '35204-D023-01747',
            '35204-D023-01766',
            '35104-D001-08837',
            '33802-D001-05049',
            '35108-D001-06402',
            '46501-D023-01102',
            '5001-D001-14489',
            '5001-D001-14490',
            '32304-D001-01126',
            '32304-D001-01127',
            '5001-D001-14542',
            '5001-D001-14543',
            '614-D003-00018',
            '35107-D001-06264',
            '33301-D001-08742',
            '2215-D001-00044',
            '2215-D002-08260',
            '604-D003-00039',
            '3404-D002-07079',
            '3404-D002-07081',
            '3404-D002-07089',
            '3404-D002-07090',
            '3136-D002-05273',
            '1711-D002-13793',
            '1711-D002-13819',
            '1711-D002-13821',
            '3207-D002-11072',
            '3207-D002-11097',
            '2209-D002-08115',
            '2209-D002-08117',
            '2931-D002-05654',
            '2931-D002-05655',
            '2928-D002-05995',
            '2914-D002-09876',
            '2809-D002-08656',
            '3404-D002-07110',
            '602-D002-11750',
            '602-D002-11753',
            '710-D002-07068',
            '710-D002-07087',
            '602-D002-11775',
            '2932-D002-04923',
            '2932-D002-04955',
            '2806-D002-09713',
            '3516-D002-06313',
            '3516-D002-06330',
            '3516-D002-06354',
            '3516-D002-06361',
            '3516-D002-06362',
            '3516-D002-06366',
            '2302-D002-13794',
            '2302-D002-13795',
            '2302-D002-13796',
            '2302-D002-13799',
            '3102-D002-12964',
            '104-D001-00674',
            '1007-D002-06118',
            '1007-D002-06120',
            '2816-D002-07380',
            '203-D002-12385',
            '2110-D002-08803',
            '2402-D002-12631',
            '2926-D002-05913',
            '2708-D002-04681',
            '2708-D002-04682',
            '2708-D002-04694',
            '2708-D002-04722',
            '2404-D002-12467',
            '2401-D002-12662',
            '2401-D002-12663',
            '2401-D002-12665',
            '112-D001-00232',
            '2104-D002-11832',
            '2104-D002-11860',
            '1402-D002-11325',
            '110-D002-09361',
            '1106-D002-10668',
            '1106-D002-10670',
            '1103-D002-12595',
            '1103-D002-12610',
            '2107-D002-09422',
            '2812-D002-09345',
            '2812-D002-09346',
            '2812-D002-09349',
            '3016-D002-05420',
            '3016-D002-05421',
            '2812-D002-09361',
            '3016-D002-05431',
            '3205-D002-12977',
            '3205-D002-12978',
            '3210-D002-09688',
            '3210-D001-00221',
            '3408-D002-06218',
            '3408-D002-06220',
            '3408-D002-06228',
            '3408-D002-06229',
            '3408-D002-06246',
            '2304-D002-11601',
            '2304-D002-11602',
            '2304-D002-11604',
            '3104-D002-12527',
            '2705-D002-09411',
            '2705-D002-09413',
            '1901-D002-12585',
            '2301-D002-13947',
            '1901-D002-12624',
            '2306-D002-08542',
            '2306-D002-08564',
            '3306-D002-08648',
            '1903-D002-09599',
            '3306-D002-08675',
            '2931-D001-00134',
            '2506-D001-00226',
            '51503-D015-00779',
            '51503-D015-00783',
            '51503-D015-00786',
            '51503-D015-00787',
            '51503-D015-00790',
            '51503-D015-00792',
            '51503-D015-00793',
            '51503-D015-00794',
            '51503-D015-00795',
            '51503-D015-00797',
            '51503-D015-00804',
            '51503-D015-00805',
            '51503-D015-00807',
            '51503-D015-00808',
            '51503-D015-00816',
            '51503-D015-00817',
            '35204-D023-01774',
            '35204-D023-01779',
            '913-D023-04489',
            '47001-D001-00973',
            '47001-D001-00974',
            '47001-D001-00975',
            '47001-D001-00982',
            '47001-D001-00983',
            '47001-D001-00984',
            '17203-D023-02331',
            '2810-D002-09413',
            '13104-D001-00487',
            '2301-D001-05034',
            '2824-D001-00115',
            '15301-D001-00352',
            '15301-D001-00368',
            '17205-D023-02295',
            '17205-D023-02300',
            '811-D003-00083',
            '3136-D001-00082',
            '32401-D001-00009',
            '32202-D001-02050',
            '32202-D001-02051',
            '801-D001-03127',
            '5001-D001-14547',
            '5001-D001-14548',
            '32201-D001-02825',
            '32201-D001-02832',
            '32201-D001-02864',
            '3206-D003-00069',
            '604-D001-00311',
            '608-D001-00253',
            '32301-D001-00595',
            '2710-D001-00158',
            '35104-D001-08880',
            '33801-D001-06846',
            '32304-D001-01166',
            '33702-D001-04135',
            '32304-D001-01176',
            '32304-D001-01177',
            '11501-D003-00022',
            '34201-D001-07405',
            '34201-D001-07408',
            '32301-D001-00630',
            '33401-D001-07711',
            '33401-D001-07713',
            '34202-D001-05111',
            '33301-D001-08843',
            '33301-D001-08845',
            '34401-D001-06061',
            '35105-D001-09282',
            '12402-D001-00575',
            '2214-D002-08304',
            '2203-D002-11198',
            '3136-D002-05312',
            '2202-D002-12222',
            '3136-D002-05341',
            '3136-D002-05350',
            '3228-D002-04891',
            '3228-D002-04902',
            '2705-D002-09424',
            '2931-D002-05682',
            '2931-D002-05687',
            '2924-D002-06067',
            '3214-D002-07668',
            '3214-D002-07681',
            '3214-D002-07682',
            '2932-D002-04971',
            '2932-D002-04982',
            '2833-D002-05446',
            '2833-D002-05447',
            '2833-D002-05484',
            '3401-D002-10292',
            '2208-D002-08367',
            '3210-D002-09742',
            '3210-D002-09745',
            '2810-D002-09467',
            '2914-D002-09915',
            '2914-D002-09916',
            '2810-D002-09490',
            '3207-D002-11126',
            '212-D001-00282',
            '1903-D002-09637',
            '1903-D002-09679',
            '2812-D002-09411',
            '2812-D002-09415',
            '2911-D002-10587',
            '2911-D002-10588',
            '2911-D002-10589',
            '2911-D002-10590',
            '2812-D002-09436',
            '2812-D002-09441',
            '2302-D002-13818',
            '1005-D001-00616',
            '2104-D002-11876',
            '2104-D002-11886',
            '2104-D002-11887',
            '2809-D002-08689',
            '2809-D002-08691',
            '2809-D002-08692',
            '2809-D002-08693',
            '2809-D002-08722',
            '2702-D002-04621',
            '213-D001-01767',
            '1007-D002-06148',
            '2711-D002-04076',
            '1402-D002-11360',
            '2711-D002-04085',
            '2102-D001-01126',
            '2306-D002-08590',
            '2111-D002-08848',
            '710-D002-07088',
            '2824-D002-05743',
            '2303-D002-04803',
            '2303-D002-04804',
            '2303-D002-04805',
            '811-D001-00591',
            '710-D002-07102',
            '3402-D002-10135',
            '2824-D002-05769',
            '2824-D002-05774',
            '2708-D002-04733',
            '2708-D001-00136',
            '1711-D002-13840',
            '1711-D002-13841',
            '1733-D002-12210',
            '3405-D002-04465',
            '3304-D002-06061',
            '3304-D002-06062',
            '3304-D002-06063',
            '6403-D001-00349',
            '3011-D002-08855',
            '3011-D002-08856',
            '3306-D002-08711',
            '1601-D002-15425',
            '604-D003-00041',
            '913-D023-04559',
            '51503-D015-00818',
            '51503-D015-00833',
            '51503-D015-00835',
            '51503-D015-00839',
            '51503-D015-00848',
            '33702-D001-04158',
            '33702-D001-04159',
            '47001-D001-01026',
            '47001-D001-01027',
            '47001-D001-01028',
            '47001-D001-01029',
            '47001-D001-01055',
            '47001-D001-01056',
            '47001-D001-01057',
            '17209-D023-01905',
            '605-D001-00173',
            '32301-D001-00632',
            '34203-D003-00041',
            '32201-D001-02871',
            '32201-D001-02872',
            '15301-D001-00386',
            '2306-D001-00254',
            '34202-D001-05152',
            '34202-D001-05172',
            '32202-D001-02104',
            '32202-D001-02130',
            '17205-D003-00033',
            '11501-D023-02489',
            '11404-D003-00033',
            '2401-D001-02316',
            '33301-D001-08901',
            '34203-D001-03784',
            '33401-D001-07733',
            '33401-D001-07743',
            '2918-D002-05846',
            '5001-D001-14629',
            '35104-D001-08904',
            '5001-D001-14631',
            '5001-D001-14650',
            '1114-D003-00045',
            '11504-D023-02055',
            '35113-D001-01573',
            '35108-D001-06501',
            '35108-D001-06505',
            '3214-D002-07729',
            '3207-D002-11163',
            '827-D002-07392',
            '2833-D002-05535',
            '15301-D027-00186',
            '15301-D027-00190',
            '3404-D002-07148',
            '2301-D002-14024',
            '826-D002-07928',
            '401-D002-12035',
            '2224-D002-09571',
            '2108-D002-09114',
            '2108-D002-09135',
            '3136-D002-05355',
            '3136-D001-00086',
            '2824-D002-05804',
            '2824-D002-05805',
            '2809-D002-08777',
            '2809-D002-08778',
            '2809-D002-08779',
            '3516-D002-06412',
            '3516-D002-06414',
            '3516-D002-06415',
            '3516-D002-06422',
            '3516-D002-06423',
            '3516-D002-06424',
            '3516-D002-06447',
            '2207-D002-11081',
            '2710-D002-04928',
            '3228-D002-04948',
            '2104-D002-11923',
            '803-D002-13394',
            '2302-D002-13888',
            '106-D001-00442',
            '2306-D002-08628',
            '3210-D002-09768',
            '2903-D002-11542',
            '2903-D002-11544',
            '707-D002-12633',
            '2703-D027-00184',
            '2401-D002-12786',
            '3405-D002-04481',
            '3402-D002-10164',
            '2111-D002-08924',
            '3206-D002-11542',
            '3206-D002-11543',
            '2902-D002-02711',
            '2902-D002-02721',
            '2902-D002-02722',
            '1903-D002-09701',
            '3016-D002-05505',
            '210-D002-08266',
            '3016-D002-05515',
            '2816-D002-07490',
            '2816-D002-07491',
            '3209-D002-11183',
            '2702-D002-04647',
            '2702-D002-04648',
            '2901-D002-13172',
            '2901-D002-13175',
            '2812-D002-09468',
            '2812-D001-00057',
            '107-D002-11094',
            '2928-D002-06068',
            '2926-D002-05988',
            '2926-D002-05989',
            '2911-D002-10644',
            '2931-D002-05715',
            '1203-D027-00088',
            '1711-D002-13938',
            '1711-D002-13944',
            '1715-D002-14356',
            '15301-D027-00200',
            '15301-D027-00201',
            '15301-D027-00208',
            '15301-D027-00209',
            '2502-D002-11316',
            '2502-D002-11317',
            '2708-D002-04803',
            '12301-D023-02240',
            '2402-D002-12721',
            '2402-D002-12732',
            '3306-D002-08736',
            '1204-D003-00041',
            '2506-D001-00241',
            '3139-D002-03275',
            '1501-D030-00003',
            '105-D003-00045',
            '47001-D001-01082',
            '47001-D001-01083',
            '47001-D001-01084',
            '47001-D001-01091',
            '47001-D001-01092',
            '47001-D001-01093',
            '47001-D001-01101',
            '47001-D001-01102',
            '47001-D001-01103',
            '47001-D001-01125',
            '47001-D001-01126',
            '47001-D001-01127',
            '51503-D015-00849',
            '51503-D015-00852',
            '51503-D015-00853',
            '51503-D015-00854',
            '51503-D015-00855',
            '51503-D015-00856',
            '51503-D015-00857',
            '51503-D015-00860',
            '51503-D015-00863',
            '51503-D015-00864',
            '51503-D015-00865',
            '51503-D015-00867',
            '51503-D015-00872',
            '51503-D015-00873',
            '51503-D015-00874',
            '51503-D015-00875',
            '51503-D015-00876',
            '51503-D015-00877',
            '51503-D015-00881',
            '51503-D015-00882',
            '51503-D015-00883',
            '51503-D015-00886',
            '51503-D015-00888',
            '1301-D030-00008',
            '33702-D027-00051',
            '32401-D001-00087',
            '32401-D001-00103',
            '32401-D027-00003',
            '16102-D003-00042',
            '32202-D001-02177',
            '32202-D001-02182',
            '32202-D001-02184',
            '35104-D001-08971',
            '35104-D001-08987',
            '2501-D030-00014',
            '32202-D001-02205',
            '32201-D001-02927',
            '35107-D001-06435',
            '32304-D001-01288',
            '32304-D001-01322',
            '33401-D001-07809',
            '33401-D001-07842',
            '11502-D023-02365',
            '11501-D003-00027',
            '32304-D027-00068',
            '33702-D001-04211',
            '33702-D001-04216',
            '34203-D001-03805',
            '34203-D001-03811',
            '34203-D001-03824',
            '48203-D023-01438',
            '48203-D023-01464',
            '33301-D001-08985',
            '33301-D001-08995',
            '33301-D001-08997',
            '15301-D027-00233',
            '15301-D027-00235',
            '34302-D001-01712',
            '34302-D001-01713',
            '34302-D001-01720',
            '34302-D001-01721',
            '34203-D001-03830',
            '2810-D002-09586',
            '2810-D002-09622',
            '2202-D002-12294',
            '2202-D002-12313',
            '2209-D002-08205',
            '2833-D002-05577',
            '2215-D002-08381',
            '2215-D002-08382',
            '2904-D002-11589',
            '2924-D002-06186',
            '3119-D002-09308',
            '2203-D002-11265',
            '1007-D002-06220',
            '1007-D002-06221',
            '1007-D002-06224',
            '1007-D002-06231',
            '2104-D002-11945',
            '2104-D002-11953',
            '2104-D002-11958',
            '1812-D002-10834',
            '2206-D002-11555',
            '2914-D002-10012',
            '2914-D002-10014',
            '2210-D002-08449',
            '2210-D002-08475',
            '702-D002-11969',
            '2208-D002-08415',
            '2208-D002-08419',
            '2208-D002-08431',
            '2108-D002-09156',
            '3516-D002-06470',
            '3516-D002-06487',
            '3516-D002-06492',
            '2809-D002-08814',
            '2501-D001-00365',
            '3136-D002-05414',
            '2207-D002-11107',
            '1008-D001-00353',
            '3016-D002-05535',
            '3405-D002-04499',
            '3405-D002-04500',
            '3405-D002-04514',
            '2401-D002-12825',
            '3405-D002-04522',
            '2705-D002-09504',
            '3228-D002-04973',
            '3228-D002-04974',
            '3228-D002-04980',
            '3228-D002-04993',
            '3304-D002-06148',
            '2824-D002-05814',
            '2824-D002-05819',
            '2824-D002-05821',
            '2404-D001-00469',
            '2702-D002-04693',
            '2402-D002-12754',
            '3210-D002-09794',
            '2911-D002-10662',
            '2911-D002-10663',
            '2911-D002-10699',
            '205-D002-12278',
            '2711-D002-04163',
            '2902-D002-02757',
            '3402-D002-10200',
            '2112-D002-08648',
            '210-D001-00175',
            '1711-D002-13997',
            '2306-D001-00259',
            '1901-D002-12756',
            '2708-D002-04809',
            '2708-D002-04827',
            '2708-D001-00142',
            '2928-D002-06094',
            '2928-D002-06113',
            '2701-D002-11361',
            '3011-D002-08924',
            '3102-D002-13007',
            '2905-D002-10475',
            '2905-D002-10476',
            '2602-D002-11990',
            '3306-D002-08757',
            '3604-D030-00008',
            '316-D001-00134',
            '51503-D015-00891',
            '51503-D015-00898',
            '51503-D015-00901',
            '51503-D015-00902',
            '3602-D030-00010',
            '35104-D001-09016',
            '35104-D001-09020',
            '47001-D001-01131',
            '47001-D001-01132',
            '47001-D001-01133',
            '2925-D030-00003',
            '32202-D001-02211',
            '32202-D001-02253',
            '32202-D001-02254',
            '32202-D001-02255',
            '809-D003-00065',
            '1501-D030-00029',
            '3208-D030-00004',
            '32201-D001-02950',
            '32201-D001-02953',
            '3102-D002-13031',
            '32201-D001-02976',
            '46501-D023-01258',
            '2703-D027-00245',
            '2703-D027-00250',
            '1302-D030-00010',
            '106-D003-00081',
            '17209-D023-01997',
            '17209-D023-02022',
            '17209-D023-02024',
            '1301-D030-00018',
            '34202-D001-05216',
            '34202-D001-05217',
            '34202-D001-05218',
            '34201-D001-07549',
            '32201-D001-02982',
            '32304-D001-01330',
            '32304-D001-01342',
            '32304-D001-01344',
            '33301-D001-09035',
            '3306-D030-00010',
            '32301-D001-00759',
            '32301-D001-00761',
            '11503-D023-02538',
            '11503-D023-02539',
            '11503-D023-02540',
            '2302-D030-00055',
            '34203-D001-03851',
            '34203-D001-03852',
            '33401-D001-07877',
            '1503-D030-00013',
            '5001-D001-14724',
            '3203-D030-00035',
            '1502-D030-00021',
            '34301-D001-01762',
            '34401-D001-06157',
            '1503-D030-00023',
            '3604-D030-00030',
            '34302-D001-01759',
            '3306-D030-00014',
            '35204-D023-01873',
            '2501-D030-00053',
            '108-D001-00185',
            '15301-D031-00009',
            '3405-D002-04543',
            '3405-D002-04545',
            '3402-D002-10224',
            '3214-D002-07806',
            '2215-D002-08399',
            '2215-D002-08416',
            '2928-D002-06139',
            '3119-D002-09354',
            '3119-D002-09361',
            '1001-D002-10085',
            '2210-D002-08481',
            '2401-D002-12835',
            '2911-D002-10703',
            '2911-D002-10714',
            '2603-D027-00359',
            '710-D002-07151',
            '2404-D002-12620',
            '1204-D001-00289',
            '2404-D001-00470',
            '2932-D002-05121',
            '2932-D002-05123',
            '3404-D002-07225',
            '2833-D002-05595',
            '2306-D002-08670',
            '2306-D002-08688',
            '3408-D002-06344',
            '3408-D002-06345',
            '3205-D002-13106',
            '3205-D002-13108',
            '1733-D002-12293',
            '1106-D002-10809',
            '1106-D002-10817',
            '1106-D002-10821',
            '1106-D002-10822',
            '1106-D002-10824',
            '2931-D002-05763',
            '2304-D002-11730',
            '3206-D002-11618',
            '2302-D002-13947',
            '2302-D002-13969',
            '3210-D002-09818',
            '3210-D002-09819',
            '2902-D002-02786',
            '1810-D002-10777',
            '2927-D002-05813',
            '3207-D002-11194',
            '3207-D002-11196',
            '2816-D002-07559',
            '2905-D002-10482',
            '2905-D002-10485',
            '1711-D002-14049',
            '3203-D002-13968',
            '3203-D002-13969',
            '3203-D002-13982',
            '3203-D002-13984',
            '3011-D002-08945',
            '2404-D003-00073',
            '3228-D002-04994',
            '3228-D002-05012',
            '3228-D002-05014',
            '3602-D030-00025',
            '3516-D002-06508',
            '3516-D002-06509',
            '2605-D002-09047',
            '3516-D002-06541',
            '3516-D002-06542',
            '3516-D002-06543',
            '51503-D015-00919',
            '51503-D015-00922',
            '51503-D015-00923',
            '51503-D015-00924',
            '51503-D015-00925',
            '51503-D015-00926',
            '51503-D015-00927',
            '51503-D015-00932',
            '51503-D015-00934',
            '51503-D015-00936',
            '51503-D015-00941',
            '1505-D030-00017',
            '3306-D002-08795',
            '3306-D002-08800',
            '3306-D002-08801',
            '3306-D002-08802',
            '3306-D002-08813',
            '16102-D031-00020',
            '3306-D002-08821',
            '3306-D002-08823',
            '1501-D030-00063',
            '2401-D030-00053',
            '813-D030-00030',
            '813-D030-00033',
            '32201-D001-03009',
            '17205-D023-02531',
            '46501-D027-00045',
            '2501-D030-00074',
            '913-D023-04756',
            '913-D023-04764',
            '913-D023-04767',
            '17209-D023-02054',
            '11102-D031-00007',
            '1901-D001-00417',
            '11103-D031-00008',
            '47001-D001-01208',
            '47001-D001-01209',
            '47001-D001-01210',
            '47001-D001-01223',
            '47001-D001-01224',
            '47001-D001-01225',
            '47001-D001-01229',
            '47001-D001-01230',
            '47001-D001-01231',
            '47001-D001-01235',
            '47001-D001-01236',
            '47001-D001-01237',
            '47001-D001-01238',
            '47001-D001-01239',
            '47001-D001-01240',
            '6404-D030-00043',
            '33301-D001-09098',
            '33301-D001-09127',
            '34203-D001-03884',
            '32401-D001-00163',
            '2215-D003-00008',
            '12402-D001-00691',
            '11503-D023-02613',
            '11503-D023-02614',
            '11503-D023-02615',
            '32401-D001-00193',
            '32401-D001-00194',
            '34202-D001-05259',
            '35104-D001-09058',
            '33702-D001-04343',
            '2501-D030-00079',
            '3604-D030-00059',
            '2703-D027-00276',
            '11702-D031-00008',
            '5001-D001-14824',
            '505-D001-00140',
            '5001-D001-14841',
            '2501-D030-00085',
            '4802-D003-00015',
            '3604-D030-00062',
            '807-D001-00521',
            '11501-D023-02692',
            '1715-D002-14465',
            '1402-D030-00026',
            '1503-D003-00038',
            '303-D027-00141',
            '1901-D001-00420',
            '14305-D031-00013',
            '2215-D002-08471',
            '2822-D002-06156',
            '3401-D002-10422',
            '3401-D002-10423',
            '3301-D002-10335',
            '3210-D002-09842',
            '3210-D002-09843',
            '3210-D002-09846',
            '2932-D002-05130',
            '2833-D002-05620',
            '2833-D002-05630',
            '2833-D002-05646',
            '2104-D002-12018',
            '2106-D002-11185',
            '203-D002-12629',
            '3206-D002-11633',
            '3206-D002-11635',
            '2708-D002-04894',
            '2708-D002-04900',
            '2708-D002-04902',
            '2224-D002-09679',
            '2112-D002-08746',
            '1417-D001-00547',
            '2810-D002-09653',
            '3228-D002-05053',
            '2401-D002-12877',
            '2710-D002-05044',
            '2209-D002-08269',
            '2209-D002-08270',
            '2209-D002-08275',
            '2208-D002-08479',
            '2208-D002-08480',
            '2809-D002-08890',
            '2809-D002-08891',
            '2806-D002-09928',
            '2301-D002-14098',
            '3203-D002-14007',
            '2824-D002-05883',
            '3207-D002-11251',
            '2302-D002-13987',
            '2302-D002-13996',
            '2911-D001-00085',
            '3209-D002-11232',
            '3209-D002-11249',
            '2929-D002-05869',
            '710-D003-00019',
            '2928-D002-06159',
            '1602-D001-00216',
            '207-D002-09724',
            '2902-D002-02837',
            '2902-D002-02838',
            '2902-D002-02840',
            '2902-D002-02860',
            '1103-D002-12848',
            '2705-D002-09594',
            '2705-D002-09602',
            '3205-D002-13131',
            '3205-D002-13132',
            '1901-D002-12824',
            '3516-D002-06560',
            '3516-D002-06586',
            '3207-D030-00021',
            '15301-D031-00047',
            '1901-D002-12841',
            '3104-D002-12754',
            '202-D030-00080',
            '202-D030-00083',
            '3604-D030-00078',
            '203-D003-00129',
            '3602-D030-00057',
            '51503-D015-00948',
            '51503-D015-00951',
            '51503-D015-00953',
            '51503-D015-00955',
            '51503-D015-00957',
            '51503-D015-00958',
            '51503-D015-00964',
            '51503-D015-00966',
            '1746-D030-00044',
            '17203-D023-02652',
            '2104-D001-00091',
            '47001-D001-01284',
            '47001-D001-01288',
            '47001-D001-01294',
            '47001-D001-01295',
            '47001-D001-01296',
            '47001-D001-01302',
            '2302-D030-00135',
            '32202-D001-02341',
            '32202-D001-02342',
            '1501-D030-00101',
            '32201-D001-03031',
            '32201-D001-03049',
            '32201-D001-03069',
            '35113-D001-01766',
            '34202-D001-05287',
            '2111-D003-00079',
            '1503-D030-00059',
            '104-D030-00076',
            '34401-D001-06214',
            '35104-D001-09131',
            '32401-D001-00212',
            '3301-D001-00364',
            '34203-D001-03896',
            '34203-D001-03922',
            '34203-D001-03929',
            '34203-D001-03937',
            '50701-D001-00009',
            '11503-D023-02652',
            '11503-D023-02653',
            '11503-D023-02654',
            '33301-D001-09185',
            '33301-D001-09187',
            '5001-D001-14897',
            '5001-D001-14899',
            '35108-D001-06704',
            '33702-D001-04355',
            '34302-D001-01901',
            '33702-D001-04391',
            '34301-D001-01914',
            '34301-D001-01916',
            '3214-D002-07851',
            '1901-D030-00035',
            '1203-D027-00142',
            '2208-D002-08495',
            '2208-D002-08497',
            '2209-D002-08298',
            '507-D002-04700',
            '2210-D002-08545',
            '2210-D002-08546',
            '2401-D002-12903',
            '2404-D001-00474',
            '2602-D030-00075',
            '111-D001-00325',
            '2833-D002-05661',
            '2833-D002-05667',
            '2833-D002-05668',
            '2833-D002-05669',
            '107-D001-00333',
            '505-D002-09711',
            '2112-D002-08772',
            '3203-D002-14022',
            '912-D002-02940',
            '2108-D002-09228',
            '3206-D002-11648',
            '3206-D002-11650',
            '2304-D002-11794',
            '1733-D002-12379',
            '2107-D002-09681',
            '1710-D002-14889',
            '2932-D002-05169',
            '2932-D002-05178',
            '3405-D002-04611',
            '3405-D002-04614',
            '3405-D002-04624',
            '3209-D002-11261',
            '3209-D002-11268',
            '2702-D002-04792',
            '2816-D002-07631',
            '2816-D002-07636',
            '2816-D002-07650',
            '2816-D002-07651',
            '2104-D002-12041',
            '2928-D002-06194',
            '2928-D002-06196',
            '2928-D002-06199',
            '2106-D002-11192',
            '3016-D002-05635',
            '2910-D002-04581',
            '2910-D002-04583',
            '810-D001-00748',
            '2911-D002-10794',
            '2911-D002-10804',
            '3016-D002-05655',
            '2824-D002-05931',
            '2931-D002-05812',
            '2301-D002-14112',
            '2301-D002-14120',
            '1505-D001-00115',
            '1903-D002-09864',
            '1903-D002-09884',
            '2812-D002-09585',
            '2301-D001-05276',
            '2927-D002-05877',
            '3504-D002-08240',
            '2501-D030-00164',
            '3214-D030-00014',
            '3304-D030-00045',
            '1122-D002-06257',
            '2501-D002-12365',
            '1106-D002-10862',
            '1106-D002-10885',
            '1102-D030-00027',
            '51503-D015-00968',
            '51503-D015-00970',
            '51503-D015-00976',
            '51503-D015-00977',
            '51503-D015-00979',
            '51503-D015-00991',
            '3402-D030-00054',
            '1505-D030-00028',
            '3602-D030-00078',
            '32201-D001-03102',
            '32201-D001-03109',
            '32201-D001-03114',
            '32201-D001-03119',
            '17205-D023-02609',
            '17205-D023-02611',
            '1711-D002-14143',
            '1711-D002-14164',
            '3218-D030-00070',
            '47001-D001-01338',
            '17207-D023-02910',
            '17207-D023-02912',
            '913-D023-04895',
            '913-D023-04914',
            '913-D023-04933',
            '2806-D001-00298',
            '1503-D030-00064',
            '1503-D030-00065',
            '1501-D030-00130',
            '2304-D001-00318',
            '33301-D001-09210',
            '33301-D001-09217',
            '33301-D001-09218',
            '12301-D023-02440',
            '1501-D030-00135',
            '1302-D030-00065',
            '34203-D001-03948',
            '1714-D002-12929',
            '1714-D002-12931',
            '1733-D003-00166',
            '1102-D030-00033',
            '1002-D030-00074',
            '35105-D003-00024',
            '3214-D030-00016',
            '46501-D023-01360',
            '3203-D030-00092',
            '46501-D023-01384',
            '11503-D023-02709',
            '803-D001-03335',
            '3602-D030-00094',
            '32401-D001-00266',
            '33401-D001-08036',
            '12402-D001-00774',
            '1501-D030-00144',
            '5001-D001-14932',
            '33501-D001-03272',
            '505-D003-00044',
            '1102-D030-00038',
            '34302-D001-01937',
            '106-D030-00040',
            '35204-D023-02030',
            '34401-D001-06280',
            '35104-D001-09183',
            '3207-D030-00048',
            '1901-D030-00056',
            '2925-D030-00023',
            '3402-D030-00078',
            '2705-D002-09669',
            '2932-D002-05221',
            '2932-D002-05238',
            '3404-D002-07297',
            '3214-D002-07886',
            '3206-D002-11681',
            '3214-D002-07888',
            '3214-D002-07899',
            '3203-D002-14057',
            '3203-D002-14058',
            '3203-D002-14062',
            '3203-D002-14063',
            '3205-D002-13171',
            '3205-D002-13173',
            '2401-D002-12964',
            '2401-D002-12966',
            '2210-D002-08575',
            '2210-D002-08576',
            '2711-D002-04320',
            '1302-D030-00074',
            '3016-D002-05664',
            '2208-D002-08517',
            '2208-D002-08518',
            '2208-D002-08538',
            '2208-D002-08539',
            '2208-D002-08540',
            '502-D030-00048',
            '2709-D002-04980',
            '812-D001-00406',
            '2809-D002-08947',
            '2809-D002-08948',
            '2809-D002-08969',
            '2302-D030-00179',
            '2824-D002-05971',
            '2302-D002-14054',
            '104-D001-00690',
            '2304-D002-11817',
            '2810-D002-09725',
            '2812-D002-09633',
            '2812-D002-09634',
            '3405-D002-04644',
            '2704-D002-10925',
            '2404-D001-00477',
            '3119-D002-09500',
            '3408-D002-06429',
            '2904-D002-11729',
            '2904-D002-11748',
            '701-D001-02286',
            '2104-D002-12079',
            '2104-D002-12094',
            '2104-D002-12096',
            '1004-D002-09818',
            '602-D002-11868',
            '1001-D002-10150',
            '2816-D002-07670',
            '2816-D002-07683',
            '2702-D002-04810',
            '2702-D002-04827',
            '2911-D002-10835',
            '3304-D002-06272',
            '2105-D002-13339',
            '2105-D002-13340',
            '2812-D001-00058',
            '2306-D002-08754',
            '1003-D030-00102',
            '2902-D002-02905',
            '202-D001-00643',
            '1007-D002-06375',
            '1007-D002-06376',
            '1007-D002-06377',
            '33702-D027-00057',
            '2108-D030-00031',
            '813-D002-11812',
            '3516-D002-06638',
            '3516-D002-06649',
            '401-D030-00203',
            '32201-D001-03126',
            '32201-D001-03128',
            '32201-D001-03135',
            '32201-D001-03137',
            '32201-D001-03164',
            '32401-D001-00309',
            '1903-D003-00047',
            '51503-D015-00995',
            '51503-D015-00996',
            '51503-D015-00998',
            '51503-D015-01004',
            '51503-D015-01015',
            '2602-D030-00114',
            '3408-D030-00098',
            '47001-D001-01348',
            '47001-D001-01349',
            '47001-D001-01350',
            '47001-D001-01357',
            '47001-D001-01358',
            '47001-D001-01359',
            '47001-D001-01360',
            '47001-D001-01361',
            '47001-D001-01362',
            '47001-D001-01366',
            '47001-D001-01367',
            '47001-D001-01368',
            '2926-D002-06125',
            '1805-D030-00067',
            '1302-D030-00080',
            '16201-D031-00016',
            '34201-D001-07742',
            '34202-D001-05354',
            '34202-D001-05359',
            '34202-D001-05360',
            '212-D001-00296',
            '710-D003-00021',
            '2708-D001-00150',
            '913-D023-04980',
            '913-D023-05003',
            '106-D003-00085',
            '32304-D001-01559',
            '32304-D001-01569',
            '32304-D001-01570',
            '32304-D001-01571',
            '32304-D001-01572',
            '33801-D001-07210',
            '3218-D030-00099',
            '105-D003-00047',
            '104-D001-00691',
            '2404-D030-00083',
            '3604-D030-00119',
            '2806-D003-00091',
            '33301-D001-09302',
            '33702-D001-04437',
            '33702-D001-04463',
            '2810-D001-00360',
            '11504-D023-02397',
            '11504-D023-02398',
            '12402-D001-00787',
            '34302-D001-01955',
            '34302-D001-01957',
            '34302-D001-01959',
            '2401-D030-00156',
            '34302-D001-01983',
            '34302-D001-01987',
            '34203-D001-04030',
            '3203-D002-14076',
            '3203-D002-14093',
            '3228-D002-05120',
            '3228-D002-05137',
            '2302-D002-14083',
            '3214-D002-07928',
            '3404-D002-07322',
            '2404-D002-12756',
            '2404-D002-12758',
            '2404-D002-12760',
            '2404-D002-12775',
            '614-D001-00143',
            '2904-D002-11781',
            '3136-D002-05547',
            '3136-D002-05554',
            '3136-D002-05564',
            '2833-D002-05746',
            '701-D002-12519',
            '701-D002-12520',
            '2810-D002-09756',
            '2810-D002-09758',
            '710-D002-07250',
            '710-D002-07270',
            '2932-D002-05244',
            '2932-D002-05245',
            '2932-D002-05271',
            '2812-D002-09655',
            '2305-D002-12745',
            '210-D002-08446',
            '210-D002-08448',
            '2106-D002-11224',
            '2106-D002-11237',
            '3137-D002-05153',
            '2108-D002-09302',
            '2112-D002-08843',
            '2107-D002-09769',
            '2107-D002-09771',
            '2816-D002-07705',
            '2702-D002-04834',
            '2702-D002-04835',
            '2702-D002-04840',
            '2809-D002-08996',
            '2809-D002-09006',
            '2911-D002-10849',
            '3011-D002-09091',
            '2911-D002-10871',
            '2911-D002-10872',
            '3119-D002-09548',
            '2905-D002-10633',
            '2905-D002-10634',
            '2905-D002-10662',
            '2928-D002-06248',
            '2816-D002-07739',
            '2104-D002-12129',
            '2104-D002-12148',
            '1503-D001-00167',
            '604-D001-00329',
            '3306-D002-08918',
            '13205-D003-00037',
            '2104-D002-12160',
            '614-D001-00144',
            '51503-D015-01024',
            '51503-D015-01026',
            '2111-D002-09072',
            '51503-D015-01031',
            '51503-D015-01033',
            '51503-D015-01035',
            '51503-D015-01036',
            '51503-D015-01037',
            '51503-D015-01038',
            '32201-D001-03208',
            '47001-D001-01378',
            '47001-D001-01380',
            '47001-D001-01391',
            '47001-D001-01392',
            '17209-D023-02260',
            '47001-D001-01404',
            '913-D023-05047',
            '913-D023-05071',
            '2304-D001-00321',
            '3516-D002-06693',
            '15301-D001-00523',
            '8201-D003-00071',
            '34203-D001-04050',
            '12301-D023-02507',
            '32304-D001-01619',
            '35204-D023-02092',
            '35204-D023-02102',
            '35204-D023-02104',
            '48203-D023-01708',
            '2902-D002-02944',
            '34202-D001-05404',
            '3203-D002-14098',
            '3203-D002-14107',
            '3203-D002-14109',
            '3404-D001-00256',
            '2209-D002-08394',
            '11701-D031-00024',
            '3205-D002-13219',
            '3228-D002-05155',
            '3228-D002-05182',
            '3212-D002-07771',
            '2710-D002-05173',
            '3209-D002-11325',
            '3209-D002-11339',
            '3209-D002-11341',
            '34203-D003-00043',
            '803-D002-13692',
            '2810-D002-09802',
            '2812-D002-09690',
            '2812-D002-09712',
            '2932-D002-05284',
            '2932-D001-00092',
            '1820-D002-10188',
            '2404-D002-12782',
            '2404-D002-12783',
            '2404-D002-12784',
            '2404-D002-12802',
            '1106-D002-10970',
            '1106-D002-10971',
            '2708-D002-05036',
            '2301-D002-14224',
            '2824-D002-06034',
            '3136-D002-05580',
            '3136-D002-05581',
            '3136-D002-05585',
            '3136-D002-05586',
            '3136-D002-05602',
            '2402-D002-12964',
            '2905-D002-10672',
            '3011-D002-09101',
            '1503-D001-00169',
            '2928-D002-06280',
            '2928-D002-06281',
            '2928-D002-06282',
            '1903-D002-10011',
            '2911-D002-10898',
            '1711-D002-14261',
            '1711-D002-14274',
            '1711-D002-14275',
            '2303-D002-05029',
            '2902-D002-02966',
            '2902-D002-02968',
            '3504-D002-08322',
            '1005-D027-00325',
            '1301-D001-00321',
            '47001-D001-01408',
            '47001-D001-01409',
            '47001-D001-01410',
            '47001-D001-01411',
            '47001-D001-01414',
            '47001-D001-01415',
            '47001-D001-01426',
            '47001-D001-01427',
            '47001-D001-01428',
            '47001-D001-01432',
            '47001-D001-01433',
            '47001-D001-01434',
            '3102-D002-13104',
            '51503-D015-01041',
            '51503-D015-01043',
            '51503-D015-01044',
            '51503-D015-01045',
            '51503-D015-01050',
            '51503-D015-01053',
            '51503-D015-01055',
            '51503-D015-01057',
            '51503-D015-01058',
            '51503-D015-01063',
            '51503-D015-01064',
            '32201-D001-03267',
            '913-D023-05132',
            '5001-D001-15031',
            '913-D023-05161',
            '15301-D001-00547',
            '15301-D001-00548',
            '33401-D001-08215',
            '35104-D001-09311',
            '35104-D001-09313',
            '32401-D001-00355',
            '32401-D001-00367',
            '17209-D023-02314',
            '35107-D001-06795',
            '11702-D003-00049',
            '35108-D001-06884',
            '35108-D001-06885',
            '35108-D001-06886',
            '2108-D002-09314',
            '35204-D023-02111',
            '35204-D023-02113',
            '35204-D023-02114',
            '35204-D023-02116',
            '35204-D023-02120',
            '35204-D023-02138',
            '3306-D002-08935',
            '2401-D002-13065',
            '2401-D002-13070',
            '203-D002-12802',
            '3136-D002-05615',
            '3214-D002-07990');
        foreach ($loan_ids as $loan_id) {
            $loan = Loans::findOne(['sanction_no' => $loan_id]);
            FixesHelper::fix_schedules_update($loan);
        }

    }

    // php yii fixes/ledger-generate
    public function actionLedgerGenerate()
    {
        $loan_ids = array(
'217-D002-05233'
);
    
        foreach ($loan_ids as $loan_id) {
            $loan = Loans::find()->where(['sanction_no' => $loan_id])->one();
            FixesHelper::ledger_regenerate($loan);
            echo $loan_id . '<===>';
        }

    }

    public function actionUpdateLoanExpiry()
    {
        $loan_ids = array();
        $loans = Loans::find()->where(['in', 'sanction_no', $loan_ids])->andFilterWhere(['deleted' => 0])->andWhere(['!=', 'disbursement_id', '0'])->all();
        foreach ($loans as $loan) {
            FixesHelper::update_loan_expiry($loan);
        }
    }

    public function actionRecoveriesCsv()
    {

        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $query = Yii::$app->db->createCommand('select * from recoveries where id <5000000');
        $data = $query->queryAll();

        $file_name = date('d-m-Y-H-i-s') . '.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/dynamic_reports/' . 'recoveries' . '/' . $file_name;

        $file_name_zip = date('d-m-Y-H-i-s') . '.zip';
        $file_path_zip = Yii::getAlias('@anyname') . '/frontend/web' . '/dynamic_reports/' . 'recoveries' . '/' . $file_name_zip;

        $fopen = fopen($file_path, 'w');
        $heading = ['id', 'application_id', 'schedule_id', 'loan_id',
            'region_id', 'area_id', 'branch_id', 'team_id', 'field_id',
            'due_date', 'receive_date', 'amount', 'receipt_no'
            , 'project_id', 'type', 'source', 'is_locked', 'deleted',
            'assigned_to', 'created_by', 'updated_by', 'created_at',
            'updated_at', 'recv_date_old', 'due_date_old', 'deleted_at'
            , 'deleted_by', 'platform', 'transaction_id', 'custom_1'];
        //fputcsv($fopen,$heading,',',chr(0));
        fputcsv($fopen, $heading);

        foreach ($data as $d) {
            fputcsv($fopen, $d);
        }

        fclose($fopen);

        $zip = new \ZipArchive();
        if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
            // Add files to the zip file
            $zip->addFile($file_path, $file_name);
            // All files are added, so close the zip file.
            $zip->close();
        }
        unlink($file_path);
    }

    public function actionCal()
    {
        $query = Yii::$app->db->createCommand("SELECT id, sanction_no FROM `loans` WHERE group_id not in (select id from groups) and project_id = 1 and loans.status != 'not collected' and loans.deleted = 0 and `date_disbursed` > '1280624461' and `date_disbursed` <= '1554033599'");
        $data = $query->queryAll();
        $id = '';
        foreach ($data as $d) {
            $id .= $d['id'] . ',';
        }
        print_r($id);
        die();
    }

    public function actionAddresses()
    {
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $query = Yii::$app->db->createCommand('select id from members where platform = 1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            $query_address = Yii::$app->db->createCommand('select * from members_address where address_type = "home" and member_id = ' . $d['id']);
            $address = $query_address->queryAll();
            if (!empty($address)) {
                $address_array = array();
                foreach ($address as $key => $a) {
                    $address_array[$a['id']] = $a['address'];
                }
                $unique_addresses = array_unique($address_array);
                //print_r("update members_address set deleted = 1 where address_type = 'home' and member_id = '".$d['id']."' and id not in (".implode(',',array_keys($unique_addresses)).") ");
                Yii::$app->db->createCommand("update members_address set deleted = 1 where address_type = 'home' and member_id = '" . $d['id'] . "' and id not in (" . implode(',', array_keys($unique_addresses)) . ") ")->execute();
            }

            $query_address_business = Yii::$app->db->createCommand('select * from members_address where address_type = "business" and member_id = ' . $d['id']);
            $address_business = $query_address_business->queryAll();
            if (!empty($address_business)) {
                $address_business_array = array();
                foreach ($address_business as $key => $a) {
                    $address_business_array[$a['id']] = $a['address'];
                }
                $unique_addresses_business = array_unique($address_business_array);
                //print_r("update members_address set deleted = 1 where address_type = 'home' and member_id = '".$d['id']."' and id not in (".implode(',',array_keys($unique_addresses)).") ");
                Yii::$app->db->createCommand("update members_address set deleted = 1 where address_type = 'business' and member_id = '" . $d['id'] . "' and id not in (" . implode(',', array_keys($unique_addresses_business)) . ") ")->execute();
            }
        }
        die('here');
    }

    public function actionActiveAddresses()
    {
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $query = Yii::$app->db->createCommand('select id from members where platform = 1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            //for home addresses
            $query_address = Yii::$app->db->createCommand('select * from members_address where deleted = 0 and address_type = "home" and member_id = ' . $d['id'] . ' order by dt_entry_old desc');
            $address = $query_address->queryAll();
            $removed = array_shift($address);
            if (!empty($address)) {
                $address_array = array();
                foreach ($address as $key => $a) {
                    $address_array[$a['id']] = $a['address'];
                }
                Yii::$app->db->createCommand("update members_address set is_current = 0 where address_type = 'home' and member_id = '" . $d['id'] . "' and id in (" . implode(',', array_keys($address_array)) . ") ")->execute();
            }
            //for business addresses
            $query_address_business = Yii::$app->db->createCommand('select * from members_address where deleted = 0 and address_type = "business" and member_id = ' . $d['id'] . ' order by dt_entry_old desc');
            $address_business = $query_address_business->queryAll();
            $removed = array_shift($address_business);
            if (!empty($address_business)) {
                $address_business_array = array();
                foreach ($address_business as $key => $a) {
                    $address_business_array[$a['id']] = $a['address'];
                }
                Yii::$app->db->createCommand("update members_address set is_current = 0 where address_type = 'business' and member_id = '" . $d['id'] . "' and id in (" . implode(',', array_keys($address_business_array)) . ") ")->execute();
            }
        }
        die('here');
    }

    public function actionPhone()
    {
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $query = Yii::$app->db->createCommand('select id from members where id in (	2337836) and platform = 1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            $query_phone = Yii::$app->db->createCommand('select * from members_phone where phone_type = "Mobile" and member_id = ' . $d['id']);
            $phones = $query_phone->queryAll();
            if (!empty($phones)) {
                $phones_array = array();
                foreach ($phones as $key => $a) {
                    $phones_array[$a['id']] = $a['phone'];
                }
                $unique_phones = array_unique($phones_array);
                //print_r("update members_address set deleted = 1 where address_type = 'home' and member_id = '".$d['id']."' and id not in (".implode(',',array_keys($unique_addresses)).") ");
                Yii::$app->db->createCommand("update members_phone set deleted = 1 where phone_type = 'Mobile' and member_id = '" . $d['id'] . "' and id not in (" . implode(',', array_keys($unique_phones)) . ") ")->execute();
            }

            $query_phone_landline = Yii::$app->db->createCommand('select * from members_phone where phone_type = "Landline" and member_id = ' . $d['id']);
            $phone_landline = $query_phone_landline->queryAll();
            if (!empty($phone_landline)) {
                $phone_landline_array = array();
                foreach ($phone_landline as $key => $a) {
                    $phone_landline_array[$a['id']] = $a['phone'];
                }
                $unique_phone_landline = array_unique($phone_landline_array);
                //print_r("update members_address set deleted = 1 where address_type = 'home' and member_id = '".$d['id']."' and id not in (".implode(',',array_keys($unique_addresses)).") ");
                Yii::$app->db->createCommand("update members_phone set deleted = 1 where phone_type = 'Landline' and member_id = '" . $d['id'] . "' and id not in (" . implode(',', array_keys($unique_phone_landline)) . ") ")->execute();
            }
        }
        die('here');
    }

    public function actionActivePhone()
    {
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $query = Yii::$app->db->createCommand('select id from members where id in (	2337836) and platform = 1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            //for home addresses
            $query_phone = Yii::$app->db->createCommand('select * from members_phone where deleted = 0 and phone_type = "Mobile" and member_id = ' . $d['id'] . ' order by dt_entry_old desc');
            $phones = $query_phone->queryAll();
            $removed = array_shift($phones);
            if (!empty($phones)) {
                $phone_array = array();
                foreach ($phones as $key => $a) {
                    $phone_array[$a['id']] = $a['phone'];
                }
                Yii::$app->db->createCommand("update members_phone set is_current = 0 where phone_type = 'Mobile' and member_id = '" . $d['id'] . "' and id in (" . implode(',', array_keys($phone_array)) . ") ")->execute();
            }
            //for business addresses
            $query_phone_landline = Yii::$app->db->createCommand('select * from members_phone where deleted = 0 and phone_type = "Landline" and member_id = ' . $d['id'] . ' order by dt_entry_old desc');
            $phone_landline = $query_phone_landline->queryAll();
            $removed = array_shift($phone_landline);
            if (!empty($phone_landline)) {
                $phone_landline_array = array();
                foreach ($phone_landline as $key => $a) {
                    $phone_landline_array[$a['id']] = $a['phone'];
                }
                Yii::$app->db->createCommand("update members_phone set is_current = 0 where phone_type = 'Landline' and member_id = '" . $d['id'] . "' and id in (" . implode(',', array_keys($phone_landline_array)) . ") ")->execute();
            }
        }
        die('here');
    }

    public function actionAddressUpdate()
    {
        ini_set('memory_limit', '1024M');
        $query = Yii::$app->db->createCommand('select id from members where (select count(id) from members_address where is_current=1 and address_type="home" and deleted=0 and member_id=members.id)<1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            $query_address = Yii::$app->db->createCommand('select id from members_address where deleted = 0 and address_type = "home" and member_id = ' . $d['id'] . ' order by id desc');
            $query_address_result = $query_address->queryOne();
            if (!empty($query_address_result)) {
                Yii::$app->db->createCommand("update members_address set is_current = 1 where id = '" . $query_address_result['id'] . "'")->execute();
            }
        }
        $query = Yii::$app->db->createCommand('select id from members where (select count(id) from members_address where is_current=1 and address_type="business" and deleted=0 and member_id=members.id)<1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            $query_address = Yii::$app->db->createCommand('select id from members_address where deleted = 0 and address_type = "business" and member_id = ' . $d['id'] . ' order by id desc');
            $query_address_result = $query_address->queryOne();
            if (!empty($query_address_result)) {
                Yii::$app->db->createCommand("update members_address set is_current = 1 where id = '" . $query_address_result['id'] . "'")->execute();
            }
        }
        die('Updated Successfully');
    }

    public function actionPhoneUpdate()
    {
        ini_set('memory_limit', '1024M');
        /*$query = Yii::$app->db->createCommand('select id from members where (select count(id) from members_phone where is_current=1 and phone_type in("Landline","phone") and deleted=0 and member_id=members.id)<1 and platform = 1');
        $data = $query->queryAll();

        foreach ($data as $d){
            $query_phone= Yii::$app->db->createCommand('select id from members_phone where deleted = 0 and phone_type in("Landline","phone")  and member_id = '.$d['id']. ' order by id desc');
            $query_phone_result = $query_phone->queryOne();
            if(!empty($query_phone_result)) {
                Yii::$app->db->createCommand("update members_phone set is_current = 1 where id = '" . $query_phone_result['id'] . "'")->execute();
            }
        }*/
        $query = Yii::$app->db->createCommand('select id from members where (select count(id) from members_phone where is_current=1 and phone_type="Mobile" and deleted=0 and member_id=members.id)<1');
        $data = $query->queryAll();
        foreach ($data as $d) {
            $query_phone = Yii::$app->db->createCommand('select id from members_phone where deleted = 0 and phone_type = "Mobile" and member_id = ' . $d['id'] . ' order by id desc');
            $query_phone_result = $query_phone->queryOne();
            if (!empty($query_phone_result)) {
                Yii::$app->db->createCommand("update members_phone set is_current = 1 where id = '" . $query_phone_result['id'] . "'")->execute();
            }
        }
        die('Updated Successfully');
    }

    public function actionUpdateInfoPspa()
    {
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'pspa.csv';
        $file_path = Yii::getAlias('@frontend') . '/frontend/web/uploads/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {
                $loan = Loans::find()->where(['sanction_no' => $fileop[0], 'deleted' => 0])->one();
                if (!empty($loan) && $loan->project_id == 26) {
                    $detail = ProjectsDisabled::find()->where(['application_id' => $loan->application->id])->one();
                    if (empty($detail)) {
                        $detail = new ProjectsDisabled();
                        $detail->application_id = $loan->application->id;
                        $detail->assigned_to = $loan->assigned_to;
                        $detail->created_by = $loan->created_by;
                    }
                    $detail->is_khidmat_card_holder = (int)$fileop[1];
                    $detail->disability = !empty($fileop[2]) ? $fileop[2] : 'none';
                    //$detail->nature=!empty($fileop[3])?$fileop[3]:'none';
                    $detail->physical_disability = !empty($fileop[4]) ? $fileop[4] : 'none';
                    $detail->visual_disability = !empty($fileop[5]) ? $fileop[5] : 'none';
                    $detail->communicative_disability = !empty($fileop[6]) ? $fileop[6] : 'none';
                    $detail->disabilities_instruments = !empty($fileop[7]) ? $fileop[7] : 'none';
                    $detail->updated_by = 1;
                    $detail->save();
                    print_r($fileop[0]);
                }

            }
            $flag = true;
        }
    }

    public function actionPsicLoans()
    {
        $arr = '(';
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        $ch = Yii::getAlias('@frontend') . '/frontend/web/uploads/psdf_check.csv';
        $myfile = fopen($ch, "r");
        $flag = true;
        $i = 0;
        $objections = [];
        while (($fileop = fgetcsv($myfile)) !== false) {

            if ($flag) {
                if ($i == 0) {
                    $arr .= "'" . $fileop[3] . "'";
                } else {
                    $arr .= ',' . "'" . $fileop[3] . "'";
                }
            }
            $i++;
        }
        $arr .= ')';
        $sql = "SELECT members.full_name as name ,members.parentage as parentage,members.cnic as cnic,
                    loans.sanction_no as sanction_no,FROM_UNIXTIME(loans.date_disbursed) as date_disbursed,
                    loans.loan_amount
                   FROM loans INNER JOIN applications ON loans.application_id = applications.id
                   INNER JOIN members ON applications.member_id = members.id
                      WHERE loans.deleted = 0 and loans.project_id=1 and members.cnic in " . $arr;
        //WHERE loans.deleted = 0 and members.cnic in ".$arr;
// and members.cnic in ".$arr
        $header = ['Name', 'Parentage', 'CNIC', 'Sanction No', 'Disb Date', 'Amount'];
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $file_name_due_aging = 'psic_cnic_check.csv';
        $file_path_due_aging = Yii::getAlias('@frontend') . '/web/uploads/' . $file_name_due_aging;
        $fopen = fopen($file_path_due_aging, 'w');

        fputcsv($fopen, $header);
        foreach ($data as $g) {
            fputcsv($fopen, $g);
        }
    }

    public function actionLedgerReGenerateHousing()
    {
        $sanction_nos = [
            //'3117-D003-00004'
            '3133-D003-00003'
        ];

        foreach ($sanction_nos as $sanction_no) {
            $loan = Loans::findOne(['sanction_no' => $sanction_no]);
            FixesHelper::ledger_regenerate_housing($loan);
        }
    }

    public function actionLedgerReGenerateRupeeDifference()
    {
        $sanction_nos = [
            '906-D003-00002'
        ];
        foreach ($sanction_nos as $sanction_no) {
            $loan = Loans::findOne(['sanction_no' => $sanction_no]);
            FixesHelper::ledger_regenerate_housing_rupee_diff($loan);
        }
    }

    public function actionRejectLoans()
    {
        $sanction_list = ['818-D019-03878',
            '52101-D015-00980'
        ];

        foreach ($sanction_list as $sanction_no) {
            $loan = Loans::find()->where(['sanction_no' => $sanction_no])->andWhere(['in', 'status', ['pending', 'collected', 'loan completed']])->one();
            if (isset($loan) && !empty($loan)) {
                if (empty($loan->recovery)) {
                    $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();

                    $ist_tranch = $loan_tranches[0];

                    if ($ist_tranch->status > 4) {
                        //update loan
                        if ($ist_tranch->tranch_no == 1) {
                            $loan->status = 'pending';
                            $loan->date_disbursed = 0;
                            $loan->disbursed_amount = 0;
                        } else {
                            $loan->disbursed_amount = $loan_tranches[0]->tranch_amount;
                        }
                        $loan->updated_by = 0;
                        $loan->save();
                        //update loan actions if housing loan
                        if ($ist_tranch->tranch_no == 1) {

                            $loan_actions = LoanActions::find()->where(['in', 'action', ['account_verification', 'takaful']])->andWhere(['parent_id' => $loan->id])->one();
                            if (!empty($loan_actions)) {
                                $loan_actions->status = 0;
                                $loan_actions->updated_by = 0;
                                if (!$loan_actions->save()) {
                                    print_r($loan_actions->getErrors());
                                    die();
                                }
                            }
                            //account no verified status change
                        }
                        //publish entry delete
                        $publish = DisbursementDetails::find()->where(['tranche_id' => $ist_tranch->id])->andWhere(['deleted' => 0])->one();
                        if (!empty($publish)) {
                            $publish->deleted = 1;
                            $publish->status = 2;
                            $publish->updated_by = 2;
                            $publish->save();
                        }
                    }
                    //update tranche actions
                    $tranche_actions = LoanTranchesActions::find()->where(['in', 'action', ['fund_request', 'takaful', 'disbursement', 'cheque_printing']])->andWhere(['parent_id' => $ist_tranch->id])->all();
                    foreach ($tranche_actions as $l_act) {
                        $l_act->status = 0;
                        $l_act->updated_by = 0;
                        $l_act->save();
                    }
                    //update tranche
                    $ist_tranch->status = 3;
                    $ist_tranch->disbursement_id = 0;
                    $ist_tranch->fund_request_id = 0;
                    $ist_tranch->date_disbursed = 0;
                    $ist_tranch->updated_by = 0;
                    $ist_tranch->attendance_status = 'info_not_available';
                    $ist_tranch->save();
                    //delete schedules if created
                    if ($ist_tranch->tranch_no == 1) {
                        $connection = \Yii::$app->db;
                        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                        $connection->createCommand($schdl_delete)->execute();
                    }


                }
            }

        }
    }

//php yii fixes/ledger-re-generates-kpp
    public function actionLedgerReGeneratesKpp()
    {
        $sanctionsArray = [
            '13202-D024-00003'
        ];
        foreach ($sanctionsArray as $sanction){
            $loan = Loans::find()->where(['sanction_no' => $sanction])->one();
            if(!empty($loan) && $loan!=null){
                $loan_id = $loan->id;
                $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])
                    ->andWhere(['deleted' => 0])->sum('tranch_amount');
                $ledger = Schedules::find()->where(['loan_id' => $loan_id])
                    ->all();

                SchedulesHousingPre::deleteAll(['loan_id' => $loan_id]);
                foreach ($ledger as $d) {
                    if(($d->credit !=0) || ($d->charges_credit !=0)){
                        $preScheduleNew = new SchedulesHousingPre();
                        $preScheduleNew->setAttributes($d->attributes);
                        $preScheduleNew->save();
                    }else{
                        $checkDate = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", $d->due_date))));
                        $CheckLedger = Schedules::find()
                            ->where(['loan_id' => $loan_id])
                            ->andWhere(['due_date' => strtotime($checkDate)])
                            ->one();
                        if(!empty($CheckLedger) && $CheckLedger!=null && ($CheckLedger->credit !=0) || ($CheckLedger->charges_credit !=0)){
                            $preScheduleNew = new SchedulesHousingPre();
                            $preScheduleNew->setAttributes($d->attributes);
                            $preScheduleNew->save();
                        }
                    }
                }

                Schedules::deleteAll(['loan_id' => $loan_id]);
                $tranches_updated = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])->andWhere(['deleted' => 0])->all();
                foreach ($tranches_updated as $tranche) {
                    $tranche->status = 6;
                    $tranche->updated_by = 1;
                    $tranche->save();
                    $d_tranches = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->one();
                    if (!empty($d_tranches) && $d_tranches != null) {
                        $d_tranches->status = 3;
                        $d_tranches->save();
                    }
                }
                $loan = Loans::find()->where(['id' => $loan_id])->one();
                $loan->status = 'collected';
                $loan->disbursed_amount = $loan_tranche;
                $loan->updated_by = 1;
                if ($loan->save()) {
                    KamyabPakistanHelper::KppHousingLedgerReGenerate($loan);
                    echo '<-saved->';
                    echo $sanction;
                    echo '<-end->';
                }
            }
        }
    }

    public function actionLedgerReGeneratesKppSingle($id)
    {
        $loan = Loans::find()->where(['id' => $id])->one();
        if(!empty($loan) && $loan!=null){
            $loan_tranche = LoanTranches::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['!=', 'date_disbursed', 0])
                ->andWhere(['status'=> 6])
                ->andWhere(['deleted' => 0])
                ->sum('tranch_amount');

            Schedules::deleteAll(['loan_id' => $loan->id]);
//            $tranches_updated = LoanTranches::find()
//                ->where(['loan_id' => $loan->id])
//                ->andWhere(['!=', 'date_disbursed', 0])
//                ->andWhere(['status'=> 6])
//                ->andWhere(['deleted' => 0])
//                ->all();
//
//            foreach ($tranches_updated as $tranche) {
//                $tranche->status = 6;
//                $tranche->updated_by = 1;
//                $tranche->save();
//                $d_tranches = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->one();
//                if (!empty($d_tranches) && $d_tranches != null) {
//                    $d_tranches->status = 3;
//                    $d_tranches->save();
//                }
//            }

            $loan = Loans::find()->where(['id' => $loan->id])->one();
            $loan->status = 'collected';
            $loan->disbursed_amount = $loan_tranche;
            $loan->updated_by = 1;
            if ($loan->save()) {
                KamyabPakistanHelper::KppHousingLedgerReGenerate($loan);
                ///update loan expiry
                FixesHelper::update_loan_expiry($loan);
                //adjust donation
                FixesHelper::adjust_donation($loan);
            }
        }
    }
}




