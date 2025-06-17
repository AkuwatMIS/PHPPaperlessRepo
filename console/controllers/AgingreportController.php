<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;

use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\ImageHelper;
use common\components\ReportHelper;
use common\models\AgingReports;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Schedules;
use yii\console\Controller;
use Yii;
use yii\db\Exception;
use yii\data\ActiveDataProvider;

class AgingreportController extends Controller
{

    public function actionLoans()
    {
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        $sql = "SELECT id,sanction_no,loan_amount,balance, (SELECT COALESCE(sum(amount),0) from recoveries WHERE deleted = 0 and loan_id = loans.id) as recovery, status FROM `loans`  WHERE deleted = 0 and status = 'loan completed' and  loan_completed_date > 1559347199 HAVING loan_amount - recovery != balance";
        $query = Yii::$app->db->createCommand($sql);
        $data = $query->queryAll();
        $file_name = 'wrong_ledger' . '.csv';
        $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name;
        $fopen = fopen($file_path, 'w');
        $header = ['Id', 'sanction no', 'Loan Amount', 'Balance', 'Recovery', 'Status'];
        fputcsv($fopen, $header);
        foreach ($data as $g) {
            fputcsv($fopen, $g);
        }
    }

    public function actionOverdue()
    {
        $sql = "SELECT sum(amount),recoveries.source
from `recoveries` 
INNER JOIN `branches` ON `recoveries`.`branch_id` = `branches`.`id` 

 WHERE (`recoveries`.`deleted` = 0) AND (branches.deleted = 0) AND (branches.status = 1)and  receive_date >= '1530403200' and receive_date <= '1546300799' group by recoveries.source";
        $query = Yii::$app->db->createCommand($sql);
        $data = $query->queryAll();
        print_r($data);
        die();
    }

    public function actionCreate()
    {
        $model_due = new AgingReports();
        $model_due->type = 'due';
        $model_due->start_month = date('Y-m-d', strtotime('last day of previous month'));
        if (!$model_due->save()) {
            print_r($model_due->getErrors());
            die();
        }
        $model_overdue = new AgingReports();
        $model_overdue->type = 'overdue';
        $model_overdue->start_month = date('Y-m-d', strtotime('last day of previous month'));
        if (!$model_overdue->save()) {
            print_r($model_overdue->getErrors());
            die();
        }
    }

    public function actionAging()
    {
        $aging = AgingReports::find()->where(['status' => '0'])->all();

        foreach ($aging as $a) {
            $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
            //echo $date;
            //die($date);
            if ($a->type == 'due') {
                $loans = Loans::find()
                    ->select(['id', 'loans.inst_amnt', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->where(['loans.status' => 'loan completed'])
                    ->andWhere(['<=', 'loans.date_disbursed', 1546300799])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    ->having('balance != 0')
                    //->andWhere(['=','loans.project_id',4])
                    ->asArray()
                    ->all();
                /*foreach ($loans as $loan) {
                    echo "'" . $loan['sanction_no'] . "',";
                }
                die();*/
                $aging_report = \common\components\Helpers\ReportHelper::due_aging_cal($loans);
                $a->one_month = round($aging_report['one_month']);
                $a->next_three_months = round($aging_report['next_three_months']);
                $a->next_six_months = round($aging_report['next_six_months']);
                $a->next_one_year = round($aging_report['next_one_year']);
                $a->next_two_year = round($aging_report['next_two_year']);
                $a->next_three_year = round($aging_report['next_three_year']);
                $a->next_five_year = round($aging_report['next_five_year']);
                $a->total = array_sum($aging_report);
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "
            SELECT l.region_id, l.area_id, l.branch_id, l.sanction_no, l.inst_months, l.inst_amnt, l.date_disbursed,/* m.full_name,*/ l.loan_amount,
            (select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '" . $date . "') as a,
             (select COALESCE(sum(r.amount),0) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $date . "') as b,
            ((select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '" . $date . "') -
             (select COALESCE(sum(r.amount),0) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $date . "')) as od
            FROM loans l
            /* inner join applications app on app.id = l.application_id 
              inner join members m on m.id = app.member_id */
            WHERE l.status not in ('not collected','grant') having a-b > 0
            ";
                $loans = Yii::$app->db->createCommand($sql)->queryAll();
                /*print_r($loans);
                die();*/
                foreach ($loans as $loan) {
                    $loan_expiry = strtotime("+" . round($loan['inst_months']) . " months", $loan['date_disbursed']);
                    if ($loan_expiry >= $date) {
                        $months = $loan['od'] / $loan['inst_amnt'];
                        $array = AgingReports::cal_od_aging($months, $loan['inst_amnt']);
                        $a->one_month += round($array['1']);
                        $a->next_three_months += round($array['2']);
                        $a->next_six_months += round($array['3']);
                        $a->next_one_year += round($array['6']);
                        $a->next_two_year += round($array['12']);
                        $a->next_three_year += round($array['36']);
                        $a->next_five_year += round($array['72']);
                        $a->total += array_sum($array);
                    } else {
                        $months = $loan['od'] / $loan['inst_amnt'];
                        $array = AgingReports::cal_od_aging_expire_loans($months, $loan['inst_amnt'], $loan_expiry, $date);
                        $a->one_month += round($array['1']);
                        $a->next_three_months += round($array['2']);
                        $a->next_six_months += round($array['3']);
                        $a->next_one_year += round($array['6']);
                        $a->next_two_year += round($array['12']);
                        $a->next_three_year += round($array['36']);
                        $a->next_five_year += round($array['72']);
                        $a->total += array_sum($array);
                    }

                }
                $a->next_two_year += ($a->next_three_year + $a->next_five_year);
                $a->next_three_year = 0;
                $a->next_five_year = 0;
                $a->status = 1;
                print_r($a);
                die();
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionAgingCron()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $header = ['Sanction No', 'Loan Amount', 'balance', '1 to 30 days', '30 to 90 days', '90 to 180 days', 'Next One Year', 'Next Two Year', 'Next Three Year', 'Next Five Year', 'Total'];
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = Loans::find()
                    ->select(['id', 'loans.inst_amnt', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->where(['=', 'loans.status', 'collected'])
                    ->andWhere(['<=', 'loans.date_disbursed', $date])
                    //->andWhere(['<=', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','3121-D003-00007'])
                    //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
                    ->asArray()
                    ->all();
                $loans1 = Loans::find()
                    ->select(['id', 'loans.inst_amnt', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->where(['=', 'loans.status', 'loan completed'])
                    ->andWhere(['<=', 'loans.date_disbursed', $date])
                    ->andWhere(['>', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','3121-D003-00007'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);

                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details($loans, $date);

                $file_name = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                /*fclose($fopen);
                die();*/
                //overdue loans due aging
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`loan_amount`,
 @amountapproved:=(loans.loan_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance,((@amountapproved-@credit) - (@schdl_amnt-@credit)) as balance ,

FROM `loans` LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE  (`loans`.`status` not in ('not collected','grant')) and loans.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details($data, $date, true);
                $file_name = 'due_od_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                fclose($fopen);
                $a->file_name = $file_name;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`loan_amount`,
 @amountapproved:=(loans.loan_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance 

FROM `loans` LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE  (`loans`.`status` not in ('not collected','grant')) and loans.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = [];
                foreach ($data as $d) {
                    $schedule_count = floor($d['credit'] / $d['inst_amnt']);
                    $date_disbursed = date('Y-m-d', $d['date_disbursed']);
                    if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                        $date_disbursed = date('Y-m-20', $d['date_disbursed']);
                        $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
                    } else {
                        $due_date = date("Y-m-10", strtotime($date_disbursed));
                    }
                    $months = DisbursementHelper::getSchdlMonths()[$d['inst_type']];
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                    if ($d['inst_months'] == 1) {
                        $overdue_date = $due_date;
                    } else {
                        $overdue_date = date("Y-m-10", strtotime("+" . $schedule_count . " months", strtotime($due_date)));
                    }
                    $days = ($date - strtotime($overdue_date)) / 60 / 60 / 24;
                    /*print_r($d);
                    print_r($date_disbursed.',');
                    print_r($due_date.',');
                    print_r($overdue_date.',');
                    print_r($schedule_count.',');
                    print_r($days);
                    die();*/
                    if ($days > 1 && $days <= 30) {
                        $d['overdue_aging'] = '1 to 30 Days';
                    } else if ($days > 30 && $days <= 60) {
                        $d['overdue_aging'] = '30  to 60 days';
                    } else if ($days > 60 && $days <= 90) {
                        $d['overdue_aging'] = '60  to 90 days';
                    } else if ($days > 90 && $days <= 180) {
                        $d['overdue_aging'] = '90  to 180 days';
                    } else if ($days > 180 && $days <= 545) {
                        $d['overdue_aging'] = 'Next One Year';
                    } else if ($days > 545 && $days <= 1315) {
                        $d['overdue_aging'] = 'Next Two Years';
                    } else if ($days > 1315 && $days <= 2410) {
                        $d['overdue_aging'] = 'Next Three Years';
                    } else if ($days > 2410 && $days <= 4235) {
                        $d['overdue_aging'] = 'Next Five Years';
//                    if ($days > 1 && $days <= 30) {
//                        $d['overdue_aging'] = '1 to 30 Days';
//                    } else if ($days > 30 && $days <= 60) {
//                        $d['overdue_aging'] = '31  to 60 days';
//                    } else if ($days > 60 && $days <= 90) {
//                        $d['overdue_aging'] = '61  to 90 days';
//                    } else if ($days > 90 && $days <= 120) {
//                        $d['overdue_aging'] = '91  to 120 days';
//                    } else if ($days > 120 && $days <= 150) {
//                        $d['overdue_aging'] = '121  to 150 days';
//                    } else if ($days > 150 && $days <= 180) {
//                        $d['overdue_aging'] = '151  to 180 days';
//                    } else if ($days > 180 && $days <= 210) {
//                        $d['overdue_aging'] = '181  to 210 days';
//                    } else if ($days > 210 && $days <= 240) {
//                        $d['overdue_aging'] = '211  to 240 days';
//                    } else if ($days > 240 && $days <= 270) {
//                        $d['overdue_aging'] = '241  to 270 days';
//                    } else if ($days > 270 && $days <= 300) {
//                        $d['overdue_aging'] = '271  to 300 days';
//                    } else if ($days > 300 && $days <= 330) {
//                        $d['overdue_aging'] = '301  to 330 days';
//                    } else if ($days > 330 && $days <= 360) {
//                        $d['overdue_aging'] = '331  to 360 days';
//                    } else if ($days > 360 ) {
//                        $d['overdue_aging'] = 'Above 360 days';
                    } else {
                        $d['overdue_aging'] = $days;
                    }
                    $new_data[] = $d;
                }
                $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance', 'Overdue Aging'];
                $file_name = 'overdue_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    $arr = [];
                    $arr[] = $g['region'];
                    $arr[] = $g['area'];
                    $arr[] = $g['branch'];
                    $arr[] = $g['branch_code'];
                    $arr[] = $g['project'];
                    $arr[] = $g['product'];
                    $arr[] = $g['sanction_no'];
                    $arr[] = $g['inst_amnt'];
                    $arr[] = $g['inst_months'];
                    $arr[] = $g['name'];
                    $arr[] = $g['parentage'];
                    $arr[] = $g['gender'];
                    $arr[] = date('Y-M-d', $g['date_disbursed']);
                    $arr[] = $g['loan_amount'];
                    $arr[] = $g['overdue_amount'];
                    $arr[] = $g['outstanding_balance'];
                    $arr[] = $g['overdue_aging'];
                    fputcsv($fopen, $arr);
                }
                fclose($fopen);
                $a->file_name = $file_name;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionAgingCronUpdated()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due') {
                $header = ['Region', 'Area', 'Branch', 'Project', 'Name', 'CNIC', 'Parentage', 'Gender', 'Other Name', 'Other CNIC', 'District', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'balance', 'Recovery', 'ChequeNo', 'LoanExpiry', 'Inst Amount', 'Inst no', '1 to 30 days', '30 to 90 days', '90 to 180 days', 'Next One Year', 'Next Two Year', 'Next Three Year', 'Next Five Year', 'Total', 'Activity Name', 'Product','Age'];

                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = LoanTranches::find()
                    ->select(['(select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) recovery_sum',
                        'branches.name branch_name', 'areas.name as area_name', 'regions.name as region_name', 'projects.name as project_name', 'loans.loan_expiry', 'loan_tranches.cheque_no', 'members.gender', 'loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'applications.name_of_other as other_name', 'applications.other_cnic as other_cnic', 'districts.name as district', 'loans.inst_type', 'loan_tranches.date_disbursed', 'loans.disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '((select coalesce(sum(loan_tranches.tranch_amount),0) from loan_tranches where loan_tranches.loan_id=loans.id and loan_tranches.date_disbursed <= "' . $date . '" and loan_tranches.status=6) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance', 'activities.name as activity_name', 'products.name as product_name','FLOOR((loans.`date_disbursed` - `members`.`dob`)/31536000) AS age' ])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('inner join', 'products', 'products.id=loans.product_id')
                    ->join('inner join', 'activities', 'activities.id=loans.activity_id')
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('inner join', 'areas', 'areas.id=loans.area_id')
                    ->join('inner join', 'regions', 'regions.id=loans.region_id')
                    ->join('inner join', 'projects', 'projects.id=loans.project_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'collected'])
                    ->andWhere(['=', 'loan_tranches.status', '6'])
                    ->andWhere(['<=', 'loan_tranches.date_disbursed', $date])
                    //->andWhere(['<=', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loan_tranches.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','3121-D003-00007'])
                    //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
                    ->asArray()
                    ->all();

                $loans1 = LoanTranches::find()
                    ->select(['(select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) recovery_sum', 'activities.name as activity_name',
                        'branches.name branch_name', 'areas.name as area_name', 'regions.name as region_name', 'projects.name as project_name', 'loans.loan_expiry', 'loan_tranches.cheque_no', 'members.gender',
                        'loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'applications.name_of_other as other_name', 'applications.other_cnic as other_cnic', 'districts.name as district', 'loans.inst_type', 'loan_tranches.date_disbursed', 'loans.disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '((select coalesce(sum(loan_tranches.tranch_amount),0) from loan_tranches where loan_tranches.loan_id=loans.id and loan_tranches.date_disbursed <= "' . $date . '" and loan_tranches.status=6) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance', 'activities.name as activity_name', 'products.name as product_name','FLOOR((loans.`date_disbursed` - `members`.`dob`)/31536000) AS age'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('inner join', 'products', 'products.id=loans.product_id')
                    ->join('inner join', 'activities', 'activities.id=loans.activity_id')
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('inner join', 'areas', 'areas.id=loans.area_id')
                    ->join('inner join', 'regions', 'regions.id=loans.region_id')
                    ->join('inner join', 'projects', 'projects.id=loans.project_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'loan completed'])
                    ->andWhere(['=', 'loan_tranches.status', '6'])
                    ->andWhere(['<=', 'loan_tranches.date_disbursed', $date])
                    ->andWhere(['>', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loan_tranches.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','1750-D001-00008'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);

                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated($loans, $date);

                $file_name_due_aging = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path_due_aging = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name_due_aging;
                $fopen = fopen($file_path_due_aging, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                /*fclose($fopen);
                die();*/
                //overdue loans due aging
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,districts.name as district,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`,`members`.``.`cnic` AS `cnic`, members.gender ,`loan_tranches`.`date_disbursed`, `loans`.`disbursed_amount`,
 @amountapproved:=(loans.disbursed_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance,((@amountapproved-@credit) - (@schdl_amnt-@credit)) as balance, loans.loan_expiry

FROM `loan_tranches`  
LEFT JOIN `loans` ON `loan_tranches`.`loan_id` = `loans`.`id`
LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
 INNER JOIN `districts` ON `branches`.`district_id` = `districts`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE (`loans`.`status` not in ('not collected','rejected','grant'))and loan_tranches.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";
                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated($data, $date, true);
                $file_name = 'due_od_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                fclose($fopen);

                $file_name_zip = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.zip';
                $file_path_zip = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name_zip;

                $zip = new \ZipArchive();
                if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
                    // Add files to the zip file
                    $zip->addFile($file_path_due_aging, $file_name_due_aging);
                    $zip->addFile($file_path, $file_name);
                    // All files are added, so close the zip file.
                    $zip->close();
                }
                $a->file_name = $file_name_zip;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`disbursed_amount`,
 @amountapproved:=(loans.disbursed_amount) as loan_amount,
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance, loans.loan_expiry,FLOOR((loans.`date_disbursed` - `members`.`dob`)/31536000) AS age

FROM  `loans`
LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE (`loans`.`status` not in ('not collected','rejected','grant')) AND loans.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0 ";

//                FROM `loan_tranches`
//LEFT JOIN `loans` ON `loan_tranches`.`loan_id` = `loans`.`id`

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = [];
                foreach ($data as $d) {
                    $schedule_count = floor($d['credit'] / $d['inst_amnt']);
                    $date_disbursed = date('Y-m-d', $d['date_disbursed']);

                    if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                        $date_disbursed = date('Y-m-20', $d['date_disbursed']);
                        $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
                    } else {
                        $due_date = date("Y-m-10", strtotime($date_disbursed));
                    }

                    $months = DisbursementHelper::getSchdlMonths()[$d['inst_type']];
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                    if ($d['inst_months'] == 1) {
                        $overdue_date = $due_date;
                    } else {
                        $overdue_date = date("Y-m-10", strtotime("+" . $schedule_count . " months", strtotime($due_date)));
                    }
                    $days = ($date - strtotime($overdue_date)) / 60 / 60 / 24;

                    $thisDate = strtotime(date('Y-m-10', strtotime($a->start_month)));

                    $overdue_amount = $d['overdue_amount'];

                    $ReportDate = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                    $loanExpiryDate = strtotime(date('Y-m-d 23:59:59', $d['loan_expiry']));

                    $date1=date_create(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                    $date2=date_create(date('Y-m-d 23:59:59', $d['loan_expiry']));

                    if($ReportDate > $loanExpiryDate){
                        $lastSchedule=strtotime(date('Y-m-10', $d['loan_expiry']));
                        $schedule_amount = Schedules::find()->where(['loan_id' => $d['id']])->andWhere(['due_date' => $lastSchedule])->select(['schdl_amnt'])->one();
                    }else{
                        $schedule_amount = Schedules::find()->where(['loan_id' => $d['id']])->andWhere(['due_date' => $thisDate])->select(['schdl_amnt'])->one();
                    }

                    if (!empty($schedule_amount) && $schedule_amount != null ) {
                        $diff=date_diff($date1,$date2);

                        if($ReportDate < $loanExpiryDate){
                            $numberCount = $overdue_amount / $schedule_amount->schdl_amnt;
                        }else{
                            $diffMonths = ceil($diff->days/30);
                            $calCount = $overdue_amount / $d['inst_amnt'];
                            $numberCount = $calCount+$diffMonths;
                        }

                        if ($numberCount <= 1) {
                            $d['overdue_aging'] = '1 to 30 Days';
                        } elseif ($numberCount > 1 && $numberCount <= 2) {
                            $d['overdue_aging'] = '30  to 60 days';
                        } elseif ($numberCount > 2 && $numberCount <= 3) {
                            $d['overdue_aging'] = '60  to 90 days';
                        } elseif ($numberCount > 3 && $numberCount <= 6) {
                            $d['overdue_aging'] = '90  to 180 days';
                        } elseif ($numberCount > 6 && $numberCount <= 18) {
                            $d['overdue_aging'] = 'Next One Year';
                        } elseif ($numberCount > 18 && $numberCount <= 44) {
                            $d['overdue_aging'] = 'Next Two Years';
                        } elseif ($numberCount > 18 && $numberCount <= 80) {
                            $d['overdue_aging'] = 'Next Three Years';
                        } elseif ($numberCount > 18 && $numberCount <= 141) {
                            $d['overdue_aging'] = 'Next Five Years';
//                        if ($days > 1 && $days <= 30) {
//                            $d['overdue_aging'] = '1 to 30 Days';
//                        } else if ($days > 30 && $days <= 60) {
//                            $d['overdue_aging'] = '31  to 60 days';
//                        } else if ($days > 60 && $days <= 90) {
//                            $d['overdue_aging'] = '61  to 90 days';
//                        } else if ($days > 90 && $days <= 120) {
//                            $d['overdue_aging'] = '91  to 120 days';
//                        } else if ($days > 120 && $days <= 150) {
//                            $d['overdue_aging'] = '121  to 150 days';
//                        } else if ($days > 150 && $days <= 180) {
//                            $d['overdue_aging'] = '151  to 180 days';
//                        } else if ($days > 180 && $days <= 210) {
//                            $d['overdue_aging'] = '181  to 210 days';
//                        } else if ($days > 210 && $days <= 240) {
//                            $d['overdue_aging'] = '211  to 240 days';
//                        } else if ($days > 240 && $days <= 270) {
//                            $d['overdue_aging'] = '241  to 270 days';
//                        } else if ($days > 270 && $days <= 300) {
//                            $d['overdue_aging'] = '271  to 300 days';
//                        } else if ($days > 300 && $days <= 330) {
//                            $d['overdue_aging'] = '301  to 330 days';
//                        } else if ($days > 330 && $days <= 360) {
//                            $d['overdue_aging'] = '331  to 360 days';
//                        } else if ($days > 360 ) {
//                            $d['overdue_aging'] = 'Above 360 days';
                        } else {
                            $d['overdue_aging'] = $days;
                        }
                    } else {
                        $d['overdue_aging'] = 0;
                    }
                    $new_data[] = $d;
                    /*print_r($d);
                    print_r($date_disbursed.',');
                    print_r($due_date.',');
                    print_r($overdue_date.',');
                    print_r($schedule_count.',');
                    print_r($days);
                    die();*/
//                    if ($days > 1 && $days <= 30) {
//                        $d['overdue_aging'] = '1 to 30 Days';
//                    } else if ($days > 30 && $days <= 60) {
//                        $d['overdue_aging'] = '30  to 60 days';
//                    } else if ($days > 60 && $days <= 90) {
//                        $d['overdue_aging'] = '60  to 90 days';
//                    } else if ($days > 90 && $days <= 180) {
//                        $d['overdue_aging'] = '90  to 180 days';
//                    } else if ($days > 180 && $days <= 545) {
//                        $d['overdue_aging'] = 'Next One Year';
//                    } else if ($days > 545 && $days <= 1315) {
//                        $d['overdue_aging'] = 'Next Two Years';
//                    } else if ($days > 1315 && $days <= 2410) {
//                        $d['overdue_aging'] = 'Next Three Years';
//                    } else if ($days > 2410 && $days <= 4235) {
//                        $d['overdue_aging'] = 'Next Five Years';
//                    } else {
//                        $d['overdue_aging'] = $days;
//                    }
//                    $new_data[] = $d;
                }
                $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance', 'Overdue Aging','Age'];
                $file_name = 'overdue_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    $arr = [];
                    $arr[] = $g['region'];
                    $arr[] = $g['area'];
                    $arr[] = $g['branch'];
                    $arr[] = $g['branch_code'];
                    $arr[] = $g['project'];
                    $arr[] = $g['product'];
                    $arr[] = $g['sanction_no'];
                    $arr[] = $g['inst_amnt'];
                    $arr[] = $g['inst_months'];
                    $arr[] = $g['name'];
                    $arr[] = $g['parentage'];
                    $arr[] = $g['gender'];
                    $arr[] = date('Y-M-d', $g['date_disbursed']);
                    $arr[] = $g['loan_amount'];
                    $arr[] = $g['overdue_amount'];
                    $arr[] = $g['outstanding_balance'];
                    $arr[] = $g['overdue_aging'];
                    $arr[] = $g['age'];
                    fputcsv($fopen, $arr);
                }
                fclose($fopen);
                $a->file_name = $file_name;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionAgingCronUpdatedTranche()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due') {
                $header = ['Name', 'CNIC', 'Parentage', 'Other Name', 'Other CNIC', 'District', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'balance', '1 to 30 days', '30 to 90 days', '90 to 180 days', 'Next One Year', 'Next Two Year', 'Next Three Year', 'Next Five Year', 'Total'];

                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = LoanTranches::find()
                    ->select(['loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'applications.name_of_other as other_name', 'applications.other_cnic as other_cnic', 'districts.name as district', 'loans.inst_type', 'loan_tranches.date_disbursed', 'coalesce(sum(loan_tranches.tranch_amount),0) as disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(sum(loan_tranches.tranch_amount),0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'collected'])
                    ->andWhere(['=', 'loan_tranches.status', '6'])
                    ->andWhere(['<=', 'loan_tranches.date_disbursed', $date])
                    //->andWhere(['<=', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loan_tranches.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    ->groupBy('loan_tranches.loan_id')
                    //->andWhere(['=','loans.sanction_no','3007-D002-05579'])
                    //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
                    ->asArray()
                    ->all();
                $loans1 = LoanTranches::find()
                    ->select(['loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'applications.name_of_other as other_name', 'applications.other_cnic as other_cnic', 'districts.name as district', 'loans.inst_type', 'loan_tranches.date_disbursed', 'coalesce(sum(loan_tranches.tranch_amount),0) as disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(sum(loan_tranches.tranch_amount),0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'loan completed'])
                    ->andWhere(['=', 'loan_tranches.status', '6'])
                    ->andWhere(['<=', 'loan_tranches.date_disbursed', $date])
                    ->andWhere(['>', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loan_tranches.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    ->groupBy('loan_tranches.loan_id')
                    //->andWhere(['=','loans.sanction_no','51801-D015-0011'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);
                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated($loans, $date);
                /*print_r($loans);
                print_r($new_data);
                die();*/

                $file_name_due_aging = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path_due_aging = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name_due_aging;
                $fopen = fopen($file_path_due_aging, 'w');
                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                /*fclose($fopen);
                die();*/
                //overdue loans due aging
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,districts.name as district,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`,`members`.``.`cnic` AS `cnic`, members.gender ,`loan_tranches`.`date_disbursed`, `loans`.`disbursed_amount`,
 @amountapproved:=(loans.disbursed_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance,((@amountapproved-@credit) - (@schdl_amnt-@credit)) as balance 

FROM `loan_tranches`  LEFT JOIN `loans` ON `loan_tranches`.`loan_id` = `loans`.`id`  
LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
 INNER JOIN `districts` ON `branches`.`district_id` = `districts`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id`
WHERE (`loans`.`status` not in ('not collected','rejected','grant')) and loan_tranches.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";
                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated($data, $date, true);
                $file_name = 'due_od_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                fclose($fopen);
                $file_name_zip = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.zip';
                $file_path_zip = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name_zip;

                $zip = new \ZipArchive();
                if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
                    // Add files to the zip file
                    $zip->addFile($file_path_due_aging, $file_name_due_aging);
                    $zip->addFile($file_path, $file_name);
                    // All files are added, so close the zip file.
                    $zip->close();
                }
                $a->file_name = $file_name_zip;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`disbursed_amount`,
 @amountapproved:=(loans.disbursed_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance 
FROM `loan_tranches`  
LEFT JOIN `loans` ON `loan_tranches`.`loan_id` = `loans`.`id`  
 LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE (`loans`.`status` not in ('not collected','rejected','grant')) and loan_tranches.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = [];
                foreach ($data as $d) {
                    $schedule_count = floor($d['credit'] / $d['inst_amnt']);
                    $date_disbursed = date('Y-m-d', $d['date_disbursed']);
                    if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                        $date_disbursed = date('Y-m-20', $d['date_disbursed']);
                        $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
                    } else {
                        $due_date = date("Y-m-10", strtotime($date_disbursed));
                    }
                    $months = DisbursementHelper::getSchdlMonths()[$d['inst_type']];
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                    if ($d['inst_months'] == 1) {
                        $overdue_date = $due_date;
                    } else {
                        $overdue_date = date("Y-m-10", strtotime("+" . $schedule_count . " months", strtotime($due_date)));
                    }
                    $days = ($date - strtotime($overdue_date)) / 60 / 60 / 24;
                    /*print_r($d);
                    print_r($date_disbursed.',');
                    print_r($due_date.',');
                    print_r($overdue_date.',');
                    print_r($schedule_count.',');
                    print_r($days);
                    die();*/
                    if ($days > 1 && $days <= 30) {
                        $d['overdue_aging'] = '1 to 30 Days';
                    } else if ($days > 30 && $days <= 60) {
                        $d['overdue_aging'] = '30  to 60 days';
                    } else if ($days > 60 && $days <= 90) {
                        $d['overdue_aging'] = '60  to 90 days';
                    } else if ($days > 90 && $days <= 180) {
                        $d['overdue_aging'] = '90  to 180 days';
                    } else if ($days > 180 && $days <= 545) {
                        $d['overdue_aging'] = 'Next One Year';
                    } else if ($days > 545 && $days <= 1315) {
                        $d['overdue_aging'] = 'Next Two Years';
                    } else if ($days > 1315 && $days <= 2410) {
                        $d['overdue_aging'] = 'Next Three Years';
                    } else if ($days > 2410 && $days <= 4235) {
                        $d['overdue_aging'] = 'Next Five Years';
//                    if ($days > 1 && $days <= 30) {
//                        $d['overdue_aging'] = '1 to 30 Days';
//                    } else if ($days > 30 && $days <= 60) {
//                        $d['overdue_aging'] = '31  to 60 days';
//                    } else if ($days > 60 && $days <= 90) {
//                        $d['overdue_aging'] = '61  to 90 days';
//                    } else if ($days > 90 && $days <= 120) {
//                        $d['overdue_aging'] = '91  to 120 days';
//                    } else if ($days > 120 && $days <= 150) {
//                        $d['overdue_aging'] = '121  to 150 days';
//                    } else if ($days > 150 && $days <= 180) {
//                        $d['overdue_aging'] = '151  to 180 days';
//                    } else if ($days > 180 && $days <= 210) {
//                        $d['overdue_aging'] = '181  to 210 days';
//                    } else if ($days > 210 && $days <= 240) {
//                        $d['overdue_aging'] = '211  to 240 days';
//                    } else if ($days > 240 && $days <= 270) {
//                        $d['overdue_aging'] = '241  to 270 days';
//                    } else if ($days > 270 && $days <= 300) {
//                        $d['overdue_aging'] = '271  to 300 days';
//                    } else if ($days > 300 && $days <= 330) {
//                        $d['overdue_aging'] = '301  to 330 days';
//                    } else if ($days > 330 && $days <= 360) {
//                        $d['overdue_aging'] = '331  to 360 days';
//                    } else if ($days > 360 ) {
//                        $d['overdue_aging'] = 'Above 360 days';
                    } else {
                        $d['overdue_aging'] = $days;
                    }
                    $new_data[] = $d;
                }
                $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance', 'Overdue Aging'];
                $file_name = 'overdue_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    $arr = [];
                    $arr[] = $g['region'];
                    $arr[] = $g['area'];
                    $arr[] = $g['branch'];
                    $arr[] = $g['branch_code'];
                    $arr[] = $g['project'];
                    $arr[] = $g['product'];
                    $arr[] = $g['sanction_no'];
                    $arr[] = $g['inst_amnt'];
                    $arr[] = $g['inst_months'];
                    $arr[] = $g['name'];
                    $arr[] = $g['parentage'];
                    $arr[] = $g['gender'];
                    $arr[] = date('Y-M-d', $g['date_disbursed']);
                    $arr[] = $g['loan_amount'];
                    $arr[] = $g['overdue_amount'];
                    $arr[] = $g['outstanding_balance'];
                    $arr[] = $g['overdue_aging'];
                    fputcsv($fopen, $arr);
                }
                fclose($fopen);
                $a->file_name = $file_name;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionAgingCronExtAuditor()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due') {
                $header = ['Name', 'CNIC', 'Parentage', 'District', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'balance', 'Next One Year', 'One to Three Year', 'Three to Five Year', 'More Than Five Year', 'Total'];

                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = Loans::find()
                    ->select(['loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'districts.name as district', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'collected'])
                    ->andWhere(['<=', 'loans.date_disbursed', $date])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','3007-D002-05579'])
                    //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
                    ->asArray()
                    ->all();
                $loans1 = Loans::find()
                    ->select(['loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'districts.name as district', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('left join', 'districts', 'districts.id=branches.district_id')
                    ->where(['=', 'loans.status', 'loan completed'])
                    ->andWhere(['<=', 'loans.date_disbursed', $date])
                    ->andWhere(['>', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','51801-D015-0011'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);

                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_ext_auditor($loans, $date);
                /*print_r($loans);
                print_r($new_data);
                die();*/

                $file_name_due_aging = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path_due_aging = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name_due_aging;
                $fopen = fopen($file_path_due_aging, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }

                $a->file_name = $file_name_due_aging;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`loan_amount`,
 @amountapproved:=(loans.loan_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance 

FROM `loans` LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE (`loans`.`status` not in ('not collected','grant')) and loans.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $new_data = [];
                foreach ($data as $d) {
                    $schedule_count = floor($d['credit'] / $d['inst_amnt']);
                    $date_disbursed = date('Y-m-d', $d['date_disbursed']);
                    if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                        $date_disbursed = date('Y-m-20', $d['date_disbursed']);
                        $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
                    } else {
                        $due_date = date("Y-m-10", strtotime($date_disbursed));
                    }
                    $months = DisbursementHelper::getSchdlMonths()[$d['inst_type']];
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                    if ($d['inst_months'] == 1) {
                        $overdue_date = $due_date;
                    } else {
                        $overdue_date = date("Y-m-10", strtotime("+" . $schedule_count . " months", strtotime($due_date)));
                    }
                    $days = ($date - strtotime($overdue_date)) / 60 / 60 / 24;
                    /*print_r($d);
                    print_r($date_disbursed.',');
                    print_r($due_date.',');
                    print_r($overdue_date.',');
                    print_r($schedule_count.',');
                    print_r($days);
                    die();*/
                    if ($days > 1 && $days <= 365) {
                        $d['overdue_aging'] = 'Next One Year';
                    } else if ($days > 365 && $days <= 1095) {
                        $d['overdue_aging'] = 'One to Three Year';
                    } else if ($days > 1095 && $days <= 1460) {
                        $d['overdue_aging'] = 'Three to Five Year';
                    } else if ($days > 1460) {
                        $d['overdue_aging'] = 'More Than Five Year';
                    } else {
                        $d['overdue_aging'] = $days;
                    }
                    $new_data[] = $d;
                }
                $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance', 'Overdue Aging'];
                $file_name = 'overdue_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name;
                $fopen = fopen($file_path, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    $arr = [];
                    $arr[] = $g['region'];
                    $arr[] = $g['area'];
                    $arr[] = $g['branch'];
                    $arr[] = $g['branch_code'];
                    $arr[] = $g['project'];
                    $arr[] = $g['product'];
                    $arr[] = $g['sanction_no'];
                    $arr[] = $g['inst_amnt'];
                    $arr[] = $g['inst_months'];
                    $arr[] = $g['name'];
                    $arr[] = $g['parentage'];
                    $arr[] = $g['gender'];
                    $arr[] = date('Y-M-d', $g['date_disbursed']);
                    $arr[] = $g['loan_amount'];
                    $arr[] = $g['overdue_amount'];
                    $arr[] = $g['outstanding_balance'];
                    $arr[] = $g['overdue_aging'];
                    fputcsv($fopen, $arr);
                }
                fclose($fopen);
                $a->file_name = $file_name;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionAgingCronPortfolio()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $header = ['Name', 'CNIC', 'Parentage', 'Gender', 'District', 'Branch Code', 'Project', 'Product', 'Purpose', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'No of Installments', 'Installment Amount', 'Inst Type', 'Expiry Date', 'Last Recovery Month', 'Last Recovery Amount', 'No of Installements Recovered', 'balance'];
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = Loans::find()
                    ->select(['loans.id', 'branches.code', 'loans.loan_expiry', 'projects.name as project', 'products.name as product', 'activities.name as purpose', 'loans.inst_amnt', 'members.full_name as name', 'members.gender as gender', 'members.parentage as parentage', 'members.cnic as cnic', 'districts.name as district', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance',
                        '(select r.receive_date from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0 order by receive_date desc limit 1) as last_rec_date',
                        '(select r.amount from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0 order by receive_date desc limit 1) as last_rec_amount',
                        '(select count(r.id) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) as recovery_no_of_istallments'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('inner join', 'districts', 'districts.id=branches.district_id')
                    ->join('inner join', 'projects', 'projects.id=loans.project_id')
                    ->join('inner join', 'products', 'products.id=applications.product_id')
                    ->join('left join', 'activities', 'activities.id=applications.activity_id')
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
                    ->select(['loans.id', 'branches.code', 'loans.loan_expiry', 'projects.name as project', 'products.name as product', 'activities.name as purpose', 'loans.inst_amnt', 'members.gender as gender', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'districts.name as district', 'loans.inst_type', 'loans.date_disbursed', 'loans.loan_amount', 'loans.inst_months', 'loans.sanction_no',
                        '(coalesce(loans.loan_amount,0) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance',
                        '(select r.receive_date from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0 order by receive_date desc limit 1) as last_rec_date',
                        '(select r.amount from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0 order by receive_date desc limit 1) as last_rec_amount',
                        '(select count(r.id) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) as recovery_no_of_istallments'])
                    //->where(['!=','loans.status','not collected'])
                    ->join('inner join', 'applications', 'applications.id=loans.application_id')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->join('inner join', 'branches', 'branches.id=loans.branch_id')
                    ->join('inner join', 'districts', 'districts.id=branches.district_id')
                    ->join('inner join', 'projects', 'projects.id=loans.project_id')
                    ->join('inner join', 'products', 'products.id=applications.product_id')
                    ->join('left join', 'activities', 'activities.id=applications.activity_id')
                    ->where(['=', 'loans.status', 'loan completed'])
                    ->andWhere(['<=', 'loans.date_disbursed', $date])
                    ->andWhere(['>', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loans.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','51801-D015-0011'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);

                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_detail($loans, $date);
                /*print_r($loans);
                print_r($new_data);
                die();*/

                $file_name_due_aging = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path_due_aging = Yii::getAlias('@frontend') . '/frontend/web' . '/overdue_report/' . $file_name_due_aging;
                $fopen = fopen($file_path_due_aging, 'w');

                fputcsv($fopen, $header);
                foreach ($new_data as $g) {
                    fputcsv($fopen, $g);
                }
                /*fclose($fopen);
                die();*/
                //overdue loans due aging

                $a->file_name = $file_name_due_aging;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            }
        }
    }

    public function actionCal()
    {
        $date = strtotime(date("Y-m-d 23:59:59", strtotime("2019-03-31")));
        $sql = "SELECT loans.branch_id,b.code,b.name as branch,count(loans.loan_amount) as loans,sum(loans.loan_amount) as loan_amount,
                (select COALESCE(sum(recoveries.amount), 0) from recoveries inner join loans l on l.id = recoveries.loan_id inner join applications a on a.id = l.application_id where (recoveries.branch_id = loans.branch_id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit
FROM `loans` INNER JOIN applications app on loans.application_id = app.id INNER JOIN members m on m.id = app.member_id   inner join branches b on b.id = loans.branch_id
WHERE  b.id = '17' and (`loans`.`status` not in ('not collected','grant')) and loans.deleted = 0 and loans.date_disbursed > 0 and  loans.date_disbursed <= '" . $date . "' group by loans.branch_id";
        $data1 = Yii::$app->db->createCommand($sql)->queryAll();
        print_r($data1);
        die();
        $header = ['branch_id', 'Code', 'Branch', 'Loans', 'Loan Amount', 'Credit'];
        $file_name = 'branch_data' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $file_path = Yii::getAlias('@frontend') . '/web' . '/overdue_report/' . $file_name;
        $fopen = fopen($file_path, 'w');

        fputcsv($fopen, $header);
        foreach ($data1 as $g) {
            $arr = [];
            $arr[] = $g['branch_id'];
            $arr[] = $g['code'];
            $arr[] = $g['branch'];
            $arr[] = $g['loans'];
            $arr[] = $g['loan_amount'];
            $arr[] = $g['credit'];
            fputcsv($fopen, $arr);
        }
        fclose($fopen);
        die();
        $collected = array('count' => 0, 'amount' => 0, 'credit' => 0);
        $i = 1;
        foreach ($data1 as $d) {
            $collected['count'] += 1;
            $collected['amount'] += $d['loan_amount'];
            $collected['credit'] += $d['credit'];
            $i++;
        }

        $sql = "SELECT loans.branch_id,b.code,b.name as branch,count(loans.loan_amount) as loans,sum(loans.loan_amount) as loan_amount,
                (select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.branch_id = loans.branch_id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit
FROM `loans` inner join branches b on b.id = loans.branch_id
WHERE  (`loans`.`status` = 'loan completed') and loans.loan_completed_date > $date and loans.deleted = 0 and loans.date_disbursed > 0 and  loans.date_disbursed <= '" . $date . "' group by loans.branch_id";
        $data2 = Yii::$app->db->createCommand($sql)->queryAll();
        $file_name = 'branch_data_completed' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $file_path = Yii::getAlias('@frontend') . '/web' . '/overdue_report/' . $file_name;
        $fopen = fopen($file_path, 'w');

        fputcsv($fopen, $header);
        foreach ($data2 as $g) {
            $arr = [];
            $arr[] = $g['branch_id'];
            $arr[] = $g['code'];
            $arr[] = $g['branch'];
            $arr[] = $g['loans'];
            $arr[] = $g['loan_amount'];
            $arr[] = $g['credit'];
            fputcsv($fopen, $arr);
        }
        fclose($fopen);

        //print_r($data1);
        die('done');
        $completed = array('count' => 0, 'amount' => 0, 'credit' => 0);
        $j = 1;
        foreach ($data2 as $d) {
            $completed['count'] += 1;
            $completed['amount'] += $d['loan_amount'];
            $completed['credit'] += $d['credit'];
            $j++;
        }
        print_r($collected);
        print_r($completed);
        die();
    }

}