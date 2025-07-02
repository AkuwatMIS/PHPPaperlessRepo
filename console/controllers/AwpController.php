<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;

use common\models\Accounts;
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

class AwpController extends Controller
{
//  nohup php yii awp/tar-ach
    public function actionTarAch()
    {
        $months = array(
//            "2024-10" => "2024-10",
            "2025-06" => "2025-06",
//            "2025-04" => "2025-04"
//            "2023-10" => "2023-10",
//            "2023-11" => "2023-11",
//            "2023-12" => "2023-12"
        );
        $failed = [];
        foreach ($months as $month) {
//            $awp = Awp::find()->select(['project_id','region_id','area_id','branch_id','sum(no_of_loans) as no_of_loans','sum(disbursement_amount) as disbursement_amount'])->where(['month' => $month])->groupBy(['branch_id'])->all();
            $awp = Awp::find()->select(['project_id', 'region_id', 'area_id', 'branch_id', 'no_of_loans', 'disbursement_amount'])->where(['month' => $month])->all();

            foreach ($awp as $awp_data) {
                $targets = new  AwpTargetVsAchievement();
                $targets->branch_id = $awp_data->branch_id;
                $targets->project_id = $awp_data->project_id;
                $targets->region_id = $awp_data->region_id;
                $targets->area_id = $awp_data->area_id;
                $targets->target_loans = $awp_data->no_of_loans;
                $targets->target_amount = $awp_data->disbursement_amount;
                $targets->month = $month;


                $achieved_loans = ArcAccountReportDetails::find()
                    ->innerJoin('arc_account_reports', 'arc_account_reports.id = arc_account_report_details.arc_account_report_id')
                    ->where(['arc_account_reports.report_name' => 'Disbursement Summary'])
                    ->andWhere(['arc_account_report_details.branch_id' => $awp_data->branch_id])
                    ->andWhere(['arc_account_reports.period' => 'monthly'])
                    ->andWhere(['arc_account_reports.status' => 1])
                    ->andWhere(['arc_account_reports.deleted' => 0])
                    ->andWhere(['arc_account_reports.project_id' => $awp_data->project_id])
                    ->andWhere(['between', 'arc_account_reports.report_date', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t', strtotime($targets->month)))])
                    ->all();

                $amount = ArcAccountReportDetails::find()
                    ->innerJoin('arc_account_reports', 'arc_account_reports.id = arc_account_report_details.arc_account_report_id')
                    ->where(['arc_account_reports.report_name' => 'Disbursement Summary'])
                    ->andWhere(['arc_account_report_details.branch_id' => $awp_data->branch_id])
                    ->andWhere(['arc_account_reports.period' => 'monthly'])
                    ->andWhere(['arc_account_reports.status' => 1])
                    ->andWhere(['arc_account_reports.deleted' => 0])
                    ->andWhere(['arc_account_reports.project_id' => $awp_data->project_id])
                    ->andWhere(['between', 'arc_account_reports.report_date', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t', strtotime($targets->month)))])
//                    ->createCommand()->getRawSql();
                    ->sum('amount');

//                $achieved_loans = Loans::find()
//                    ->where(['loans.branch_id' => $targets->branch_id])
//                    ->andWhere(['!=', 'status', 'not collected'])
//                    ->andWhere(['!=', 'status', 'rejected'])
//                    ->andWhere(['!=', 'status', 'processed'])
//                    ->andWhere(['!=', 'status', 'pending'])
//                    ->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t', strtotime($targets->month)))])
//                    ->select('id')
//                    ->all();
//
//                $amount = Loans::find()
//                    ->where(['loans.branch_id' => $targets->branch_id])
//                    ->andWhere(['!=', 'status', 'not collected'])
//                    ->andWhere(['!=', 'status', 'rejected'])
//                    ->andWhere(['!=', 'status', 'processed'])
//                    ->andWhere(['!=', 'status', 'pending'])
//                    ->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t', strtotime($targets->month)))])
//                    ->sum('disbursed_amount');

                $targets->achieved_loans = !empty($achieved_loans) ? $achieved_loans[0]['objects_count'] : 0;
                $targets->achieved_amount = !empty($amount) ? $amount : 0;

                $targets->loans_dif = $targets->achieved_loans - $targets->target_loans;
                $targets->amount_dif = $targets->achieved_amount - $targets->target_amount;

                if (!$targets->save()) {
                    $failed[] = $awp_data->branch_id . ' branch and month = ' . $month;
                }
                echo $month . ' saved </br>';
            }

        }

        if (!empty($failed)) {
            print_r($failed);
            die();
        }
    }

    public function actionTarVsAchData()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'targets.csv';
        $file_path = Yii::getAlias('@anyname') . '/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;
        $array = array(6, 7, 8, 9, 10, 11, 12);
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {
                // if ($i != 0) {
                // print_r($fileop[3]);
                //die();
                /*print_r(date('Y-m',strtotime($fileop[3])));
                  die();*/
                $branch = Branches::find()->select(['id', 'region_id', 'area_id'])->where(['code' => $fileop[0]])->one();
                if (!empty($branch)) {

                    $targets = new  AwpTargetVsAchievement();
                    $targets->branch_id = $branch['id'];
                    $targets->region_id = $branch['region_id'];
                    $targets->area_id = $branch['area_id'];
                    /*if(filter_var($fileop[17], FILTER_SANITIZE_NUMBER_INT)==null){
                        die('hlo');
                    }*/

                    $targets->target_loans = !empty(filter_var($fileop[1], FILTER_SANITIZE_NUMBER_INT)) ? filter_var($fileop[1], FILTER_SANITIZE_NUMBER_INT) : 0;
                    $targets->target_amount = !empty(filter_var($fileop[2], FILTER_SANITIZE_NUMBER_INT)) ? filter_var($fileop[2], FILTER_SANITIZE_NUMBER_INT) : 0;
                    /*if (in_array(date('m', strtotime($fileop[19])), $array)) {
                        $targets->month = "2017-" . date('m', strtotime($fileop[19]));
                    } else {
                        $targets->month = "2018-" . date('m', strtotime($fileop[19]));
                    }*/
                    $targets->month = date('Y-m', strtotime($fileop[3]));

                    $achieved_loans = Loans::find()->where(['branch_id' => $targets->branch_id])->andWhere(['!=', 'status', 'not nollected'])->andWhere(['between', 'date_disbursed', date('Y-m-01', strtotime($targets->month)), date('Y-m-t', strtotime($targets->month))])->count();
                    $amount = Loans::find()
                        ->where(['branch_id' => $targets->branch_id])
                        ->andWhere(['!=', 'status', 'not collected'])
                        ->andWhere(['between', 'datedisbursed', date('Y-m-01', strtotime($targets->month)), date('Y-m-t', strtotime($targets->month))])
                        ->sum('loan_amount');

                    $targets->achieved_loans = $achieved_loans;
                    $targets->achieved_amount = !empty($amount) ? $amount : 0;

                    $targets->loans_dif = $targets->achieved_loans - $targets->target_loans;
                    $targets->amount_dif = $targets->achieved_amount - $targets->target_amount;

                    $targets->save();
                }
                //}

                $i++;

            }
            $flag = true;
        }
    }

    public function actionAwp()
    {
        $months = array(
            "2023-05" => "2023-05",
            "2023-06" => "2023-06",
            "2023-07" => "2023-07",
            "2023-08" => "2023-08",
            "2023-09" => "2023-09",
            "2023-10" => "2023-10",
            "2023-11" => "2023-11",
            "2023-12" => "2023-12",
            "2024-01" => "2024-01",
            "2024-02" => "2024-02",
            "2024-03" => "2024-03",
            "2024-04" => "2024-04",
            "2024-05" => "2024-05",
            "2024-06" => "2024-06"
        );

        $branches = Branches::find()->andWhere(['status' => 1])->asArray()->all();
        foreach ($branches as $branch) {
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch['id']])->all();
            foreach ($branch_projects as $proj) {
                foreach ($months as $month) {
                    $awp = new Awp();
                    $awp->branch_id = $branch['id'];
                    $awp->region_id = $branch['region_id'];
                    $awp->area_id = $branch['area_id'];
                    $awp->project_id = $proj['project_id'];
                    $awp->month = $month;
                    if (!$awp->save()) {
                        print_r($awp->getErrors());
                        die();
                    };

                }
            }

        }
    }

//  php yii awp/awp-yearly
    public function actionAwpYearly()
    {
        $months = array(
//            "2025-06" => "2025-06",
            "2025-07" => "2025-07",
            "2025-08" => "2025-08",
            "2025-09" => "2025-09",
            "2025-10" => "2025-10",
            "2025-11" => "2025-11",
            "2025-12" => "2025-12",
            "2026-01" => "2026-01",
            "2026-02" => "2026-02",
            "2026-03" => "2026-03",
            "2026-04" => "2026-04",
            "2026-05" => "2026-05",
            "2026-06" => "2026-06"
        );


//         foreach ($months as $month) {
//             $awp = Awp::find()/*->where(['in', 'branch_id', $branchesArray])*/->andWhere(['month' => '2022-06'])->all();
//             foreach ($awp as $a) {
//                 $a->status = 1;
//                 $a->is_lock = 0;
//                 $a->save();
//                 echo $month;
//                 echo '----';
//             }
//         }


        $branches = Branches::find()->andWhere(['status' => 1])->asArray()->all();

        foreach ($branches as $branch) {
            $avg_recovery = 0;
            $avg_r = [];
//          $branch_projects=BranchProjectsMapping::find()->where(['branch_id'=>$branch['id']])->andWhere(['in','project_id',[69]])->all();
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch['id']])->all();
            foreach ($branch_projects as $proj) {
                foreach ($months as $month) {

                    if ($month == "2025-06") {
                        $awp = new Awp();
                        $awp->branch_id = $branch['id'];
                        $awp->region_id = $branch['region_id'];
                        $awp->area_id = $branch['area_id'];
                        $awp->project_id = $proj['project_id'];
                        $awp->month = $month;
                        $awp->no_of_loans = 0;
                        $awp->avg_loan_size = 0;
                        $awp->active_loans = 0;
                        $progress_report_date = strtotime(date('Y-m-t', strtotime($month . ' -1 month')));

                        $progres_report = ProgressReports::find()->where(['project_id' => $awp->project_id])
                            ->andWhere(['gender' => '0'])
                            ->andWhere(['between', 'report_date', $progress_report_date, strtotime(date('Y-m-t-23:59', strtotime($month . ' -1 month')))])
                            ->one();
                   
                        if (!empty($progres_report)) {
                            $progress_report_details = ProgressReportDetails::find()
                                ->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])
                                ->one();

                            if (!empty($progress_report_details)) {

                                $awp->active_loans = $progress_report_details->active_loans;
                                $awp->monthly_olp = $progress_report_details->olp_amount;
                            }
                        }
                        $array1 = $this->GetAvgRecocoveryYearly($branch['id'], $month, $awp->project_id);
                        if ($progress_report_details->active_loans != 0) {
                            //avg rec
                            $awp->avg_recovery = round($array1['schedule_amount'] / $progress_report_details->active_loans);
                        } else {
                            $awp->avg_recovery = 0;
                        }
                        $avg_r[$awp->project_id] = $awp->avg_recovery;
                        $awp->monthly_recovery = $awp->avg_recovery * $awp->active_loans;
                        if (!$awp->save()) {
                            print_r($awp->getErrors());
                            die();
                        }

                    } else {
                        $awp = new Awp();
                        $awp->branch_id = $branch['id'];
                        $awp->region_id = $branch['region_id'];
                        $awp->area_id = $branch['area_id'];
                        $awp->project_id = $proj['project_id'];
                        $awp->month = $month;
                        $awp->no_of_loans = 0;
                        $awp->avg_loan_size = 0;
                        $awp->avg_recovery = isset($avg_r[$awp->project_id]) ? $avg_r[$awp->project_id] : 0;
                        if (!$awp->save()) {
                            print_r($awp->getErrors());
                            die();
                        }
                    }
                }
            }
            print_r($branch['id']);
        }
    }

    // php yii awp/close-loans-yearly

    public function actionCloseLoansYearly()
    {
        $closed_loans_month_wise = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
        );

        // $id = [
        //     1,
        //     2,
        //     3,
        //     4,
        //     5,
        //     6,
        //     7,
        //     8,
        //     9,
        //     10,
        //     11,
        //     12,
        //     13,
        //     14,
        //     15,
        //     16,
        //     17,
        //     19,
        //     20,
        //     21,
        //     22,
        //     23,
        //     24,
        //     25,
        //     26,
        //     27,
        //     28,
        //     29,
        //     30,
        // ];
        $date = date('Y-05-t', strtotime('last day of previous month'));

        $branches = Branches::find()->where(['status' => 1])
//            ->andWhere(['in','id',$id])
            ->all();
        $closed_loans = 0;
        $monthly_recovery = 0;

        foreach ($branches as $b) {
            $branch_projects = BranchProjectsMapping::find()/*->where(['branch_id' => $b->id])->andWhere(['in','project_id',[61,62,64]])*/
            ->all();
            if (isset($branch_projects)) {
                foreach ($branch_projects as $p) {
                    $loans = Loans::find()
                        ->select(['loans.id', 'loans.loan_amount', 'loans.inst_amnt', '(select COALESCE(sum(amount),0) from recoveries where recoveries.loan_id=loans.id and recoveries.deleted=0 and receive_date <= "' . strtotime($date) . '") as credit'])
                        ->where(['branch_id' => $b->id, 'project_id' => $p->project_id, 'deleted' => 0])
                        ->andWhere(['or', ['status' => 'collected'], ['and', ['status' => 'loan completed'], ['>=', 'loan_completed_date', '1688151599']]])
                        ->asArray()
                        ->all();
                    foreach ($loans as $loan) {
                        $schdl_amnt = $loan['loan_amount'];
                        $balance = $schdl_amnt - $loan['credit'];
                        $inst = $balance / $loan['inst_amnt'];
                        $closed_loans_month_wise = $this->GetActiveLoansYearly($inst, $closed_loans_month_wise);
                    }

                    $awp = Awp::find()->where(['branch_id' => $b->id, 'project_id' => $p->project_id])->andWhere(['>', 'month', '2023-05'])->orderBy('month ASC')->all();
                    foreach ($awp as $key => $a) {
                        echo '----'.$a->monthly_closed_loans.'-----';
                        $a->monthly_closed_loans = $closed_loans_month_wise[$key + 1];
                        $a->save();
                    }
                    $closed_loans_month_wise = array(
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                        6 => 0,
                        7 => 0,
                        8 => 0,
                        9 => 0,
                        10 => 0,
                        11 => 0,
                        12 => 0,
                        13 => 0
                    );
                }
            }

            echo '-----'.$b->id.'-------';
        }
    }

    public function actionUpdateAwp()
    {
        $months = array(
            "2021-07" => "2021-07",
            "2021-08" => "2021-08",
            "2021-09" => "2021-09",
            "2021-10" => "2021-10",
            "2021-11" => "2021-11",
            "2021-12" => "2021-12",
            "2022-01" => "2022-01",
            "2022-02" => "2022-02",
            "2022-03" => "2022-03",
            "2022-04" => "2022-04",
            "2022-05" => "2022-05",
            "2022-06" => "2022-06"
        );

//        $branches = Branches::find()->where(['status'=>1])->asArray()->all();
        $branches = Branches::find()->where(['status' => 1])->where(['id' => [610, 363, 606, 607, 608, 609]])->asArray()->all();
        foreach ($branches as $branch) {
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch['id']])->all();
            foreach ($branch_projects as $proj) {
                foreach ($months as $month) {
                    $awp = Awp::find()->where(['project_id' => $proj['project_id'], 'month' => $month, 'branch_id' => $branch['id']])->one();
                    if (empty($awp)) {
                        $awp = new Awp();
                        $awp->branch_id = $branch['id'];
                        $awp->region_id = $branch['region_id'];
                        $awp->area_id = $branch['area_id'];
                        $awp->project_id = $proj['project_id'];
                        $awp->month = $month;
                        if (!$awp->save()) {
                            print_r($awp->getErrors());
                            die();
                        } else {
                            echo $branch['id'];
                            echo '<><>';
                        }
                    }
                }
            }

        }
    }

    //    nohup php yii awp/branch-sustainability-data

    public function actionBranchSustainabilityData()
    {
//        $modelSustain = AwpBranchSustainability::find()->where(['month'=>'2021-12'])->all();
//        foreach ($modelSustain as $modelS){
//            $modelS->delete();
//            echo 'saved';
//        }
//        die();

        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $files = ['expenses.csv'];
        foreach ($files as $file) {
            $file_name = $file;
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/awp_files/' . $file_name;
            $myfile = fopen($file_path, "r");
            $flag = true;
            $i = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    //$month = date('Y-m', strtotime($fileop[0]));
                    $branch = Branches::find()->select(['id', 'code', 'region_id', 'area_id'])->where(['code' => $fileop[0], 'status' => 1])->asArray()->one();

                    if (!empty($branch)) {

                        $branch_sus = new  AwpBranchSustainability();
                        $branch_sus->branch_id = $branch['id'];
                        $branch_sus->branch_code = $branch['code'];
                        $branch_sus->area_id = $branch['area_id'];
                        $branch_sus->region_id = $branch['region_id'];
                        $branch_sus->actual_expense = (int)(ceil($fileop[3]));
                        $branch_sus->amount_disbursed = (int)$fileop[1];
                        //$branch_sus->month = $month;
                        $branch_sus->month = '2022-12';
                        //$amount = Loans::find()->where(['branch_id' => $branch_sus->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($branch_sus->month))), strtotime(date('Y-m-t', strtotime($branch_sus->month)))])->sum('loan_amount');
                        // $amount = Loans::find()->where(['branch_id' => $branch_sus->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['deleted'=>0])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($branch_sus->month))),'1562803199'])->sum('loan_amount');
                        //$branch_sus->amount_disbursed = !empty($amount) ? $amount : 0;
                        $branch_sus->percentage = 5;
                        $branch_sus->income = (int)(ceil($fileop[2]));
                        $branch_sus->surplus_deficit = round((int)$branch_sus->income - (int)$branch_sus->actual_expense);
                        if (!$branch_sus->save()) {
                            print_r($branch_sus->getErrors());

                        }

                    }
                    $i++;
                    $flag = true;
                }

            }
        }
    }

//  php yii awp/overdue-this-year

    public function actionOverdueThisYear()
    {
        $months = [
           "2025-06" => "2025-06",
//           "2025-04" => "2025-04",
//           "2024-09" => "2024-09",
//           "2024-10" => "2024-10",
//           "2024-11" => "2024-11",
//            "2023-12" => "2023-12"
        ];

        foreach ($months as $month){
            $branches = Branches::find()->where(['status' => 1])->all();

            foreach ($branches as $branch) {
                $overdue = new  AwpOverdue();
                $overdue->branch_id = $branch->id;
                $overdue->region_id = $branch->region_id;
                $overdue->area_id = $branch->area_id;
                $overdue->month = $month;
                $overdue->awp_active_loans = 0;
                $overdue->awp_olp = 0;
                $overdue->diff_olp = 0;
                $overdue->diff_active_loans = 0;
                $overdue->active_loans = 0;
                $overdue->olp = 0;
                $overdue->overdue_numbers = 0;
                $overdue->overdue_amount = 0;

                $start_date = strtotime(date("Y-m-t", strtotime($month)));
                $end_date = strtotime(date("Y-m-t-23:59", strtotime($month)));
                $progres_report = ProgressReports::find()->where(['project_id' => 0])->andWhere(['between', 'report_date', $start_date, $end_date])->one();

                if (!empty($progres_report)) {
                    $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])->one();

                    if (!empty($progress_report_details)) {
                        $overdue->active_loans = $progress_report_details->active_loans;
                        $overdue->olp = $progress_report_details->olp_amount;
                        $overdue->overdue_numbers = $progress_report_details->overdue_borrowers;
                        $overdue->overdue_amount = $progress_report_details->overdue_amount;
                    }
                }
                if (!$overdue->save()) {
                    print_r($overdue->getErrors());
                    die();
                }else{
                    echo '<-month->';
                    echo $month;
                    echo '<-branch->';
                    echo $branch->id;
                    echo '<-->';
                }
            }
        }
    }

//  php yii awp/update-write-off

    public function actionUpdateWriteOff()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'write_off.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/awp_files/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {
                $branch = Branches::find()->where(['code' => $fileop[0]])->one();
                if (!empty($branch)) {
                    $awp_overdue = AwpOverdue::find()->where(['branch_id' => $branch->id, 'month' => '2020-07'])->one();
                    if (!empty($awp_overdue)) {
//                        $awp_overdue->awp_active_loans=$fileop[1];    //no pyment
//                        $awp_overdue->awp_olp=$fileop[2];             //payment 1
                        $awp_overdue->write_off_amount=$fileop[1];    //payment 2
                        $awp_overdue->write_off_recovered=$fileop[4]; //payment 3
//                        $awp_overdue->diff_olp = $fileop[1];  //total
                        $awp_overdue->save();
                    }
                }
            }
            $flag = true;
        }
    }

    public function GetClosedLoans($branch_id, $project_id = 0, $closed_loans_month_wise = [], $month)
    {
        $date = strtotime(date('Y-m-t', strtotime('-1 month', strtotime($month))));

        if ($project_id != 0) {
            $active_loans = Loans::find()->where(['status' => 'Collected', 'branch_id' => $branch_id, 'project_id' => $project_id])->all();
        } else {
            $active_loans = Loans::find()->where(['status' => 'Collected', 'branch_id' => $branch_id])->all();
        }
        $closed_loans = 0;
        $monthly_recovery = 0;
        foreach ($active_loans as $loans) {
            $recv_amnt = Recoveries::find()->where(['loan_id' => $loans->id])->andWhere('receive_date < "' . $date . '"')->sum('amount');

            //$schdl_amnt = Schedules::find()->where(['loan_id' => $loans->id])->andWhere('due_date < "' . date('Y-m-d') . '"')->sum('schdl_amnt');
            $schdl_amnt = $loans->loan_amount;

            $balance = $schdl_amnt - $recv_amnt;
            if ($project_id != 0) {
                $closed_loans_array = $this->GetActiveLoans($loans, $balance, $project_id, $closed_loans_month_wise);
            }

            if ($loans->inst_amnt <= $balance) {
                $closed_loans++;
                $monthly_recovery += $recv_amnt;
            }
        }


        if (isset($closed_loans_array)) {
            $array = array("closed_loans" => $closed_loans, "monthly_recovery" => $monthly_recovery, "closed_loans_array" => $closed_loans_array);


            //die();
            // $array = array("closed_loans" => $closed_loans, "monthly_recovery" => $monthly_recovery,"closed_loans_array"=>$closed_loans_array);
        } else {
            $array = array("closed_loans" => $closed_loans, "monthly_recovery" => $monthly_recovery, "closed_loans_array" => $closed_loans_month_wise);

        }

        return $array;

    }

    public function GetActiveLoans($active_loan, $balance, $project_id, $closed_loans_month_wise)
    {
        $inst = $balance / $active_loan->inst_amnt;

        if ($inst > 0 && $inst <= 1) {
            $closed_loans_month_wise[1][$project_id] = $closed_loans_month_wise[1][$project_id] + 1;


        } else if ($inst > 1 && $inst <= 2) {
            $closed_loans_month_wise[2][$project_id] = $closed_loans_month_wise[2][$project_id] + 1;


        } else if ($inst > 2 && $inst <= 3) {
            $closed_loans_month_wise[3][$project_id] = $closed_loans_month_wise[3][$project_id] + 1;


        } else if ($inst > 3 && $inst <= 4) {

            $closed_loans_month_wise[4][$project_id] = $closed_loans_month_wise[4][$project_id] + 1;

        } else if ($inst > 4 && $inst <= 5) {
            $closed_loans_month_wise[5][$project_id] = $closed_loans_month_wise[5][$project_id] + 1;

        } else if ($inst > 5 && $inst <= 6) {


            $closed_loans_month_wise[6][$project_id] = $closed_loans_month_wise[6][$project_id] + 1;


        } else if ($inst > 6 && $inst <= 7) {
            $closed_loans_month_wise[7][$project_id] = $closed_loans_month_wise[7][$project_id] + 1;


        } else if ($inst > 8 && $inst <= 9) {
            $closed_loans_month_wise[8][$project_id] = $closed_loans_month_wise[8][$project_id] + 1;


        } else if ($inst > 9 && $inst <= 10) {
            $closed_loans_month_wise[9][$project_id] = $closed_loans_month_wise[9][$project_id] + 1;


        } else if ($inst > 10 && $inst <= 11) {
            $closed_loans_month_wise[10][$project_id] = $closed_loans_month_wise[10][$project_id] + 1;


        } else if ($inst > 11 && $inst <= 12) {
            $closed_loans_month_wise[11][$project_id] = $closed_loans_month_wise[11][$project_id] + 1;

        } else if ($inst > 12 && $inst <= 13) {
            $closed_loans_month_wise[11][$project_id] = $closed_loans_month_wise[11][$project_id] + 1;

        }

        return $closed_loans_month_wise;

    }

    public function GetAvgRecocovery($branch_id, $date, $project_id = 0)
    {
        $aschedule_date_start = strtotime(date("Y-m-01"));
        $aschedule_date_end = strtotime(date("Y-m-t"));

        if ($project_id != 0) {
            $active_loans = Loans::find()->where(['status' => 'Collected', 'branch_id' => $branch_id, 'project_id' => $project_id])->all();
        } else {
            $active_loans = Loans::find()->where(['status' => 'Collected', 'branch_id' => $branch_id])->all();
        }

        $schedule_amount_total = 0;
        foreach ($active_loans as $loans) {
            $schedule_amount_total += Schedules::find()->where(['loan_id' => $loans->id])->andWhere(['between', 'due_date', $aschedule_date_start, $aschedule_date_end])->sum('schdl_amnt');
        }

        $array = array("schedule_amount" => $schedule_amount_total);

        return $array;
    }

    public function actionLoanManagementCost()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'loan_mangement_cost.csv';
        $file_path = Yii::getAlias('@anyname') . '/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {

                $branch = Branches::find()->select(['id', 'region_id', 'area_id', 'opening_date'])->where(['code' => $fileop[0], 'status' => 1])->one();
                if (!empty($branch)) {
                    $overdue = new  AwpLoanManagementCost();
                    $overdue->branch_id = $branch['id'];
                    $overdue->region_id = $branch['region_id'];
                    $overdue->area_id = $branch['area_id'];
                    //$overdue->date_of_opening = $branch['opening_date'];
                    $overdue->opening_active_loans = filter_var($fileop[1], FILTER_SANITIZE_NUMBER_INT);
                    $overdue->closing_active_loans = filter_var($fileop[2], FILTER_SANITIZE_NUMBER_INT);
                    $overdue->average = filter_var($fileop[3], FILTER_SANITIZE_NUMBER_INT);
                    $overdue->amount = filter_var($fileop[4], FILTER_SANITIZE_NUMBER_INT);
                    $overdue->lmc = filter_var($fileop[5], FILTER_SANITIZE_NUMBER_INT);
                    // $overdue->month =date('Y-m-t',strtotime('last day of previous month')) ;
                    $overdue->save();
                }

            }
            $flag = true;
        }

    }

    public function actionTarVsAch()
    {
        $months = array(
            "2022-01" => "2022-01",
            "2022-02" => "2022-02",
            "2022-03" => "2022-03",
            "2022-04" => "2022-04",
            "2022-05" => "2022-05"

        );
        foreach ($months as $m) {
            $tar_vs_ach = AwpTargetVsAchievement::find()->where(['in', 'month', $m])->all();
//            print_r($tar_vs_ach);
//            die();
            if (!empty($tar_vs_ach) && $tar_vs_ach != null) {
                foreach ($tar_vs_ach as $d) {
                    $branch_id = $d->branch_id;
                    $achieved_loans = Loans::find()->where(['branch_id' => $branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($d->month))), strtotime(date('Y-m-t', strtotime($d->month)))])->count();
                    $amount = Loans::find()->where(['branch_id' => $branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($d->month))), strtotime(date('Y-m-t', strtotime($d->month)))])->sum('loan_amount');

                    $d->achieved_loans = $achieved_loans;
                    $d->achieved_amount = !empty($amount) ? $amount : 0;

                    $d->loans_dif = $d->target_loans - $d->achieved_loans;
                    $d->amount_dif = $d->target_amount - $d->achieved_amount;

                    $d->save();
                }
            }
        }

    }

    public function actionHousingTarVsAch()
    {
        $months = array(
            "2022-01" => "2022-01",
            "2022-02" => "2022-02",
            "2022-03" => "2022-03",
            "2022-04" => "2022-04",
            "2022-05" => "2022-05"

        );
        foreach ($months as $m) {
            $tar_vs_ach = AwpTargetVsAchievement::find()->where(['in', 'month', $m])->andWhere(['in', 'project_id', [52, 61, 62, 64, 67, 76, 77, 78, 79, 83, 90]])->all();
//            print_r($tar_vs_ach);
//            die();
            if (!empty($tar_vs_ach) && $tar_vs_ach != null) {
                foreach ($tar_vs_ach as $d) {
                    $tarcount = $d->achieved_loans;
                    $tarAmount = $d->achieved_amount;

                    $d->target_loans = $tarcount;
                    $d->target_amount = !empty($tarAmount) ? $tarAmount : 0;
                    $d->loans_dif = 0;
                    $d->amount_dif = 0;

                    $d->save();

                    echo '----';
                    echo $m;
                    echo '----';
                }
            }
        }

    }

    public function actionAwpUpdateStatus()
    {
        $branches = [895,
            896,
            897,
            898,
            899,
            900,
            901,
            902,
            903,
            904,
            905,
            906,
            907,
            908,
            909,
            910,
            911,
            912,
            913,
            914,
            915,
            916,
            917,
            918,
            919,
            920,
            921,
            922,
            923,
            924,
            925,
            926,
            927,
            928,
            929,
            930,
            931,
            932,
            933,
            934,
            935
        ];
        foreach ($branches as $m) {
            $model = Awp::find()->where(['branch_id' => $m])->andWhere(['month' => '2022-05'])->all();
            print_r($model);
            die();
            if (!empty($model) && $model != null) {
                foreach ($model as $d) {
                    $d->status = 1;
                    $d->save();

                    echo '----';
                    echo $m;
                    echo '----';
                }
            }
        }

    }

    public function actionAwpCount()
    {
        $dashboard_vis = \common\models\Awp::find()->where(['branch_id' => 506])->andWhere(['month' => '2022-05'])->sum('no_of_loans');

//        $monthFrom = '2022-07';
//        $monthTo = '2023-06';
//        $awp = Awp::find()->where(['>=','month',$monthFrom])->andWhere(['<=','month',$monthTo])->count();
        echo $dashboard_vis;
        die();
    }

    public function actionBranchSustainabilityUpdate()
    {
        $months = array(
//            '2020-07','2020-08','2020-09','2020-10','2020-11','2020-12'
//            '2020-01','2020-02','2020-03','2020-04','2020-05','2020-06'
            '2022-06'
        );
        foreach ($months as $m) {
            $branch_suss = AwpBranchSustainability::find()->where(['in', 'month', $m])->all();
            /*print_r($tar_vs_ach);
            die();*/
            foreach ($branch_suss as $d) {
                $branch_id = $d->branch_id;

                $amount = ArcAccountReportDetails::find()
                    ->innerJoin('arc_account_reports', 'arc_account_reports.id = arc_account_report_details.arc_account_report_id')
                    ->where(['arc_account_reports.report_name' => 'Disbursement Summary'])
                    ->andWhere(['arc_account_report_details.branch_id' => $branch_id])
                    ->andWhere(['arc_account_reports.period' => 'monthly'])
                    ->andWhere(['arc_account_reports.status' => 1])
                    ->andWhere(['arc_account_reports.deleted' => 0])
                    ->andWhere(['not in', 'arc_account_reports.project_id', [52, 62, 61, 0, 64, 67, 76, 77]])
                    ->andWhere(['between', 'arc_account_reports.report_date', strtotime(date('Y-m-01', strtotime($d->month))), strtotime(date('Y-m-t', strtotime($d->month)))])
//                    ->createCommand()->getRawSql();
                    ->sum('amount');

//                $amount = Loans::find()->where(['branch_id' => $branch_id])
//                    ->andWhere(['!=', 'status', 'not Collected'])
//                    ->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($d->month))), strtotime(date('Y-m-t', strtotime($d->month)))])
//                    ->sum('loan_amount');

                $d->amount_disbursed = !empty($amount) ? $amount : 0;
                $d->percentage = 5;

                $d->income = ($d->percentage * $d->amount_disbursed) / 100;
                $d->surplus_deficit = $d->income - $d->actual_expense;
                $d->save(false);
            }
        }
    }

    public function actionBranchSustainabilityDataJuly()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $files = ['expenses_december.csv'];
        foreach ($files as $file) {
            $file_name = $file;
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/awp_files/' . $file_name;
            $myfile = fopen($file_path, "r");
            $flag = true;
            $i = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {

                if ($flag) {
                    $month = date('Y-m', strtotime($fileop[0]));
                    $branch = Branches::find()->select(['id', 'code', 'region_id', 'area_id'])->where(['code' => $fileop[1], 'status' => 1])->asArray()->one();
                    if (!empty($branch)) {
                        $branch_sus = new  AwpBranchSustainability();

                        $branch_sus->branch_id = $branch['id'];
                        $branch_sus->branch_code = $branch['code'];
                        $branch_sus->area_id = $branch['area_id'];
                        $branch_sus->region_id = $branch['region_id'];
                        $branch_sus->actual_expense = $fileop[2];
                        $branch_sus->month = $month;
                        //$amount = Loans::find()->where(['branch_id' => $branch_sus->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($branch_sus->month))), strtotime(date('Y-m-t', strtotime($branch_sus->month)))])->sum('loan_amount');
                        if ($branch_sus->month == '2019-07') {
                            $amount = Loans::find()->where(['branch_id' => $branch_sus->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['deleted' => 0])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-11', strtotime($branch_sus->month))), strtotime(date('Y-m-t-23:59', strtotime($branch_sus->month)))])->sum('loan_amount');
                        } else {
                            $amount = Loans::find()->where(['branch_id' => $branch_sus->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['deleted' => 0])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($branch_sus->month))), strtotime(date('Y-m-t-23:59', strtotime($branch_sus->month)))])->sum('loan_amount');
                        }
                        $branch_sus->amount_disbursed = !empty($amount) ? $amount : 0;
                        $branch_sus->percentage = 5;

                        $branch_sus->income = ceil(($branch_sus->percentage * $branch_sus->amount_disbursed) / 100);
                        $branch_sus->surplus_deficit = $branch_sus->income - $branch_sus->actual_expense;
                        if (!$branch_sus->save()) {
                            print_r($branch_sus->getErrors());

                        }

                    }
                    $i++;
                    $flag = true;
                }

            }
        }
    }

    public function actionBranchSustainabilityDataUpdateJuly()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $files = ['updated_expenses_july.csv'];
        foreach ($files as $file) {
            $file_name = $file;
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/awp_files/' . $file_name;
            $myfile = fopen($file_path, "r");
            $flag = true;
            $i = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $month = date('Y-m', strtotime($fileop[0]));
                    $branch = Branches::find()->select(['id', 'code', 'region_id', 'area_id'])->where(['code' => $fileop[1], 'status' => 1])->asArray()->one();
                    if (!empty($branch)) {
                        $branch_sus = AwpBranchSustainability::find()->where(['month' => $month, 'branch_id' => $branch['id']])->one();
                        if (!empty($branch_sus)) {
                            $branch_sus->actual_expense = $fileop[2];
                            $branch_sus->income = ceil(($branch_sus->percentage * $branch_sus->amount_disbursed) / 100);
                            $branch_sus->surplus_deficit = $branch_sus->income - $branch_sus->actual_expense;
                            if (!$branch_sus->save()) {
                                print_r($branch_sus->getErrors());
                            }
                        }
                    }
                    $i++;
                    $flag = true;
                }
            }
        }
    }

    public function actionTarVsAchUpdateProjectJuly()
    {
        $months = [
            //"2019-07" => "2019-07",
            //"2019-08" => "2019-08",
            "2019-09" => "2019-09",
        ];
        $branches = Branches::find()/*->where(['in', 'region_id', [42]])*/
        ->all();
        foreach ($branches as $b) {
            $projects = BranchProjectsMapping::find()->where(['branch_id' => $b->id])->all();
            foreach ($projects as $pr) {
                foreach ($months as $m) {
                    $target = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                    if (empty($target)) {
                        $target_ach = new AwpTargetVsAchievement();
                        $target_ach->region_id = $b->region_id;
                        $target_ach->area_id = $b->area_id;
                        $target_ach->branch_id = $b->id;
                        $target_ach->project_id = $pr->project_id;
                        $target_ach->month = $m;
                        $awp_data = Awp::find()->where(['month' => $m, 'branch_id' => $b->id, 'project_id' => $pr->project_id])->one();
                        if ($m == '2019-07') {
                            $achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-11', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                            $amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-11', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');

                            $target_ach->target_loans = isset($achieved_loans) ? $achieved_loans : 0;
                            $target_ach->target_amount = isset($amount) ? $amount : 0;
                        } else {
                            $achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                            $amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');

                            $target_ach->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                            $target_ach->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;
                        }


                        $target_ach->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                        $target_ach->achieved_amount = !empty($amount) ? $amount : 0;


                        $target_ach->loans_dif = $target->achieved_loans - $target->target_loans;
                        $target_ach->amount_dif = $target->achieved_amount - $target->target_amount;

                        if (!$target_ach->save()) {
                            print_r($target_ach->getErrors());
                        } else {
                            print_r($target_ach->branch_id . '--' . $target_ach->project_id . ',');
                        }
                    }
                }
            }
        }
    }

    public function actionAwpOverdueMonthly()
    {
        $branches = Branches::find()->select(['id', 'region_id', 'area_id', 'opening_date'])->where(['status' => 1])->all();
        foreach ($branches as $branch) {
            if (!empty($branch)) {
                $overdue = new  AwpOverdue();
                $overdue->branch_id = $branch->id;
                $overdue->region_id = $branch->region_id;
                $overdue->area_id = $branch->area_id;
                $overdue->date_of_opening = $branch->opening_date;
                $overdue->awp_active_loans = 0;
                $overdue->awp_olp = 0;
                // $overdue->month =date('Y-m-t',strtotime('last day of previous month')) ;
                $overdue->month = "2022-05-31";

                $progres_report = ProgressReports::find()->select('id')->where(['project_id' => 0, 'report_date' => $overdue->month])->one();

                if (!empty($progres_report)) {
                    $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch->id])->one();
                    if (!empty($progress_report_details)) {
                        $overdue->overdue_numbers = $progress_report_details->overdue_borrowers;
                        $overdue->overdue_amount = $progress_report_details->overdue_amount;
                        $overdue->active_loans = $progress_report_details->active_loans;
                        $overdue->olp = $progress_report_details->olp_amount;
                    }
                }

                $overdue->diff_active_loans = $overdue->active_loans - $overdue->awp_active_loans;
                $overdue->diff_olp = $overdue->olp - $overdue->awp_olp;

                $overdue->save();
            }
        }
    }

    public function actionAwpOverdue()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'Active loan and OLP.csv';
        $file_path = Yii::getAlias('@anyname') . '/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {

                if ($i != 0) {
                    $branch = Branches::find()->select(['id', 'region_id', 'area_id', 'opening_date'])->where(['code' => $fileop[0], 'status' => 1])->one();
                    if (!empty($branch)) {
                        $overdue = new  AwpOverdue();
                        $overdue->branch_id = $branch['id'];
                        $overdue->region_id = $branch['region_id'];
                        $overdue->area_id = $branch['area_id'];
                        //$overdue->date_of_opening = $branch['opening_date'];
                        $overdue->awp_active_loans = filter_var($fileop[1], FILTER_SANITIZE_NUMBER_INT);
                        $overdue->awp_olp = filter_var($fileop[2], FILTER_SANITIZE_NUMBER_INT);
                        // $overdue->month =date('Y-m-t',strtotime('last day of previous month')) ;
                        $overdue->month = "2022-05-31";
                        $month = "2022-05-31";


                        $progres_report = ProgressReports::find()->select('id')->where(['project_id' => 0, 'report_date' => strtotime($month)])->one();

                        if (!empty($progres_report)) {
                            $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])->one();
                            if (!empty($progress_report_details)) {

                                $overdue->active_loans = $progress_report_details->active_loans;
                                $overdue->olp = $progress_report_details->olp_amount;
                            }
                        }


                        $progres_report1 = ProgressReports::find()->select('id')->where(['project_id' => 0, 'report_date' => strtotime($overdue->month)])->one();

                        if (!empty($progres_report1)) {
                            $progress_report_details1 = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report1->id, 'branch_id' => $branch['id']])->one();
                            if (!empty($progress_report_details1)) {
                                $overdue->overdue_numbers = $progress_report_details1->overdue_borrowers;
                                $overdue->overdue_amount = $progress_report_details1->overdue_amount;
                            }
                        }
                        $overdue->diff_active_loans = $overdue->active_loans - $overdue->awp_active_loans;
                        $overdue->diff_olp = $overdue->olp - $overdue->awp_olp;

                        $overdue->save();
                    }
                }
                $i++;
            }
            $flag = true;
        }

    }

    public function actionCloseLoans()
    {
        $closed_loans_month_wise = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0
        );
        //$date = date('Y-m-t', strtotime('-1 month', strtotime($month)));
        $date = strtotime(date('Y-06-t'));
        $branches = Branches::find()->where(['in', 'id', array(1)])->all();

        $closed_loans = 0;
        $monthly_recovery = 0;

        foreach ($branches as $b) {
            if (isset($b->projects)) {
                foreach ($b->projects as $p) {

                    $loans = Loans::find()
                        ->select(['loans.id', 'loans.disbursed_amount', 'loans.inst_amnt', '(select COALESCE(sum(amount),0) from recoveries where recoveries.loan_id=loans.id and receive_date <= "' . $date . '") as credit'])
                        ->where(['status' => 'collected', 'branch_id' => $b->id, 'project_id' => $p->project_id])
                        ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $date], ['status' => 'loan completed']]])
                        ->asArray()
                        ->all();

                    foreach ($loans as $loan) {
                        $schdl_amnt = $loan['disbursed_amount'];
                        $balance = $schdl_amnt - $loan['credit'];
                        $inst = $balance / $loan['inst_amnt'];
                        $closed_loans_month_wise = $this->GetActiveLoans1($inst, $closed_loans_month_wise);
                        print_r($loan);
                        print_r($closed_loans_month_wise);
                        die();
                    }

                    print_r($closed_loans_month_wise);
                    die();

                    $awp = Awp::find()->where(['branch_id' => $b->id])->andWhere(['>=', 'month', '2022-07'])->orderBy('month ASC')->all();
//
                    foreach ($awp as $key => $a) {
                        $count = $closed_loans_month_wise[$key + 1];
                        $update_query = 'update awp a
                                    set a.monthly_closed_loans = "' . (int)$count . '" where a.id = "' . $a->id . '" and a.branch_id = "' . $b->id . '" and a.project_id = "' . $p->project_id . '"';
                        $connection = Yii::$app->db;
                        $connection->createCommand($update_query)->execute();
                    }
//                    $closed_loans_month_wise = array(
//                        1 => 0,
//                        2 => 0,
//                        3 => 0,
//                        4 => 0,
//                        5 => 0,
//                        6 => 0,
//                        7 => 0,
//                        8 => 0,
//                        9 => 0,
//                        10 => 0,
//                        11 => 0,
//                        12 => 0
//                    );
//                    print_r($awp);
                }
            }
        }
    }

    public function GetActiveLoans1($inst, $closed_loans_month_wise)
    {
        if ($inst > 0 && $inst <= 1) {
            $closed_loans_month_wise[1] = $closed_loans_month_wise[1] + 1;
        } else if ($inst > 1 && $inst <= 2) {
            $closed_loans_month_wise[2] = $closed_loans_month_wise[2] + 1;
        } else if ($inst > 2 && $inst <= 3) {
            $closed_loans_month_wise[3] = $closed_loans_month_wise[3] + 1;
        } else if ($inst > 3 && $inst <= 4) {
            $closed_loans_month_wise[4] = $closed_loans_month_wise[4] + 1;
        } else if ($inst > 4 && $inst <= 5) {
            $closed_loans_month_wise[5] = $closed_loans_month_wise[5] + 1;
        } else if ($inst > 5 && $inst <= 6) {
            $closed_loans_month_wise[6] = $closed_loans_month_wise[6] + 1;
        } else if ($inst > 6 && $inst <= 7) {
            $closed_loans_month_wise[7] = $closed_loans_month_wise[7] + 1;
        } else if ($inst > 7 && $inst <= 8) {
            $closed_loans_month_wise[8] = $closed_loans_month_wise[8] + 1;
        } else if ($inst > 8 && $inst <= 9) {
            $closed_loans_month_wise[9] = $closed_loans_month_wise[9] + 1;
        } else if ($inst > 9 && $inst <= 10) {
            $closed_loans_month_wise[10] = $closed_loans_month_wise[10] + 1;
        } else if ($inst > 10 && $inst <= 11) {
            $closed_loans_month_wise[11] = $closed_loans_month_wise[11] + 1;
        } else if ($inst > 11 && $inst <= 12) {
            $closed_loans_month_wise[12] = $closed_loans_month_wise[12] + 1;
        } else if ($inst > 12 && $inst <= 13) {
            $closed_loans_month_wise[12] = $closed_loans_month_wise[12] + 1;
        }
        return $closed_loans_month_wise;
    }

    public function actionAwpCrones()
    {
        $this->actionAwpOverdue();
        $this->actionBranchSustainabilityData();
        $this->actionTarVsAchData();


    }

    public function actionAwpProjectWise()
    {
        $months = array(
            "2022-07" => "2022-07",
            "2022-08" => "2022-08",
            "2022-09" => "2022-09",
            "2022-10" => "2022-10",
            "2022-11" => "2022-11",
            "2022-12" => "2022-12",
            "2023-01" => "2023-01",
            "2023-02" => "2023-02",
            "2023-03" => "2023-03",
            "2023-04" => "2023-04",
            "2023-05" => "2023-05",
            "2023-06" => "2023-06"

        );

        $month = date('Y-m');
        $date = strtotime(date('Y-m-t'));
        $date_first = strtotime(date('Y-m-01'));
        $awps = Awp::find()->where(['month' => $month])->all();
        foreach ($awps as $awp) {
            $actual_no_of_loans = Loans::find()->where(['status' => 'Collected', 'branch_id' => $awp->branch_id, 'project_id' => $awp->project_id])->andWhere(['between', 'date_disbursed', $date_first, $date])->count();
            $actual_recovery = Recoveries::find()->where(['branch_id' => $awp->branch_id, 'project_id' => $awp->project_id])->andWhere(['between', 'receive_date', $date_first, $date])->sum('amount');
            $actual_disbursement = Loans::find()->where(['branch_id' => $awp->branch_id, 'project_id' => $awp->project_id])->andWhere(['between', 'date_disbursed', $date_first, $date])->andWhere(['!=', 'status', 'Not Collected'])->sum('loan_amount');
            $awp->actual_no_of_loans = !empty($actual_no_of_loans) ? $actual_no_of_loans : 0;
            $awp->actual_recovery = !empty($actual_recovery) ? $actual_recovery : 0;
            $awp->actual_disbursement = !empty($actual_disbursement) ? $actual_disbursement : 0;
            $awp->save();
        }
    }

    public function actionUnlockAwp()
    {
        $last_month = date('Y-m', strtotime('last day of previous month'));
        $awp_last = Awp::find()->where(['month' => $last_month, 'is_lock' => 0])->all();
        foreach ($awp_last as $model) {
            $model->is_lock = 1;
            if ($model->save()) {
            } else {
                print_r($model->getErrors());
            }
        }
        $current_month = date('Y-m');
        $awp_cur = Awp::find()->where(['month' => $current_month])->all();
        foreach ($awp_cur as $model) {
            $model->is_lock = 0;
            if ($model->save()) {
            } else {
                print_r($model->getErrors());
            }
        }
    }

    public function actionUnlockAwpExtended()
    {
        $months = array(
            "2022-07" => "2022-07",
            "2022-08" => "2022-08",
            "2022-09" => "2022-09",
            "2022-10" => "2022-10",
            "2022-11" => "2022-11",
            "2022-12" => "2022-12",
            "2023-01" => "2023-01",
            "2023-02" => "2023-02",
            "2023-03" => "2023-03",
            "2023-04" => "2023-04",
            "2023-05" => "2023-05",
            "2023-06" => "2023-06"

        );

        foreach ($months as $month) {
            $model = Awp::find()->where(['month' => $month, 'status' => 0])->all();
            foreach ($model as $m) {
                if (!empty($m) && $m != null) {
                    $m->status = 1;
                    if ($m->save()) {
                        echo 'saved';
                        echo '----';
                    } else {
                        print_r($m->getErrors());
                    }
                }
            }
        }
    }

    public function actionUpdateExpectedRecovery()
    {
        //echo  ' start time: '. time();
        $last_month = strtotime(date('Y-m', strtotime('last day of previous month')));
        $last_month_11 = strtotime(date('Y-m-11', ($last_month)));
        $first_day_of_this_month = strtotime(date('Y-m-01'));
        $last_day_of_current_month = strtotime(date('Y-m-t'));

        $first_day_of_previous_month = strtotime(date('Y-m-1', ($last_month)));
        $last_day_of_previous_month = strtotime(date('Y-m-t', ($last_month)));
        $current_month = (date('Y-m'));
        $current_date = strtotime(date('Y-m-d'));
        $awp_map = Awp::find()->where(['month' => $current_month])/*->andWhere(['in','branch_id',[315,316,782,389,111,466,598,599,600,601,776,778,63 ]])*//*->andWhere(['in','project_id',array(39,47)])*/
        ->all();

        $total_exp_recovery = 0;
        foreach ($awp_map as $map) {
            $monthly_expected_recovery = Recoveries::find()
                ->where(['branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])
                ->andWhere(['deleted' => 0])
                ->sum('amount');
            /*print_r($monthly_expected_recovery);
            die();*/

            /*$previous_month_recovery = Recoveries::find()
                ->where(['branch_id'=>$map->branch_id])
                ->andWhere(['project_id'=>$map->project_id])
                ->andWhere(['between','receive_date',$first_day_of_previous_month,$last_day_of_previous_month])
                ->andWhere(['deleted'=>0])
                ->sum('amount');*/

            $active_loans = Loans::find()
                ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $first_day_of_this_month], ['status' => 'loan completed']]])
                ->andWhere(['<', 'date_disbursed', $last_month_11])
                ->andWhere(['branch_id' => $map->branch_id, 'project_id' => $map->project_id])
                ->andWhere(['deleted' => 0])
                ->all();

            $exp_recovery = 0;
            foreach ($active_loans as $loan) {

                $cur_recovery = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])->andWhere(['deleted' => 0])->sum('amount');
                if (empty($cur_recovery)) {
                    //$exp_recovery+=$cur_recovery;
                    $balance = 0;
                    $schdl_amnt = 0;
                    $amount = 0;
                    $recovery = Recoveries::find()->select(['coalesce(sum(amount),0) as sum'])->where(['loan_id' => $loan->id])->andWhere(['deleted' => 0])->asArray()->one();
                    //$recovery=Recoveries::find()->where(['loan_id'=>$loan->id])->sum('coalesce(credit,0)');
                    $recovery = !empty($recovery['sum']) ? $recovery['sum'] : 0;
                    $balance = $loan->loan_amount - $recovery;
                    $schdl_amnt = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as sum'])
                        ->where(['loan_id' => $loan->id])
                        ->andWhere(['between', 'due_date', $first_day_of_this_month, $last_day_of_current_month])
                        ->asArray()->one();
                    $schdl_amnt = (!empty($schdl_amnt['sum']) ? $schdl_amnt['sum'] : 0);
                    if ($schdl_amnt > $balance) {
                        $amount = $balance;
                    } else {
                        $amount = $schdl_amnt;
                    }
                    $exp_recovery += $amount;
                }
            }
            echo '(' . $map->branch_id . '-' . $map->project_id . ')' . '<br>';
            $exp_recovery = $exp_recovery + $monthly_expected_recovery/*+$previous_month_recovery*/
            ;
            $map->monthly_recovery = $exp_recovery;
            //$total_exp_recovery+=$exp_recovery;
            $map->save();
            echo ' end time: ' . time();
            echo '(' . $map->id . ',' . $map->monthly_recovery . ')-';
        }

    }

    public function actionUpdateExpectedRecoveryReport()
    {
        //echo  ' start time: '. time();
        $last_month = strtotime(date('Y-m', strtotime('last day of previous month')));
        $last_month_11 = strtotime(date('Y-m-11', ($last_month)));
        $first_day_of_this_month = strtotime(date('Y-m-01'));
        $last_day_of_current_month = strtotime(date('Y-m-t'));

        $first_day_of_previous_month = strtotime(date('Y-m-1', ($last_month)));
        $last_day_of_previous_month = strtotime(date('Y-m-t', ($last_month)));
        $current_month = (date('Y-m'));
        $current_date = strtotime(date('Y-m-d'));
        $awp_map = Awp::find()->where(['month' => $current_month])->andWhere(['between', 'branch_id', 201, 900])/*->andWhere(['in','branch_id',[315,316,782,389,111,466,598,599,600,601,776,778,63 ]])*//*->andWhere(['in','project_id',array(39,47)])*/
        ->all();

        $total_exp_recovery = 0;
        foreach ($awp_map as $map) {
            $monthly_expected_recovery = ArcAccountReportDetails::find()
                ->join('inner join', 'arc_account_reports', 'arc_account_report_details.arc_account_report_id = arc_account_reports.id')
                ->where(['branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['report_name' => 'Recovery Summary'])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['report_date' => $last_day_of_current_month])
                ->andWhere(['deleted' => 0, 'status' => 1])
                ->sum('amount');


            $active_loans = Loans::find()
                ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $first_day_of_this_month], ['status' => 'loan completed']]])
                ->andWhere(['<', 'date_disbursed', $last_month_11])
                ->andWhere(['branch_id' => $map->branch_id, 'project_id' => $map->project_id])
                ->andWhere(['deleted' => 0])
                ->all();

            $exp_recovery = 0;
            foreach ($active_loans as $loan) {

                $cur_recovery = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])->andWhere(['deleted' => 0])->sum('amount');
                if (empty($cur_recovery)) {
                    //$exp_recovery+=$cur_recovery;
                    $balance = 0;
                    $schdl_amnt = 0;
                    $amount = 0;
                    $recovery = Recoveries::find()->select(['coalesce(sum(amount),0) as sum'])->where(['loan_id' => $loan->id])->andWhere(['deleted' => 0])->asArray()->one();

                    $recovery = !empty($recovery['sum']) ? $recovery['sum'] : 0;
                    $balance = $loan->loan_amount - $recovery;
                    $schdl_amnt = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as sum'])
                        ->where(['loan_id' => $loan->id])
                        ->andWhere(['between', 'due_date', $first_day_of_this_month, $last_day_of_current_month])
                        ->asArray()->one();
                    $schdl_amnt = (!empty($schdl_amnt['sum']) ? $schdl_amnt['sum'] : 0);
                    if ($schdl_amnt > $balance) {
                        $amount = $balance;
                    } else {
                        $amount = $schdl_amnt;
                    }
                    $exp_recovery += $amount;
                }
            }
            echo '(' . $map->branch_id . '-' . $map->project_id . ')' . '<br>';
            $exp_recovery = $exp_recovery + $monthly_expected_recovery/*+$previous_month_recovery*/
            ;
            $map->monthly_recovery = $exp_recovery;
            //$total_exp_recovery+=$exp_recovery;
            $map->save();
            /*echo  ' end time: '. time();
            echo '('.$map->id.','.$map->monthly_recovery.')-';*/
        }

    }

    public function actionUpdateExpectedRecoveryUpdated()
    {

        $last_month = strtotime(date('Y-m', strtotime('last day of previous month')));
        $second_last_month = strtotime(date('Y-m', strtotime('-1 month', strtotime('first day of previous month'))));
        $last_month_11 = strtotime(date('Y-m-11', ($last_month)));
        $first_day_of_this_month = strtotime(date('Y-m-01'));
        $last_day_of_current_month = strtotime(date('Y-m-t'));

        $first_day_of_previous_month = strtotime(date('Y-m-1', ($last_month)));
        $last_day_of_previous_month = strtotime(date('Y-m-t', ($last_month)));
        $last_day_of_second_previous_month = strtotime(date('Y-m-t', ($second_last_month)));
        $last_day_of__month = strtotime(date('Y-m-t', ($last_month)));
        $current_month = (date('Y-m'));
        $current_date = 1612949308;
        // $current_date=strtotime(date('Y-m-d'));
//        ->andWhere(['in','branch_id',[1]])
        $awp_map = Awp::find()->where(['month' => $current_month])
//            ->andWhere(['in','project_id',[2,59,30]])
            ->all();
        $total_exp_recovery = 0;
        $exp_recovery = 0;
        foreach ($awp_map as $map) {
            $monthly_expected_recovery = 0;
            $previous_month_recovery = 0;
            $previous_month_disbursement = 0;
            $monthly_expected_recovery = Recoveries::find()
                ->where(['branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])
                ->andWhere(['deleted' => 0])
                ->sum('amount');
            /*print_r($monthly_expected_recovery);
            die();*/

            $previous_month_recovery = ArcAccountReports::find()->join('left join', 'arc_account_report_details', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Recovery Summary'])
                ->andWhere(['between', 'report_date', $last_day_of_second_previous_month, $last_day_of_previous_month])
                ->andWhere(['deleted' => 0])->sum('arc_account_report_details.amount');

            $previous_month_disbursement = ArcAccountReports::find()->join('left join', 'arc_account_report_details', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Disbursement Summary'])
                ->andWhere(['between', 'report_date', $last_day_of_second_previous_month, $last_day_of_previous_month])
                ->andWhere(['deleted' => 0])->sum('arc_account_report_details.amount');

            $active_loans = Loans::find()
                ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $first_day_of_this_month], ['status' => 'loan completed']]])
                ->andWhere(['<', 'date_disbursed', $last_month_11])
                ->andWhere(['branch_id' => $map->branch_id, 'project_id' => $map->project_id])
                ->andWhere(['deleted' => 0])
                ->all();

            $exp_recovery = 0;
            foreach ($active_loans as $loan) {

                $cur_recovery = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])->andWhere(['deleted' => 0])->sum('amount');
                if (empty($cur_recovery)) {
                    //$exp_recovery+=$cur_recovery;
                    $balance = 0;
                    $schdl_amnt = 0;
                    $amount = 0;
                    $recovery = Recoveries::find()->select(['coalesce(sum(amount),0) as sum'])->where(['loan_id' => $loan->id])->andWhere(['deleted' => 0])->asArray()->one();
                    //$recovery=Recoveries::find()->where(['loan_id'=>$loan->id])->sum('coalesce(credit,0)');
                    $recovery = !empty($recovery['sum']) ? $recovery['sum'] : 0;
                    $balance = $loan->loan_amount - $recovery;
                    $schdl_amnt = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as sum'])
                        ->where(['loan_id' => $loan->id])
                        ->andWhere(['between', 'due_date', $first_day_of_this_month, $last_day_of_current_month])
                        ->asArray()->one();
                    $schdl_amnt = (!empty($schdl_amnt['sum']) ? $schdl_amnt['sum'] : 0);
                    if ($schdl_amnt > $balance) {
                        $amount = $balance;
                    } else {
                        $amount = $schdl_amnt;
                    }
                    $exp_recovery += $amount;
                }
            }

            echo '(' . $map->branch_id . '-' . $map->project_id . ')' . '<br>';
            print_r($exp_recovery . '  m ' . $monthly_expected_recovery . " s " . $previous_month_recovery . ' d ' . $previous_month_disbursement);
            $exp_recovery = $exp_recovery + ($monthly_expected_recovery + $previous_month_recovery) - $previous_month_disbursement;
            print_r('sdds' . $exp_recovery);
            $map->monthly_recovery = $exp_recovery;
            //$total_exp_recovery+=$exp_recovery;
            $map->save();
            //echo '('.$map->id.','.$map->monthly_recovery.')-';
        }

    }

    public function actionUpdateExpectedRecoveryUpdated1()
    {

        $last_month = strtotime(date('Y-m', strtotime('last day of previous month')));
        $second_last_month = strtotime(date('Y-m', strtotime('-1 month', strtotime('first day of previous month'))));
        $third_last_month = strtotime(date('Y-m', strtotime('-2 month', strtotime('first day of previous month'))));
        $last_month_11 = strtotime(date('Y-m-11', ($last_month)));
        $first_day_of_this_month = strtotime(date('Y-m-01'));
        $last_day_of_current_month = strtotime(date('Y-m-t'));

        $first_day_of_previous_month = strtotime(date('Y-m-1', ($last_month)));
        $last_day_of_previous_month = strtotime(date('Y-m-t', ($last_month)));
        $last_day_of_second_previous_month = strtotime(date('Y-m-t', ($second_last_month)));
        $last_day_of_third_previous_month = strtotime(date('Y-m-t', ($third_last_month)));
        $last_day_of__month = strtotime(date('Y-m-t', ($last_month)));
        $current_month = (date('Y-m'));
        $current_date = 1612949462;
        $march_last = 1585612800;
        $last_day_current_date = strtotime(date('Y-m-t'));
        $awp_map = Awp::find()->where(['month' => $current_month])
//            ->andWhere(['in','branch_id',[389,466,826,827,828,830,829,102]])
            ->all();

        $total_exp_recovery = 0;
        $exp_recovery = 0;
        foreach ($awp_map as $map) {
            $monthly_expected_recovery = 0;
            $previous_month_recovery = 0;
            $previous_month_disbursement = 0;
            /*$monthly_expected_recovery = Recoveries::find()
                ->where(['branch_id'=>$map->branch_id])
                ->andWhere(['project_id'=>$map->project_id])
                ->andWhere(['between','receive_date',$first_day_of_this_month,$current_date])
                ->andWhere(['deleted'=>0])
                ->sum('amount');*/
            /*print_r($monthly_expected_recovery);
            die();*/

            $previous_month_recovery = ArcAccountReports::find()->join('left join', 'arc_account_report_details', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Recovery Summary'])
                ->andWhere(['between', 'report_date', $march_last, $last_day_current_date])
                ->andWhere(['deleted' => 0])->sum('arc_account_report_details.amount');
            //print_r($last_day_of_third_previous_month.',,,'.$march_last.',,,,,');
            // print_r($previous_month_recovery);
            // die('aa');

            $previous_month_disbursement = ArcAccountReports::find()->join('left join', 'arc_account_report_details', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Disbursement Summary'])
                ->andWhere(['between', 'report_date', $march_last, $last_day_of_previous_month])
                ->andWhere(['deleted' => 0])->sum('arc_account_report_details.amount');

            $active_loans = Loans::find()
                ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $first_day_of_this_month], ['status' => 'loan completed']]])
                ->andWhere(['<', 'date_disbursed', $last_month_11])
                ->andWhere(['branch_id' => $map->branch_id, 'project_id' => $map->project_id])
                ->andWhere(['deleted' => 0])
                ->all();

            $exp_recovery = 0;
            foreach ($active_loans as $loan) {

                $cur_recovery = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_date])->andWhere(['deleted' => 0])->sum('amount');
                if (empty($cur_recovery)) {
                    //$exp_recovery+=$cur_recovery;
                    $balance = 0;
                    $schdl_amnt = 0;
                    $amount = 0;
                    $recovery = Recoveries::find()->select(['coalesce(sum(amount),0) as sum'])->where(['loan_id' => $loan->id])->andWhere(['deleted' => 0])->asArray()->one();
                    //$recovery=Recoveries::find()->where(['loan_id'=>$loan->id])->sum('coalesce(credit,0)');
                    $recovery = !empty($recovery['sum']) ? $recovery['sum'] : 0;
                    $balance = $loan->loan_amount - $recovery;
                    $schdl_amnt = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as sum'])
                        ->where(['loan_id' => $loan->id])
                        //->andWhere(['between','due_date',$first_day_of_this_month,$last_day_of_current_month])
                        ->andWhere(['<', 'due_date', $last_day_of_current_month])
                        ->asArray()->one();
                    $schdl_amnt = (!empty($schdl_amnt['sum']) ? $schdl_amnt['sum'] : 0);
                    /*if($schdl_amnt>$balance){
                        $amount=$balance;
                    }
                    else{
                        $amount=$schdl_amnt;
                    }*/
                    /*if($schdl_amnt>$balance){
                        $amount=$balance;
                    }
                    else*/
                    if ($schdl_amnt > $recovery) {
                        $amount = $schdl_amnt - $recovery;
                    } else {
                        $amount = 0;
                    }
                    /*if($loan->sanction_no=='203-D002-07444'){
                        print_r($recovery.',,,');
                        print_r($balance.',,,');
                        print_r($schdl_amnt.',,,,');
                        print_r($amount);
                        die();
                    }*/
                    //print_r($amount);
                    $exp_recovery += $amount;
                }
            }

            echo '(' . $map->branch_id . '-' . $map->project_id . ')' . '<br>';
            print_r($exp_recovery . '  m ' . $monthly_expected_recovery . " s " . $previous_month_recovery . ' d ' . $previous_month_disbursement);
            $exp_recovery = $exp_recovery + ($monthly_expected_recovery + $previous_month_recovery) - $previous_month_disbursement;
            print_r('sdds' . $exp_recovery);
            $map->monthly_recovery = $exp_recovery;
            //$total_exp_recovery+=$exp_recovery;
            $map->save();
            //echo '('.$map->id.','.$map->monthly_recovery.')-';
            // die();
        }

    }

    public function actionExpectedRecoveryUpdated()
    {

        $last_month = strtotime(date('Y-m', strtotime('last day of previous month')));
        $second_last_month = strtotime(date('Y-m', strtotime('-1 month', strtotime('first day of previous month'))));
        $last_month_11 = strtotime(date('Y-m-11', ($last_month)));
        $first_day_of_this_month = strtotime(date('Y-m-01'));
        $last_day_of_current_month = strtotime(date('Y-m-t'));

        $first_day_of_previous_month = strtotime(date('Y-m-1', ($last_month)));
        $last_day_of_previous_month = strtotime(date('Y-m-t', ($last_month)));
        $last_day_of_second_previous_month = strtotime(date('Y-m-t', ($second_last_month)));
        $last_day_of__month = strtotime(date('Y-m-t', ($last_month)));
        $current_month = (date('Y-m'));
        //$current_date=strtotime(date('Y-m-d'));
        //$current_date=1560643200;
        $current_date = 1612949492;
//        $current_datee=1602047965;
        $current_datee = strtotime(date('Y-m-d'));
        $last_of_month_1 = 1610428467;
        $awp_map = Awp::find()->where(['month' => $current_month])
//            ->andWhere(['in','project_id',[30]])
//            ->andWhere(['=','branch_id',330])
            ->all();
//        $awp_map = Awp::find()->where(['month' => $current_month])->andWhere(['in','branch_id',[14,
//            15, 21, 76, 81, 82, 95, 108, 121, 150, 152, 159, 169, 171, 186, 189, 191,
//            333,
//            336,
//            357,
//            383,
//            395,
//            399,
//            414,
//            470,
//            474,
//            480,
//            504,
//            515,
//            553,
//            555,
//            558,
//            585,
//            594,
//            610,
//            634,
//            649,
//            650,
//            654,
//            655,
//            656,
//            660,
//            670,
//            681,
//            808
//        ]])->all();
        $total_exp_recovery = 0;
        $exp_recovery = 0;
        foreach ($awp_map as $map) {
            /*$monthly_expected_recovery = Recoveries::find()
                ->where(['branch_id'=>$map->branch_id])
                ->andWhere(['project_id'=>$map->project_id])
                ->andWhere(['between','receive_date',$first_day_of_this_month,$current_date])
                ->andWhere(['deleted'=>0])
                ->sum('amount');*/
            $monthly_expected_recovery = ArcAccountReports::find()->join('left join', 'arc_account_report_details', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $map->branch_id])
                ->andWhere(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Recovery Summary'])
                ->andWhere(['between', 'report_date', $first_day_of_this_month, $last_day_of_current_month])
                ->andWhere(['deleted' => 0])->sum('arc_account_report_details.amount');


            $active_loans = Loans::find()
                ->where(['or', ['status' => 'collected'], ['and', ['>=', 'loan_completed_date', $first_day_of_this_month], ['status' => 'loan completed']]])
                ->andWhere(['<', 'date_disbursed', $last_month_11])
                ->andWhere(['branch_id' => $map->branch_id, 'project_id' => $map->project_id])
                ->andWhere(['deleted' => 0])
                ->all();

            $exp_recovery = 0;
            foreach ($active_loans as $loan) {
                $cur_recovery = Recoveries::find()->where(['loan_id' => $loan->id])
                    ->andWhere(['between', 'receive_date', $first_day_of_this_month, $current_datee])
                    ->andWhere(['deleted' => 0])->sum('amount');
                if (empty($cur_recovery)) {
                    //$exp_recovery+=$cur_recovery;
                    $balance = 0;
                    $schdl_amnt = 0;
                    $amount = 0;
                    $recovery = Recoveries::find()->select(['coalesce(sum(amount),0) as sum'])->where(['loan_id' => $loan->id])->andWhere(['deleted' => 0])->asArray()->one();
                    //$recovery=Recoveries::find()->where(['loan_id'=>$loan->id])->sum('coalesce(credit,0)');
                    $recovery = !empty($recovery['sum']) ? $recovery['sum'] : 0;
                    $balance = $loan->loan_amount - $recovery;
                    $schdl_amnt = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as sum'])
                        ->where(['loan_id' => $loan->id])
                        ->andWhere(['between', 'due_date', $first_day_of_this_month, $last_day_of_current_month])
                        ->asArray()->one();
                    $schdl_amnt = (!empty($schdl_amnt['sum']) ? $schdl_amnt['sum'] : 0);
                    if ($schdl_amnt > $balance) {
                        $amount = $balance;
                    } else {
                        $schedule_amount = Schedules::find()->select(['coalesce(sum(schdl_amnt),0) as schdamt'])
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['<=', 'due_date', $last_day_of_current_month])
                            ->asArray()
                            ->one();
                        $recovery_amt = Recoveries::find()->select(['coalesce(sum(amount),0) as recvamt'])
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['deleted' => 0])
                            ->andWhere(['<=', 'receive_date', $last_day_of_current_month])
                            ->asArray()
                            ->one();

                        if ($recovery_amt['recvamt'] > $schedule_amount['schdamt']) {
                            $amount = 0;
                        } else {
                            $amount = $schedule_amount['schdamt'] - $recovery_amt['recvamt'];
//                            $amount=$schdl_amnt;
                        }


                    }
                    $exp_recovery += $amount;
                }
            }

            echo '(' . $map->branch_id . '-' . $map->project_id . ')' . '<br>';
            print_r($monthly_expected_recovery . '  ' . $exp_recovery);
            // $exp_recovery = $map->monthly_recovery + $exp_recovery ;
            //$map->monthly_recovery= $exp_recovery;
            $map->monthly_recovery = $monthly_expected_recovery + $exp_recovery;
            //$total_exp_recovery+=$exp_recovery;
            $map->save();
            //echo '('.$map->id.','.$map->monthly_recovery.')-';

            $arc_account_report_model = ArcAccountReports::find()->where(['project_id' => $map->project_id])
                ->andWhere(['=', 'report_name', 'Recovery Summary'])
                ->andWhere(['between', 'report_date', $first_day_of_this_month, $last_day_of_current_month])
                ->one();
            if ($arc_account_report_model) {
                $arc_account_report_model->is_awp = 0;
                $arc_account_report_model->save(false);
            }

        }

    }

    public function actionMonthlyAwpCrones()
    {
        $this->actionUnlockAwp();
        $this->actionUpdateExpectedRecovery();
    }

//php yii awp/tar-arc-fixes

    public function actionTarArcFixes()
    {
        $months = array(
            "2021-07" => "2021-07",
            "2021-08" => "2021-08",
            "2021-09" => "2021-09",
            "2021-10" => "2021-10",
            "2021-11" => "2021-11",
            "2021-12" => "2021-12",
            "2022-01" => "2022-01",
            "2022-02" => "2022-02",
            "2022-03" => "2022-03",
            "2022-04" => "2022-04",
            "2022-05" => "2022-05"
        );
        foreach ($months as $month) {
            $branches = Branches::find()->andWhere(['status' => 1])->all();
            foreach ($branches as $branch) {
                $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                foreach ($branch_projects as $project) {
                    $tarVsAch = AwpTargetVsAchievement::find()
                        ->where(['branch_id' => $branch->id])
                        ->andWhere(['project_id' => $project->project_id])
                        ->andWhere(['month' => $month])
                        ->one();
                    if(empty($tarVsAch) && $tarVsAch == null){

                        $start_date = strtotime(date('Y-m-01 00:00:00', strtotime($month)));
                        $end_date = strtotime(date('Y-m-t 23:59:59', strtotime($month)));

                        $arcAccRep = ArcAccountReports::find()
                            ->select(['arc_account_reports.project_id', 'sum(d.objects_count) as objects_count', 'sum(d.amount) as amount'])
                            ->join('inner join', 'arc_account_report_details as d', 'd.arc_account_report_id=arc_account_reports.id')
                            ->where(['arc_account_reports.code' => 'disb'])
                            ->andWhere(['>=', 'arc_account_reports.report_date' , $start_date])
                            ->andWhere(['<=', 'arc_account_reports.report_date' , $end_date])
                            ->andWhere(['d.branch_id' => $branch->id])
                            ->andWhere(['arc_account_reports.project_id' => $project->project_id])
                            ->asArray()
                            ->all();
                        if(!empty($arcAccRep[0]['objects_count']) && $arcAccRep[0]['objects_count'] !=null){
                            foreach ($arcAccRep as $data){
                                $tarVsAchModel = new AwpTargetVsAchievement();
                                $tarVsAchModel->month           = $month;
                                $tarVsAchModel->area_id         = $branch->area_id;
                                $tarVsAchModel->region_id       = $branch->region_id;
                                $tarVsAchModel->branch_id       = $branch->id;
                                $tarVsAchModel->project_id      = $data['project_id'];
                                $tarVsAchModel->loans_dif       = 0;
                                $tarVsAchModel->amount_dif      = 0;
                                $tarVsAchModel->target_loans    = $data['objects_count'];
                                $tarVsAchModel->target_amount   = $data['amount'];
                                $tarVsAchModel->achieved_loans  = $data['objects_count'];
                                $tarVsAchModel->achieved_amount = $data['amount'];
                                if($tarVsAchModel->save()){

                                }else{
                                    var_dump($tarVsAchModel);die();
                                }
                            }

                            echo 'branch: ';
                            echo $branch->id;
                            echo 'project: ';
                            echo $project->project_id;
                            echo 'month: ';
                            echo $month;
                            echo '---- ';
                            print_r($arcAccRep);
                        }
                    }
                }
            }
        }
    }



    public function actionUpdateAwpYearly()
    {
        $months = array(
            "2022-06" => "2022-06",
            "2022-07" => "2022-07",
            "2022-08" => "2022-08",
            "2022-09" => "2022-09",
            "2022-10" => "2022-10",
            "2022-11" => "2022-11",
            "2022-12" => "2022-12",
            "2023-01" => "2023-01",
            "2023-02" => "2023-02",
            "2023-03" => "2023-03",
            "2023-04" => "2023-04",
            "2023-05" => "2023-05",
            "2023-06" => "2023-06"
        );

        $branches = Branches::find()->asArray()->all();
        foreach ($branches as $branch) {
            $avg_recovery = 0;
            $avg_r = [];
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch['id']])/*->andWhere(['in','project_id',[69]])*/
            ->all();
            foreach ($branch_projects as $proj) {
                foreach ($months as $month) {
                    $awp = Awp::find()->where(['month' => $month, 'branch_id' => $branch['id'], 'project_id' => $proj['project_id']])->one();
                    if (!empty($awp)) {
                        if ($awp->active_loans < 0) {
                            $awp->active_loans = 0;
                            $awp->monthly_recovery = 0;
                            $awp->monthly_olp = 0;
                            $awp->funds_required = 0;
                            $awp->save();
                        }


//                        if ($month == "2022-06") {
//                            $progres_report = ProgressReports::find()->where(['project_id' => $awp->project_id])->andWhere(['between', 'report_date', $progress_report_date, strtotime(date('Y-m-t-23:59', strtotime($month . ' -1 month')))])->one();
//                            $progres_report = ProgressReports::find()->where(['project_id' => $awp->project_id])->andWhere(['report_date'=> $progress_report_date])->one();
//                            if (!empty($progres_report)) {
//                                $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])->one();
//
//                                if (!empty($progress_report_details)) {
//
//                                    $awp->active_loans = $progress_report_details->active_loans;
//                                    $awp->monthly_olp = $progress_report_details->olp_amount;
//                                }
//                            }
//
//                            $array1 = $this->GetAvgRecocoveryYearly($branch['id'], $month, $awp->project_id);
//                            if ($progress_report_details->active_loans != 0) {
//                                //avg rec
//                                $awp->avg_recovery = round($array1['schedule_amount'] / $progress_report_details->active_loans);
//                            } else {
//                                $awp->avg_recovery = 0;
//                            }
//                            $avg_r[$awp->project_id] = $awp->avg_recovery;
//                            $awp->monthly_recovery = $awp->avg_recovery * $awp->active_loans;
//                            if (!$awp->save()) {
//                                print_r($awp->getErrors());
//                                die();
//                            }else{
//                                print_r($month);
//                                echo '<>';
//                                print_r($branch['id']);
//                            }
//
//                        }
                    }
                }
            }
            print_r($branch['id']);
        }
    }

    public function GetAvgRecocoveryYearly($branch_id, $date, $project_id = 0)
    {
        /*$aschedule_date_start = strtotime(date("Y-m-01"));
        $aschedule_date_end = strtotime(date("Y-m-t"));*/

        $aschedule_date_start = strtotime(date('Y-m-01', strtotime($date)));
        $aschedule_date_end = strtotime(date('Y-m-t', strtotime($date)));
        $active_loans = Loans::find()->where(['status' => 'collected', 'branch_id' => $branch_id, 'project_id' => $project_id])->andWhere(['deleted' => 0])->all();
        $schedule_amount_total = 0;
        foreach ($active_loans as $loans) {
            $schedule_amount_total += Schedules::find()->where(['loan_id' => $loans->id])->andWhere(['between', 'due_date', $aschedule_date_start, $aschedule_date_end])->sum('schdl_amnt');
        }

        $array = array("schedule_amount" => $schedule_amount_total);

        return $array;
    }


    public function GetActiveLoansYearly($inst, $closed_loans_month_wise)
    {
        if ($inst >= 0 && $inst <= 1) {
            $closed_loans_month_wise[1] = $closed_loans_month_wise[1] + 1;
        } else if ($inst > 1 && $inst <= 2) {
            $closed_loans_month_wise[2] = $closed_loans_month_wise[2] + 1;
        } else if ($inst > 2 && $inst <= 3) {
            $closed_loans_month_wise[3] = $closed_loans_month_wise[3] + 1;
        } else if ($inst > 3 && $inst <= 4) {
            $closed_loans_month_wise[4] = $closed_loans_month_wise[4] + 1;
        } else if ($inst > 4 && $inst <= 5) {
            $closed_loans_month_wise[5] = $closed_loans_month_wise[5] + 1;
        } else if ($inst > 5 && $inst <= 6) {
            $closed_loans_month_wise[6] = $closed_loans_month_wise[6] + 1;
        } else if ($inst > 6 && $inst <= 7) {
            $closed_loans_month_wise[7] = $closed_loans_month_wise[7] + 1;
        } else if ($inst > 7 && $inst <= 8) {
            $closed_loans_month_wise[8] = $closed_loans_month_wise[8] + 1;
        } else if ($inst > 8 && $inst <= 9) {
            $closed_loans_month_wise[9] = $closed_loans_month_wise[9] + 1;
        } else if ($inst > 9 && $inst <= 10) {
            $closed_loans_month_wise[10] = $closed_loans_month_wise[10] + 1;
        } else if ($inst > 10 && $inst <= 11) {
            $closed_loans_month_wise[11] = $closed_loans_month_wise[11] + 1;
        } else if ($inst > 11 && $inst <= 12) {
            $closed_loans_month_wise[12] = $closed_loans_month_wise[12] + 1;
        }else if ($inst > 12 && $inst <= 13) {
            $closed_loans_month_wise[13] = $closed_loans_month_wise[13] + 1;
        }
        return $closed_loans_month_wise;
    }

    public function actionLoanPerUm()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'branch_details.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/awp_files/' . $file_name;
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;


        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag) {

                $branch = Branches::find()->select(['id', 'region_id', 'area_id'])->where(['code' => $fileop[4]])->one();
                if (!empty($branch)) {
                    $loans_per_um = new  AwpLoansUm();
                    $loans_per_um->branch_id = $branch['id'];
                    $loans_per_um->region_id = $branch['region_id'];
                    $loans_per_um->area_id = $branch['area_id'];

                    $loans_per_um->active_loans = 0;
                    $progress_report_date = strtotime('2019-06-30');
                    $progres_report = ProgressReports::find()->where(['project_id' => 0])->andWhere(['between', 'report_date', $progress_report_date, strtotime('2019-06-30-23:59')])->one();
                    if (!empty($progres_report)) {
                        $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])->one();

                        if (!empty($progress_report_details)) {
                            $loans_per_um->active_loans = $progress_report_details->active_loans;
                        }
                    }
                    $loans_per_um->no_of_um = $fileop[6];
                    $loans_per_um->no_of_branch_managers = $fileop[7];
                    if ($loans_per_um->no_of_um == 0) {
                        $loans_per_um->active_loans_per_um = 0;
                    } else {
                        $loans_per_um->active_loans_per_um = ceil($loans_per_um->active_loans / $loans_per_um->no_of_um);
                    }
                    if (!$loans_per_um->save()) {
                        print_r($loans_per_um->getErrors());
                        die();
                    }
                }

            }
            $flag = true;
        }

    }

    public function actionTarVsAchThisYear()
    {
        $months = [
            "2019-01" => "2019-01",
            "2019-02" => "2019-02",
            "2019-03" => "2019-03",
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            "2019-06" => "2019-06",
        ];
        $selected_months = [
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            "2019-06" => "2019-06",
        ];
        $failed = [];
        $branches = Branches::find()->all();
        foreach ($branches as $branch) {
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
            foreach ($branch_projects as $proj) {
                foreach ($months as $month) {
                    $awp_data = Awp::find()->where(['month' => $month, 'branch_id' => $branch->id, 'project_id' => $proj->project_id])->one();
                    if (!empty($awp_data)) {
                        $targets = new  AwpTargetVsAchievement();
                        $targets->branch_id = $awp_data->branch_id;
                        $targets->region_id = $awp_data->region_id;
                        $targets->area_id = $awp_data->area_id;
                        $targets->project_id = $awp_data->project_id;
                        $targets->month = $month;

                        $achieved_loans = Loans::find()->where(['branch_id' => $targets->branch_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t-23:59', strtotime($targets->month)))])->count();
                        $amount = Loans::find()->where(['branch_id' => $targets->branch_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($targets->month))), strtotime(date('Y-m-t-23:59', strtotime($targets->month)))])->sum('loan_amount');

                        $targets->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                        $targets->achieved_amount = !empty($amount) ? $amount : 0;

                        if (!in_array($targets->month, $selected_months)) {
                            $targets->target_loans = $awp_data->no_of_loans;
                            $targets->target_amount = $awp_data->disbursement_amount;
                        } else {
                            $targets->target_loans = $targets->achieved_loans;
                            $targets->target_amount = $targets->achieved_amount;
                        }

                        $targets->loans_dif = $targets->achieved_loans - $targets->target_loans;
                        $targets->amount_dif = $targets->achieved_amount - $targets->target_amount;

                        if (!$targets->save()) {
                            $failed[] = $awp_data->branch_id . ' branch and month = ' . $month;
                        }
                    }
                }
            }
        }
        if (!empty($failed)) {
            print_r($failed);
            die();
        }
    }

    public function actionUpdateSustainabilityLastYear()
    {
        $months = [
            /*'2018-12',
            '2019-01',
            '2019-02',
            '2019-03',
            '2019-04',
            '2019-05'*/
            '2020-01',
            '2020-02',
            '2020-03',
            '2020-04',
            '2020-05',
            '2020-06',
        ];
        $branches = Branches::find()->where(['in', 'code',
            [
                '101',
                '107',
                '111',
                '202',
                '205',
                '209',
                '213',
                '216',
                '502',
                '813',
                '1103',
                '1709',
                '1728',
                '1744',
                '1746',
                '1749',
                '1750',
                '1755',
                '1819',
                '1825',
                '1831',
                '2104',
                '2118',
                '2807',
                '2816',
                '2826',
                '3115',
                '3309',
                '3507',
                '14301',
                '14306',
                '50206'
            ]])->all();
        foreach ($branches as $b) {
            $awp_sustainabilities = AwpBranchSustainability::find()->where(['in', 'month', $months])->andWhere(['branch_id' => $b->id])->all();
            foreach ($awp_sustainabilities as $awp_sustainability) {


                $amount = LoanTranches::find()->join('left join', 'loans', 'loans.id =loan_tranches.loan_id ')
                    ->where(['loans.branch_id' => $awp_sustainability->branch_id])
                    ->andWhere(['not in', 'loans.status', ['not collected', 'pending', 'rejected']])
                    ->andWhere(['loans.deleted' => 0])
                    ->andWhere(['not in', 'loans.project_id', [52, 61, 62]])
                    ->andWhere(['between', 'loan_tranches.date_disbursed', strtotime(date('Y-m-01', strtotime($awp_sustainability->month))), strtotime(date('Y-m-t-23:59', strtotime($awp_sustainability->month)))])
                    ->sum('loan_tranches.tranch_amount');


                //$amount = LoanTranches::find()->where(['branch_id' => $awp_sustainability->branch_id])->andWhere(['!=', 'status', 'not collected'])->andWhere(['deleted' => 0])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($awp_sustainability->month))), strtotime(date('Y-m-t-23:59', strtotime($awp_sustainability->month)))])->sum('loan_amount');
                $awp_sustainability->amount_disbursed = !empty($amount) ? $amount : 0;
                $awp_sustainability->income = ceil(($awp_sustainability->percentage * $awp_sustainability->amount_disbursed) / 100);
                $awp_sustainability->surplus_deficit = $awp_sustainability->income - $awp_sustainability->actual_expense;
                if (!$awp_sustainability->save()) {
                    print_r($awp_sustainability->getErrors());
                } else {
                    print_r($awp_sustainability->month . '--' . $b->id . ',');
                }
            }
        }
    }

    public function actionTarVsAchThisYearUpdate()
    {
        $months = [
            /*"2019-01" => "2019-01",
            "2019-02" => "2019-02",
            "2019-03" => "2019-03",*/
            "2019-04" => "2019-04",
            /*"2019-05" => "2019-05",
            "2019-06" => "2019-06",*/
        ];
        $selected_months = [
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            "2019-06" => "2019-06",
        ];
        $branches = Branches::find()->where(['=', 'id', '1'])->all();

        foreach ($branches as $b) {
            foreach ($months as $m) {
                $targets = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m])->all();
                foreach ($targets as $target) {
                    $awp_data = Awp::find()->where(['month' => $target->month, 'branch_id' => $target->branch_id, 'project_id' => $target->project_id])->one();

                    $achieved_loans = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))), strtotime(date('Y-m-t-23:59', strtotime($target->month)))])->count();
                    $amount = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))), strtotime(date('Y-m-t-23:59', strtotime($target->month)))])->sum('loan_amount');

                    $target->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                    $target->achieved_amount = !empty($amount) ? $amount : 0;

                    if (!in_array($target->month, $selected_months)) {
                        $target->target_loans = $awp_data->no_of_loans;
                        $target->target_amount = $awp_data->disbursement_amount;
                    } else {
                        $target->target_loans = $target->achieved_loans;
                        $target->target_amount = $target->achieved_amount;
                    }
                    $target->loans_dif = $target->achieved_loans - $target->target_loans;
                    $target->amount_dif = $target->achieved_amount - $target->target_amount;

                    if (!$target->save()) {
                        print_r($target->getErrors());
                    } else {
                        print_r($target->month . '--' . $target->branch_id . '--' . $target->project_id . ',');
                    }
                }
            }
        }
    }

    //php yii awp/recovery-percent

    public function actionRecoveryPercent()
    {
        $months = [
           "2025-05" => "2025-06",
//           "2025-02" => "2025-02",
//           "2025-03" => "2025-03",
//            "2025-04" => "2025-04",
//           "2024-11" => "2024-11",
//           "2023-12" => "2023-12",
//            "2023-01" => "2023-01",
//            "2023-02" => "2023-02",
//            "2023-03" => "2023-03",
//            "2023-04" => "2023-04",
//            "2023-05" => "2023-05",
            // "2023-06" => "2023-06",
        ];
        $branches = Branches::find()->where(['status' => '1'])->all();

        foreach ($branches as $branch) {
            foreach ($months as $month) {

                if (!empty($branch)) {
                    $rec_per = new  AwpRecoveryPercentage();

                    $rec_per->branch_id = $branch->id;
                    $rec_per->branch_code = $branch->code;
                    $rec_per->area_id = $branch->area_id;
                    $rec_per->region_id = $branch->region_id;
                    $rec_per->month = $month;

                    $connection = Yii::$app->db;

                    $rec_all = $connection->createCommand("select count(distinct(loan_id)) as count,
                                          case 
                                           when receive_date between " . strtotime(date('Y-m-01', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-10-23:59', strtotime($rec_per->month))) . " then '1st'
                                           when receive_date between " . strtotime(date('Y-m-11', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-20-23:59', strtotime($rec_per->month))) . " then '2nd'
                                           when receive_date between " . strtotime(date('Y-m-21', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-t-23:59', strtotime($rec_per->month))) . " then '3rd' END as row from recoveries where branch_id=" . $branch->id . " and deleted=0  and receive_date between " . strtotime(date('Y-m-01', strtotime($rec_per->month))) . " and  " . strtotime(date('Y-m-t-23:59', strtotime($rec_per->month))) . " group by row")->queryAll();


                    $total = 0;
                    foreach ($rec_all as $r) {
                        if ($r['row'] == '1st') {
                            $rec_per->recovery_one_to_ten = $r['count'];
                        } else if ($r['row'] == '2nd') {
                            $rec_per->recovery_eleven_to_twenty = $r['count'];
                        } else if ($r['row'] == '3rd') {
                            $rec_per->recovery_twentyone_to_thirty = $r['count'];
                        }
                        $total += $r['count'];
                    }
                    $rec_per->recovery_count = $total;
                    /*$total_count = $connection->createCommand("select count(distinct(loan_id)) as recv_count from recoveries WHERE branch_id=" .$branch->id." and deleted=0 and recoveries.receive_date between " . strtotime(date('Y-m-01', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-t-23:59', strtotime($rec_per->month))) . "")->queryAll();
                    $rec_per->recovery_count=isset($total_count[0]['recv_count'])?$total_count[0]['recv_count']:0;

                    $rec_1st=$connection->createCommand("select count(distinct(loan_id)) as recv_count from recoveries WHERE branch_id=" .$branch->id." and deleted=0 and recoveries.receive_date between " . strtotime(date('Y-m-01', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-10-23:59', strtotime($rec_per->month))) . "")->queryAll();
                    $rec_2nd=$connection->createCommand("select count(distinct(loan_id)) as recv_count from recoveries WHERE branch_id=" .$branch->id." and deleted=0 and recoveries.receive_date between " . strtotime(date('Y-m-11', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-20-23:59', strtotime($rec_per->month))) . "")->queryAll();
                    $rec_3rd=$connection->createCommand("select count(distinct(loan_id)) as recv_count from recoveries WHERE branch_id=" .$branch->id." and deleted=0 and recoveries.receive_date between " . strtotime(date('Y-m-21', strtotime($rec_per->month))) . " and " . strtotime(date('Y-m-t-23:59', strtotime($rec_per->month))) . "")->queryAll();

                    $rec_per->recovery_one_to_ten=isset($rec_1st[0]['recv_count'])?$rec_1st[0]['recv_count']:0;
                    $rec_per->recovery_eleven_to_twenty=isset($rec_2nd[0]['recv_count'])?$rec_2nd[0]['recv_count']:0;
                    $rec_per->recovery_twentyone_to_thirty=isset($rec_3rd[0]['recv_count'])?$rec_3rd[0]['recv_count']:0;*/

                    /*if($rec_per->recovery_count!=0) {
                        $rec_per->recovery_one_to_ten = ceil(($rec_1st / $rec_per->recovery_count) * 100);
                        $rec_per->recovery_eleven_to_twenty = ceil(($rec_2nd / $rec_per->recovery_count) * 100);
                        $rec_per->recovery_twentyone_to_thirty = ceil(($rec_3rd / $rec_per->recovery_count) * 100);
                    }*/

                    /*$rec_per->recovery_count =Recoveries::find()->where(['branch_id' => $rec_per->branch_id])->andWhere(['deleted' => 0])->andWhere(['between', 'receive_date', strtotime(date('Y-m-01', strtotime($rec_per->month))), strtotime(date('Y-m-t-23:59', strtotime($rec_per->month)))])->distinct('loan_id')->count();
                    $rec_1st = Recoveries::find()->where(['branch_id' => $rec_per->branch_id])->andWhere(['deleted' => 0])->andWhere(['between', 'receive_date', strtotime(date('Y-m-01', strtotime($rec_per->month))), strtotime(date('Y-m-10-23:59', strtotime($rec_per->month)))])->distinct('loan_id')->count();
                    $rec_2nd = Recoveries::find()->where(['branch_id' => $rec_per->branch_id])->andWhere(['deleted' => 0])->andWhere(['between', 'receive_date', strtotime(date('Y-m-11', strtotime($rec_per->month))), strtotime(date('Y-m-20-23:59', strtotime($rec_per->month)))])->distinct('loan_id')->count();
                    $rec_3rd = Recoveries::find()->where(['branch_id' => $rec_per->branch_id])->andWhere(['deleted' => 0])->andWhere(['between', 'receive_date', strtotime(date('Y-m-21', strtotime($rec_per->month))), strtotime(date('Y-m-t-23:59', strtotime($rec_per->month)))])->distinct('loan_id')->count();

                    $rec_per->recovery_one_to_ten = ceil(($rec_1st / $rec_per->recovery_count) * 100);
                    $rec_per->recovery_eleven_to_twenty = ceil(($rec_2nd / $rec_per->recovery_count) * 100);
                    $rec_per->recovery_twentyone_to_thirty = ceil(($rec_3rd / $rec_per->recovery_count) * 100);*/

                    if (!$rec_per->save()) {
                        print_r($rec_per->getErrors());

                    }
                }
            }
        }
    }

    public function actionRecoveryPercentUpdate()
    {
        $months = [
            /*"2018-07" => "2018-07",
            "2018-08" => "2018-08",
            "2018-09" => "2018-09",
            "2018-10" => "2018-10",
            "2018-11" => "2018-11",
            "2018-12" => "2018-12",

            "2019-01" => "2019-01",
            "2019-02" => "2019-02",
            "2019-03" => "2019-03",
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            "2019-06" => "2019-06"
            "2019-07" => "2019-07",
            "2019-08" => "2019-08"*/
            /*"2019-10" => "2019-10",
            "2019-11" => "2019-11"*/
            "2019-12" => "2019-12"

        ];
        $branches = Branches::find()->where(['status' => '1'])/*->andWhere(['>','id',400])*/
        ->all();
        foreach ($branches as $branch) {
            $sql = "select count(distinct(loan_id)) as count, case ";
            foreach ($months as $month) {
                $sql .= "when receive_date between " . strtotime(date('Y-m-01', strtotime($month))) . " and " . strtotime(date('Y-m-10-23:59', strtotime($month))) . " then '1 " . $month . "'
                                           when receive_date between " . strtotime(date('Y-m-11', strtotime($month))) . " and " . strtotime(date('Y-m-20-23:59', strtotime($month))) . " then '2 " . $month . "'
                                           when receive_date between " . strtotime(date('Y-m-21', strtotime($month))) . " and " . strtotime(date('Y-m-t-23:59', strtotime($month))) . " then '3 " . $month . "' ";
            }

            $sql .= "END as row from recoveries where branch_id=" . $branch->id . " and deleted=0  and receive_date between 1575158400 and  1577836799 group by row";
            $connection = Yii::$app->db;

            $rec_all = $connection->createCommand($sql)->queryAll();
            $data = [];
            foreach ($rec_all as $r) {
                if ($r['row'] == '1 2019-12') {
                    $data['2019-12']['a'] = $r['count'];
                } else if ($r['row'] == '2 2019-12') {
                    $data['2019-12']['b'] = $r['count'];
                } else if ($r['row'] == '3 2019-12') {
                    $data['2019-12']['c'] = $r['count'];
                }
            }
            foreach ($months as $month) {
                if (isset($data[$month])) {

                    $rec_per = new  AwpRecoveryPercentage();

                    $rec_per->branch_id = $branch->id;
                    $rec_per->branch_code = $branch->code;
                    $rec_per->area_id = $branch->area_id;
                    $rec_per->region_id = $branch->region_id;
                    $rec_per->month = $month;
                    $rec_per->recovery_one_to_ten = isset($data[$month]['a']) ? $data[$month]['a'] : 0;
                    $rec_per->recovery_eleven_to_twenty = isset($data[$month]['b']) ? $data[$month]['b'] : 0;
                    $rec_per->recovery_twentyone_to_thirty = isset($data[$month]['c']) ? $data[$month]['c'] : 0;
                    $rec_per->recovery_count = $rec_per->recovery_one_to_ten + $rec_per->recovery_eleven_to_twenty + $rec_per->recovery_twentyone_to_thirty;
                    if (!$rec_per->save()) {
                        print_r($rec_per->getErrors());

                    } else {
                        print_r($rec_per->branch_id . ',');
                    }
                }
            }

        }
    }

    public function actionShuffleBranches()
    {


        /*////awp_branch_sustainability
        $months = [
            "2017-07" => "2017-07",
            "2017-08" => "2017-08",
            "2017-09" => "2017-09",
            "2017-10" => "2017-10",
            "2017-11" => "2017-11",
            "2017-12" => "2017-12",
            "2018-01" => "2018-01",
            "2018-02" => "2018-02",
            "2018-03" => "2018-03",
            "2018-04" => "2018-04",
            "2018-05" => "2018-05",
            "2018-06" => "2018-06",
            "2018-07" => "2018-07",
            "2018-08" => "2018-08",
            "2018-09" => "2018-09",
            "2018-10" => "2018-10",
            "2018-11" => "2018-11",
            "2018-12" => "2018-12"
        ];
        $branches=Branches::find()->all();
        foreach ($branches as $b){
            foreach ($months as $month){
                $sustains=AwpBranchSustainability::find()->where(['branch_id'=>$b->id,'month'=>$month])->all();
                foreach ($sustains as $sustain){
                    $sustain->area_id=$b->area_id;
                    $sustain->region_id=$b->region_id;
                    if($sustain->save()){
                        print_r($month.'--'.$b->id.',');
                    }
                }
            }
        }*/


        ////awp_overdue
        /* $months = [
             "2018-12" => "2018-12"
         ];
         $branches=Branches::find()->all();
         oreach ($branches as $b){
             foreach ($months as $month){
                 $overdues=AwpOverdue::find()->where(['branch_id'=>$b->id,'month'=>$month])->all();
                 foreach ($overdues as $overdue){
                     $overdue->area_id=$b->area_id;
                     $overdue->region_id=$b->region_id;
                     if($overdue->save()){
                         print_r($month.'--'.$b->id.',');
                     }
                 }
             }
         }*/

        /////
        ///awp_target_vs_Ach
        $months = [
            "2018-07" => "2018-07",
            "2018-08" => "2018-08",
            "2018-09" => "2018-09",
            "2018-10" => "2018-10",
            "2018-11" => "2018-11",
            "2018-12" => "2018-12",
        ];
        $branches = Branches::find()->all();
        foreach ($branches as $b) {
            foreach ($months as $month) {
                $targets = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $month])->all();
                foreach ($targets as $target) {
                    $target->area_id = $b->area_id;
                    $target->region_id = $b->region_id;
                    if ($target->save()) {
                        print_r($month . '--' . $b->id . ',');
                    }
                }
            }
        }
    }

    public function actionTarVsAchUpdateJune()
    {
        $months = [
            "2019-06" => "2019-06",
        ];
        $branches = Branches::find()->where(['in', 'id', ['226', '341'/*,'227','300'*/]])->all();

        foreach ($branches as $b) {
            foreach ($months as $m) {
                $targets = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m])->all();
                foreach ($targets as $target) {
                    $achieved_loans = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))), '1562803199'])->count();
                    $amount = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))), '1562803199'])->sum('loan_amount');

                    $target->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                    $target->achieved_amount = !empty($amount) ? $amount : 0;

                    $target->target_loans = $target->achieved_loans;
                    $target->target_amount = $target->achieved_amount;

                    $target->loans_dif = 0;
                    $target->amount_dif = 0;

                    if (!$target->save()) {
                        print_r($target->getErrors());
                    } else {
                        print_r($target->branch_id . '--' . $target->project_id . ',');
                    }
                }
            }
        }
    }

    public function actionTarVsAchUpdateMonthly()
    {
        $months = [
            //"2018-07" => "2018-07",
            //"2018-08" => "2018-08",
            //"2018-09" => "2018-09",
            //"2018-10" => "2018-10",
            //"2018-11" => "2018-11",
            //"2018-12" => "2018-12",
            "2019-08" => "2019-08",
        ];
        $branches = Branches::find()->where(['in', 'code', [2901, 2905, 2904, 2909, 2910, 2915, 2917]])->all();

        foreach ($branches as $b) {
            foreach ($months as $m) {
                $targets = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m])->all();
                foreach ($targets as $target) {
                    $awp_data = Awp::find()->where(['month' => $m, 'branch_id' => $b->id, 'project_id' => $target->project_id])->one();
                    //$achieved_loans = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted'=>0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))),strtotime(date('Y-m-t-23:59', strtotime($target->month)))])->count();
                    //$amount = Loans::find()->where(['branch_id' => $target->branch_id, 'project_id' => $target->project_id])->andWhere(['deleted'=>0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($target->month))), strtotime(date('Y-m-t-23:59', strtotime($target->month)))])->sum('loan_amount');

                    //$target->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                    //$target->achieved_amount = !empty($amount) ? $amount : 0;

                    $target->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                    $target->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;

                    $target->loans_dif = $target->achieved_loans - $target->target_loans;
                    $target->amount_dif = $target->achieved_amount - $target->target_amount;

                    if (!$target->save()) {
                        print_r($target->getErrors());
                    } else {
                        print_r($target->branch_id . '--' . $target->project_id . ',');
                    }
                }
            }
        }
    }

    public function actionTarVsAchUpdateProjectMap()
    {
        $months = [
            "2018-07" => "2018-07",
            "2018-08" => "2018-08",
            "2018-09" => "2018-09",
            "2018-10" => "2018-10",
            "2018-11" => "2018-11",
            "2018-12" => "2018-12",
        ];
        $branches = Branches::find()->where(['in', 'region_id', [42]])->all();
        foreach ($branches as $b) {
            $projects = BranchProjectsMapping::find()->where(['branch_id' => $b->id])->all();
            foreach ($projects as $pr) {
                foreach ($months as $m) {
                    $target = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                    if (empty($target)) {
                        $target_ach = new AwpTargetVsAchievement();
                        $target_ach->region_id = $b->region_id;
                        $target_ach->area_id = $b->area_id;
                        $target_ach->branch_id = $b->id;
                        $target_ach->project_id = $pr->project_id;
                        $target_ach->month = $m;
                        $awp_data = Awp::find()->where(['month' => $m, 'branch_id' => $b->id, 'project_id' => $pr->project_id])->one();
                        $achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                        $amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');

                        $target_ach->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                        $target_ach->achieved_amount = !empty($amount) ? $amount : 0;

                        $target_ach->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                        $target_ach->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;

                        $target_ach->loans_dif = $target->achieved_loans - $target->target_loans;
                        $target_ach->amount_dif = $target->achieved_amount - $target->target_amount;

                        if (!$target_ach->save()) {
                            print_r($target_ach->getErrors());
                        } else {
                            print_r($target_ach->branch_id . '--' . $target_ach->project_id . ',');
                        }
                    }
                }
            }
        }
    }

    public function actionTarVsAchUpdateProjectMapThisYear()
    {
        $months = [
            "2019-01" => "2019-01",
            "2019-02" => "2019-02",
            "2019-03" => "2019-03",
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            //"2019-06" => "2019-06",
        ];
        $selected_months = [
            "2019-04" => "2019-04",
            "2019-05" => "2019-05",
            //"2019-06" => "2019-06",
        ];
        $branches = Branches::find()->where(['status' => 1])/*->where(['in', 'region_id', [43]])*/
        ->all();
        foreach ($branches as $b) {
            $projects = BranchProjectsMapping::find()->where(['branch_id' => $b->id])->all();
            foreach ($projects as $pr) {
                foreach ($months as $m) {
                    $target = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                    if (empty($target)) {
                        $target_ach = new AwpTargetVsAchievement();
                        $target_ach->region_id = $b->region_id;
                        $target_ach->area_id = $b->area_id;
                        $target_ach->branch_id = $b->id;
                        $target_ach->project_id = $pr->project_id;
                        $target_ach->month = $m;
                        $awp_data = Awp::find()->where(['month' => $m, 'branch_id' => $b->id, 'project_id' => $pr->project_id])->one();
                        $achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                        $amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');

                        $target_ach->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                        $target_ach->achieved_amount = !empty($amount) ? $amount : 0;
                        if (!in_array($m, $selected_months)) {
                            $target_ach->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                            $target_ach->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;
                        } else {
                            $target_ach->target_loans = $target_ach->achieved_loans;
                            $target_ach->target_amount = $target_ach->achieved_amount;
                        }
                        $target_ach->loans_dif = $target->achieved_loans - $target->target_loans;
                        $target_ach->amount_dif = $target->achieved_amount - $target->target_amount;
                        if (!$target_ach->save()) {
                            print_r($target_ach->getErrors());
                        } else {
                            print_r($target_ach->branch_id . '--' . $target_ach->project_id . ',');
                        }
                    }
                }
            }
        }
    }

    public function actionLoanPerUmUpdate()
    {
        $branches = Branches::find()->where(['in', 'id', ['226', '341'/*,'227','300'*/]])->all();
        foreach ($branches as $b) {
            $loans_ums = AwpLoansUm::find()->where(['branch_id' => $b->id])->all();
            foreach ($loans_ums as $loans_um) {
                $loans_um->active_loans = 0;
                $progress_report_date = strtotime('2019-06-30');
                $progres_report = ProgressReports::find()->where(['project_id' => 0])->andWhere(['between', 'report_date', $progress_report_date, strtotime('2019-06-30-23:59')])->one();
                if (!empty($progres_report)) {
                    $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $b->id])->one();

                    if (!empty($progress_report_details)) {
                        $loans_um->active_loans = $progress_report_details->active_loans;
                    }
                }
                $achieved_loans = Loans::find()->where(['branch_id' => $b->id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', '1561939200', '1562803140'])->count();
                $achieved_loans = isset($achieved_loans) ? $achieved_loans : 0;
                $loans_um->active_loans = $loans_um->active_loans + $achieved_loans;
                if ($loans_um->no_of_um == 0) {
                    $loans_um->active_loans_per_um = 0;
                } else {
                    $loans_um->active_loans_per_um = ceil($loans_um->active_loans / $loans_um->no_of_um);
                }
                if (!$loans_um->save()) {
                    print_r($loans_um->getErrors());
                    // die();
                }
            }

        }
    }

    public function actionSustainabilityUpdateJune()
    {
        $months = [
            "2019-06" => "2019-06",
        ];
        $branches = Branches::find()->where(['in', 'id', ['226', '341'/*,'227','300'*/]])->all();

        foreach ($branches as $b) {
            foreach ($months as $m) {
                $targets = AwpBranchSustainability::find()->where(['branch_id' => $b->id, 'month' => $m])->all();
                foreach ($targets as $target) {
                    $tar = AwpTargetVsAchievement::find()->where(['branch_id' => $target->branch_id, 'month' => $m])->sum('achieved_amount');
                    $target->amount_disbursed = !empty($tar) ? $tar : 0;
                    $target->percentage = 5;

                    $target->income = ceil(($target->percentage * $target->amount_disbursed) / 100);
                    $target->surplus_deficit = $target->income - $target->actual_expense;

                    if (!$target->save()) {
                        print_r($target->getErrors());
                    } else {
                        print_r($target->branch_id . ',');
                    }
                }
            }
        }
    }

    public function actionUpdateBranchDetails()
    {
        ////awp_branch_sustainability
        $sustains = AwpBranchSustainability::find()->all();
        foreach ($sustains as $sustain) {
            $branch = Branches::find()->where(['id' => $sustain->branch_id])->one();
            if (!empty($branch)) {
                $sustain->area_id = $branch->area_id;
                $sustain->region_id = $branch->region_id;
                if ($sustain->save()) {
                    print_r('branch-sustain' . '--' . $sustain->month . ',');
                }
            }
        }
        ////awp_overdue
        $overdues = AwpOverdue::find()->all();
        foreach ($overdues as $overdue) {
            $branch = Branches::find()->where(['id' => $overdue->branch_id])->one();
            $overdue->area_id = $branch->area_id;
            $overdue->region_id = $branch->region_id;
            if ($overdue->save()) {
                print_r('overdue' . '--' . $overdue->month . ',');
            }
        }
        /////
        ///awp_target_vs_Ach
        $targets = AwpTargetVsAchievement::find()->all();
        foreach ($targets as $target) {
            $branch = Branches::find()->where(['id' => $target->branch_id])->one();
            $target->area_id = $branch->area_id;
            $target->region_id = $branch->region_id;
            if ($target->save()) {
                print_r('tar_vs_ach' . '--' . $target->month . ',');
            }
        }
        /////
        ///awp_loans_per_um
        $loans_um = AwpLoansUm::find()->all();
        foreach ($loans_um as $loan_um) {
            $branch = Branches::find()->where(['id' => $loan_um->branch_id])->one();
            $loan_um->area_id = $branch->area_id;
            $loan_um->region_id = $branch->region_id;
            if ($loan_um->save()) {
                print_r('loans_per_um' . '--' . $loan_um->id . ',');
            }
        }
        /////
        ///awp_recovery_percentage
        $recoveriy_percentage = AwpRecoveryPercentage::find()->all();
        foreach ($recoveriy_percentage as $recoveriy_percent) {
            $branch = Branches::find()->where(['id' => $recoveriy_percent->branch_id])->one();
            $recoveriy_percent->area_id = $branch->area_id;
            $recoveriy_percent->region_id = $branch->region_id;
            if ($recoveriy_percent->save()) {
                print_r('recovery_percent' . '--' . $recoveriy_percent->month . ',');
            }
        }
    }

    public function actionTarVsAchUpdateJulyToOctober()
    {
        $months = [
            /*"2019-07" => "2019-07",
            "2019-08" => "2019-08",
            "2019-09" => "2019-09",
            "2019-10" => "2019-10",
            "2019-11" => "2019-11"
            "2019-12" => "2019-12"*/
            "2020-01" => "2020-01",
            "2020-02" => "2020-02",
            //"2020-03"=>"2020-03",
            //"2020-04"=>"2020-04",
            //"2020-05"=>"2020-05",
            //"2020-06"=>"2020-06",
        ];
        $branches = Branches::find()->where(['status' => 1])/*->where(['=', 'id', 1])*/
        ->all();
        foreach ($branches as $b) {
            $projects = BranchProjectsMapping::find()->where(['branch_id' => $b->id])->andWhere(['in', 'project_id', [52, 61, 62]])->all();
            foreach ($projects as $pr) {
                foreach ($months as $m) {
                    $target = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                    if (empty($target)) {
                        $target = new AwpTargetVsAchievement();
                        $target->region_id = $b->region_id;
                        $target->area_id = $b->area_id;
                        $target->branch_id = $b->id;
                        $target->project_id = $pr->project_id;
                        $target->month = $m;
                    }

                    $awp_data = Awp::find()->where(['month' => $m, 'branch_id' => $b->id, 'project_id' => $pr->project_id])->one();
                    $account_report = ArcAccountReportDetails::find()->join('left join', 'arc_account_reports', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                        ->where(['arc_account_report_details.branch_id' => $b->id])
                        ->andWhere(['project_id' => $pr->project_id])
                        ->andWhere(['=', 'report_name', 'Disbursement Summary'])
                        ->andWhere(['between', 'report_date', strtotime(date('Y-m-t 00:00:00', strtotime($m))), strtotime(date('Y-m-t 23:59:59', strtotime($m)))])
                        ->andWhere(['deleted' => 0])->one();
                    $achieved_loans = isset($account_report->objects_count) ? $account_report->objects_count : 0;
                    $amount = isset($account_report->amount) ? $account_report->amount : 0;
                    if (in_array($m, ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06'])) {
                        //$achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-11', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                        //$amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-11', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');
                        $target->target_loans = isset($achieved_loans) ? $achieved_loans : 0;
                        $target->target_amount = isset($amount) ? $amount : 0;
                    } else {
                        //$achieved_loans = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->count();
                        //$amount = Loans::find()->where(['branch_id' => $b->id, 'project_id' => $pr->project_id])->andWhere(['deleted' => 0])->andWhere(['!=', 'status', 'not collected'])->andWhere(['between', 'date_disbursed', strtotime(date('Y-m-01', strtotime($m))), strtotime(date('Y-m-t-23:59', strtotime($m)))])->sum('loan_amount');
                        $target->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                        $target->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;
                        /*if(isset($awp_data->no_of_loans) && $awp_data->no_of_loans!=0){
                            $target->target_loans = isset($awp_data->no_of_loans) ? $awp_data->no_of_loans : 0;
                            $target->target_amount = isset($awp_data->disbursement_amount) ? $awp_data->disbursement_amount : 0;
                        }else{
                            $target->target_loans = isset($achieved_loans) ? $achieved_loans : 0;
                            $target->target_amount = isset($amount) ? $amount : 0;
                        }*/
                    }
                    $target->achieved_loans = !empty($achieved_loans) ? $achieved_loans : 0;
                    $target->achieved_amount = !empty($amount) ? $amount : 0;
                    $target->loans_dif = $target->achieved_loans - $target->target_loans;
                    $target->amount_dif = $target->achieved_amount - $target->target_amount;
                    if (!$target->save()) {
                        print_r($target->getErrors());
                    } else {
                        print_r($target->branch_id . '--' . $target->project_id . ',');
                    }
                }
            }
        }
    }

    public function actionUpdateAwpVsTar()
    {
        $months = [
            "2023-07" => "2023-07",
            "2023-08" => "2023-08",
            "2023-09" => "2023-09",
            "2023-10" => "2023-10",
            "2023-11" => "2023-11",
            "2023-12" => "2023-12",
            // "2023-01" => "2023-01",
            // "2023-02" => "2023-02",
            // "2023-03" => "2023-03",
            // "2023-04" => "2023-04",
            // "2023-05" => "2023-05",
            // "2023-06" => "2023-06"
        ];
        $branches = Branches::find()/*->where(['>', 'id', 72])*/
        ->all();
        foreach ($branches as $b) {
            $projects = BranchProjectsMapping::find()->where(['branch_id' => $b->id])->all();
            foreach ($projects as $pr) {
                foreach ($months as $m) {
                    $target = AwpTargetVsAchievement::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                    if (!empty($target)) {
                        $awp = Awp::find()->where(['branch_id' => $b->id, 'month' => $m, 'project_id' => $pr->project_id])->one();
                        if (!empty($awp)) {
                            if ($awp->no_of_loans == 0) {
                                $awp->no_of_loans = $target->target_loans;
                                if ($target->target_loans != 0) {
                                    $awp->avg_loan_size = $target->target_amount / $target->target_loans;
                                } else {
                                    $awp->avg_loan_size = 0;
                                }
                                $awp->amount_disbursed = $target->target_amount;
                                $awp->funds_required = $awp->monthly_recovery - $target->amount_disbursed;
                                $awp->save();
                            }
                        } else {
                            $awp = new Awp();
                            $awp->region_id = $b->region_id;
                            $awp->area_id = $b->area_id;
                            $awp->branch_id = $b->id;
                            $awp->project_id = $pr->project_id;
                            $awp->month = $m;
                            $awp->no_of_loans = $target->target_loans;
                            if ($target->target_loans != 0) {
                                $awp->avg_loan_size = $target->target_amount / $target->target_loans;
                            }
                            $awp->amount_disbursed = $target->target_amount;
                            $first_day_of_month = strtotime(date('Y-m-1', strtotime($m)));
                            $last_day_of_month = strtotime(date('Y-m-t', strtotime($m)));
                            $monthly_recovery = Recoveries::find()
                                ->where(['branch_id' => $b->id])
                                ->andWhere(['project_id' => $pr->project_id])
                                ->andWhere(['between', 'receive_date', $first_day_of_month, $last_day_of_month])
                                ->andWhere(['deleted' => 0])
                                ->sum('amount');
                            $awp->monthly_recovery = isset($monthly_recovery) ? $monthly_recovery : 0;
                            $awp->funds_required = $awp->monthly_recovery - $target->amount_disbursed;
                            $awp->save();
                        }
                    }
                }
            }
        }
    }

    public function actionUpdateAwpJuly()
    {
        $awps = Awp::find()->where(['month' => '2020-07'])->all();
        foreach ($awps as $awp) {
            $account_report = ArcAccountReportDetails::find()->join('left join', 'arc_account_reports', 'arc_account_report_details.arc_account_report_id =arc_account_reports.id ')
                ->where(['arc_account_report_details.branch_id' => $awp->branch_id])
                ->andWhere(['project_id' => $awp->project_id])
                ->andWhere(['=', 'report_name', 'Disbursement Summary'])
                ->andWhere(['between', 'report_date', strtotime(date('Y-m-t 00:00:00', strtotime($awp->month))), strtotime(date('Y-m-t 23:59:59', strtotime($awp->month)))])
                ->andWhere(['deleted' => 0])->one();
            $achieved_loans = isset($account_report->objects_count) ? $account_report->objects_count : 0;
            $achieved_amount = isset($account_report->amount) ? $account_report->amount : 0;
            $awp->no_of_loans = $achieved_loans;
            $awp->disbursement_amount = $achieved_amount;
            if ($awp->save()) {
                print_r($awp->branch_id . ',');
            }

        }
    }

    public function actionOverdueUpdate()
    {
        $branches = Branches::find()->all();
        $month = '2021-12';
        foreach ($branches as $branch) {
            $overdue = AwpOverdue::find()->where(['branch_id' => $branch->id])
                ->andWhere(['region_id' => $branch->region_id])
                ->andWhere(['area_id' => $branch->area_id])
                ->andWhere(['month' => $month])
                ->one();

            if ($overdue) {
                $progress_report_date = strtotime('2021-12-31');
                $progres_report = ProgressReports::find()->where(['project_id' => 0])->andWhere(['between', 'report_date', $progress_report_date, strtotime('2021-12-31-23:59')])->one();
                if (!empty($progres_report)) {
                    $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch['id']])->one();
                    if (!empty($progress_report_details)) {
                        $overdue->active_loans = $progress_report_details->active_loans;
                        $overdue->overdue_numbers = $progress_report_details->overdue_borrowers;
                        $overdue->overdue_amount = $progress_report_details->overdue_amount;
                    }
                }
                if (!$overdue->save()) {
                    print_r($overdue->getErrors());
                    die();
                }
            }

        }
    }

    public function actionLockAwp()
    {

        $awp = Awp::find()->where(['in','branch_id', [85,386,385,651,653,100]])->andWhere(['>', 'month', '2022-06'])->all();
        foreach ($awp as $as) {
            print_r($as->id);
            $as->status = 1;
            $as->is_lock = 0;
            $as->save();
        }
    }
}


