<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 15/08/17
 * Time: 11:36 PM
 */

namespace common\components\Helpers;

use common\models\AgingReports;
use common\models\Areas;
use common\models\BankAccounts;
use common\models\Branches;
use common\models\Projects;
use common\models\Products;
use common\models\Activities;
use common\models\Provinces;
use common\models\Recoveries;
use common\models\Regions;
use common\models\ReportDefinations;
use Yii;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class ReportHelper
{

    public static function getColumnName($report_name)
    {
        $list = [];
        $filter_list = ReportDefinations::find()->select(['filter_list'])->where(['name' => $report_name])->one();
        $filter_list = explode(',', $filter_list->filter_list);

        foreach ($filter_list as $val) {
            $a = explode('.', $val);
            if (isset($a[1])) {
                $list[$a[1]] = $val;
            } else {
                $list[$a[0]] = $val;
            }
        }
        return $list;
    }

    public static function parse_json_cihreport($progress)
    {
        $big_array = [];
        /* echo'<pre>';
         print_r($progress);
         die();*/

        if (empty($progress)) {
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;
        /*echo'<pre>';
        print_r($progress);
        die();*/
        foreach ($progress as $p) {
            $result[$i]['pd'] = $p;
            $i++;
        }

        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $temp = [];
        $old_region_id = 0;
        $old_area_id = 0;
        $old_branch_id = 0;
        end($result);
        $last_key = key($result);

        foreach ($result as $key => $one) {
            $pd = $one['pd'];
            $old_region_id = $pd['region_id'];
            $old_area_id = $pd['area_id'];
            $old_branch_id = $pd['branch_id'];
            if (isset($big_array[$old_region_id][$old_area_id])) {
                //print_r(count($big_array[$old_region_id]));
                $co = count($big_array[$old_region_id][$old_area_id]);
            } else {
                $co = -1;
                $co++;
            }

            $big_array[$old_region_id][$old_area_id][$co] = $pd;
            //$big_array[$old_region_id][$old_area_id][$co]['detail'] = "<a href=\"recoverydetail?branch_id=" . $old_branch_id . "&date=2000-01-01+-+2018-03-31\"><i class=\"fa fa-eye\"></i></a>";

        }
        $temp_sum = array('name' => 0, 'total_amount' => 0, 'cih' => 0, 'deposited' => 0);
        $new_big_array = array();
        $grand_sum = $temp_sum;
        $grand_sum['id'] = 100;
        $grand_sum['name'] = 'Grand Total';

        $count_region = 0;

        foreach ($big_array as $key => $region) {
            $count_region++;
            $region_sum = $temp_sum;
            $region_sum['id'] = $key;
            $region_sum['name'] = isset($regions[$key]) ? ($regions[$key]) : '';

            $count_area = 0;
            $count = 0;
            foreach ($region as $key_area => $area) {
                $count_area++;
                $area_sum = $temp_sum;
                $area_sum['id'] = $key_area;
                $area_sum['name'] = isset($areas[$key_area]) ? ($areas[$key_area]) : '';
                $count_branch = 0;

                foreach ($area as $b_key => $branch) {

                    $count_branch++;
                    unset($branch['branch_id']);
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['id']];
                    unset($branch['branch_id']);

                    $grand_sum['total_amount'] += $branch['total_amount'];
                    $grand_sum['cih'] += $branch['cih'];
                    $grand_sum['deposited'] += $branch['deposited'];

                    $region_sum['total_amount'] += $branch['total_amount'];
                    $region_sum['cih'] += $branch['cih'];
                    $region_sum['deposited'] += $branch['deposited'];

                    $area_sum['total_amount'] += $branch['total_amount'];
                    $area_sum['cih'] += $branch['cih'];
                    $area_sum['deposited'] += $branch['deposited'];

                    $area_sum['children'][$b_key] = $branch;
                }


                $region_sum['children'][$count] = $area_sum;
                $count++;
            }

            $new_big_array[] = $region_sum;
        }
        $new_big_array[] = $grand_sum;

        $progress_report = json_encode($new_big_array);
        $big_array = [];
        return $progress_report;


    }

    public static function olp_aging_report($a)
    {

        $date = $a->start_month;
        $date = (date('Y-m-01', strtotime($date)));
        $date1 = (date('Y-m-t', strtotime($date)));

        $due_report = AgingReports::find()->select('(select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . ($date) . '" and "' . ($date1) . '")) as one_month,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+1 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+3 months', strtotime($date))))) . '")) as next_three_months,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+4 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+9 months', strtotime($date1))))) . '")) as next_six_months,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+10 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+21 months', strtotime($date1))))) . '")) as next_one_year,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+22 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+45 months', strtotime($date1))))) . '")) as next_two_year,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+46 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+81 months', strtotime($date1))))) . '")) as next_three_year,
      (select sum(schdl_amnt) from schedules  where(schedules.due_date between "' . (date('Y-m-d', (strtotime('+82 months', strtotime($date))))) . '" and "' . (date('Y-m-t', (strtotime('+141 months', strtotime($date1))))) . '")) as next_five_year,
      (select sum(schdl_amnt) from schedules) as total,status
       ')->all();
        return $due_report;
    }


    public static function od_aging_report($a)
    {

        $date = $a->start_month;
        $date = (date('Y-m-t', strtotime($date)));
        $connection = Yii::$app->db;
        $due_report =

            /*$due_report=Loans::find()->select("
            (SELECT l.sanction_no),
            (select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '".$date."') as a,
             (select sum(r.credit) from recoveries r where r.loan_id = l.id and r.recv_date <= '".$date."') as b,
            ((select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '".$date."') -
             (select sum(r.credit) from recoveries r where r.loan_id = l.id and r.recv_date <= '".$date."')) as overdue
            FROM loans l
            inner join borrowers b on b.id = l.borrower_id
            WHERE 1 and l.dsb_status != 'Not Collected' having a-b > 0
            ")->all();*/
        $due_report = "SELECT l.sanction_no,l.loanexpiry,
        (select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '" . $date . "') as a,
         (select sum(r.credit) from recoveries r where r.loan_id = l.id and r.recv_date <= '" . $date . "') as b,
        ((select sum(s.schdl_amnt) from schedules s where s.loan_id = l.id and s.due_date <= '" . $date . "') -
         (select sum(r.credit) from recoveries r where r.loan_id = l.id and r.recv_date <= '" . $date . "')) as overdue,
         (select l.amountapproved - (select sum(r.credit) from recoveries r where r.loan_id = l.id and r.recv_date <= '" . $date . "')) as balance
        FROM loans l
        inner join borrowers b on b.id = l.borrower_id 
        WHERE 1 and l.dsb_status != 'Not Collected' having a-b > 0
        ";
        $due_report = $connection->createCommand($due_report)->queryAll();
        //  $due_report=$connection->createCommand($due_report)->all();


        $aging_report = array();
        foreach ($due_report as $k => $r) {
            /* $aging_report[$r['l']['sanction_no']]['sacntion_no'] = $r['l']['sanction_no'];
             $aging_report[$r['l']['sanction_no']]['overdue'] = $r[0]['overdue'];*/

            $current_date = date('Y-m-d', strtotime($date));
            $one_month = date('Y-m-d', strtotime("-1 months", strtotime($date)));
            $next_three_months = date('Y-m-d', strtotime("-3 months", strtotime($date)));
            $next_six_months = date('Y-m-d', strtotime("-6 months", strtotime($date)));
            $next_one_year = date('Y-m-d', strtotime("-12 months", strtotime($date)));
            $next_two_year = date('Y-m-d', strtotime("-24 months", strtotime($date)));
            $next_three_year = date('Y-m-d', strtotime("-36 months", strtotime($date)));
            $next_five_year = date('Y-m-d', strtotime("-60 months", strtotime($date)));

            /*echo "<br><br><br>";
            echo $current_date."<br>";
            echo $three_months."<br>";
            echo $six_months."<br>";
            echo $nine_months."<br>";
            echo $twelve_months."<br>";
            die();*/

            if ($r['balance'] == $r['overdue']) {
                if ($current_date >= $r['loanexpiry'] && $one_month <= $r['loanexpiry']) {
                    //die("one");
                    $aging_report[$k]['one_month'] = $r['overdue'];
                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($current_date >= $r['loanexpiry'] && $next_three_months <= $r['loanexpiry']) {
                    //die("one");
                    $aging_report[$k]['one_month'] = 0;
                    $aging_report[$k]['next_three_months'] = $r['overdue'];
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_three_months >= $r['loanexpiry'] && $next_six_months <= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = $r['overdue'];
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_six_months >= $r['loanexpiry'] && $next_one_year <= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = $r['overdue'];
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_one_year >= $r['loanexpiry'] && $next_two_year <= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = $r['overdue'];
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_two_year >= $r['loanexpiry'] && $next_three_year <= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = $r['overdue'];
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_three_year >= $r['loanexpiry'] && $next_five_year <= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = $r['overdue'];
                    $aging_report[$k]['next_five_year'] = 0;
                }
                if ($next_five_year >= $r['loanexpiry']) {
                    $aging_report[$k]['one_month'] = 0;

                    $aging_report[$k]['next_three_months'] = 0;
                    $aging_report[$k]['next_six_months'] = 0;
                    $aging_report[$k]['next_one_year'] = 0;
                    $aging_report[$k]['next_two_year'] = 0;
                    $aging_report[$k]['next_three_year'] = 0;
                    $aging_report[$k]['next_five_year'] = $r['overdue'];
                }
            } else {
                $aging_report[$k]['one_month'] = 0;

                $aging_report[$k]['next_three_months'] = $r['overdue'];
                $aging_report[$k]['next_six_months'] = 0;
                $aging_report[$k]['next_one_year'] = 0;
                $aging_report[$k]['next_two_year'] = 0;
                $aging_report[$k]['next_three_year'] = 0;
                $aging_report[$k]['next_five_year'] = 0;
            }
            //print_r($r);
            //die();
        }

        $aging_report1['one_month'] = 0;
        $aging_report1['next_three_months'] = 0;
        $aging_report1['next_six_months'] = 0;
        $aging_report1['next_one_year'] = 0;
        $aging_report1['next_two_year'] = 0;
        $aging_report1['next_three_year'] = 0;
        $aging_report1['next_five_year'] = 0;

        foreach ($aging_report as $a) {
            $aging_report1['one_month'] += $a['one_month'];
            $aging_report1['next_three_months'] += $a['next_three_months'];
            $aging_report1['next_six_months'] += $a['next_six_months'];
            $aging_report1['next_one_year'] += $a['next_one_year'];
            $aging_report1['next_two_year'] += $a['next_two_year'];
            $aging_report1['next_three_year'] += $a['next_three_year'];
            $aging_report1['next_five_year'] += $a['next_five_year'];

        }

        return $aging_report1;
    }

    public static function due_aging_cal($loans)
    {
        /*$loans = array(
            array(
                'id' => 1,
                'inst_amnt' => 1000,
                'inst_months' => 100,
                'balance' => 90000,
            ),
            array()
        );*/
        $report = array();
        foreach ($loans as $loan) {
            $aging_report = array(
                'one_month' => 0,
                'two_month' => 0,
                'three_month' => 0,
                'six_month' => 0,
                'next_one_year' => 0,
                'next_two_year' => 0,
                'next_three_year' => 0,
                'next_five_year' => 0
            );
            //print_r($loans);
            $month_count = $loan['balance'] / $loan['inst_amnt'];
            //echo $month_count."\n";
            /*while ($month_count > 0){
                $month_count -
            }*/
            if ($month_count <= $loan['inst_months']) {
                if ($loan['balance'] >= $loan['inst_amnt']) {
                    if ($month_count - 1 >= 0) {
                        $aging_report['one_month'] += $loan['inst_amnt'] * 1;
                    }

                    if ($month_count - 2 >= 0) {
                        $aging_report['two_month'] += $loan['inst_amnt'] * 1;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 1);
                        $aging_report['two_month'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 3 >= 0) {
                        $aging_report['three_month'] += $loan['inst_amnt'] * 1;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 2);
                        $aging_report['three_month'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 6 >= 0) {
                        $aging_report['six_month'] += $loan['inst_amnt'] * 3;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 3);
                        $aging_report['six_month'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 18 >= 0) {
                        $aging_report['next_one_year'] += $loan['inst_amnt'] * 12;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 6);
                        $aging_report['next_one_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 30 >= 0) {
                        $aging_report['next_two_year'] += $loan['inst_amnt'] * 12;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 18);
                        $aging_report['next_two_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 42 >= 0) {
                        $aging_report['next_three_year'] += $loan['inst_amnt'] * 12;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 30);
                        $aging_report['next_three_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count > 42) {
                        $aging_report['next_five_year'] += $loan['inst_amnt'] * ($month_count - 42);
                    } /*else {
                        $aging_report['next_five_year'] += $loan['inst_amnt'] * ($month_count - 42);
                        die('here2');
                        $amount = $loan['inst_amnt'] * ($month_count - 36);
                        $aging_report['one_month'] += 0;
                        $aging_report['next_two_month'] += 0;
                        $aging_report['next_three_month'] += 0;
                        $aging_report['next_six_month'] += 0;
                        $aging_report['next_twelve_month'] += 0;
                        $aging_report['next_twelve_month1'] += 0;
                        $aging_report['next_twenty_four_month'] += ($amount > 0) ? $amount : 0;
                    }*/
                } else {
                    $aging_report['one_month'] += $loan['balance'];
                }
            }
            /*print_r($loan);
            print_r($aging_report);
            die();*/
            $report[] = $aging_report;

        }
        /*print_r($report);
        die();*/
        $report_sum_array = array(
            'one_month' => 0,
            'two_month' => 0,
            'three_month' => 0,
            'six_month' => 0,
            'next_one_year' => 0,
            'next_two_year' => 0,
            'next_three_year' => 0,
            'next_five_year' => 0
        );
        foreach ($report as $r) {
            $report_sum_array['one_month'] += $r['one_month'];
            $report_sum_array['two_month'] += $r['two_month'];
            $report_sum_array['three_month'] += $r['three_month'];
            $report_sum_array['six_month'] += $r['six_month'];
            $report_sum_array['next_one_year'] += $r['next_one_year'];
            $report_sum_array['next_two_year'] += $r['next_two_year'];
            $report_sum_array['next_three_year'] += $r['next_three_year'];
            $report_sum_array['next_five_year'] += $r['next_five_year'];
        }
        print_r($report_sum_array);
        print_r(array_sum($report_sum_array));
        die();
        return $report_sum_array;
    }

    public static function due_aging_cal_details($loans, $date, $od=false)
    {

        $report = array();
        foreach ($loans as $loan) {
            $aging_report = array(
                'sanction_no' => $loan['sanction_no'],
                'loan_amount' => $loan['loan_amount'],
                'balance' => $loan['balance'],
                'one_to_thirty_days' => 0,
                'thirty_to_ninety_days' => 0,
                'ninety_to_one_eighty_days' => 0,
                'next_one_year' => 0,
                'next_two_year' => 0,
                'next_three_year' => 0,
                'next_five_year' => 0,
                'total' => 0
            );
            $month_count = $loan['balance'] / $loan['inst_amnt'];
            $date_disbursed = date('Y-m-d', $loan['date_disbursed']);
            if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                $date_disbursed = date('Y-m-20', $loan['date_disbursed']);
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
            } else {
                $due_date = date("Y-m-10", strtotime($date_disbursed));
            }
            $months = DisbursementHelper::getSchdlMonths()[$loan['inst_type']];
            $due_date = strtotime(date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date))));
            $days = ($due_date - $date) / 60 / 60 / 24;
            if ($days > 30) {
                $month_count++;
            }
            if ($loan['inst_months'] == 1 && $due_date > $date) {
                if ($days > 1 && $days <= 30) {
                    if($od){
                        $aging_report['one_to_thirty_days'] = $loan['balance']+$loan['overdue_amount'];
                    }else{
                        $aging_report['one_to_thirty_days'] = $loan['balance'];
                    }

                } else if ($days > 30 && $days <= 90) {
                    $aging_report['thirty_to_ninety_days'] = $loan['balance'];
                } else if ($days > 90 && $days <= 180) {
                    $aging_report['ninety_to_one_eighty_days'] = $loan['balance'];
                } else if ($days > 180 && $days <= 365) {
                    $aging_report['next_one_year'] = $loan['balance'];
                } else if ($days > 365 && $days <= 730) {
                    $aging_report['next_two_year'] = $loan['balance'];
                } else if ($days > 730 && $days <= 1095) {
                    $aging_report['next_three_year'] = $loan['balance'];
                } else if ($days > 1095) {
                    $aging_report['next_five_year'] = $loan['balance'];
                }

            } else { /*if($month_count <= $loan['inst_months']){*/

                if ($od && ($loan['balance'] == 0)) {
                    $aging_report['one_to_thirty_days'] += $loan['overdue_amount'];
                }

                if ($loan['balance'] >= $loan['inst_amnt']) {
                    //if ($loan['inst_months'] != 1) {
                        if ($days <= 30) {
                            if ($od) {
                                $aging_report['one_to_thirty_days'] += ($loan['inst_amnt'] * 1) + $loan['overdue_amount'];
                            } else {
                                if ($month_count - 1 >= 0) {
                                    $aging_report['one_to_thirty_days'] += $loan['inst_amnt'] * 1;
                                }
                            }
                        }
                        if ($month_count - 3 >= 0) {
                            $aging_report['thirty_to_ninety_days'] += $loan['inst_amnt'] * 2;
                        } else {
                            $amount = $loan['inst_amnt'] * ($month_count - 1);
                            $aging_report['thirty_to_ninety_days'] += ($amount > 0) ? $amount : 0;
                        }

                        if ($month_count - 6 >= 0) {
                            $aging_report['ninety_to_one_eighty_days'] += $loan['inst_amnt'] * 3;
                        } else {
                            $amount = $loan['inst_amnt'] * ($month_count - 3);
                            $aging_report['ninety_to_one_eighty_days'] += ($amount > 0) ? $amount : 0;
                        }

                        if ($month_count - 12 >= 0) {
                            $aging_report['next_one_year'] += $loan['inst_amnt'] * 6;
                        } else {
                            $amount = $loan['inst_amnt'] * ($month_count - 6);
                            $aging_report['next_one_year'] += ($amount > 0) ? $amount : 0;
                        }

                        if ($month_count - 24 >= 0) {
                            $aging_report['next_two_year'] += $loan['inst_amnt'] * 12;
                        } else {
                            $amount = $loan['inst_amnt'] * ($month_count - 12);
                            $aging_report['next_two_year'] += ($amount > 0) ? $amount : 0;
                        }

                        if ($month_count - 36 >= 0) {
                            $aging_report['next_three_year'] += $loan['inst_amnt'] * 12;
                        } else {
                            $amount = $loan['inst_amnt'] * ($month_count - 24);
                            $aging_report['next_three_year'] += ($amount > 0) ? $amount : 0;
                        }

                        if ($month_count > 36) {
                            $aging_report['next_five_year'] += $loan['inst_amnt'] * ($month_count - 36);
                        }
                    //}
                } else {
                    $aging_report['one_to_thirty_days'] += $loan['balance'];
                }
            }
            $aging_report['total'] = $aging_report['one_to_thirty_days'] + $aging_report['thirty_to_ninety_days'] + $aging_report['ninety_to_one_eighty_days'] + $aging_report['next_one_year'] + $aging_report['next_two_year'] + $aging_report['next_three_year'] + $aging_report['next_five_year'];
            $report[] = $aging_report;

        }
        return $report;
    }

    public static function due_aging_cal_details_updated($loans, $date, $od=false)
    {

        $report = array();
        foreach ($loans as $loan) {
            $cheque_no = $loan['cheque_no'];
            $aging_report = array(
                'region' => $loan['region_name'],
                'area' => $loan['area_name'],
                'branch' => $loan['branch_name'],
                'project' => $loan['project_name'],
                'name' => $loan['name'],
                'cnic' => $loan['cnic'],
                'parentage' => $loan['parentage'],
                'gender' => $loan['gender'],
                'other_name' => $loan['other_name'],
                'other_cnic' => $loan['other_cnic'],
                'district' => $loan['district'],
                'date_disbursed' => date('Y-m-d',$loan['date_disbursed']),
                'sanction_no' => $loan['sanction_no'],
                'loan_amount' => $loan['disbursed_amount'],
                'balance' => $loan['balance'],
                'recovery_sum' => $loan['recovery_sum'],
                'cheque_no' => "'$cheque_no'",
                'loan_expiry' => date('Y-m-d',$loan['loan_expiry']),
                'installment_amount' => $loan['inst_amnt'],
                'installment_month' => $loan['inst_months'],
                'one_to_thirty_days' => 0,
                'thirty_to_ninety_days' => 0,
                'ninety_to_one_eighty_days' => 0,
                'next_one_year' => 0,
                'next_two_year' => 0,
                'next_three_year' => 0,
                'next_five_year' => 0,
                'total' => 0,
                'activity_name' => $loan['activity_name'],
                'prodcut_name' => $loan['product_name'],
                'age' => $loan['age']
            );
            $month_count = $loan['balance'] / $loan['inst_amnt'];
            $date_disbursed = date('Y-m-d', $loan['date_disbursed']);
            if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                $date_disbursed = date('Y-m-20', $loan['date_disbursed']);
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
            } else {
                $due_date = date("Y-m-10", strtotime($date_disbursed));
            }
            $months = DisbursementHelper::getSchdlMonths()[$loan['inst_type']];
            $due_date = strtotime(date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date))));
            $days = ($due_date - $date) / 60 / 60 / 24;
            if ($days > 30) {
                $month_count++;
            }
            if ($loan['inst_months'] == 1 && $due_date > $date) {
                if ($days > 1 && $days <= 30) {
                    if($od){
                        $aging_report['one_to_thirty_days'] = $loan['balance']+$loan['overdue_amount'];
                    }else{
                        $aging_report['one_to_thirty_days'] = $loan['balance'];
                    }

                } else if ($days > 30 && $days <= 90) {
                    $aging_report['thirty_to_ninety_days'] = $loan['balance'];
                } else if ($days > 90 && $days <= 180) {
                    $aging_report['ninety_to_one_eighty_days'] = $loan['balance'];
                } else if ($days > 180 && $days <= 365) {
                    $aging_report['next_one_year'] = $loan['balance'];
                } else if ($days > 365 && $days <= 730) {
                    $aging_report['next_two_year'] = $loan['balance'];
                } else if ($days > 730 && $days <= 1095) {
                    $aging_report['next_three_year'] = $loan['balance'];
                } else if ($days > 1095) {
                    $aging_report['next_five_year'] = $loan['balance'];
                }

            } else { /*if($month_count <= $loan['inst_months']){*/

                if ($od && ($loan['balance'] == 0)) {
                    $aging_report['one_to_thirty_days'] += $loan['overdue_amount'];
                }

                if ($loan['balance'] >= $loan['inst_amnt']) {
                    //if ($loan['inst_months'] != 1) {
                    if ($days <= 30) {
                        if ($od) {
                            $aging_report['one_to_thirty_days'] += ($loan['inst_amnt'] * 1) + $loan['overdue_amount'];
                        } else {
                            if ($month_count - 1 >= 0) {
                                $aging_report['one_to_thirty_days'] += $loan['inst_amnt'] * 1;
                            }
                        }
                    }
                    if ($month_count - 3 >= 0) {
                        $aging_report['thirty_to_ninety_days'] += $loan['inst_amnt'] * 2;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 1);
                        $aging_report['thirty_to_ninety_days'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 6 >= 0) {
                        $aging_report['ninety_to_one_eighty_days'] += $loan['inst_amnt'] * 3;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 3);
                        $aging_report['ninety_to_one_eighty_days'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 12 >= 0) {
                        $aging_report['next_one_year'] += $loan['inst_amnt'] * 6;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 6);
                        $aging_report['next_one_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 24 >= 0) {
                        $aging_report['next_two_year'] += $loan['inst_amnt'] * 12;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 12);
                        $aging_report['next_two_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count - 36 >= 0) {
                        $aging_report['next_three_year'] += $loan['inst_amnt'] * 12;
                    } else {
                        $amount = $loan['inst_amnt'] * ($month_count - 24);
                        $aging_report['next_three_year'] += ($amount > 0) ? $amount : 0;
                    }

                    if ($month_count > 36) {
                        $aging_report['next_five_year'] += $loan['inst_amnt'] * ($month_count - 36);
                    }
                    //}
                } else {
                    $aging_report['one_to_thirty_days'] += $loan['balance'];
                }
            }
            $aging_report['total'] = $aging_report['one_to_thirty_days'] + $aging_report['thirty_to_ninety_days'] + $aging_report['ninety_to_one_eighty_days'] + $aging_report['next_one_year'] + $aging_report['next_two_year'] + $aging_report['next_three_year'] + $aging_report['next_five_year'];
            $report[] = $aging_report;

        }
        return $report;
    }
    public static function due_aging_cal_details_updated_acc($loans, $date, $od=false)
    {

        $report = array();
        foreach ($loans as $loan) {
            $cheque_no = $loan['cheque_no'];
            $aging_report = array(
                'region' => $loan['region_name'],
                'area' => $loan['area_name'],
                'branch' => $loan['branch_name'],
                'project' => $loan['project_name'],
                'name' => $loan['name'],
                'cnic' => $loan['cnic'],
                'parentage' => $loan['parentage'],
                'gender' => $loan['gender'],
                'date_disbursed' => date('Y-m-d',$loan['date_disbursed']),
                'sanction_no' => $loan['sanction_no'],
                'loan_amount' => $loan['disbursed_amount'],
                'balance' => $loan['balance'],
                'recovery_sum' => $loan['recovery_sum'],
                'cheque_no' => "'$cheque_no'",
                'loan_expiry' => date('Y-m-d',$loan['loan_expiry']),
                'installment_amount' => $loan['inst_amnt'],
                'installment_month' => $loan['inst_months'],
                'activity_name' => $loan['activity_name'],
                'prodcut_name' => $loan['product_name']
            );
            $month_count = $loan['balance'] / $loan['inst_amnt'];
            $date_disbursed = date('Y-m-d', $loan['date_disbursed']);
            if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                $date_disbursed = date('Y-m-20', $loan['date_disbursed']);
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
            } else {
                $due_date = date("Y-m-10", strtotime($date_disbursed));
            }
            $months = DisbursementHelper::getSchdlMonths()[$loan['inst_type']];
            $due_date = strtotime(date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date))));
//            $days = ($due_date - $date) / 60 / 60 / 24;
            $report[] = $aging_report;

        }
        return $report;
    }

    public static function due_aging_cal_details_ext_auditor($loans, $date, $od=false)
    {

        $report = array();
        foreach ($loans as $loan) {
            $aging_report = array(
                'name' => $loan['name'],
                'cnic' => $loan['cnic'],
                'parentage' => $loan['parentage'],
                'district' => $loan['district'],
                'date_disbursed' => date('Y-m-d', $loan['date_disbursed']),
                'sanction_no' => $loan['sanction_no'],
                'loan_amount' => $loan['loan_amount'],
                'balance' => $loan['balance'],
                'one_year' => 0,
                'one_to_three' => 0,
                'three_to_five' => 0,
                'more_than_five' => 0,
                'total' => 0
            );
            $month_count = $loan['balance'] / $loan['inst_amnt'];
            $date_disbursed = date('Y-m-d', $loan['date_disbursed']);
            if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                $date_disbursed = date('Y-m-20', $loan['date_disbursed']);
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
            } else {
                $due_date = date("Y-m-10", strtotime($date_disbursed));
            }
            $months = DisbursementHelper::getSchdlMonths()[$loan['inst_type']];
            $due_date = strtotime(date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date))));
            $days = ($due_date - $date) / 60 / 60 / 24;
            if ($days > 365) {
                $month_count++;
            }
            /*if ($loan['inst_months'] == 1 && $due_date > $date) {
                /*if ($days > 1 && $days <= 365) {
                    if($od){
                        $aging_report['one_year'] = $loan['balance']+$loan['overdue_amount'];
                    }else{
                        $aging_report['one_year'] = $loan['balance'];
                    }

                } else if ($days > 365 && $days <= 1095) {
                    $aging_report['one_to_three'] = $loan['balance'];
                } else if ($days > 1095 && $days <= 1460) {
                    $aging_report['three_to_five'] = $loan['balance'];
                } else if ($days > 1460) {
                    $aging_report['more_than_five'] = $loan['balance'];
                }

            } else {*/ /*if($month_count <= $loan['inst_months']){*/

            if ($od && ($loan['balance'] == 0)) {
                $aging_report['one_year'] += $loan['overdue_amount'];
            }

            if ($loan['balance'] >= $loan['inst_amnt']) {
                //if ($loan['inst_months'] != 1) {
                if ($days <= 365) {
                    if ($od) {
                        $aging_report['one_year'] += ($loan['inst_amnt'] * 1) + $loan['overdue_amount'];
                    } else {
                        if ($month_count - 12 >= 0) {
                            $aging_report['one_year'] += $loan['inst_amnt'] * 12;
                        } else {
                            $aging_report['one_year'] += $loan['inst_amnt'] * $month_count;
                        }
                    }
                }
                if ($month_count - 36 >= 0) {
                    $aging_report['one_to_three'] += $loan['inst_amnt'] * 24;
                } else {
                    $amount = $loan['inst_amnt'] * ($month_count - 12);
                    $aging_report['one_to_three'] += ($amount > 0) ? $amount : 0;
                }

                if ($month_count - 60 >= 0) {
                    $aging_report['three_to_five'] += $loan['inst_amnt'] * 24;
                } else {
                    $amount = $loan['inst_amnt'] * ($month_count - 36);
                    $aging_report['three_to_five'] += ($amount > 0) ? $amount : 0;
                }


                if ($month_count > 60) {
                    $aging_report['more_than_five'] += $loan['inst_amnt'] * ($month_count - 60);
                }
                //}
            } else {
                $aging_report['one_year'] += $loan['balance'];
            }

            $aging_report['total'] = $aging_report['one_year'] + $aging_report['one_to_three'] + $aging_report['three_to_five'] + $aging_report['more_than_five'];
            $report[] = $aging_report;
        }

        return $report;
    }
    public static function due_aging_cal_detail($loans, $date, $od=false)
    {
        $report = array();
        foreach ($loans as $loan) {
            $aging_report = array(
                'name' => $loan['name'],
                'cnic' => $loan['cnic'],
                'parentage' => $loan['parentage'],
                'gender' => $loan['gender'],
                'district' => $loan['district'],
                'branch_code' => $loan['code'],
                'project' => $loan['project'],
                'product' => $loan['product'],
                'purpose' => $loan['purpose'],
                'date_disbursed' => date('Y-m-d', $loan['date_disbursed']),
                'sanction_no' => $loan['sanction_no'],
                'loan_amount' => $loan['loan_amount'],
                'inst_months' => $loan['inst_months'],
                'inst_amnt' => $loan['inst_amnt'],
                'inst_type' => $loan['inst_type'],
                'loan_expiry' => date('Y-m-d', $loan['loan_expiry']),
                'last_rec_date' => date('Y-m-d', $loan['last_rec_date']),
                'last_rec_amount' => $loan['last_rec_amount'],
                'recovery_no_of_istallments' => $loan['recovery_no_of_istallments'],
                'balance' => $loan['balance'],
            );
            $report[]= $aging_report;
        }
        return $report;
    }
} ?>
