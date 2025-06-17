<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\components\Helpers\ReportsHelper;
use common\models\Branches;
use common\models\Districts;
use common\models\ProgressReports;
use Yii;
use yii\helpers\ArrayHelper;

class ProgressReportHelper
{
    public static function getTotalDisbursementLastMonth($project_id)
    {
        $report = ProgressReports::find()->where(['status' => 1,'project_id' => $project_id])->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->one();
        $sql = "SELECT sum(cum_disb) as cum_disb FROM progress_report_details where recovery_percentage != 0 and progress_report_id = '".$report->id."' ";

        $command = Yii::$app->db->createCommand($sql);
        $report_data = $command->queryOne();
        return $report_data['cum_disb'];

    }

    public static function getProgressReportData($project_id)
    {
        $report = ProgressReports::find()->where(['status' => 1,'project_id' => $project_id])->andWhere(['between','report_date',strtotime(date('Y-m-d 00:00:00',strtotime('last day of previous month'))),strtotime(date('Y-m-d 23:59:59',strtotime('last day of previous month')))])->one();
        $sql = "SELECT sum(cum_disb) as cum_disb,sum(olp_amount) as olp_amount FROM progress_report_details where recovery_percentage != 0 and progress_report_id = '".$report->id."' ";

        $command = Yii::$app->db->createCommand($sql);
        $report_data = $command->queryOne();
        return $report_data;

    }

    public static function get_male_female($report_date, $region_id, $area_id, $branch_id, $project_id, $gender = 0)
    {
        $connection = Yii::$app->db;
        $cond = $project_gender_cond = $project_cond_p = '';
        $male_female = array();

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
            $project_cond_p = "&& l.project_id = '" . $project_id . "'";
        }
        if (in_array($gender, array('m', 'f'))) {
            $project_gender_cond = " && m.gender = '" . $gender . "'";

        }
        $select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                              (SELECT
                                    CONCAT_WS('+',
                                        CAST(coalesce(SUM(CASE WHEN m.gender = 'm' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 'f' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 't' THEN 1 ELSE 0 END),0) AS char(20) )
                                    )
                                  FROM loans l
                                  INNER JOIN applications app ON l.application_id = app.id 
                                  INNER JOIN members m ON app.member_id=m.id
                                  
                                  WHERE l.status not in ('not collected','rejected','pending','processed','grant') AND l.deleted = 0 
                                  AND l.branch_id = b.id " . $project_gender_cond . " 
                                  AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0 " . $project_cond_p . "
                              ) male_female_others 
                         FROM branches b 
                         WHERE 1 " . $cond . " 
                         ORDER BY b.region_id, b.code";
        /*$select_query = "
                        SELECT l.branch_id as id, l.area_id, l.region_id,
                                    CONCAT_WS('+',
                                        CAST(coalesce(SUM(CASE WHEN m.gender = 'm' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 'f' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 't' THEN 1 ELSE 0 END),0) AS char(20) )
                                    ) as male_female_others
                                  FROM loans l
                                  INNER JOIN applications app ON l.application_id = app.id 
                                  INNER JOIN members m ON app.member_id=m.id
                                  WHERE l.status != 'not collected' " . $project_gender_cond . " 
                                  AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0   
                                  group by l.branch_id
                                  ORDER BY l.region_id
                                    ";*/
        //die($select_query);
        $male_female = $connection->createCommand($select_query)->queryAll();
        foreach ($male_female as $key => $mf) {
            if ($gender == 'm') {
                $mf['gender'] = 'm';
            } else if ($gender == 'f') {
                $mf['gender'] = 'f';
            } else {
                $mf['gender'] = 0;
            }
            $male_female[$key]['gender'] = $mf['gender'];
        }
        return $male_female;
    }

    public static function get_activeloans_cumdisbursement($report_date, $region_id, $area_id, $branch_id, $project_id, $gender)
    {
        $connection = Yii::$app->db;
        $cond = $project_cond = $project_cond_p = $gender_cond = '';

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
            $project_cond_p = "&& loans.project_id = '" . $project_id . "'";
        }
        if (in_array($gender, array('m', 'f'))) {
            $gender_cond = " && m.gender = '" . $gender . "'";
        }

        $current_date = date('Y-m', ($report_date));

        if ($current_date == date('Y-m')) {
            $first_date = strtotime(date('Y-m-01', ($report_date)));
            $last_date = strtotime(date('Y-m-10', ($report_date)));
            $curr_date = strtotime(date('Y-m-d', ($report_date)));
            if ($curr_date >= $first_date && $curr_date <= $last_date) {
                $last_month_date = strtotime('last day of last month');
                $new_date = $last_month_date;
            } else {
                $new_date = $report_date;
            }
        } else {
            $new_date = $report_date;
        }

        $select_query = "SELECT b.id,b.name,b.region_id,b.area_id,
                            (SELECT
                                    CONCAT_WS('+',
                                        CAST(coalesce(SUM(CASE WHEN m.gender = 'm' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 'f' THEN 1 ELSE 0 END),0) AS char(20)) ,
                                        CAST(coalesce(SUM(case WHEN m.gender = 't' THEN 1 ELSE 0 END),0) AS char(20) )
                                    )
                                  FROM loans l
                                  INNER JOIN applications app ON l.application_id = app.id 
                                  INNER JOIN members m ON app.member_id=m.id
                                  
                                  WHERE l.status not in ('not collected','rejected','pending','processed','grant') AND l.deleted = 0 
                                  AND l.branch_id = b.id " . $gender_cond . " 
                                  AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0 " . $project_cond . "
                              ) male_female_others ,
                              (SELECT
                                  count(m.id)
                                  FROM members m
                                  INNER JOIN applications app ON m.id = app.member_id 
                                  INNER JOIN loans l ON app.id=l.application_id
                                  and l.id=
                                  (select loans.id from loans
                                       INNER JOIN applications app ON loans.application_id = app.id
                                       where app.member_id=m.id ". $project_cond_p ." AND loans.disbursed_amount!=0
                                   ORDER by id LIMIT 1)
                                  WHERE l.status not in ('not collected','rejected','pending','processed','grant') AND l.deleted = 0 and m.deleted= 0
                                  AND l.branch_id = b.id " . $gender_cond . " 
                                  AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0 " . $project_cond . "
                              ) members_count ,
                            (SELECT  
                                CONCAT_WS('+',
                                        CAST((SELECT COUNT(loans.status) 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = b.id " . $project_cond_p . " 
                                        AND `date_disbursed` <= '" . $report_date . "' && date_disbursed > 0 " . $gender_cond . " 
                                        AND loans.status = 'collected' AND loans.deleted = 0
                                     ) AS char(20)),CAST((
                                        SELECT COUNT(loans.status) as status 
                                        FROM loans INNER JOIN applications app ON loans.application_id = app.id
                                        INNER JOIN members m ON app.member_id=m.id
                                        WHERE loans.branch_id = b.id " . $project_cond_p . " 
                                        AND `date_disbursed` <= '" . $report_date . "' && date_disbursed > 0
                                        AND loans.status = 'loan completed' AND loans.deleted = 0
                                        AND `loan_completed_date` > '" . $report_date . "' " . $gender_cond . "
                                    )AS char(20)),
                                    CAST(SUM(l.loan_amount) AS char(20))
                                )
                            FROM loans l 
                            INNER JOIN applications app ON l.application_id = app.id 
                            INNER JOIN members m ON app.member_id = m.id 
                            WHERE l.status not in ('not collected','rejected','pending','processed','grant') AND l.deleted = 0
                            AND l.branch_id = b.id" . $project_cond . " 
                            AND l.date_disbursed <= '" . $report_date . "' && l.date_disbursed > 0 " . $gender_cond . "
                            ) as activeloans_cumdisbursement ,
                           
                            (SELECT COALESCE(SUM(t.tranch_amount),0)
                            FROM loan_tranches t 
                        
                            Left JOIN loans l ON l.id = t.loan_id 
                            INNER JOIN applications app ON l.application_id = app.id 
                            INNER JOIN members m ON app.member_id = m.id 
                            WHERE l.status not in ('not collected','rejected','pending','processed','grant') AND l.deleted = 0
                            AND l.branch_id = b.id" . $project_cond . " 
                            AND t.date_disbursed <= '" . $report_date . "' && t.date_disbursed > 0 " . $gender_cond . "
                            ) as cumdisbursement ,
                        
        (SELECT CONCAT_WS('+', 
              (SELECT CAST(COALESCE(sum(r.amount),0) AS char(20)) 
                from recoveries r join loans l on l.id = r.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                where r.receive_date <= '" . $report_date . "' 
                and r.receive_date > 0 and r.deleted = 0
                and  r.branch_id = b.id  " . $project_cond . $gender_cond . "
              ),
              (SELECT CAST(COALESCE(sum(don.amount),0)  AS char(20)) 
                from donations don join loans l on l.id = don.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                where don.receive_date <= '" . $report_date . "' and don.receive_date > 0 and don.deleted = 0 and  don.branch_id = b.id " . $project_cond . $gender_cond . "
              ),
              (SUM(case when ((SELECT sum(s.schdl_amnt)  from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date >0 ) -
	              COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0 ),0 )) > 0 then 
				  (l.disbursed_amount  - COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0 ))	else 0 end)
			  ),  
              (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id and  s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and  r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 1 else 0 end)
	          ),
	                                    
	          (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id  and s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and  r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 
              ((SELECT COALESCE(sum(s.schdl_amnt),0) from schedules s where s.loan_id = l.id and  s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)
	          )
	                             else 0 end)
	                             )
         ) as borrower_overdue from loans l INNER JOIN applications app on l.application_id = app.id INNER JOIN members m ON app.member_id=m.id  where l.status not in ('not collected','rejected','pending','processed','grant') and l.deleted = 0 and l.branch_id = b.id
            " . $project_cond . $gender_cond . " ) as borrower_overdue
                          
                        FROM branches b where 1 " . $cond . " ORDER BY b.region_id";

        $activeloans_cumdis = $connection->createCommand($select_query)->queryAll();
//        $activeloans_cumdis = $connection->createCommand($select_query)->getRawSql();
//        print_r($activeloans_cumdis);
//        die();
        return $activeloans_cumdis;
    }

    public static function get_due_overdue($report_date, $region_id, $area_id, $branch_id, $project_id, $gender)
    {
        $connection = Yii::$app->db;
        $cond = $project_cond = $project_cond_p = $gender_cond = '';

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
            $project_cond_p = "&& project_id = '" . $project_id . "'";
        }
        if (in_array($gender, array('m', 'f'))) {
            $gender_cond = " && m.gender = '" . $gender . "'";
        }
        $current_date = date('Y-m', ($report_date));

        if ($current_date == date('Y-m')) {
            $first_date = strtotime(date('Y-m-01', ($report_date)));
            $last_date = strtotime(date('Y-m-10', ($report_date)));
            $curr_date = strtotime(date('Y-m-d', ($report_date)));
            if ($curr_date >= $first_date && $curr_date <= $last_date) {
                $last_month_date = strtotime('last day of last month');
                $new_date = $last_month_date;
            } else {
                $new_date = $report_date;
            }
        } else {
            $new_date = $report_date;
        }
        $select_query = "
          SELECT b.id,b.name,b.region_id,b.area_id, 
          (SELECT CONCAT_WS('+', 
              (SELECT CAST(COALESCE(sum(r.amount),0) AS char(20)) 
                from recoveries r join loans l on l.id = r.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                INNER JOIN members m on app.member_id = m.id
                where r.receive_date <= '" . $report_date . "' 
                and r.receive_date > 0 and r.deleted = 0
                and  r.branch_id = b.id  " . $project_cond . $gender_cond . "
              ),
              (SELECT CAST(COALESCE(sum(don.amount),0)  AS char(20)) 
                from donations don join loans l on l.id = don.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                INNER JOIN members m on app.member_id = m.id
                where don.receive_date <= '" . $report_date . "' and don.receive_date > 0 and don.deleted = 0 and  don.branch_id = b.id " . $project_cond . $gender_cond . "
              ),
              (SUM(case when ((SELECT sum(s.schdl_amnt)  from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date >0 ) -
	              COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0 ),0 )) > 0 then 
				  (l.loan_amount  - COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0 ))	else 0 end)
			  ),  
              (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id and  s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and  r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 1 else 0 end)
	          ),
	                                    
	          (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id  and s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and  r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 
              ((SELECT COALESCE(sum(s.schdl_amnt),0) from schedules s where s.loan_id = l.id and  s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)
	          )
	                             else 0 end)
	                             )
         ) as borrower_overdue from loans l INNER JOIN applications app on l.application_id = app.id INNER JOIN members m ON app.member_id=m.id  where l.status not in ('not collected','rejected','pending','processed','grant') and l.deleted = 0 and l.branch_id = b.id
            " . $project_cond . $gender_cond . " ) as borrower_overdue from branches b where 1 " . $cond . " order by b.region_id, b.code";
        /*$select_query = "SELECT l.branch_id as id, l.area_id, l.region_id,CONCAT_WS('+',
              (SELECT CAST(COALESCE(sum(r.amount),0) AS char(20)) 
                from recoveries r join loans l on l.id = r.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                where r.receive_date <= '" . $report_date . "' 
                and r.receive_date > 0 and r.deleted = 0
                and  r.branch_id = l.branch_id  " . $project_cond . $gender_cond . "
              ),
              (SELECT CAST(COALESCE(sum(don.amount),0)  AS char(20)) 
                from donations don join loans l on l.id = don.loan_id 
                INNER JOIN applications app on l.application_id = app.id 
                where don.receive_date <= '" . $report_date . "' and don.receive_date > 0 and don.deleted = 0 and  don.branch_id = l.branch_id " . $project_cond . $gender_cond . "
              ),
              (SUM(case when ((SELECT sum(s.schdl_amnt)  from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date >0 ) -
	              COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0 ),0 )) > 0 then 
				  (l.loan_amount  - COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0 ))	else 0 end)
			  ),  
              (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 1 else 0 end)
	          ),
	                                    
	          (SUM(case when ((SELECT COALESCE(sum(s.schdl_amnt),0)  from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)   ) > 0 then 
              ((SELECT COALESCE(sum(s.schdl_amnt),0) from schedules s where s.loan_id = l.id and s.due_date <= '" . $new_date . "' and s.due_date>0) -
	                                    COALESCE((SELECT sum(r.amount) from recoveries r where r.loan_id = l.id and r.receive_date <= '" . $new_date . "' and r.receive_date>0 and r.deleted = 0),0)
	          )
	                             else 0 end)
	                             )
         ) as borrower_overdue from loans l INNER JOIN applications app on l.application_id = app.id INNER JOIN members m ON app.member_id=m.id  where l.status != 'not collected' 
            " . $project_cond . $gender_cond . " 
            group by l.branch_id
            order by l.region_id";*/
        //die($select_query);
        $due_overdue = $connection->createCommand($select_query)->queryAll();
        return $due_overdue;
    }

    public static function parse_male_female($data)
    {
        $array = explode('+', $data);
        $male = isset($array[0]) ? ($array[0]) : 0;
        $female = isset($array[1]) ? ($array[1]) : 0;
        $others = isset($array[2]) ? ($array[2]) : 0;
        $project = isset($array[3]) ? ($array[3]) : 0;
        return array('male' => $male, 'female' => $female, 'others' => $others, 'project' => $project, 'total' => ($male + $female + $others));
    }

    public static function parse_activeloans_cumdis($data)
    {
        $array = explode('+', $data);

        $active_loans = isset($array[0]) ? ($array[0]) : 0;
        $cum_disbursement = isset($array[1]) ? ($array[1]) : 0;

        return array('active_loans' => $active_loans, 'cum_disbursement' => $cum_disbursement);
    }

    public static function parse_activeloans_cumdis_new($data)
    {
        $array = explode('+', $data['male_female_others']);
        $male = isset($array[0]) ? ($array[0]) : 0;
        $female = isset($array[1]) ? ($array[1]) : 0;
        $others = isset($array[2]) ? ($array[2]) : 0;
        $project = isset($array[3]) ? ($array[3]) : 0;

        $active_loans =  $data['activeloans_cumdisbursement'];
        $cum_disbursement =  $data['cumdisbursement'];
        $array = explode('+', $active_loans);

        $active_loans1 = isset($array[0]) ? ($array[0]) : 0;
        $active_loans2 = isset($array[1]) ? ($array[1]) : 0;

        $array = explode('+', $data['borrower_overdue']);

        $cum_recovery = isset($array[0]) ? ($array[0]) : 0;
        $mdp = isset($array[1]) ? ($array[1]) : 0;
        $par = isset($array[2]) ? ($array[2]) : 0;
        $no_borrowers = isset($array[3]) ? ($array[3]) : 0;
        $overdue = isset($array[4]) ? ($array[4]) : 0;
        $cum_due = $cum_recovery + $overdue;

        return array('male' => $male, 'female' => $female, 'others' => $others, 'project' => $project, 'total' => ($male + $female + $others),'members_count' => $data['members_count'],
            'active_loans' => $active_loans1 + $active_loans2, 'cum_disbursement' => $cum_disbursement,
            'cum_recovery' => $cum_recovery, 'mdp' => $mdp, 'par' => $par, 'no_borrowers' => $no_borrowers, 'overdue' => $overdue, 'cum_due' => $cum_due);
    }

    public static function parse_due_overdue($data)
    {
        $array = explode('+', $data);

        $cum_recovery = isset($array[0]) ? ($array[0]) : 0;
        $mdp = isset($array[1]) ? ($array[1]) : 0;
        $par = isset($array[2]) ? ($array[2]) : 0;
        $no_borrowers = isset($array[3]) ? ($array[3]) : 0;
        $overdue = isset($array[4]) ? ($array[4]) : 0;
        $cum_due = $cum_recovery + $overdue;

        return array('cum_recovery' => $cum_recovery, 'mdp' => $mdp, 'par' => $par, 'no_borrowers' => $no_borrowers, 'overdue' => $overdue, 'cum_due' => $cum_due);
    }

    public static function getProgressSummary($user, $report_date)
    {
        $report_date_prev =    strtotime("-1 month",strtotime(date('Y-m-t 00:00:00'))) .','. strtotime("-1 month",strtotime(date('Y-m-t 23:59:59')));
        $data_array = ReportsHelper\RbacHelper::apiRbacNetwork($user);

        $region_array = $data_array['region_array'];
        $area_array = $data_array['area_array'];
        $branch_array = $data_array['branch_array'];

        $query = Branches::find()
            ->select(['ROUND(AVG(progress_report_details.overdue_percentage),2) as overdue_percentage','ROUND(AVG(progress_report_details.par_percentage),2) as par_percentage',
                'ROUND(AVG(progress_report_details.recovery_percentage),2) as recovery_percentage', 'SUM(progress_report_details.mdp) as mdp',
                'SUM(progress_report_details.cum_due) as cum_due','SUM(progress_report_details.cum_recv) as cum_recv',
                'SUM(progress_report_details.overdue_borrowers) as overdue_borrowers','SUM(progress_report_details.overdue_amount) as overdue_amount',
                'SUM(progress_report_details.par_amount) as par_amount','SUM(progress_report_details.no_of_loans) as no_of_loans',
                'SUM(progress_report_details.active_loans) as active_loans','SUM(progress_report_details.family_loans) as family_loans',
                'SUM(progress_report_details.female_loans) as female_loans','SUM(progress_report_details.cum_disb) as cum_disb',
                'SUM(progress_report_details.olp_amount) as olp_amount','SUM(progress_report_details.olp_amount) as olp_amount','SUM(progress_report_details.members_count) as members_count',
                '(SUM(progress_report_details.mdp)/SUM(progress_report_details.no_of_loans)) as mdp_per_borrower'

            ])
            ->join('inner join', 'progress_report_details', 'progress_report_details.branch_id = branches.id')
            ->join('inner join', 'progress_reports', 'progress_reports.id = progress_report_details.progress_report_id')
            ->join('inner join', 'regions', 'regions.id = branches.region_id')
            ->join('inner join', 'areas', 'areas.id = branches.area_id')
            ->join('inner join', 'districts', 'districts.id = branches.district_id')
            ->where(['progress_reports.report_date' => $report_date, 'progress_reports.project_id' => 0, 'regions.status' => 1, 'areas.status' => 1, 'branches.status' => 1])
            ->andWhere(['like', 'progress_report_details.gender', '0'])
            ->andFilterWhere(['in', 'progress_report_details.region_id', $region_array])
            ->andFilterWhere(['in', 'progress_report_details.area_id', $area_array])
            ->andFilterWhere(['in', 'progress_report_details.branch_id', $branch_array]);
        $query1 = Branches::find()
            ->select(['ROUND(AVG(progress_report_details.overdue_percentage),2) as overdue_percentage','ROUND(AVG(progress_report_details.par_percentage),2) as par_percentage',
                'ROUND(AVG(progress_report_details.recovery_percentage),2) as recovery_percentage', 'SUM(progress_report_details.mdp) as mdp',
                'SUM(progress_report_details.cum_due) as cum_due','SUM(progress_report_details.cum_recv) as cum_recv',
                'SUM(progress_report_details.overdue_borrowers) as overdue_borrowers','SUM(progress_report_details.overdue_amount) as overdue_amount',
                'SUM(progress_report_details.par_amount) as par_amount','SUM(progress_report_details.no_of_loans) as no_of_loans',
                'SUM(progress_report_details.active_loans) as active_loans','SUM(progress_report_details.family_loans) as family_loans',
                'SUM(progress_report_details.female_loans) as female_loans','SUM(progress_report_details.cum_disb) as cum_disb',
                'SUM(progress_report_details.olp_amount) as olp_amount','SUM(progress_report_details.olp_amount) as olp_amount','SUM(progress_report_details.members_count) as members_count',
                '(SUM(progress_report_details.mdp)/SUM(progress_report_details.no_of_loans)) as mdp_per_borrower'

            ])
            ->join('inner join', 'progress_report_details', 'progress_report_details.branch_id = branches.id')
            ->join('inner join', 'progress_reports', 'progress_reports.id = progress_report_details.progress_report_id')
            ->join('inner join', 'regions', 'regions.id = branches.region_id')
            ->join('inner join', 'areas', 'areas.id = branches.area_id')
            ->join('inner join', 'districts', 'districts.id = branches.district_id')
            ->where(['progress_reports.report_date' => $report_date_prev, 'progress_reports.project_id' => 0, 'regions.status' => 1, 'areas.status' => 1, 'branches.status' => 1])
            ->andWhere(['like', 'progress_report_details.gender', '0'])
            ->andFilterWhere(['in', 'progress_report_details.region_id', $region_array])
            ->andFilterWhere(['in', 'progress_report_details.area_id', $area_array])
            ->andFilterWhere(['in', 'progress_report_details.branch_id', $branch_array]);
        return $arr=['a'=>$query->createCommand()->queryAll(),'b'=>$query1->createCommand()->queryAll()];
    }

    public static function getAttributeWiseProgress($user, $search_by, $sort_by, $limit, $report_date, $column)
    {
        $percentage_values = array('overdue_percentage', 'par_percentage', 'recovery_percentage');
        $group_by_cond = 'branches.id';
        $group_by_cond_select = 'branches.id as attribute_id';
        $select_name_cond = 'branches.name';
        $limit_cond = '';
        $order_by_cond = SORT_DESC;
        $report_date_mdp = strtotime(date('Y-M-t'));
        if($column == 'mdp/borrower')
        {
            $function = '(SUM(progress_report_details.mdp)/SUM(progress_report_details.no_of_loans))';
        }
        else if($column == 'mdp') {
            $function = 'SUM(progress_report_details.' . $column . ')';
        } else {
            $function = 'SUM(progress_report_details.' . $column . ')';
        }

        $data_array = ReportsHelper\RbacHelper::apiRbacNetwork($user);

        $region_array = $data_array['region_array'];
        $area_array = $data_array['area_array'];
        $branch_array = $data_array['branch_array'];


        if (!empty($search_by)) {
            if ($search_by == 'REGION') {
                $group_by_cond = 'branches.region_id';
                $group_by_cond_select = 'branches.region_id as attribute_id';
                $select_name_cond = 'regions.name';
            } else if ($search_by == 'AREA') {
                $group_by_cond = 'branches.area_id';
                $group_by_cond_select = 'branches.area_id as attribute_id';
                $select_name_cond = 'areas.name';
            } else if ($search_by == 'BRANCH') {
                $group_by_cond = 'branches.id';
                $group_by_cond_select = 'branches.id as attribute_id';
                $select_name_cond = 'branches.name';
            } else if ($search_by == 'DISTRICT') {
                $group_by_cond = 'branches.district_id';
                $group_by_cond_select = 'branches.district_id as attribute_id';
                $select_name_cond = 'districts.name';
            }  else if ($search_by == 'OVERALL') {
                $group_by_cond_select = '';
                $select_name_cond = '';
            }
        }

        if (!empty($sort_by)) {
            if ($sort_by == 'DESC') {
                $order_by_cond = SORT_ASC;
            }
        }
        if (!empty($limit)) {
            $limit_cond = $limit;
        }
        if (in_array($column, $percentage_values)) {
            $function = 'AVG(progress_report_details.' . $column . ')';
        }

        if($column == 'mdp')
        {
            $query = Branches::find();
            if($search_by == 'OVERALL') {
                $query->select([' SUM(amount) AS attribute,  (SUM(amount)/SUM(objects_count)) as mdp_borrower']);
            } else {
                $query->select([$group_by_cond_select, $select_name_cond, ' SUM(amount) AS attribute,  (SUM(amount)/SUM(objects_count)) as mdp_borrower']);
            }

            $query->join('inner join', 'arc_account_report_details', 'arc_account_report_details.branch_id = branches.id')
            ->join('inner join', 'arc_account_reports', 'arc_account_reports.id = arc_account_report_details.arc_account_report_id')
            ->join('inner join', 'regions', 'regions.id = branches.region_id')
            ->join('inner join', 'areas', 'areas.id = branches.area_id')
            ->join('inner join', 'districts', 'districts.id = branches.district_id')
            ->where(['arc_account_reports.report_date' => $report_date_mdp, 'arc_account_reports.project_id' => 0, 'regions.status' => 1, 'areas.status' => 1, 'branches.status' => 1])
            ->andWhere([ 'arc_account_reports.report_name' => 'Donation Summary'])
            ->andFilterWhere(['in', 'arc_account_report_details.region_id', $region_array])
            ->andFilterWhere(['in', 'arc_account_report_details.area_id', $area_array])
            ->andFilterWhere(['in', 'arc_account_report_details.branch_id', $branch_array]);

            if($search_by == 'OVERALL')
            {
                $query->orderBy(['attribute' => $order_by_cond])
                ->limit($limit_cond);
            } else {
                $query
                    ->groupBy($group_by_cond)
                    ->orderBy(['attribute' => $order_by_cond])
                    ->limit($limit_cond);
            }

        }elseif ($column == 'disb'){
            $query = Branches::find();
            if($search_by == 'OVERALL') {
                $query->select([' SUM(amount) AS attribute,  (SUM(amount)/SUM(objects_count)) as mdp_borrower']);
            } else {
                $query->select([$group_by_cond_select, $select_name_cond, ' SUM(amount) AS attribute,  (SUM(amount)/SUM(objects_count)) as mdp_borrower']);
            }

            $query->join('inner join', 'arc_account_report_details', 'arc_account_report_details.branch_id = branches.id')
                ->join('inner join', 'arc_account_reports', 'arc_account_reports.id = arc_account_report_details.arc_account_report_id')
                ->join('inner join', 'regions', 'regions.id = branches.region_id')
                ->join('inner join', 'areas', 'areas.id = branches.area_id')
                ->join('inner join', 'districts', 'districts.id = branches.district_id')
                ->where(['arc_account_reports.report_date' => $report_date_mdp, 'arc_account_reports.project_id' => 0, 'regions.status' => 1, 'areas.status' => 1, 'branches.status' => 1])
                ->andWhere([ 'arc_account_reports.report_name' => 'Disbursement Summary'])
                ->andFilterWhere(['in', 'arc_account_report_details.region_id', $region_array])
                ->andFilterWhere(['in', 'arc_account_report_details.area_id', $area_array])
                ->andFilterWhere(['in', 'arc_account_report_details.branch_id', $branch_array]);

            if($search_by == 'OVERALL')
            {
                $query->orderBy(['attribute' => $order_by_cond])
                    ->limit($limit_cond);
            } else {
                $query
                    ->groupBy($group_by_cond)
                    ->orderBy(['attribute' => $order_by_cond])
                    ->limit($limit_cond);
            }
        }
        else {
            $query = Branches::find()
                ->select([$group_by_cond_select, $select_name_cond, $function . ' AS attribute'])
                ->join('inner join', 'progress_report_details', 'progress_report_details.branch_id = branches.id')
                ->join('inner join', 'progress_reports', 'progress_reports.id = progress_report_details.progress_report_id')
                ->join('inner join', 'regions', 'regions.id = branches.region_id')
                ->join('inner join', 'areas', 'areas.id = branches.area_id')
                ->join('inner join', 'districts', 'districts.id = branches.district_id')
                ->where(['progress_reports.report_date' => $report_date, 'progress_reports.project_id' => 0, 'regions.status' => 1, 'areas.status' => 1, 'branches.status' => 1])
                ->andWhere(['like', 'progress_report_details.gender', '0'])
                ->andFilterWhere(['in', 'progress_report_details.region_id', $region_array])
                ->andFilterWhere(['in', 'progress_report_details.area_id', $area_array])
                ->andFilterWhere(['in', 'progress_report_details.branch_id', $branch_array])
                ->groupBy($group_by_cond)
                ->orderBy(['attribute' => $order_by_cond])
                ->limit($limit_cond);

        }
        return $query->createCommand()->queryAll();
    }

    public static function getAttributeWiseYearlyTrend($user, $search_by, $column,$attribute_id)
    {
        $percentage_values = array('overdue_percentage', 'par_percentage', 'recovery_percentage');

        $group_by_cond = '';
        $select_name_cond = 'branches.name';
        $limit_cond = '';
        $order_by_cond = SORT_DESC;
        if($column == 'mdp/borrower')
        {
            $function = '(SUM(progress_report_details.mdp)/SUM(progress_report_details.no_of_loans))';
        }
        else {
            $function = 'SUM(progress_report_details.' . $column . ')';
        }
        $cond = '';

        if(!in_array($column,['mdp','disb'])) {
            if (!empty($search_by)) {
                if ($search_by == 'REGION') {
                    if($attribute_id != 0) {
                        $cond .= " AND progress_report_details.region_id = " . $attribute_id;
                    }
                    $group_by_cond = 'branches.region_id,';
                    $select_name_cond = 'regions.name';
                } else if ($search_by == 'AREA') {
                    if($attribute_id != 0) {
                        $cond .= " AND progress_report_details.area_id = " . $attribute_id;
                    }
                    $group_by_cond = 'branches.area_id,';
                    $select_name_cond = 'areas.name';
                } else if ($search_by == 'BRANCH') {
                    if($attribute_id != 0) {
                        $cond .= " AND progress_report_details.branch_id = " . $attribute_id;
                    }
                    $group_by_cond = 'branches.id,';
                    $select_name_cond = 'branches.name';
                } else if ($search_by == 'DISTRICT') {
                    $group_by_cond = 'branches.district_id,';
                    $select_name_cond = 'districts.name';
                }
                else if ($search_by == 'OVERALL') {
                    $group_by_cond = '';
                    $select_name_cond = 'districts.name';
                }
            }
        }
        else {
            if (!empty($search_by)) {
                if ($search_by == 'REGION') {
                    if($attribute_id != 0) {
                        $cond .= " AND arc_account_report_details.region_id = " . $attribute_id;
                        $group_by_cond = 'branches.region_id,';
                    }
                    $select_name_cond = 'regions.name';
                } else if ($search_by == 'AREA') {
                    if($attribute_id != 0) {
                        $cond .= " AND arc_account_report_details.area_id = " . $attribute_id;
                        $group_by_cond = 'branches.area_id,';
                    }
                    $select_name_cond = 'areas.name';
                } else if ($search_by == 'BRANCH') {
                    if($attribute_id != 0) {
                        $cond .= " AND arc_account_report_details.branch_id = " . $attribute_id;
                        $group_by_cond = 'branches.id,';
                    }
                    $select_name_cond = 'branches.name';
                } else if ($search_by == 'DISTRICT') {
                    $group_by_cond = 'branches.district_id,';
                    $select_name_cond = 'districts.name';
                }
                else if ($search_by == 'OVERALL') {
                    $group_by_cond = '';
                    $select_name_cond = 'districts.name';
                }
            }
        }

        if (in_array($column, $percentage_values)) {
            $function = "ROUND(AVG(progress_report_details." . $column . "),2)";
        }
        $months_array = '';
        $months =[];
        /*for($i=-1;$i>=-8;$i--) {
            $m = date('Y-M-t',strtotime( $i ."month"));
            $key = date('M-Y',strtotime( $i ."month"));
            $months[$key] = strtotime($m);
            $months_array .=  strtotime($m) . ',';
        }*/
        for ($i = -1; $i >= -8; $i--) {
            if (date('m') + ($i) == 2) {
                $days=((date('m')-2)*30)-3;
                $m = date('Y-M-t', strtotime('-' . $days . ' days'));
                $key = date('M-Y', strtotime('-' . $days . ' days'));
                $months[$key] = strtotime($m);
                $months_array .=  strtotime($m) . ',';

            }else {
                $m = date('Y-M-t',strtotime( $i ."month"));
                $key = date('M-Y',strtotime( $i ."month"));
                $months[$key] = strtotime($m);
                $months_array .=  strtotime($m) . ',';
            }
        }
        $cond .= " AND report_date in (".trim($months_array,',').")";

        if($column == 'mdp')
        {
            $query = "Select " . $group_by_cond . "SUM(arc_account_report_details.amount) AS attribute,
        case ";
            foreach ($months as $k => $m) {
                $query .= " when report_date = " . $m . " then '" . $k . "'";
            }

            $query .= " End as month FROM branches 
        inner join arc_account_report_details ON arc_account_report_details.branch_id = branches.id 
        inner join arc_account_reports ON arc_account_reports.id = arc_account_report_details.arc_account_report_id  
        WHERE ((arc_account_reports.project_id=0) AND (branches.status=1)) AND report_name ='Donation Summary'
        " . $cond . " group by " . $group_by_cond . "month order by report_date desc";
        }
        elseif($column == 'disb')
        {
            $query = "Select " . $group_by_cond . "SUM(arc_account_report_details.amount) AS attribute,
        case ";
            foreach ($months as $k => $m) {
                $query .= " when report_date = " . $m . " then '" . $k . "'";
            }

            $query .= " End as month FROM branches 
        inner join arc_account_report_details ON arc_account_report_details.branch_id = branches.id 
        inner join arc_account_reports ON arc_account_reports.id = arc_account_report_details.arc_account_report_id  
        WHERE ((arc_account_reports.project_id=0) AND (branches.status=1)) AND report_name ='Disbursement Summary'
        " . $cond . " group by " . $group_by_cond . "month order by report_date desc";
        }
        else {
            $query = "Select " . $group_by_cond . $function . " AS attribute,
        case ";
            foreach ($months as $k => $m) {
                $query .= " when report_date = " . $m . " then '" . $k . "'";
            }

            $query .= " End as month FROM branches 
        inner join progress_report_details ON progress_report_details.branch_id = branches.id 
        inner join progress_reports ON progress_reports.id = progress_report_details.progress_report_id  
        WHERE ((progress_reports.project_id=0) AND (branches.status=1)) AND (progress_report_details.gender ='0')
        " . $cond . " group by " . $group_by_cond . "month order by report_date desc";
        }

        return Yii::$app->db->createCommand($query)->queryAll();
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

    public static function parse_json_progress($progress)
    {
        $big_array = [];
        if (empty($progress)) {
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;

        foreach ($progress as $p) {
            unset($p['region']);
            unset($p['area']);
            unset($p['branch']);
            $result[$i]['pr']['project_id'] = $p['progress']['project_id'];
            unset($p['progress']);
            $result[$i]['pd'] = $p;
            $i++;
        }

        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        //$district = Districts::find()->select('name')->where(['id' => $branch['district_id']])->one();
        $districts = ArrayHelper::map(Districts::find()->all(), 'id', 'name');
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
        $temp_sum = array('id' => 0, 'no_of_loans' => 0,'members_count' => 0, 'family_loans' => 0, 'female_loans' => 0, 'active_loans' => 0, 'cum_disb' => 0, 'cum_due' => 0, 'cum_recv' => 0, 'overdue_borrowers' => 0, 'overdue_amount' => 0, 'overdue_percentage' => 0, 'par_amount' => 0, 'par_percentage' => 0, 'not_yet_due' => 0, 'olp_amount' => 0, 'recovery_percentage' => 0);
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
                    unset($branch['progress_report_id']);
                    unset($branch['division_id']);
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['branch_id']];

                    $branch['district'] = $districts[$branch['district_id']];
                    unset($branch['branch_id']);

                    $grand_sum['members_count'] += $branch['members_count'];
                    $grand_sum['no_of_loans'] += $branch['no_of_loans'];
                    $grand_sum['family_loans'] += $branch['family_loans'];
                    $grand_sum['female_loans'] += $branch['female_loans'];
                    $grand_sum['active_loans'] += $branch['active_loans'];
                    $grand_sum['cum_disb'] += $branch['cum_disb'];
                    $grand_sum['cum_due'] += $branch['cum_due'];
                    $grand_sum['cum_recv'] += $branch['cum_recv'];
                    $grand_sum['overdue_borrowers'] += $branch['overdue_borrowers'];
                    $grand_sum['overdue_amount'] += $branch['overdue_amount'];
                    //$grand_sum['overdue_percentage']   += $branch['overdue_percentage'];
                    $grand_sum['par_amount'] += $branch['par_amount'];
                    //$grand_sum['par_percentage']       += $branch['par_percentage'];
                    $grand_sum['not_yet_due'] += $branch['not_yet_due'];
                    $grand_sum['olp_amount'] += $branch['olp_amount'];
                    //$grand_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $region_sum['members_count'] += $branch['members_count'];
                    $region_sum['no_of_loans'] += $branch['no_of_loans'];
                    $region_sum['family_loans'] += $branch['family_loans'];
                    $region_sum['female_loans'] += $branch['female_loans'];
                    $region_sum['active_loans'] += $branch['active_loans'];
                    $region_sum['cum_disb'] += $branch['cum_disb'];
                    $region_sum['cum_due'] += $branch['cum_due'];
                    $region_sum['cum_recv'] += $branch['cum_recv'];
                    $region_sum['overdue_borrowers'] += $branch['overdue_borrowers'];
                    $region_sum['overdue_amount'] += $branch['overdue_amount'];
                    //$region_sum['overdue_percentage']   += $branch['overdue_percentage'];
                    $region_sum['par_amount'] += $branch['par_amount'];
                    //$region_sum['par_percentage']       += $branch['par_percentage'];
                    $region_sum['not_yet_due'] += $branch['not_yet_due'];
                    $region_sum['olp_amount'] += $branch['olp_amount'];
                    //$region_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $area_sum['members_count'] += $branch['members_count'];
                    $area_sum['no_of_loans'] += $branch['no_of_loans'];
                    $area_sum['family_loans'] += $branch['family_loans'];
                    $area_sum['female_loans'] += $branch['female_loans'];
                    $area_sum['active_loans'] += $branch['active_loans'];
                    $area_sum['cum_disb'] += $branch['cum_disb'];
                    $area_sum['cum_due'] += $branch['cum_due'];
                    $area_sum['cum_recv'] += $branch['cum_recv'];
                    $area_sum['overdue_borrowers'] += $branch['overdue_borrowers'];
                    $area_sum['overdue_amount'] += $branch['overdue_amount'];
                    $area_sum['overdue_percentage'] += $branch['overdue_percentage'];
                    $area_sum['par_percentage'] += $branch['par_percentage'];
                    $area_sum['par_amount'] += $branch['par_amount'];
                    $area_sum['not_yet_due'] += $branch['not_yet_due'];
                    $area_sum['olp_amount'] += $branch['olp_amount'];
                    $area_sum['recovery_percentage'] += $branch['recovery_percentage'];
                    $area_sum['children'][$b_key] = $branch;
                }

                $area_sum['par_percentage'] = round($area_sum['par_percentage'] / ($count_branch), 2);
                $region_sum['par_percentage'] += $area_sum['par_percentage'];

                $area_sum['overdue_percentage'] = round($area_sum['overdue_percentage'] / ($count_branch), 2);
                $region_sum['overdue_percentage'] += $area_sum['overdue_percentage'];

                $area_sum['recovery_percentage'] = round($area_sum['recovery_percentage'] / ($count_branch), 2);
                $region_sum['recovery_percentage'] += $area_sum['recovery_percentage'];

                $region_sum['children'][$count] = $area_sum;
                $count++;
            }
            $region_sum['par_percentage'] = round($region_sum['par_percentage'] / $count_area, 2);
            $region_sum['overdue_percentage'] = round($region_sum['overdue_percentage'] / $count_area, 2);
            $region_sum['recovery_percentage'] = round($region_sum['recovery_percentage'] / $count_area, 2);

            $grand_sum['par_percentage'] += $region_sum['par_percentage'];
            $grand_sum['overdue_percentage'] += $region_sum['overdue_percentage'];
            $grand_sum['recovery_percentage'] += $region_sum['recovery_percentage'];
            $new_big_array[] = $region_sum;
        }

        $grand_sum['par_percentage'] = round($grand_sum['par_percentage'] / $count_region, 4);
        $grand_sum['overdue_percentage'] = round($grand_sum['overdue_percentage'] / $count_region, 4);
        $grand_sum['recovery_percentage'] = round($grand_sum['recovery_percentage'] / $count_region, 4);

        $new_big_array[] = $grand_sum;
        $progress_report = json_encode($new_big_array);
        return $progress_report;
    }

    static public function getProgressReports($project_id)
    {
        return ProgressReports::find()->select('id,report_date')->distinct()->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])->filterWhere(['=', 'project_id', $project_id])->orderBy(['report_date' => SORT_DESC])->all();
    }

    static public function getProgress($cond)
    {
        $query = ProgressReports::find()
            ->select(['sum(d.no_of_loans) as no_of_loans', 'sum(d.family_loans) as family_loans', 'sum(d.female_loans) as female_loans', 'sum(d.active_loans) as active_loans', 'sum(d.cum_disb) as cum_disb', 'sum(d.cum_due) as cum_due', 'sum(d.cum_recv) as cum_recv', 'sum(d.overdue_borrowers) as overdue_borrowers',
                'sum(d.overdue_amount) as overdue_amount', 'avg(d.overdue_percentage) as overdue_percentage', 'sum(d.par_amount) as par_amount', 'avg(d.par_percentage) as par_percentage', 'sum(d.not_yet_due) as not_yet_due', 'sum(d.olp_amount) as olp_amount', 'avg(d.recovery_percentage) as recovery_percentage'])
            ->join('inner join', 'progress_report_details as d', 'd.progress_report_id=progress_reports.id')
            ->where([
                'progress_reports.project_id' => '0',
                //'progress_reports.report_date' => strtotime(date('Y-m-d')),
                'progress_reports.report_date' => strtotime("-1 days", strtotime(date("Y-m-d"))),
            ]);
        if (!empty($cond)) {
            $query->andWhere([$cond['column'] => $cond['value']]);
        }
        return $query->asArray()->all();
    }

    static public function getProgressOfBranch($branch_id)
    {
        $query = ProgressReports::find()
            ->select(['sum(d.no_of_loans) as no_of_loans', 'sum(d.family_loans) as family_loans', 'sum(d.female_loans) as female_loans', 'sum(d.active_loans) as active_loans', 'sum(d.cum_disb) as cum_disb', 'sum(d.cum_due) as cum_due', 'sum(d.cum_recv) as cum_recv', 'sum(d.overdue_borrowers) as overdue_borrowers',
                'sum(d.overdue_amount) as overdue_amount', 'avg(d.overdue_percentage) as overdue_percentage', 'sum(d.par_amount) as par_amount', 'avg(d.par_percentage) as par_percentage', 'sum(d.not_yet_due) as not_yet_due', 'sum(d.olp_amount) as olp_amount', 'avg(d.recovery_percentage) as recovery_percentage'])
            ->join('inner join', 'progress_report_details as d', 'd.progress_report_id=progress_reports.id')
            ->where([
                'progress_reports.project_id' => '0',
                'progress_reports.report_date' => strtotime(date('Y-m-d')),
                'd.branch_id' => $branch_id
            ]);
        return $query->asArray()->all();
    }
}