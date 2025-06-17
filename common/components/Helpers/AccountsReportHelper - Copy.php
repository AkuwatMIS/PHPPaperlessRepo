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
                                         COALESCE(sum(d.amount),0) as amount
                        FROM donations d 
                        WHERE d.deleted = 0 " . $project_cond . " 
                        AND d.receive_date between '" . $report_date . "' AND  '" . $report_date_end . "' AND d.receive_date > 0
                        group by d.branch_id order by d.region_id asc";
        //die($select_query);
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
                            WHERE l.status != 'not collected' AND l.deleted = 0
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

        $select_query = "SELECT l.branch_id as id, l.area_id, l.region_id, COALESCE(count(l.id),0) as objects_count, COALESCE(sum(l.loan_amount),0) as amount
                         FROM loans l 
                         WHERE l.deleted = 0 " . $project_cond . " 
                         AND l.date_disbursed between '" . $report_date . "' AND  '" . $report_date_end . "' AND l.date_disbursed > 0
                         AND l.status not in ('not collected','pending')
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


                    $region_sum['objects_count'] += $branch['objects_count'];
                    $region_sum['amount'] += $branch['amount'];
                    $region_sum['rejected_applications'] += $branch['rejected_applications'];
                    $region_sum['disbursed_applications'] += $branch['disbursed_applications'];

                    $area_sum['objects_count'] += $branch['objects_count'];
                    $area_sum['amount'] += $branch['amount'];
                    $area_sum['rejected_applications'] += $branch['rejected_applications'];
                    $area_sum['disbursed_applications'] += $branch['disbursed_applications'];
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
            foreach ($params['ArcAccountReportDetailsSearch']['project_ids'] as $p){
                $project_ids .= $p.',';
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

    public static function parse_object($data)
    {
        $array = explode('+', $data);

        $active_loans1 = isset($array[0]) ? ($array[0]) : 0;
        $active_loans2 = isset($array[1]) ? ($array[1]) : 0;
        return array('objects_count' => ($active_loans1 + $active_loans2) /*$data['objects_count']*/,'amount' => $data['amount'],
            'applications_rejected' => isset($data['applications_rejected']) ? $data['applications_rejected'] : 0,
            'disbursed_applications' => isset($data['disbursed_applications']) ? $data['disbursed_applications'] : 0
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