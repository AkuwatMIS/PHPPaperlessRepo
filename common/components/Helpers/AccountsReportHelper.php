<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\components\Helpers\ReportsHelper;
use common\models\ArcAccountReports;
use common\models\Branches;
use common\models\ProgressReports;
use function PHPSTORM_META\type;
use Yii;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class AccountsReportHelper
{
    public static function getMonths()
    {
        $report_months =[];
        $report_dates = ArcAccountReports::find()->select(['report_date'])->where(['code' => 'recv'])->groupBy('report_date')->all();

        foreach ($report_dates as $report_date)
        {
            $report_months[date('Y-m',$report_date->report_date)]= date('F-Y',$report_date->report_date);
        }
        /*print_r($report_months);
        die();*/
        return $report_months;


        /*return array(
            "" => "",

            "2018-07" => "July-2018",
            "2018-08" => "August-2018",
            "2018-09" => "September-2018",
            "2018-10" => "October-2018",
            "2018-11" => "November-2018",
            "2018-12" => "December-2018",
            "2019-01" => "January-2019",
            "2019-02" => "February-2019",
            "2019-03" => "March-2019",
            "2019-04" => "April-2019",
            "2019-05" => "May-2019",
            "2019-06" => "June-2019",

        );*/
    }

    public static function getReportList($role)
    {
        $list = [];
        if(in_array($role, ['DMA','ADMIN','CFO','ACCM'])) {
            $list[9] = 'monthly progress';
        }
        if(in_array($role, ['PM','ADMIN'])) {
            $list[10] = 'month progress details';
        }

        return $list;
    }
    public static function getMonthsApplications()
    {
        $report_months =[];
        $report_dates = ArcAccountReports::find()->select(['report_date'])->where(['code' => 'app_disb'])->groupBy('report_date')->all();

        foreach ($report_dates as $report_date)
        {
            $report_months[date('Y-m',$report_date->report_date)]= date('F-Y',$report_date->report_date);
        }
        /*print_r($report_months);
        die();*/
        return $report_months;


        /*return array(
            "" => "",

            "2018-07" => "July-2018",
            "2018-08" => "August-2018",
            "2018-09" => "September-2018",
            "2018-10" => "October-2018",
            "2018-11" => "November-2018",
            "2018-12" => "December-2018",
            "2019-01" => "January-2019",
            "2019-02" => "February-2019",
            "2019-03" => "March-2019",
            "2019-04" => "April-2019",
            "2019-05" => "May-2019",
            "2019-06" => "June-2019",

        );*/
    }

    public static function get_recovery_data($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id)
    {
        $connection = Yii::$app->db;
        $cond = '';
        $project_cond = '';

        if ($region_id > 0) {
            $cond .= " && b.region_id = '" . $region_id . "'";
        }
        if ($area_id > 0) {
            $cond .= " && b.area_id = '" . $area_id . "'";
        }
        if ($branch_id > 0) {
            $cond .= " && b.id = '" . $branch_id . "'";
        }
        if ($project_id > 0) {
            $project_cond = "&& r.project_id = '" . $project_id . "'";
        }


        /*$select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                            ( SELECT COALESCE(count(loan_id),0)
                              FROM recoveries r 
                              WHERE r.deleted = 0 
                              AND r.branch_id = b.id " . $project_cond . " 
                              AND r.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND r.receive_date > 0
                            ) as objects_count,
                            ( SELECT COALESCE(sum(amount),0)
                              FROM recoveries r 
                              WHERE r.deleted = 0 
                              AND r.branch_id = b.id " . $project_cond . " 
                              AND r.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND r.receive_date > 0
                            ) as amount
                            
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";*/
        $select_query = "SELECT r.branch_id as id, r.area_id, r.region_id, COALESCE(count(r.loan_id),0) as objects_count, COALESCE(sum(r.amount),0) as amount
                          FROM recoveries r
                          WHERE r.deleted = 0 " . $project_cond . " 
                          AND r.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND r.receive_date > 0
                          group by r.branch_id order by r.region_id asc";
        //die($select_query);
        $total_loans = $connection->createCommand($select_query)->queryAll();
        return $total_loans;
    }

    public static function get_donation_data($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id)
    {
        $previous_date = strtotime(date('Y-m-t 23:59:59',strtotime('-1 month', $report_date)));
        $connection = Yii::$app->db;
        $cond = '';
        $project_cond = '';

        if ($region_id > 0) {
            $cond .= " && b.region_id = '" . $region_id . "'";
        }
        if ($area_id > 0) {
            $cond .= " && b.area_id = '" . $area_id . "'";
        }
        if ($branch_id > 0) {
            $cond .= " && b.id = '" . $branch_id . "'";
        }
        if ($project_id > 0) {
            $project_cond = "&& d.project_id = '" . $project_id . "'";
            $project_cond_loan = "&& loans.project_id = '" . $project_id . "'";
        }

        /*$select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                            ( SELECT COALESCE(count(loan_id),0)
                              FROM donations d 
                              WHERE d.deleted = 0 
                              AND d.branch_id = b.id " . $project_cond . " 
                              AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                            ) as objects_count,
                            ( SELECT COALESCE(sum(amount),0)
                              FROM donations d 
                              WHERE d.deleted = 0 
                              AND d.branch_id = b.id " . $project_cond . " 
                              AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                            ) as amount
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";*/
       //////////////////////////////
        /*$select_query = "SELECT b.id,b.name,b.region_id,b.area_id,(SELECT
                                           CONCAT_WS('+',
                                        CAST((SELECT COUNT(loans.status) 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . " 
                                        AND `date_disbursed` <= '" . $previous_date . "' && date_disbursed > 0
                                        AND loans.status = 'collected' AND loans.deleted = 0) AS char(20)),
                            CAST((SELECT COUNT(loans.status) as status 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . " 
                                        AND `date_disbursed` <= '" . $previous_date . "' && date_disbursed > 0
                                        AND loans.status = 'loan completed' AND loans.deleted = 0
                                        AND `loan_completed_date` > '" . $previous_date . "') AS char(20)),
                                         CAST(COALESCE(sum(d.amount),0) AS char(20)))
                        FROM donations d 
                        WHERE d.deleted = 0 " . $project_cond . " 
                        AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0 AND d.branch_id = b.id ) as mdp
                       FROM branches b where b.status = 1 ORDER BY b.region_id";*/
        $select_query = "SELECT d.branch_id as id, d.area_id, d.region_id, COALESCE(count(d.loan_id),0) as objects_count, COALESCE(sum(d.amount),0) as amount
                          FROM donations d
                          WHERE d.deleted = 0 " . $project_cond . " 
                          AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                          group by d.branch_id order by d.region_id asc";
        ////////////////////////////////
        /*$select_query = "SELECT d.branch_id as id, d.area_id,d.region_id,
                              (SELECT COUNT(loans.status)
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . "
                                        AND `date_disbursed` <= '" . $previous_date . "' && date_disbursed > 0
                                        AND loans.status = 'collected' AND loans.deleted = 0) as activeloans_1,
                            (SELECT COUNT(loans.status) as status
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . "
                                        AND `date_disbursed` <= '" . $previous_date . "' && date_disbursed > 0
                                        AND loans.status = 'loan completed' AND loans.deleted = 0
                                        AND `loan_completed_date` > '" . $previous_date . "') as activeloans_2,
                                         COALESCE(sum(d.amount),0) as amount
                        FROM donations d
                        WHERE d.deleted = 0 " . $project_cond . "
                        AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                        group by d.branch_id order by d.region_id asc";*/
        //die($select_query);
        $total_loans = $connection->createCommand($select_query)->queryAll();
        return $total_loans;
    }
    public static function get_takaful_data($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id){
        $connection = Yii::$app->db;
        $project_cond = '';
        if ($project_id > 0) {
            $project_cond = "&& d.project_id = '" . $project_id . "'";
            $project_cond_loan = "&& loans.project_id = '" . $project_id . "'";
        }
        $select_query="Select d.branch_id as id, d.area_id, d.region_id, 
                    COALESCE(count(d.loan_id),0) as objects_count,COALESCE(sum(d.credit),0) as amount
                          from operations d WHERE d.deleted = 0 " . $project_cond . " 
                          AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "'  
                          AND d.receive_date > 0
                          AND d.operation_type_id=2
                          group by d.branch_id order by d.region_id asc";
        $total_loans = $connection->createCommand($select_query)->queryAll();
        return $total_loans;
    }

    public static function get_donation_active_loans($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id)
    {
        $connection = Yii::$app->db;
        $cond = '';
        $project_cond = '';

        if ($region_id > 0) {
            $cond .= " && b.region_id = '" . $region_id . "'";
        }
        if ($area_id > 0) {
            $cond .= " && b.area_id = '" . $area_id . "'";
        }
        if ($branch_id > 0) {
            $cond .= " && b.id = '" . $branch_id . "'";
        }
        if ($project_id > 0) {
            $project_cond = "&& d.project_id = '" . $project_id . "'";
            $project_cond_loan = "&& loans.project_id = '" . $project_id . "'";
        }

        /*$select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                            ( SELECT COALESCE(count(loan_id),0)
                              FROM donations d
                              WHERE d.deleted = 0
                              AND d.branch_id = b.id " . $project_cond . "
                              AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                            ) as objects_count,
                            ( SELECT COALESCE(sum(amount),0)
                              FROM donations d
                              WHERE d.deleted = 0
                              AND d.branch_id = b.id " . $project_cond . "
                              AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                            ) as amount
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";*/

        $select_query = "SELECT d.branch_id as id, d.area_id,d.region_id,   
                                         (SELECT  
                                CONCAT_WS('+',
                                        CAST((SELECT COUNT(loans.status) 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . " 
                                        AND `date_disbursed` <= '" . $report_date . "' && date_disbursed > 0
                                        AND loans.status = 'collected' AND loans.deleted = 0
                                     ) AS char(20)),CAST((
                                        SELECT COUNT(loans.status) as status 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = d.branch_id " . $project_cond_loan . " 
                                        AND `date_disbursed` <= '" . $report_date . "' && date_disbursed > 0
                                        AND loans.status = 'loan completed' AND loans.deleted = 0
                                        AND `loan_completed_date` > '" . $report_date . "' 
                                    )AS char(20)),
                                    CAST(SUM(l.loan_amount) AS char(20))
                                )
                            FROM loans l 
                            INNER JOIN applications app ON l.application_id = app.id 
                            INNER JOIN members m ON app.member_id = m.id 
                            WHERE l.status not in ['not collected','rejected'] AND l.deleted = 0
                            AND l.branch_id = d.branch_id" . $project_cond_loan . " 
                            AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0
                            ) as activeloans
                        FROM donations d 
                        WHERE d.deleted = 0 " . $project_cond . " 
                        AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                        group by d.branch_id order by d.region_id asc";
        //die($select_query);
        $total_loans = $connection->createCommand($select_query)->queryAll();
        return $total_loans;
    }

    public static function get_disb_data($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id)
    {
        $connection = Yii::$app->db;
        $cond = '';
        $project_cond = '';

        if ($region_id > 0) {
            $cond .= " && b.region_id = '" . $region_id . "'";
        }
        if ($area_id > 0) {
            $cond .= " && b.area_id = '" . $area_id . "'";
        }
        if ($branch_id > 0) {
            $cond .= " && b.id = '" . $branch_id . "'";
        }
        if ($project_id > 0) {
            $project_cond = "&& l.project_id = '" . $project_id . "'";
            $project_cond_a = "&& loans.project_id = '" . $project_id . "'";
        }

        /*$select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                            ( SELECT COALESCE(count(l.id),0)
                              FROM loans l
                              WHERE l.deleted = 0
                              AND l.branch_id = b.id " . $project_cond . "
                              AND l.date_disbursed between '" . $report_date . "' AND  '" . $report_date_end . "' AND l.date_disbursed > 0
                              AND l.status not in ('not collected','pending')
                            ) as objects_count,
                            ( SELECT COALESCE(sum(loan_amount),0)
                              FROM loans l
                              WHERE l.deleted = 0
                              AND l.branch_id = b.id " . $project_cond . "
                              AND l.date_disbursed between '" . $report_date . "' AND  '" . $report_date_end . "' AND l.date_disbursed > 0
                              AND l.status not in ('not collected','pending')
                            ) as amount
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";*/

        $select_query = "SELECT l.branch_id as id, l.area_id, l.region_id, (SELECT COALESCE(count(loans.id),0) from loans where  loans.date_disbursed between '" . $report_date . "' AND  '" . $report_date_end . "' AND loans.date_disbursed > 0 AND loans.deleted = 0  AND  loans.branch_id = l.branch_id AND loans.status not in ('not collected','pending','grant') " . $project_cond_a . " ) as objects_count,COALESCE(count(t.id),0) as no_of_tranches, COALESCE(sum(t.tranch_amount),0) as amount
                         FROM loan_tranches t 
                         INNER JOIN loans l on l.id = t.loan_id
                         WHERE l.deleted = 0 " . $project_cond . " 
                         AND t.date_disbursed between '" . $report_date . "' AND  '" . $report_date_end . "' AND t.date_disbursed > 0                    
                         AND l.status not in ('not collected','pending','rejected','grant')
                         group by l.branch_id order by l.region_id asc";

        //die($select_query);
        $total_loans = $connection->createCommand($select_query)->queryAll();
        return $total_loans;
    }

    public static function get_applications_count($report_date,$report_date_end, $region_id, $area_id, $branch_id, $project_id)
    {
        $connection = Yii::$app->db;
        $cond = '';
        $project_cond = '';

        if ($region_id > 0) {
            $cond .= " && b.region_id = '" . $region_id . "'";
        }
        if ($area_id > 0) {
            $cond .= " && b.area_id = '" . $area_id . "'";
        }
        if ($branch_id > 0) {
            $cond .= " && b.id = '" . $branch_id . "'";
        }
        if ($project_id > 0) {
            $project_cond = "&& a.project_id = '" . $project_id . "'";
        }

        $select_query = "SELECT b.id,b.name,b.region_id,b.area_id, 
                            ( SELECT COALESCE(count(a.id),0)
                              FROM applications a 
                              WHERE a.deleted = 0 
                              AND a.branch_id = b.id " . $project_cond . " 
                              AND a.application_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND a.application_date > 0
                            ) as objects_count,
                            ( SELECT COALESCE(count(a.id),0)
                              FROM applications a 
                              WHERE a.deleted = 0 
                              AND a.branch_id = b.id " . $project_cond . " 
                              AND a.application_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND a.application_date > 0
                              AND a.status='rejected'
                            ) as rejected_applications,
                            ( SELECT COALESCE(count(a.id),0)
                              FROM applications a 
                              inner join loans l on a.id=l.application_id
                              WHERE a.deleted = 0 
                              AND a.branch_id = b.id " . $project_cond . " 
                              AND a.application_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND a.application_date > 0
                              AND l.disbursement_id <> 0 and l.disbursement_id IS NOT NULL and l.status != 'not collected'
                            ) as disbursed_applications
                            
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";

        $total_applications = $connection->createCommand($select_query)->queryAll();
        return $total_applications;
    }

    public static function parse_branches($branches)
    {
        $list = array();
        foreach ($branches as $one) {
            $list[$one['id']] = array(
                'branch_id' => $one['id'],
                'branch_code' => $one['code'],
                'branch' => $one['name'],
                'area_id' => $one['area_id'],
                'region_id' => $one['region_id'],
                'cr_division_id' => $one['cr_division_id'],
                'city_id' => $one['city_id'],
                'tehsil_id' => $one['tehsil_id'],
                'district_id' => $one['district_id'],
                'division_id' => $one['division_id'],
                'province_id' => $one['province_id'],
                'country_id' => $one['country_id'],
            );
        }
        return $list;
    }

    public static function parse_json_account_reports($account_report)
    {
        $big_array = [];
        if (empty($account_report)) {
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;

        foreach ($account_report as $p) {
            unset($p['region']);
            unset($p['area']);
            unset($p['branch']);
            $result[$i]['pr']['project_id'] = $p['report']['project_id'];
            unset($p['report']);
            $result[$i]['pd'] = $p;
            $i++;
        }

        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $temp = [];
        $old_region_id = 0;
        $old_area_id = 0;
        $old_branch_id = 0;
        end($result);
        $last_key = key($result);
        foreach ($result as $key => $one) {
            $pd = $one['pd'];
            if ($old_area_id == 0) {
                $old_region_id = $pd['region_id'];
                $old_area_id = $pd['area_id'];
                $old_branch_id = $pd['branch_id'];
            }
            if ($pd['area_id'] == $old_area_id) {
                $temp[] = $pd;
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                }
                continue;
            } else {
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    unset($temp);
                    $temp[] = $pd;
                    $big_array[$pd['region_id']][$pd['area_id']] = $temp;

                } else {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    $old_region_id = $pd['region_id'];
                    $old_area_id = $pd['area_id'];
                    $old_branch_id = $pd['branch_id'];

                    unset($temp);
                    $temp[] = $pd;
                }

            }
        }
        $temp_sum = array('id' => 0, 'objects_count' => 0, 'amount' => 0, 'disbursed_applications' => 0, 'rejected_applications' => 0);
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
                    unset($branch['arc_account_report_id']);
                    unset($branch['division_id']);
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['branch_id']];
                    unset($branch['branch_id']);

                    $grand_sum['objects_count'] += $branch['objects_count'];
                    $grand_sum['amount'] += $branch['amount'];
                    $grand_sum['rejected_applications'] += $branch['rejected_applications'];
                    $grand_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($branch['objects_count']!=0){
                        $grand_sum['average'] += ($branch['amount']/$branch['objects_count']) ;
                    }else{
                        $grand_sum['average'] += 0;
                    }


                    $region_sum['objects_count'] += $branch['objects_count'];
                    $region_sum['amount'] += $branch['amount'];
                    $region_sum['rejected_applications'] += $branch['rejected_applications'];
                    $region_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($branch['objects_count']!=0){
                        $region_sum['average'] += ($branch['amount']/$branch['objects_count']) ;
                    }else{
                        $region_sum['average'] += 0;
                    }

                    $area_sum['objects_count'] += $branch['objects_count'];
                    $area_sum['amount'] += $branch['amount'];
                    $area_sum['rejected_applications'] += $branch['rejected_applications'];
                    $area_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($branch['objects_count']!=0){
                        $area_sum['average'] += ($branch['amount']/$branch['objects_count']) ;
                    }else{
                        $area_sum['average'] += 0;
                    }
                    $area_sum['children'][$b_key] = $branch;
                }

                $region_sum['children'][$count] = $area_sum;
                $count++;
            }

            $new_big_array[] = $region_sum;
        }

        $new_big_array[] = $grand_sum;
        $progress_report = json_encode($new_big_array);
        return $progress_report;
    }

    public static function parse_json_fundRequest_reports($fundRequest_report)
    {


        $big_array = [];
        //print_r($fundRequest_report);die();
        if (empty($fundRequest_report)) {
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;

        foreach ($fundRequest_report as $fr) {
            unset($fr['region']);
            unset($fr['area']);
            unset($fr['branch']);
            $result[$i]['pd'] = $fr;
            $i++;
        }
       // print_r($result);die();
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $temp = [];
        $old_region_id = 0;
        $old_area_id = 0;
        $old_branch_id = 0;
        end($result);
        $last_key = key($result);
        foreach ($result as $key => $one) {
            $pd = $one['pd'];
            if ($old_area_id == 0) {
                $old_region_id = $pd['region_id'];
                $old_area_id = $pd['area_id'];
                $old_branch_id = $pd['branch_id'];
            }
            if ($pd['area_id'] == $old_area_id) {
                $temp[] = $pd;
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                }
                continue;
            } else {
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    unset($temp);
                    $temp[] = $pd;
                    $big_array[$pd['region_id']][$pd['area_id']] = $temp;

                } else {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    $old_region_id = $pd['region_id'];
                    $old_area_id = $pd['area_id'];
                    $old_branch_id = $pd['branch_id'];

                    unset($temp);
                    $temp[] = $pd;
                }

            }
        }
        $temp_sum = array('id' => 0,/*'objects_count' => 0, 'amount' => 0,*/ 'disbursement_amount' => 0,'account_report_amount_disbursed' => 0, 'fund_requests_amount_processed' => 0);
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
            //print_r($region);die();
            foreach ($region as $key_area => $area) {
                //print_r($area);die();
                $count_area++;
                $area_sum = $temp_sum;
                $area_sum['id'] = $key_area;
                $area_sum['name'] = isset($areas[$key_area]) ? ($areas[$key_area]) : '';
                $count_branch = 0;
                foreach ($area as $b_key => $branch) {
                    $count_branch++;
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['branch_id']];

                   unset($branch['branch_id']);
                    $grand_sum['disbursement_amount'] += $branch['disbursement_amount'];
                    $grand_sum['fund_requests_amount_processed'] += $branch['fund_requests_amount_processed'];
                    $grand_sum['account_report_amount_disbursed'] += $branch['account_report_amount_disbursed'];

                    $region_sum['disbursement_amount'] += $branch['disbursement_amount'];
                    $region_sum['fund_requests_amount_processed'] += $branch['fund_requests_amount_processed'];
                    $region_sum['account_report_amount_disbursed'] += $branch['account_report_amount_disbursed'];

                    $area_sum['disbursement_amount'] += $branch['disbursement_amount'];
                    $area_sum['fund_requests_amount_processed'] += $branch['fund_requests_amount_processed'];
                    $area_sum['account_report_amount_disbursed'] += $branch['account_report_amount_disbursed'];

                    $area_sum['children'][$b_key] = $branch;
                }

                $region_sum['children'][$count] = $area_sum;
                $count++;
            }
            $new_big_array[] = $region_sum;
        }

        $new_big_array[] = $grand_sum;
        $fund_Request_report = json_encode($new_big_array);
        //print_r($fund_Request_report);die();
        return $fund_Request_report;
    }

    public static function parse_json_account_reports_donation($account_report)
    {
        $big_array = [];
        if (empty($account_report)) {
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;

        foreach ($account_report as $p) {
            unset($p['region']);
            unset($p['area']);
            unset($p['branch']);
            $result[$i]['pr']['project_id'] = $p['report']['project_id'];
            unset($p['report']);
            $result[$i]['pd'] = $p;
            $i++;
        }

        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $temp = [];
        $old_region_id = 0;
        $old_area_id = 0;
        $old_branch_id = 0;
        end($result);
        $last_key = key($result);
        foreach ($result as $key => $one) {
            $pd = $one['pd'];
            if ($old_area_id == 0) {
                $old_region_id = $pd['region_id'];
                $old_area_id = $pd['area_id'];
                $old_branch_id = $pd['branch_id'];
            }
            if ($pd['area_id'] == $old_area_id) {
                $temp[] = $pd;
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                }
                continue;
            } else {
                if ($last_key == $key) {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    unset($temp);
                    $temp[] = $pd;
                    $big_array[$pd['region_id']][$pd['area_id']] = $temp;

                } else {
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    $old_region_id = $pd['region_id'];
                    $old_area_id = $pd['area_id'];
                    $old_branch_id = $pd['branch_id'];

                    unset($temp);
                    $temp[] = $pd;
                }

            }
        }
        $temp_sum = array('id' => 0, 'objects_count' => 0, 'amount' => 0, 'disbursed_applications' => 0, 'rejected_applications' => 0);
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
                    unset($branch['arc_account_report_id']);
                    unset($branch['division_id']);
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['branch_id']];
                    unset($branch['branch_id']);
                    if($branch['objects_count']!=0){
                        $branch['average'] = ($branch['amount']/$branch['objects_count']) ;
                    }else{
                        $branch['average'] = 0;
                    }
                    $grand_sum['objects_count'] += $branch['objects_count'];
                    $grand_sum['amount'] += $branch['amount'];
                    $grand_sum['rejected_applications'] += $branch['rejected_applications'];
                    $grand_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($grand_sum['objects_count']!=0){
                        $grand_sum['average'] = ($grand_sum['amount']/$grand_sum['objects_count']) ;
                    }else{
                        $grand_sum['average'] = 0;
                    }


                    $region_sum['objects_count'] += $branch['objects_count'];
                    $region_sum['amount'] += $branch['amount'];
                    $region_sum['rejected_applications'] += $branch['rejected_applications'];
                    $region_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($region_sum['objects_count']!=0){
                        $region_sum['average'] = ($region_sum['amount']/$region_sum['objects_count']) ;
                    }else{
                        $region_sum['average'] = 0;
                    }

                    $area_sum['objects_count'] += $branch['objects_count'];
                    $area_sum['amount'] += $branch['amount'];
                    $area_sum['rejected_applications'] += $branch['rejected_applications'];
                    $area_sum['disbursed_applications'] += $branch['disbursed_applications'];
                    if($area_sum['objects_count']!=0){
                        $area_sum['average'] = ($area_sum['amount']/$area_sum['objects_count']) ;
                    }else{
                        $area_sum['average'] = 0;
                    }
                    $area_sum['children'][$b_key] = $branch;
                }

                $region_sum['children'][$count] = $area_sum;
                $count++;
            }

            $new_big_array[] = $region_sum;
        }

        $new_big_array[] = $grand_sum;
        $progress_report = json_encode($new_big_array);
        return $progress_report;
    }
    static public function getAccountReports($project_id,$code)
    {
        return ArcAccountReports::find()->select('id,report_date')->distinct()->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])->filterWhere(['=', 'project_id', $project_id])->andFilterWhere(['code' => $code])->orderBy(['report_date' => SORT_DESC])->all();
    }

    static public function getReports($cond)
    {
        $query = ArcAccountReports::find()
            ->select(['sum(d.objects_count) as objects_count', 'sum(d.amount) as amount', 'sum(d.rejected_applications) as rejected_applications', 'sum(d.disbursed_applications) as disbursed_applications'])
            ->join('inner join', 'arc_account_report_details as d', 'd.arc_account_report_id=arc_account_reports.id')
            ->where([
                'arc_account_reports.project_id' => '0',
                'arc_account_reports.report_date' => strtotime(date('Y-m-d')),
            ]);
        if (!empty($cond)) {
            $query->andWhere([$cond['column'] => $cond['value']]);
        }
        return $query->asArray()->all();
    }

    static public function getReportsOfBranch($branch_id)
    {
        $query = ArcAccountReports::find()
            ->select(['sum(d.objects_count) as objects_count', 'sum(d.amount) as amount', 'sum(d.rejected_applications) as rejected_applications', 'sum(d.disbursed_applications) as disbursed_applications'])
            ->join('inner join', 'arc_account_report_details as d', 'd.arc_account_report_id=arc_account_reports.id')
            ->where([
                'arc_account_reports.project_id' => '0',
                'arc_account_reports.report_date' => strtotime(date('Y-m-d')),
                'd.branch_id' => $branch_id
            ]);
        return $query->asArray()->all();
    }

    static function Summary($params){
        $cond = '';
        $code = $params['code'];
        /*$period = "'daily','daily-project'";
        $cond .= " && a.period in (".trim($period,',').")";*/
        $group_by_cond = 'd.region_id';
        $columns_name = 'd.id, regions.name as region_name,';

        if (empty($params['ArcAccountReportDetailsSearch']['from_date']) && empty($params['ArcAccountReportDetailsSearch']['to_date']))
        {

            $start_date = strtotime(date('Y-m-01'));
            $end_date = strtotime(date('Y-m-t 23:59:59'));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
        }

        if (!empty($params['ArcAccountReportDetailsSearch']['to_date']) && !empty($params['ArcAccountReportDetailsSearch']['from_date'])) {

            $start_date = strtotime(date('Y-m-01', strtotime($params['ArcAccountReportDetailsSearch']['from_date'])));
            $end_date = strtotime(date('Y-m-t 23:59:59', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";

        }

        /*if(!empty($params['ArcAccountReportDetailsSearch']['report_date'])){

            $date = explode(' - ', $params['ArcAccountReportDetailsSearch']['report_date']);
            $cond .= " && a.report_date between '".strtotime($date[0])."' and '".strtotime($date[1])."'";

        }*/
        if(!empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $cond .= " && d.region_id = '".$params['ArcAccountReportDetailsSearch']['region_id']."'";
            $cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
            if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
                $group_by_cond = 'd.branch_id';
                $columns_name = 'd.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            }else{
                $group_by_cond = 'd.area_id';
                $columns_name = 'regions.name as region_name, areas.name as area_name,';
            }
        }
        if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
            $cond .= " && d.area_id = '".$params['ArcAccountReportDetailsSearch']['area_id']."'";
        }
        if(isset($params['ArcAccountReportDetailsSearch']['branch_id']) && !empty($params['ArcAccountReportDetailsSearch']['branch_id'])){
            $cond .= " && d.branch_id = '".$params['ArcAccountReportDetailsSearch']['branch_id']."'";
        }
        if(!empty($params['ArcAccountReportDetailsSearch']['project_ids'])){
            $project_ids = '';
            if(is_array($params['ArcAccountReportDetailsSearch']['project_ids'] )) {
                foreach ($params['ArcAccountReportDetailsSearch']['project_ids'] as $p) {
                    $project_ids .= $p . ',';
                }
            } else {
                $project_ids = $params['ArcAccountReportDetailsSearch']['project_ids'];
            }
            $cond .= " && a.project_id in (".trim($project_ids,',').")";
        } else {
            $cond .= " && a.project_id = 0";
        }
        /*if(!empty($params['LoansSearch']['crop_type'])){
            $cond .= " && borrowers.cropType = '".$params['LoansSearch']['crop_type']."'";
        }*/
        if(empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
        }

        $sql = "SELECT  ".$columns_name."   sum(d.objects_count) as no_of_loans , sum(d.amount) as amount
               ,sum(d.rejected_applications) as rejected_applications,sum(d.disbursed_applications) as disbursed_applications from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id
               inner join branches on branches.id = d.branch_id
               inner join areas on areas.id = d.area_id
               inner join regions on regions.id = d.region_id
               where 1 and a.deleted=0 and a.status=1 and a.code = '".$code."' ".$cond." group by ".$group_by_cond." ";
        /*$sql = "SELECT ".$columns_name." COALESCE(count(loans.id),0) as no_of_loans, COALESCE(sum(loan_amount),0) as loan_amount from loans
                        inner join branches on branches.id = loans.branch_id
                        inner join areas on areas.id = loans.area_id
                        inner join regions on regions.id = loans.region_id
                        where 1 and loans.status not in ('not collected','pending') and loans.deleted=0 ".$cond." group by ".$group_by_cond." ";*/

        //die($sql);
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            //'totalCount' => 22,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        /*echo'<pre>';
        print_r($dataProvider->getModels());
        die();*/
        return $dataProvider;
    }
    static function TakafulSummary($params){
        $cond = '';
        $code = $params['code'];
        /*$period = "'daily','daily-project'";
        $cond .= " && a.period in (".trim($period,',').")";*/
        $group_by_cond = 'd.region_id';
        $columns_name = 'd.id, regions.name as region_name,';

        if (empty($params['ArcAccountReportDetailsSearch']['from_date']) && empty($params['ArcAccountReportDetailsSearch']['to_date']))
        {

            $start_date = strtotime(date('Y-m-01'));
            $end_date = strtotime(date('Y-m-t 23:59:59'));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
        }

        if (!empty($params['ArcAccountReportDetailsSearch']['to_date']) && !empty($params['ArcAccountReportDetailsSearch']['from_date'])) {

            $start_date = strtotime(date('Y-m-01', strtotime($params['ArcAccountReportDetailsSearch']['from_date'])));
            $end_date = strtotime(date('Y-m-t 23:59:59', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";

        }

        /*if(!empty($params['ArcAccountReportDetailsSearch']['report_date'])){

            $date = explode(' - ', $params['ArcAccountReportDetailsSearch']['report_date']);
            $cond .= " && a.report_date between '".strtotime($date[0])."' and '".strtotime($date[1])."'";

        }*/
        if(!empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $cond .= " && d.region_id = '".$params['ArcAccountReportDetailsSearch']['region_id']."'";
            $cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);

            if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
                $group_by_cond = 'd.branch_id';
                $columns_name = 'd.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            }else{
                $group_by_cond = 'd.area_id';
                $columns_name = 'regions.name as region_name, areas.name as area_name,';
            }
        }
        if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
            $cond .= " && d.area_id = '".$params['ArcAccountReportDetailsSearch']['area_id']."'";
        }
        if(isset($params['ArcAccountReportDetailsSearch']['branch_id']) && !empty($params['ArcAccountReportDetailsSearch']['branch_id'])){
            $cond .= " && d.branch_id = '".$params['ArcAccountReportDetailsSearch']['branch_id']."'";
        }
        if(!empty($params['ArcAccountReportDetailsSearch']['project_ids'])){
            $project_ids = '';
            if(is_array($params['ArcAccountReportDetailsSearch']['project_ids'] )) {
                foreach ($params['ArcAccountReportDetailsSearch']['project_ids'] as $p) {
                    $project_ids .= $p . ',';
                }
            } else {
                $project_ids = $params['ArcAccountReportDetailsSearch']['project_ids'];
            }
            $cond .= " && a.project_id in (".trim($project_ids,',').")";
        } else {
            $cond .= " && a.project_id = 0";
        }

        if(empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
        }

        $sql = "SELECT ".$columns_name." sum(d.objects_count) as no_of_loans , 
               sum(d.amount) as amount,
               sum(d.rejected_applications) as rejected_applications,
               sum(d.disbursed_applications) as disbursed_applications 
               from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id
               inner join branches on branches.id = d.branch_id
               inner join areas on areas.id = d.area_id
               inner join regions on regions.id = d.region_id
               where 1 and a.deleted=0 and a.status=1 and a.code = '".$code."' ".$cond." group by ".$group_by_cond." ";



        $dataProvider = new SqlDataProvider([
            'sql' => $sql,

            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    static function ProgressSummary($params,$export=false){

        $connection = Yii::$app->db;

        $p_cond = '';
        $cond = '';
        $b_cond = '';
        $group_by_cond = 'b.id,p.id';
        $columns_name = 'regions.name as region_name, areas.name as area_name, b.code as branch_code,b.name as branch_name,p.name as project_name,';

        if (empty($params['ArcAccountReportDetailsSearch']['from_date']) && empty($params['ArcAccountReportDetailsSearch']['to_date']))
        {
            $start_date = strtotime(date('Y-m-01'));
            $end_strat_date = strtotime(date('Y-m-t'));
            $end_date = strtotime(date('Y-m-t 23:59:59'));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
            $p_cond .= " && a.report_date between '" . $end_strat_date . "' and '" . $end_date . "'";
        }

        if (!empty($params['ArcAccountReportDetailsSearch']['to_date']) && !empty($params['ArcAccountReportDetailsSearch']['from_date'])) {

            $start_date = strtotime(date('Y-m-01', strtotime($params['ArcAccountReportDetailsSearch']['from_date'])));
            $end_strat_date = strtotime(date('Y-m-t', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $end_date = strtotime(date('Y-m-t 23:59:59', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
            $p_cond .= " && a.report_date between '" . $end_strat_date . "' and '" . $end_date . "'";
        }

        if(!empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $b_cond .= " && b.region_id = '".$params['ArcAccountReportDetailsSearch']['region_id']."'";
            //$b_cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
            /*if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
                $group_by_cond = 'b.id';

                $columns_name = 'regions.name as region_name, areas.name as area_name, b.code as branch_code,b.name as branch_name,';
            }else{
                $group_by_cond = 'b.area_id';
                $columns_name = 'regions.name as region_name, areas.name as area_name';
            }*/
        }


        if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
            $b_cond .= " && b.area_id = '".$params['ArcAccountReportDetailsSearch']['area_id']."'";
        }
        if(isset($params['ArcAccountReportDetailsSearch']['branch_id']) && !empty($params['ArcAccountReportDetailsSearch']['branch_id'])){
           $b_cond .= " && b.id = '".$params['ArcAccountReportDetailsSearch']['branch_id']."'";
        }
        if(!empty($params['ArcAccountReportDetailsSearch']['project_ids'])){
            $project_ids = '';
            foreach ($params['ArcAccountReportDetailsSearch']['project_ids'] as $p){
                $project_ids .= $p.',';
            }
            //$cond .= " && a.project_id in (".trim($project_ids,',').")";
            //$p_cond .= " && a.project_id in (".trim($project_ids,',').")";
            $b_cond .=" && bp.project_id in (".trim($project_ids,',').")";
        } else {
            //$cond .= " && a.project_id = 0";
            //$p_cond .= " && a.project_id = 0";
            $b_cond .="";
        }
        /*print_r($cond);
        die();*/

        $sql = "SELECT  ".$columns_name."  (select sum(d.amount) from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id where 1 and a.deleted=0 and a.status=1 and a.code = 'recv' and d.branch_id = b.id and a.project_id=bp.project_id ".$cond.")  as recv_amount, 
               (select sum(d.amount) from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id where 1 and a.deleted=0 and a.status=1 and a.code = 'disb' and d.branch_id = b.id  and a.project_id=bp.project_id ".$cond.")  as disb_amount,
               (select sum(d.olp_amount) from progress_report_details as d
               inner join progress_reports as a on a.id=d.progress_report_id where 1 and a.gender='0' and a.deleted=0 and a.status=1 and d.branch_id = b.id and a.project_id=bp.project_id ".$p_cond.")  as olp_amount
               from branch_projects_mapping as bp
               INNER join branches as b on b.id =bp.branch_id
               inner join projects p on p.id = bp.project_id
             
               inner join areas on areas.id = b.area_id
               inner join regions on regions.id = b.region_id
               where 1 and b.deleted=0 and b.status=1 ".$b_cond." group by ".$group_by_cond." ";
        if($export) {

            $data = $connection->createCommand($sql)->queryAll();
            return $data;
        }
        else{
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                //'totalCount' => 22,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);
            return $dataProvider;
        }
    }

    static function ProgressSummaryDetails($params,$export=false){

        $connection = Yii::$app->db;

        $p_cond = '';
        $closing_p_cond = '';
        $cond = '';
        $b_cond = '';
        $group_by_cond = 'b.id,p.id';
        $columns_name = 'regions.name as region_name, areas.name as area_name, b.code as branch_code,b.name as branch_name,p.name as project_name,';


        if (empty($params['ArcAccountReportDetailsSearch']['from_date']) && empty($params['ArcAccountReportDetailsSearch']['to_date']))
        {
            $start_date = strtotime(date('Y-m-01'));
            $end_strat_date = strtotime(date('Y-m-t'));
            $end_date = strtotime(date('Y-m-t 23:59:59'));
            $closing_end_date = strtotime(date('Y-m-t 23:59:59',strtotime('-1 month', strtotime(date('Y-m-t')))));
            $closing_end_strat_date = strtotime(date('Y-m-t',strtotime('-1 month', strtotime(date('Y-m-t')))));
            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
            $p_cond .= " && a.report_date between '" . $end_strat_date . "' and '" . $end_date . "'";
            $closing_p_cond .= " && a.report_date between '" . $closing_end_strat_date . "' and '" . $closing_end_date . "'";
        }

        if (!empty($params['ArcAccountReportDetailsSearch']['to_date']) && !empty($params['ArcAccountReportDetailsSearch']['from_date'])) {

            $start_date = strtotime(date('Y-m-01', strtotime($params['ArcAccountReportDetailsSearch']['from_date'])));
            $end_strat_date = strtotime(date('Y-m-t', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $end_date = strtotime(date('Y-m-t 23:59:59', strtotime($params['ArcAccountReportDetailsSearch']['to_date'])));
            $closing_end_date = strtotime(date('Y-m-t 23:59:59',strtotime('-1 month', strtotime($params['ArcAccountReportDetailsSearch']['to_date']))));
            $closing_end_strat_date = strtotime(date('Y-m-t',strtotime('-1 month', strtotime($params['ArcAccountReportDetailsSearch']['to_date']))));

            $cond .= " && a.report_date between '" . $start_date . "' and '" . $end_date . "'";
            $p_cond .= " && a.report_date between '" . $end_strat_date . "' and '" . $end_date . "'";
            $closing_p_cond .= " && a.report_date between '" . $closing_end_strat_date . "' and '" . $closing_end_date . "'";
        }

        if(!empty($params['ArcAccountReportDetailsSearch']['region_id'])){
            $b_cond .= " && b.region_id = '".$params['ArcAccountReportDetailsSearch']['region_id']."'";
            //$b_cond .= Yii::$app->Permission->searchAccountReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
            /*if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
                $group_by_cond = 'b.id';

                $columns_name = 'regions.name as region_name, areas.name as area_name, b.code as branch_code,b.name as branch_name,';
            }else{
                $group_by_cond = 'b.area_id';
                $columns_name = 'regions.name as region_name, areas.name as area_name';
            }*/
        }
        if(isset($params['ArcAccountReportDetailsSearch']['area_id']) && !empty($params['ArcAccountReportDetailsSearch']['area_id'])){
           $b_cond .= " && b.area_id = '".$params['ArcAccountReportDetailsSearch']['area_id']."'";
        }
        if(isset($params['ArcAccountReportDetailsSearch']['branch_id']) && !empty($params['ArcAccountReportDetailsSearch']['branch_id'])){
            $b_cond .= " && b.id = '".$params['ArcAccountReportDetailsSearch']['branch_id']."'";
        }
        if(!empty($params['ArcAccountReportDetailsSearch']['project_ids'])){
            $project_ids = '';
            foreach ($params['ArcAccountReportDetailsSearch']['project_ids'] as $p){
                $project_ids .= $p.',';
            }
            //$cond .= " && a.project_id in (".trim($project_ids,',').")";
            //$p_cond .= " && a.project_id in (".trim($project_ids,',').")";
            $b_cond .= " && bp.project_id in (".trim($project_ids,',').")";
            //$closing_p_cond .= " && a.project_id in (".trim($project_ids,',').")";
        } else {
            //$cond .= " && a.project_id = 0";
            //$p_cond .= " && a.project_id = 0";
            $b_cond .= "";
            //$closing_p_cond .= " && a.project_id = 0";
        }

        $sql = "SELECT  ".$columns_name."  (select sum(d.amount) from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id where 1 and a.deleted=0 and a.status=1 and a.code = 'recv' and d.branch_id = b.id and a.project_id=bp.project_id  ".$cond.")  as recv_amount, 
               (select CONCAT_WS('+', CAST(sum(d.amount)AS char(20)), CAST(sum(d.objects_count) AS char(20)) ) from arc_account_report_details as d
               inner join arc_account_reports as a on a.id=d.arc_account_report_id where 1 and a.deleted=0 and a.status=1 and a.code = 'disb' and d.branch_id = b.id and a.project_id=bp.project_id  ".$cond.")  as disb,
               (select  CONCAT_WS('+', CAST(sum(d.olp_amount)  AS char(20)),CAST(sum(active_loans) AS char(20)) ) from progress_report_details as d
               inner join progress_reports as a on a.id=d.progress_report_id where 1 and a.gender='0' and a.deleted=0 and a.status=1 and d.branch_id = b.id and a.project_id=bp.project_id ".$p_cond.")  as opening,
               (select  CONCAT_WS('+', CAST(sum(d.olp_amount)  AS char(20)),CAST(sum(active_loans) AS char(20)) ) from progress_report_details as d
               inner join progress_reports as a on a.id=d.progress_report_id where 1 and a.gender='0' and a.deleted=0 and a.status=1 and d.branch_id = b.id and a.project_id=bp.project_id ".$closing_p_cond.")  as closing
               from branch_projects_mapping as bp
               INNER join branches as b on b.id =bp.branch_id
               inner join projects p on p.id = bp.project_id
             
               inner join areas on areas.id = b.area_id
               inner join regions on regions.id = b.region_id
               where 1 and b.deleted=0 and b.status=1 ".$b_cond." group by ".$group_by_cond." ";
        if($export) {
            $data = $connection->createCommand($sql)->queryAll();
            return $data;
        }
        else{
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                //'totalCount' => 22,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);
            return $dataProvider;
        }
    }

    public static function parse_object($data)
    {
        $array = [];
        if(isset($data['activeloans_1']) || isset($data['activeloans_2']))
        {
            $data['objects_count'] = $data['activeloans_1'] + $data['activeloans_2'];
        }
        return array('objects_count' => $data['objects_count'],'amount' => $data['amount'],
            'applications_rejected' => isset($data['applications_rejected']) ? $data['applications_rejected'] : 0,
            'disbursed_applications' => isset($data['disbursed_applications']) ? $data['disbursed_applications'] : 0,
            'no_of_tranches' => isset($data['no_of_tranches']) ? $data['no_of_tranches'] : 0
        );
    }

    public static function parse_mdp_object($data)
    {
        $array = explode('+', $data);

        $active_loans1 = isset($array[0]) ? ($array[0]) : 0;
        $active_loans2 = isset($array[1]) ? ($array[1]) : 0;
        $amount = isset($array[2]) ? ($array[2]) : 0;

        return array('objects_count' => $active_loans1 + $active_loans2,'amount' => $amount
        );
    }

    public static function parse_active_loans($data)
    {
        $array = explode('+', $data);

        $active_loans1 = isset($array[0]) ? ($array[0]) : 0;
        $active_loans2 = isset($array[1]) ? ($array[1]) : 0;

        return array('objects_count' => ($active_loans1 + $active_loans2)
        );
    }
}