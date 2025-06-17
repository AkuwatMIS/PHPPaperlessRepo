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
use common\models\Recoveries;
use common\models\Schedules;
use yii\console\Controller;
use Yii;
use yii\db\Exception;
use yii\data\ActiveDataProvider;

class AgingCustomReportController extends Controller
{
    public function actionAgingCronUpdated()
    {
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $aging = AgingReports::find()->where(['status' => '0'])->all();
        foreach ($aging as $a) {
            if ($a->type == 'due_acc') {
                $header = ['Region', 'Area', 'Branch', 'Project', 'Name', 'CNIC', 'Parentage', 'Gender', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'balance', 'Recovery', 'ChequeNo', 'LoanExpiry', 'Inst Amount', 'Inst no', 'Activity Name', 'Product'];

                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $loans = LoanTranches::find()
                    ->select(['(select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) recovery_sum',
                        'branches.name branch_name', 'areas.name as area_name', 'regions.name as region_name', 'projects.name as project_name', 'loans.loan_expiry', 'loan_tranches.cheque_no', 'members.gender', 'loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'loans.inst_type', 'loan_tranches.date_disbursed', 'loans.disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '((select coalesce(sum(loan_tranches.tranch_amount),0) from loan_tranches where loan_tranches.loan_id=loans.id and loan_tranches.date_disbursed <= "' . $date . '" and loan_tranches.status=6) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance', 'activities.name as activity_name', 'products.name as product_name'])
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
                    ->where(['=', 'loans.status', 'collected'])
                    ->andWhere(['=', 'loan_tranches.status', '6'])
                    ->andWhere(['<=', 'loan_tranches.date_disbursed', $date])
                    //->andWhere(['<=', 'loans.loan_completed_date', $date])
                    ->andWhere(['>', 'loan_tranches.date_disbursed', 0])
                    ->andWhere(['<=', 'loans.deleted', 0])
                    //->andWhere(['=','loans.sanction_no','3007-D002-05579'])
                    //->andWhere(['=','loans.sanction_no','16104-D021-01558'])
                    ->asArray()
                    ->all();

                $loans1 = LoanTranches::find()
                    ->select(['(select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0) recovery_sum', 'activities.name as activity_name',
                        'branches.name branch_name', 'areas.name as area_name', 'regions.name as region_name', 'projects.name as project_name', 'loans.loan_expiry', 'loan_tranches.cheque_no', 'members.gender',
                        'loans.id', 'loans.inst_amnt', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'loans.inst_type', 'loan_tranches.date_disbursed', 'loans.disbursed_amount', 'loans.inst_months', 'loans.sanction_no',
                        '((select coalesce(sum(loan_tranches.tranch_amount),0) from loan_tranches where loan_tranches.loan_id=loans.id and loan_tranches.date_disbursed <= "' . $date . '" and loan_tranches.status=6) - (select coalesce(sum(amount),0) from recoveries r where r.receive_date <= "' . $date . '" and r.loan_id = loans.id and r.deleted = 0)) as balance', 'activities.name as activity_name', 'products.name as product_name'])
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
                    //->andWhere(['=','loans.sanction_no','51801-D015-0011'])
                    ->asArray()
                    ->all();

                $loans = array_merge($loans, $loans1);

                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated_acc($loans, $date);

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
//                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,districts.name as district,
//branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`,
//`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`,`members`.``.`cnic` AS `cnic`, members.gender ,`loan_tranches`.`date_disbursed`, `loans`.`disbursed_amount`,
// @amountapproved:=(loans.disbursed_amount),
// @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
// @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0
//and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount,
//(@amountapproved-@credit) as outstanding_balance,((@amountapproved-@credit) - (@schdl_amnt-@credit)) as balance, loans.loan_expiry
//
//FROM `loan_tranches`
//LEFT JOIN `loans` ON `loan_tranches`.`loan_id` = `loans`.`id`
//LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
//INNER JOIN `members` ON `applications`.`member_id` = `members`.`id`
//INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
//INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id`
//INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
// INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id`
// INNER JOIN `districts` ON `branches`.`district_id` = `districts`.`id`
//INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id`
//INNER JOIN `products` ON `loans`.`product_id` = `products`.`id`
//WHERE (`loans`.`status` not in ('not collected','rejected','grant')) and loan_tranches.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";
//                $data = Yii::$app->db->createCommand($sql)->queryAll();
//                $new_data = \common\components\Helpers\ReportHelper::due_aging_cal_details_updated($data, $date, true);
//                $file_name = 'due_od_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
//                $file_path = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name;
//                $fopen = fopen($file_path, 'w');
//
//                fputcsv($fopen, $header);
//                foreach ($new_data as $g) {
//                    fputcsv($fopen, $g);
//                }
                fclose($fopen);

                $file_name_zip = 'due_aging_report' . '_' . date('d-m-Y-H-i-s') . '.zip';
                $file_path_zip = ImageHelper::getAttachmentPath() . '/overdue_report/' . $file_name_zip;

                $zip = new \ZipArchive();
                if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
                    // Add files to the zip file
                    $zip->addFile($file_path_due_aging, $file_name_due_aging);
//                    $zip->addFile($file_path, $file_name);
                    // All files are added, so close the zip file.
                    $zip->close();
                }
                $a->file_name = $file_name_zip;
                $a->status = 1;
                if (!$a->save()) {
                    print_r($a->getErrors());
                    die();
                }
            } else if ($a->type == 'overdue_acc') {
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`disbursed_amount`,
 @amountapproved:=(loans.disbursed_amount) as loan_amount,
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '" . $date . "' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '" . $date . "' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance, loans.loan_expiry

FROM  `loans`
LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE (`loans`.`status` not in ('not collected','rejected','grant')) AND loans.date_disbursed <= '" . $date . "' and loans.deleted = 0 HAVING schdl_amnt - credit > 0";

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

                    $date1 = date_create(date('Y-m-d 23:59:59', strtotime($a->start_month)));
                    $date2 = date_create(date('Y-m-d 23:59:59', $d['loan_expiry']));

                    if ($ReportDate > $loanExpiryDate) {
                        $lastSchedule = strtotime(date('Y-m-10', $d['loan_expiry']));
                        $schedule_amount = Schedules::find()->where(['loan_id' => $d['id']])->andWhere(['due_date' => $lastSchedule])->select(['schdl_amnt'])->one();
                    } else {
                        $schedule_amount = Schedules::find()->where(['loan_id' => $d['id']])->andWhere(['due_date' => $thisDate])->select(['schdl_amnt'])->one();
                    }

                    if (!empty($schedule_amount) && $schedule_amount != null) {
                        $diff = date_diff($date1, $date2);

                        if ($ReportDate < $loanExpiryDate) {
                            $numberCount = $overdue_amount / $schedule_amount->schdl_amnt;
                        } else {
                            $diffMonths = ceil($diff->days / 30);
                            $calCount = $overdue_amount / $d['inst_amnt'];
                            $numberCount = $calCount + $diffMonths;
                        }

                        if ($numberCount <= 1) {
                            $d['overdue_aging'] = '1 to 30 Days';
                        } elseif ($numberCount > 1 && $numberCount <= 2) {
                            $d['overdue_aging'] = '31  to 60 days';
                        } elseif ($numberCount > 2 && $numberCount <= 3) {
                            $d['overdue_aging'] = '61  to 90 days';
                        } elseif ($numberCount > 3 && $numberCount <= 4) {
                            $d['overdue_aging'] = '91  to 120 days';
                        } elseif ($numberCount > 4 && $numberCount <= 5) {
                            $d['overdue_aging'] = '121  to 150 days';
                        } elseif ($numberCount > 5 && $numberCount <= 6) {
                            $d['overdue_aging'] = '151  to 180 days';
                        } elseif ($numberCount > 6 && $numberCount <= 7) {
                            $d['overdue_aging'] = '181  to 210 days';
                        } elseif ($numberCount > 7 && $numberCount <= 8) {
                            $d['overdue_aging'] = '211  to 240 days';
                        } elseif ($numberCount > 8 && $numberCount <= 9) {
                            $d['overdue_aging'] = '241  to 270 days';
                        } elseif ($numberCount > 9 && $numberCount <= 10) {
                            $d['overdue_aging'] = '271  to 300 days';
                        } elseif ($numberCount > 10 && $numberCount <= 11) {
                            $d['overdue_aging'] = '301  to 330 days';
                        } elseif ($numberCount > 11 && $numberCount <= 12) {
                            $d['overdue_aging'] = '331  to 360 days';
                        } elseif ($numberCount > 12) {
                            $d['overdue_aging'] = 'Above 360 days';
                        } else {
                            $d['overdue_aging'] = $days;
                        }
                    } else {
                        $d['overdue_aging'] = 0;
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

//  aging-custom-report/olp-aging-report
    public function actionOlpAgingReport()
    {
        
        //define alias
        Yii::setAlias('@frontend', realpath(dirname(__FILE__) . '/../../'));
        //get all aging reports
        $disbursementDate   = strtotime(date('2024-06-30 23:59:59'));

        $dateFrom = strtotime(date('2024-06-30'));
        $dateTo   = strtotime(date('2037-04-30'));

        $activeLoans = Loans::find()
//            ->select(['loans.id as loan_id','loans.sanction_no sanction_no','members.cnic as cnic','members.full_name as member_name','projects.name project_name', 'products.name product_name', '(select coalesce(sum(loan_tranches.tranch_amount),0) from loan_tranches where loan_tranches.loan_id=loans.id and loan_tranches.date_disbursed <= "' . $disbursementDate . '" and loan_tranches.status=6) as loan_amount'])
            ->select(['loans.id as loan_id','loans.sanction_no sanction_no','members.cnic as cnic','members.full_name as member_name','projects.name project_name', 'products.name product_name', 'coalesce(sum(loan_tranches.tranch_amount),0) as loan_amount'])
            ->join('inner join', 'loan_tranches', 'loans.id=loan_tranches.loan_id')
            ->join('inner join', 'applications', 'loans.application_id=applications.id')
            ->join('inner join', 'members', 'applications.member_id=members.id')
            ->join('inner join', 'projects', 'applications.project_id=projects.id')
            ->join('inner join', 'products', 'applications.product_id=products.id')
            ->where(['<=','loan_tranches.date_disbursed' , $disbursementDate])
            ->andWhere(['loans.status' => 'collected'])
            ->orWhere(['and',
                ['loans.status' => 'loan completed'],
                ['>', 'loans.loan_completed_date', $disbursementDate]
            ])
            ->andWhere(['loan_tranches.status' => 6])
            ->andWhere(['loans.deleted' => 0])
            ->andWhere(['loan_tranches.deleted' => 0])
            ->groupBy(['loans.id'])
            ->asArray()
            ->all();
//        echo $activeLoans->createCommand()->rawSql;
//        print_r(count($activeLoans));
//        die();

        $resultData = [];

        foreach ($activeLoans as $key=>$loan){

            $recoverySum = Recoveries::find()->where(['loan_id' => $loan['loan_id']])
                ->andWhere(['deleted'=>0])
                ->andWhere(['<=','receive_date' , $dateFrom])
                ->sum('amount');
            $recoverySum = isset($recoverySum)&&!empty($recoverySum)?$recoverySum:0;
            $olp = $loan['loan_amount']-$recoverySum;

            $schedules = Schedules::find()->where(['loan_id' => $loan['loan_id']])
                ->andWhere(['>','due_date' , $dateFrom])
                ->andWhere(['<','due_date' , $dateTo])
                ->all();

            if(!empty($schedules) && $schedules!=null){
                $resultData[$key]['sanction_no'] = $loan['sanction_no'];
                $resultData[$key]['CNIC'] = $loan['cnic'];
                $resultData[$key]['Name'] = $loan['member_name'];
                $resultData[$key]['Project'] = $loan['project_name'];
                $resultData[$key]['Product'] = $loan['product_name'];
                $resultData[$key]['olp_2024-06-30'] = $olp;

                foreach ($schedules as $schedule){
                    $olp = $olp-$schedule->schdl_amnt;
                    if($olp < 0){
                        $resultData[$key]['olp_'.date('Y-m-d',$schedule->due_date)] = 0;
                    }else{
                        $resultData[$key]['olp_'.date('Y-m-d',$schedule->due_date)] = $olp;
                    }
                }
            }
        }

        $start = new \DateTime('2024-07-10');
        $end = new \DateTime('2037-05-10');
        $interval = new \DateInterval('P1M'); // 1 month interval
        $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));
        $headersMonth = [];

        foreach ($period as $date) {
            $headersMonth[] = 'olp_'.$date->format('Y-m-d');
        }
        $headersBorrower = ['Sanction_No', 'CNIC', 'Borrower_Name', 'Project', 'Product', 'olp_2024-06-30'];
        $mergedHeaders = array_merge($headersBorrower, $headersMonth);


        $file_name = 'olp_aging_report_from_2024_to_2037' . '.csv';
        $file_path = ImageHelper::getAttachmentPath() . '/complete_data_extract/' . $file_name;
        $fopen = fopen($file_path, 'w');

        fputcsv($fopen, $mergedHeaders);
        foreach ($resultData as $arr) {
            fputcsv($fopen, $arr);
        }
        fclose($fopen);
    }
}
