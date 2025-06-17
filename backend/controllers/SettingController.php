<?php

namespace backend\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\KamyabPakistanHelper;
use common\components\Helpers\LoanHelper;
use common\models\Activities;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\DisbursementDetails;
use common\models\Donations;
use common\models\Fields;
use common\models\FilesAccounts;
use common\models\Images;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\LoginFormAdmin;
use common\models\Members;
use common\models\MembersAccount;
use common\models\News;
use common\models\Operations;
use common\models\ProductActivityMapping;
use common\models\Products;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\SchedulesKpp;
use common\models\search\DuelistSearch;
use common\models\Teams;
use common\widgets\Cib\Cib;
use console\controllers\FixesController;
use console\controllers\ProgressReportController;
use inventory\Helpers\StructureHelper;
use Yii;
use yii\base\Application;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\Request;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SettingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUploadIndex()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            foreach ($dispatchData as $data) {
                $sanction = $data['sanction_no'];
                $loan = Loans::findOne(['sanction_no' => $sanction]);
                FixesHelper::ledger_regenerate($loan);
            }
        }
        return $this->render('uploads-index');
    }

    public function actionBeneficiaryUpdate()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            foreach ($dispatchData as $data) {
                $sanction = $data['sanction_no'];
                $beneficiary_name = $data['name'];
                $beneficiary_cnic = $data['cnic'];
                $loan = Loans::findOne(['sanction_no' => $sanction]);
               if(!empty($loan) && $loan!=null){
                   $application = Applications::find()->where(['id'=>$loan->application_id])->one();
                   if(!empty($application) && $application!=null){
                       $application->name_of_other = $beneficiary_name;
                       $application->other_cnic = $beneficiary_cnic;
                       if(!$application->save(false)){
                           var_dump($application->getErrors());
                           die();
                       }
                   }
               }
            }
        }
        return $this->render('beneficiary-update');
    }

    public function actionMemberUpdate()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['cnics']['tmp_name']);
            foreach ($dispatchData as $data) {
                $cnic = $data['member_cnic'];
                $family_name = $data['family_name'];
                $family_cnic = $data['family_cnic'];
                $member = Members::find()->where(['cnic' => $cnic])->one();
                if(!empty($member) && $member!=null){
                    $member->family_member_name = $family_name;
                    $member->family_member_cnic = $family_cnic;
                    if(!$member->save(false)){
                        var_dump($member->getErrors());
                        die();
                    }
                }
            }
        }
        return $this->render('member-update');
    }

    public function actionExportIndex()
    {
        $searchModel = new DuelistSearch();
        if ($_POST) {
            $queryFilter = '';
            $regions = $this->csvToArray($_FILES['regions']['tmp_name']);
            if (!empty($regions) && $regions != null) {
                $regionsIds = '0';
                foreach ($regions as $r) {
                    $regionsIds = $regionsIds . ',' . $r['region_id'];
                }
                $queryFilter = "(`loans`.`region_id` IN($regionsIds)) AND(`loans`.`deleted` = 0)";
            }
            $areas = $this->csvToArray($_FILES['areas']['tmp_name']);
            if (!empty($areas) && $areas != null) {
                $areasIds = '0';
                foreach ($areas as $a) {
                    $areasIds = $areasIds . ',' . $a['area_id'];
                }
                $queryFilter = "(`loans`.`area_id` IN($areasIds)) AND(`loans`.`deleted` = 0)";
            }
            $branches = $this->csvToArray($_FILES['branches']['tmp_name']);
            if (!empty($branches) && $branches != null) {
                $branchIds = '0';
                foreach ($branches as $b) {
                    $branchIds = $branchIds . ',' . $b['branch_id'];
                }
                $queryFilter = "(`loans`.`branch_id` IN($branchIds)) AND(`loans`.`deleted` = 0)";
            }
            $report_date = $_POST['DuelistSearch']['report_date'];
            $schedule_date = strtotime(date('Y-m-d-23:59', (strtotime($report_date . '+ 9 days'))));
            $disbursed_date = strtotime(date("Y-m-d-23:59", $schedule_date) . " -1 months");
            $recovery_date = strtotime(date('Y-m-t-23:59', (strtotime($report_date . '- 1 months'))));


            $connection = Yii::$app->db;
            $due_report = 'SELECT
                    `loans`.`id` as `member_sync_id`,
                    `applications`.`application_no`,
                    `loans`.`sanction_no`,
                    `loans`.`inst_amnt`,
                    `loans`.`project_id`,
                    `members`.`full_name` AS `full_name`,
                    `members`.`cnic` AS `cnic`,
                    `members`.`parentage` AS `parentage`,
                    `loans`.`loan_amount`,
                    @amountapproved:=`loans`.`disbursed_amount` AS `disbursed_amount`,
                    `applications`.`region_id`,
                    `applications`.`area_id`,
                    `applications`.`branch_id`,
                    FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date_disbursed`,
                    (
                    SELECT
                        code
                    FROM
                        branches
                    WHERE
                        id = applications.branch_id

                ) AS `branch_code`,
                    (
                    SELECT
                        phone
                    FROM
                        members_phone
                    WHERE
                        is_current = 1 AND member_id = members.id AND phone_type = "Mobile"
                    ORDER BY
                        id
                    DESC
                LIMIT 1
                ) AS `phone`, `groups`.`grp_no` AS `grp_no`, @schdl_till_current_month :=(
                SELECT
                  (sum(schedules.schdl_amnt)+sum(schedules.charges_schdl_amount))
                FROM
                    schedules
                WHERE
                    (
                        schedules.loan_id = loans.id and schedules.due_date <= ' . $schedule_date . '
                    )
                ) AS `schdl_till_current_month`,
                @recovery :=(
                SELECT 
                   coalesce((sum(recoveries.amount)+sum(recoveries.charges_amount)),0)
                FROM
                    recoveries
                WHERE
                    ( 
                        recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= ' . $recovery_date . '
                    )
                ) AS `recovery`,
                (
                   IF(( @schdl_till_current_month - @recovery )>0,
                      ( IF(( @schdl_till_current_month - @recovery )<(`loans`.`inst_amnt`) AND  ((@amountapproved - @recovery)>(`loans`.`inst_amnt`)), (`loans`.`inst_amnt`) , (@schdl_till_current_month - @recovery) ) ), `loans`.`inst_amnt`) ) AS `due`,
                (
                    (
                        (@amountapproved-@recovery)
                    )
                ) AS `olp`,
                `teams`.`name` As team
                FROM
                    `loan_tranches`
                LEFT JOIN
                    `loans`
                ON
                    `loan_tranches`.`loan_id` = `loans`.`id`
                LEFT JOIN
                    `applications`
                ON
                    `loans`.`application_id` = `applications`.`id`
                LEFT JOIN
                    `members`
                ON
                    `applications`.`member_id` = `members`.`id`
                LEFT JOIN
                    `groups`
                ON
                    `applications`.`group_id` = `groups`.`id`
                LEFT JOIN
                    `teams`
                ON
                    `applications`.`team_id` = `teams`.`id`
                WHERE
                    (
                        ' . $queryFilter . '
                    ) AND(
                        `loan_tranches`.`date_disbursed` <= ' . $disbursed_date . '
                    ) AND(`loans`.`status` = \'collected\') AND(`loan_tranches`.`status` = 6) GROUP BY `loans`.`id`';
            $due_report = $connection->createCommand($due_report)->queryAll();
            $headers = array('member_sync_id', 'application_no', 'sanction_no', 'inst_amnt', 'project_id', 'full_name', 'cnic', 'parentage', 'loan_amount', 'disbursed_amount', 'region_id', 'area_id', 'branch_id', 'date_disbursed', 'branch_code', 'phone', 'grp_no', 'schdl_till_current_month', 'recovery', 'due', 'olp', 'team');
            ExportHelper::ExportCSV('DueList-Report-' . 'active-loans-' . $report_date . '.csv', $headers, $due_report, '');
            die();
        }

        return $this->render('export-index', [
            'model' => $searchModel
        ]);
    }

    public function actionDisbursedDate()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            $disbursement_date = $_POST['date'];

            if (!empty($dispatchData)) {
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $tranche_no = $data['tranche_no'];
                    if ($tranche_no == 1) {
                        $loan = Loans::findOne(['sanction_no' => $sanction]);
                        if ($loan) {
                            $loan->date_disbursed = strtotime($disbursement_date);
                            if ($loan->save()) {
                                $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan->id])
                                    ->andWhere(['tranch_no' => $tranche_no])
                                    ->one();
                                if (!empty($loan_tranche)) {
                                    $loan_tranche->date_disbursed = strtotime($disbursement_date);
                                    if ($loan_tranche->save()) {

                                    } else {
                                        die('Loan Tranche Update date interruption at ' . $sanction);
                                    }
                                }
                            } else {
                                die('Loan Update date Save interruption at ' . $sanction);
                            }
                        }
                    } else {

                        $loan = Loans::findOne(['sanction_no' => $sanction]);
                        $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan->id])
                            ->andWhere(['tranch_no' => $tranche_no])
                            ->one();

                        if (!empty($loan_tranche)) {
                            $loan_tranche->date_disbursed = strtotime($disbursement_date);
                            if ($loan_tranche->save()) {

                            } else {
                                die('Loan Tranche Update date interruption at ' . $sanction);
                            }
                        }
                    }

                }
            }
        }

        return $this->render('date-update', [
        ]);
    }

    public function actionUpdateLoan()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            if (!empty($dispatchData)) {
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $inst_amount = $data['inst_amount'];
                    $inst_months = $data['inst_months'];
                    $inst_type = $data['inst_type'];
                    $loan = Loans::findOne(['sanction_no' => $sanction]);
                    if ($loan) {
                        $loan->inst_amnt = $inst_amount;
                        $loan->inst_months = $inst_months;
                        $loan->inst_type = $inst_type;
                        if ($loan->save()) {
                        } else {
                            die('Loan Update failed on data' . $sanction);
                        }
                    }
                }
            }
        }

        return $this->render('loan-update', [
        ]);
    }

    public function actionUpdateProject()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            if (!empty($dispatchData)) {
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $project_id = $data['project_id'];
                    $loan = Loans::findOne(['sanction_no' => $sanction]);
                    if ($loan) {
                        $loan->project_id = $project_id;
                        if ($loan->save()) {
                            $application = Applications::find()->where(['id' => $loan->application_id])->one();
                            $application->project_id = $project_id;
                            $application->save();
                        } else {
                            die('Loan Update failed on data' . $sanction);
                        }
                    }
                }
            }
        }

        return $this->render('project-update', [
        ]);
    }

    public function actionUpdateReceipt()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            if (!empty($dispatchData)) {
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $wrong_receipt = $data['receipt_no_wrong'];
                    $correct_receipt = $data['receipt_no_correct'];

                    $loan = Loans::findOne(['sanction_no' => $sanction]);
                    if ($loan) {
                        $recovery = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['receipt_no' => $wrong_receipt])->one();
                        if ($recovery) {
                            $recovery->receipt_no = $correct_receipt;
                            if ($recovery->save(false)) {
                                $donations = Donations::find()->where(['loan_id' => $loan->id])->andWhere(['receipt_no' => $wrong_receipt])->all();
                                foreach ($donations as $donation) {
                                    $donation->receipt_no = $correct_receipt;
                                    if ($donation->save(false)) {

                                    } else {
                                        var_dump($donation->getErrors());
                                        die('Donation Receipt failed to update on data' . $sanction);
                                    }
                                }
                            } else {
                                var_dump($recovery->getErrors());
                                die('Recovery Update failed on data' . $sanction);
                            }
                        }
                    }
                }
            }
        }

        return $this->render('receipt-update', [
        ]);
    }

    public function actionRejectLoan()
    {
        if ($_POST) {
            $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
            if (!empty($dispatchData)) {
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $reject_type = $data['reject_type'];
                    $loan = Loans::find()->where(['sanction_no' => $sanction])->andWhere(['in', 'status', ['pending', 'collected', 'loan completed','grant']])->one();
                    if (empty($loan->recovery)) {
                        if ($reject_type == 'permanent_reject') {
                            if (in_array($loan->project_id, \common\components\Helpers\StructureHelper::trancheProjectsReject())) {
                                $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no asc')->all();
                                if (count($tranch_reject) > 1) {
                                    $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                                    if ($tranche_reject[0]->status > 0) {
                                        $tranche_reject[0]->status = 9;
                                        $tranche_reject[0]->date_disbursed = 0;
                                        $tranche_reject[0]->disbursement_id = 0;
                                        if ($tranche_reject[0]->save()) {
                                            $loan->disbursed_amount = $tranche_reject[1]->tranch_amount;
                                            $loan->date_disbursed = $tranche_reject[1]->date_disbursed;
                                            $loan->disbursement_id = $tranche_reject[1]->disbursement_id;
                                            if ($loan->save(false)) {
                                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranche_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                                foreach ($disbursement_details as $d) {
                                                    $d->status = 2;
                                                    $d->updated_by = 1;
                                                    if (!$d->save()) {
                                                        print_r($d->getErrors());
                                                        die();
                                                    }
                                                }
                                                FixesHelper::ledger_regenerate($loan);
                                                $loan->status = 1;
                                                $loan->save();
                                            }
                                        } else {
                                            die('action failed on' . $sanction);
                                        }
                                    }
                                } else {
                                    if ($tranch_reject[0]->status > 0) {
                                        $tranch_reject[0]->status = 9;
                                        $tranch_reject[0]->date_disbursed = 0;
                                        $tranch_reject[0]->disbursement_id = 0;
                                        if ($tranch_reject[0]->save()) {
                                            $loan->status = 'rejected';
                                            $loan->disbursed_amount = 0;
                                            $loan->date_disbursed = 0;
                                            $loan->disbursement_id = 0;
                                            if ($loan->save()) {
                                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                                foreach ($disbursement_details as $d) {
                                                    $d->status = 2;
                                                    $d->updated_by = 1;
                                                    if (!$d->save()) {
                                                        print_r($d->getErrors());
                                                        die();
                                                    }
                                                }
                                                $connection = \Yii::$app->db;
                                                $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                                $connection->createCommand($schdl_delete)->execute();
                                            }
                                        } else {
                                            die('action failed on' . $sanction);
                                        }
                                    }
                                }
                            } else {
                                $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->one();
                                if ($tranch_reject->status > 0) {
                                    $tranch_reject->status = 9;
                                    $tranch_reject->date_disbursed = 0;
                                    $tranch_reject->disbursement_id = 0;
                                    if ($tranch_reject->save()) {
                                        $loan->status = 'rejected';
                                        $loan->disbursed_amount = 0;
                                        $loan->date_disbursed = 0;
                                        $loan->disbursement_id = 0;
                                        if ($loan->save()) {
                                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                            foreach ($disbursement_details as $d) {
                                                $d->status = 2;
                                                $d->updated_by = 1;
                                                if (!$d->save()) {
                                                    print_r($d->getErrors());
                                                    die();
                                                }
                                            }
                                            $connection = \Yii::$app->db;
                                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                            $connection->createCommand($schdl_delete)->execute();
                                        }
                                    } else {
                                        die('action failed on' . $sanction);
                                    }
                                }
                            }
                            $application = Applications::find()->where(['id'=>$loan->application_id])->one();
                            if(!empty($application) && $application!=null){
                                $application->status = 'rejected';
                                $application->save();
                            }

                        } elseif ($reject_type == 'ready_for_disbursement') {
                            $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();
                            $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                            $tranche_reject_count = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->count();
                            $amount_sum = 0;
                            foreach ($tranche_reject as $key => $amount) {
                                if ($tranche_reject_count > 1) {
                                    if ($amount[$key] == 0) {
                                    } else {
                                        $amount_sum += $amount->tranch_amount;
                                    }
                                } else {
                                    $amount_sum += $amount->tranch_amount;
                                }
                            }
                            if (in_array($loan->project_id, \common\components\Helpers\StructureHelper::trancheProjectsReject()) && $loan->loan_amount > 200000) {
                                $ist_tranch = $tranche_reject[0];

//                            if($loan_tranches[1]->status>0){
//                                $ist_tranch=$loan_tranches[1];
//                            }else{
//                                $ist_tranch=$loan_tranches[0];
//                            }
                            } else {
                                $ist_tranch = $loan_tranches[0];
                            }
                            if ($ist_tranch->status > 4) {
                                //update loan
                                if ($ist_tranch->tranch_no == 1) {
                                    $loan->status = 'pending';
                                    $loan->date_disbursed = 0;
                                    $loan->disbursed_amount = 0;
                                } else {
                                    $loan->disbursed_amount = $amount_sum;
//                                $loan->disbursed_amount=$loan_tranches[0]->tranch_amount;
                                }
                                $loan->save();
                                //update loan actions if housing loan
                                if (in_array($loan->project_id, \common\components\Helpers\StructureHelper::trancheProjectsReject())) {
                                    if ($ist_tranch->tranch_no == 1) {

                                        //account no verified status change
                                        $account_no = MembersAccount::find()->where(['member_id' => $loan->application->member_id])->andWhere(['status' => 1])->andWhere(['is_current' => 1])->one();
                                        if (!empty($account_no)) {
                                            $account_no->status = 0;
                                            if ($account_no->save(false)) {

                                            } else {
                                                var_dump($account_no->getErrors());
                                                die();
                                            }
                                        }

                                    }

                                    if ($ist_tranch->tranch_no == 1) {

                                        $loan_actions = LoanActions::find()->where(['parent_id' => $loan->id])
                                            ->andWhere(['action' => 'account_verification'])
                                            ->one();
                                        if (!empty($loan_actions)) {
                                            $loan_actions->status = 0;
                                            $loan_actions->updated_by = 1;
                                            if ($loan_actions->save(false)) {
                                            } else {
                                                var_dump($loan_actions->getErrors());
                                                die();
                                            }
                                        }
                                    }
                                }
                                //publish entry delete
                                $publish = DisbursementDetails::find()->where(['tranche_id' => $ist_tranch->id])->andWhere(['deleted' => 0])->one();
                                if (!empty($publish)) {
                                    $publish->deleted = 1;
                                    $publish->status = 2;
                                    $publish->save();
                                }
                                //update tranche actions
                                $tranche_actions = LoanTranchesActions::find()->where(['in', 'action', ['disbursement']])->andWhere(['parent_id' => $ist_tranch->id])->all();
                                foreach ($tranche_actions as $l_act) {
                                    $l_act->status = 0;
                                    $l_act->save();
                                }
                                //update tranche
                                $ist_tranch->status = 4;
                                $ist_tranch->disbursement_id = 0;
                                $ist_tranch->date_disbursed = 0;
                                $ist_tranch->attendance_status = 'info_not_available';
                                $ist_tranch->save();
                                //delete schedules if created
                                if ($ist_tranch->tranch_no == 1) {
                                    $connection = \Yii::$app->db;
                                    $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                    $connection->createCommand($schdl_delete)->execute();
                                }
                            } else {
                                die('action failed on' . $sanction);
                            }
                        } elseif ($reject_type == 'ready_for_fund_request') {
                            $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no asc')->all();

                            if (count($loan_tranches) > 1) {
                                $tranche_rejected = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                                $tranche_rejected[0]->status = 3;
                                $tranche_rejected[0]->updated_by = 1;
                                $tranche_rejected[0]->fund_request_id = 0;
                                $tranche_rejected[0]->disbursement_id = 0;
                                $tranche_rejected[0]->cheque_no = '0';
                                $tranche_rejected[0]->date_disbursed = 0;
                                $tranche_rejected[0]->disbursement_id = 0;
                                $tranche_rejected[0]->cheque_date = 0;
                                $tranche_rejected[0]->attendance_status = 'info_not_available';
                                if (!$tranche_rejected[0]->save()) {
                                    print_r($tranche_rejected[0]->getErrors());
                                }

                                $tranche_action = LoanTranchesActions::find()->where(['parent_id' => $tranche_rejected[0]->id])->andWhere(['in', 'action', ['cheque_printing', 'disbursement', 'fund_request']])->andWhere(['status' => 1])->all();
                                foreach ($tranche_action as $t_action) {
                                    $t_action->status = 0;
                                    $t_action->updated_by = 1;
                                    if (!$t_action->save()) {
                                        print_r($t_action->getErrors());
                                    }
                                }
                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranche_rejected[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                foreach ($disbursement_details as $d) {
                                    $d->status = 2;
                                    $d->updated_by = 1;
                                    if (!$d->save()) {
                                        print_r($d->getErrors());
                                        die();
                                    }
                                }

                                $loan->status = 'pending';
                                $loan->date_disbursed = 0;
                                $loan->disbursement_id = 0;
                                $loan->disbursed_amount = 0;

                                if (!$loan->save()) {
                                    print_r($loan->getErrors());
                                }
                                $connection = \Yii::$app->db;
                                $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                $connection->createCommand($schdl_delete)->execute();
                                $loan_actions = LoanActions::find()->where(['parent_id' => $loan->id])->andWhere(['in', 'action', ['cheque_printing', 'takaful', 'disbursement']])->andWhere(['status' => 1])->all();
                                foreach ($loan_actions as $l_action) {
                                    $l_action->status = 0;
                                    $l_action->updated_by = 1;
                                    if (!$l_action->save()) {
                                        print_r($l_action->getErrors());
                                    }
                                }

//                            if($loan_tranches[1]->status>0){
//                            }
                            } else {
                                $loan_tranches[0]->status = 3;
                                $loan_tranches[0]->updated_by = 1;
                                $loan_tranches[0]->fund_request_id = 0;
                                $loan_tranches[0]->disbursement_id = 0;
                                $loan_tranches[0]->cheque_no = '0';
                                $loan_tranches[0]->date_disbursed = 0;
                                $loan_tranches[0]->disbursement_id = 0;
                                $loan_tranches[0]->cheque_date = 0;
                                $loan_tranches[0]->attendance_status = 'info_not_available';
                                if (!$loan_tranches[0]->save()) {
                                    print_r($loan_tranches[0]->getErrors());
                                }

                                $tranche_action = LoanTranchesActions::find()->where(['parent_id' => $loan_tranches[0]->id])->andWhere(['in', 'action', ['cheque_printing', 'disbursement', 'fund_request']])->andWhere(['status' => 1])->all();
                                foreach ($tranche_action as $t_action) {
                                    $t_action->status = 0;
                                    $t_action->updated_by = 1;
                                    if (!$t_action->save()) {
                                        print_r($t_action->getErrors());
                                    }
                                }
                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $loan_tranches[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                foreach ($disbursement_details as $d) {
                                    $d->status = 2;
                                    $d->updated_by = 1;
                                    if (!$d->save()) {
                                        print_r($d->getErrors());
                                        die();
                                    }
                                }

                                $loan->status = 'pending';
                                $loan->date_disbursed = 0;
                                $loan->disbursement_id = 0;
                                $loan->disbursed_amount = 0;
                                if (!$loan->save()) {
                                    print_r($loan->getErrors());
                                }
                                $connection = \Yii::$app->db;
                                $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                $connection->createCommand($schdl_delete)->execute();
                                $loan_actions = LoanActions::find()->where(['parent_id' => $loan->id])->andWhere(['in', 'action', ['cheque_printing', 'takaful', 'disbursement']])->andWhere(['status' => 1])->all();
                                foreach ($loan_actions as $l_action) {
                                    $l_action->status = 0;
                                    $l_action->updated_by = 1;
                                    if (!$l_action->save()) {
                                        print_r($l_action->getErrors());
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }

        return $this->render('reject-loan', [
        ]);
    }

    public function actionRecoveriesPost()
    {
        $controller = new \console\controllers\RecoveriesController(Yii::$app->controller->id, Yii::$app);
        $controller->actionPost();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionResponseRejected()
    {
        $controller = new \console\controllers\PaymentPinsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionRejected();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionAccountNoUpdate()
    {
        $controller = new \console\controllers\AccountsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionUpdateAccountNo();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAccountsReport()
    {
        $controller = new \console\controllers\AccountReportsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionExecuteAccountReport();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionProgressReport()
    {
        $controller = new \console\controllers\ProgressReportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionExecuteProgressReport();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionDynamicReport()
    {
        $controller = new \console\controllers\DynamicReportsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionGenerate();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAgingReport()
    {
        $controller = new \console\controllers\AgingreportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionAgingCronUpdated();
        return $this->redirect(Yii::$app->request->referrer);
    }
    public function actionAgingAccountReport()
    {
        $controller = new \console\controllers\AgingCustomReportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionAgingCronUpdated();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionPublish()
    {
        $controller = new \console\controllers\BankAccountsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionPublish();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPublishTranche()
    {
        $controller = new \console\controllers\BankAccountsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionPublishOut();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionAwp()
    {
        $controller = new \console\controllers\AwpController(Yii::$app->controller->id, Yii::$app);
        $controller->actionExpectedRecoveryUpdated();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPublishAccountUpdate()
    {
        $controller = new \console\controllers\AccountsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionUpdateAccountNoPublished();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdateProgressReport()
    {
        $controller = new ProgressReportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionExecuteProgressReportSpecific();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddMonthlyProgressReport()
    {
        $controller = new ProgressReportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionAddProjectsMonthlyProgress();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddDailyProgressReport()
    {
        $controller = new ProgressReportController(Yii::$app->controller->id, Yii::$app);
        $controller->actionAddDailyProgress();
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionMemberAccountUpdate()
    {
        if ($_POST) {
            if (!empty($_FILES['accounts_file'])) {
                $dispatchData = $this->csvToArray($_FILES['accounts_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $name = trim($data['member_name']);
                    $cnic = trim($data['member_cnic']);
                    $account = trim($data['member_account']);

                    $member = Members::find()->where(['cnic' => $cnic])->select(['id'])->one();
                    if ($member) {
                        $memberAccounts = MembersAccount::find()->where(['member_id' => $member->id])->andWhere(['is_current' => 1])->one();
                        if ($memberAccounts) {
                            $memberAccounts->title = $name;
                            $memberAccounts->account_no = $account;
                            if ($memberAccounts->save()) {
                                $application = Applications::find()->where(['member_id' => $member->id])->orderBy(['id' => SORT_DESC,])->select(['id'])->one()->toArray();
                                if ($application) {
                                    $loan = Loans::find()->where(['application_id' => $application['id']])->one();
                                    if ($loan) {
                                        $tranch = LoanTranches::find()->where(['loan_id' => $loan['id']])->select(['id'])->one()->toArray();
                                        $disbursement = DisbursementDetails::find()->where(['tranche_id' => $tranch['id']])->one();
                                        if ($disbursement) {
                                            $disbursement->account_no = $account;
                                            $disbursement->save();
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            } else {
                $name = trim($_POST['member_name']);
                $cnic = trim($_POST['member_cnic']);
                $account = trim($_POST['member_account']);

                $member = Members::find()->where(['cnic' => $cnic])->select(['id'])->one();
                if ($member) {
                    $memberAccounts = MembersAccount::find()->where(['member_id' => $member->id])->andWhere(['is_current' => 1])->one();
                    if ($memberAccounts) {
                        $memberAccounts->title = $name;
                        $memberAccounts->account_no = $account;
                        if ($memberAccounts->save()) {
                            $application = Applications::find()->where(['member_id' => $member->id])->orderBy(['id' => SORT_DESC,])->select(['id'])->one()->toArray();
                            if ($application) {
                                $loan = Loans::find()->where(['application_id' => $application['id']])->one();
                                if ($loan) {
                                    $tranch = LoanTranches::find()->where(['loan_id' => $loan['id']])->select(['id'])->one()->toArray();
                                    $disbursement = DisbursementDetails::find()->where(['tranche_id' => $tranch['id']])->one();
                                    if ($disbursement) {
                                        $disbursement->account_no = $account;
                                        $disbursement->save();
                                    }
                                }
                            }

                        }
                    }
                }
            }

        }
        return $this->render('accounts-index');
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = array();
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }


    public function actionKppLedgerGenerate()
    {
        if ($_POST) {
            if (!empty($_FILES['ledger_file'])) {
                $dispatchData = $this->csvToArray($_FILES['ledger_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanctions = $data['sanction no'];
                    $loan = Loans::find()->where(['sanction_no' => $sanctions])->one();
                    $schedules = KamyabPakistanHelper::KppHousingLedger($loan);
                    if (!empty($schedules)) {
                        foreach ($schedules as $schedule) {
                            $modelSchedules = new Schedules();
                            $modelSchedules->application_id = $loan->application_id;
                            $modelSchedules->loan_id = $loan->id;
                            $modelSchedules->branch_id = $loan->branch_id;
                            $modelSchedules->due_date = strtotime($schedule['due_date']);
                            $modelSchedules->schdl_amnt = $schedule['p_baseRent'];
                            $modelSchedules->charges_schdl_amount = $schedule['p_fixedRent'];
                            $modelSchedules->due_amnt = $schedule['due_amount'];
                            if ($modelSchedules->save()) {

                            } else {
                                var_dump($modelSchedules->getErrors());
                                die();
                            }
                        }
                    }


                }


            }
        }
        return $this->render('ledger-index');
    }

    public function actionUpdateDonationRecoveryDate()
    {
        if ($_POST) {
            if (!empty($_FILES['donation_file'])) {
                $dispatchData = $this->csvToArray($_FILES['donation_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $receipt = $data['receipt'];
                    $sanction = $data['sanction'];
                    $date = $data['date'];

                    $loan = Loans::find()->where(['sanction_no' => $sanction])->one();
                    if (!empty($loan) && $loan != null) {
                        $recovery = Recoveries::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['receipt_no' => $receipt])
                            ->one();
                        if (!empty($recovery) && $recovery != null) {
                            $recovery->receive_date = strtotime($date);
                            if ($recovery->save(false)) {
                                $donation = Donations::find()
                                    ->where(['loan_id' => $loan->id])
                                    ->andWhere(['receipt_no' => $receipt])
                                    ->one();
                                if (!empty($donation) && $donation != null) {
                                    $donation->receive_date = strtotime($date);
                                    $donation->save(false);
                                }
                            }
                        } else {
                            $donation = Donations::find()
                                ->where(['loan_id' => $loan->id])
                                ->andWhere(['receipt_no' => $receipt])
                                ->one();
                            if (!empty($donation) && $donation != null) {
                                $donation->receive_date = strtotime($date);
                                $donation->save(false);
                            }
                        }
                    }

                }
            }
        }
        return $this->render('donation-recovery-index');
    }

    public function actionAddActivities()
    {
        if ($_POST) {
            if (!empty($_FILES['activity_file'])) {
                $dispatchData = $this->csvToArray($_FILES['activity_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $product = Products::find()->where(['id' => $data['product']])->one();
                    if ($product) {
                        $activity = preg_replace('/[^A-Za-z0-9\-]/', '', $data['activity']);
                        $existingActivity = Activities::find()->where(['name' => $activity])->one();
                        if (!empty($existingActivity) && $existingActivity != null) {
                            $existingActivity->name = trim($data['activity']);
                            if ($existingActivity->save()) {

                            } else {
                                var_dump($existingActivity->getErrors());
                                die();
                            }
                        } else {
                            $model = new Activities();
                            $model->product_id = $product->id;
                            $model->name = trim($data['activity']);
                            $model->status = 1;
                            if ($model->save()) {
                                $modelPAM = new ProductActivityMapping();
                                $modelPAM->product_id = $product->id;
                                $modelPAM->activity_id = $model->id;
                                if ($modelPAM->save()) {

                                } else {
                                    var_dump($modelPAM->getErrors());
                                    die();
                                }
                            } else {
                                var_dump($model->getErrors());
                                die();
                            }
                        }
                    }
                }
            }
        }
        return $this->render('activity-mapping-index');
    }


    public function actionKppLedgerReGenerate()
    {
        if ($_POST) {
            if (!empty($_FILES['ledger_file'])) {
                $dispatchData = $this->csvToArray($_FILES['ledger_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanctions = $data['sanction no'];
                    $loan = Loans::find()->where(['sanction_no' => $sanctions])->one();
                    if (!empty($loan->project_id) && $loan->project_id != null && $loan->project_id == 77) {
                        $controller = new FixesController(Yii::$app->controller->id, Yii::$app);
                        $controller->actionLedgerReGeneratesKppSingle($loan->id);
                    }
                }
            }
        }
        return $this->render('re-ledger-index');
    }

    public function actionDueLoans()
    {
        if ($_POST) {
            if (!empty($_FILES['branches'])) {
                $receipt = strtotime($_POST['receipt']);
                $due_date = strtotime($_POST['due_date']);
                $disburse_date = strtotime($_POST['disb_date']);

                $dispatchbranches = $this->csvToArray($_FILES['branches']['tmp_name']);
                $connection = Yii::$app->db;

                $branch_Array = [];
                foreach ($dispatchbranches as $key=>$branch){
                    $branch = $branch['branch_code'];
                    $branch_Array[$key] = "'$branch'";
                }
                $data_Array = implode(",", $branch_Array);

                $due_report = "
                    select
                    ln.id 'id',
                    ln.id 'member_sync_id',
                    m.full_name,
                    m.parentage,
                    m.cnic,
                    mphon.phone,
                    ln.project_id,
                    '1234' as 'otp',
                    app.application_no,
                    grp.grp_no,
                    DATE_FORMAT(FROM_UNIXTIME(ln.date_disbursed), '%Y-%m-%d') 'date_disbursed',
                    null as 'date_etl',
                    br.code 'branch_code',
                    if(ln.loan_completed_date>0,1,0) 'is_completed',
                    ln.sanction_no,
                    '1' as 'recovery_pending',
                    ln.region_id,
                    ln.branch_id,
                    ln.area_id,
                    if(tm.name='Team1',1,2) 'team',
                    ln.loan_amount,
                    ln.disbursed_amount,
                    ln.inst_amnt,
                    @recovery:=(select COALESCE(sum(amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date < $receipt) 'recovery',
                    @balance:=(ln.disbursed_amount-@recovery) as 'olp',
                    @due_amount:=((select COALESCE(sum(schdl_amnt),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@recovery) 'temp_due',
                    @monthly_due:=if(@due_amount<=0,(if(@balance>sch.schdl_amnt, sch.schdl_amnt, @balance)),(if(@due_amount<sch.schdl_amnt , if(@due_amount=@balance,@due_amount,sch.schdl_amnt), @due_amount))) 'monthly_due',
                    if (@due_amount=0,sch.schdl_amnt,@monthly_due) 'due_amount'
                    from
                    loans ln
                    inner join applications app
                    on ln.application_id = app.id and app.deleted=0 and app.status='approved'
                    inner join members m
                    on app.member_id = m.id
                    left join members_phone mphon
                    on mphon.member_id = m.id and mphon.is_current and LENGTH(mphon.phone) >= 11
                    inner join groups grp
                    on ln.group_id = grp.id
                    inner join branches br
                    on ln.branch_id = br.id
                    left join teams tm
                    on ln.team_id = tm.id
                    inner join schedules sch
                    on sch.loan_id = ln.id
                    where
                    ln.status in ('collected','loan completed')
                    and
                    ln.date_disbursed <= $disburse_date
                    AND br.code in ($data_Array)
                    group by ln.sanction_no
                    having olp>0 ";

                $due_report = $connection->createCommand($due_report)->queryAll();
                $headers = array('loan id', 'member_sync_id', 'full_name', 'parentage', 'cnic', 'phone', 'project_id', 'otp', 'application_no', 'grp_no', 'date_disbursed', 'date_etl', 'branch_code', 'is_completed','sanction_no', 'recovery_pending' , 'region_id', 'branch_id', 'area_id', 'team', 'loan_amount', 'disbursed_amount', 'inst_amnt','recovery','olp','temp_due','due_amount','monthly_due');
                ExportHelper::ExportCSV('DueList-Report-' . 'due-loans-.csv', $headers, $due_report, '');
                die();
            }
        }
        return $this->render('due-index');
    }

    public function actionDueCronJob(){
        $controller = new \console\controllers\DynamicReportsController(Yii::$app->controller->id, Yii::$app);
        $controller->actionExportDueList();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddTranche()
    {
        if ($_POST) {
            if (!empty($_FILES['sanctions_file'])) {
                $dispatchData = $this->csvToArray($_FILES['sanctions_file']['tmp_name']);
                foreach ($dispatchData as $data) {

                    $sanction = $data['sanction no'];
                    $tranch_no = $data['tranch no'];
                    $tranch_amount = $data['tranch amount'];

                    $model = Loans::find()->where(['sanction_no' => $sanction])->one();
                    $transaction = Yii::$app->db->beginTransaction();

                    if (!empty($model) && $model != null) {
                        $tranch_model = new LoanTranches();
                        $tranch_model->loan_id = $model->id;
                        $tranch_model->tranch_no = $tranch_no;
                        $tranch_model->tranch_amount = $tranch_amount;
                        $tranch_model->tranch_charges_amount = 0;
                        $tranch_model->status = 0;
                        $tranch_model->platform = isset($model->platform) ? $model->platform : 1;

                        if ($tranch_model->save(false)) {
                            ActionsHelper::insertActions('loan_tranches', $model->project_id, $tranch_model->id, $model->created_by);
                        } else {
                            $transaction->rollBack();
                            return $tranch_model->getErrors();
                        }

                    }

                    $model->loan_amount += $tranch_amount;
                    if ($model->save(false)) {
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                        var_dump($model->getErrors());
                        die();
                    }
                }
            }
        }
//        if ($_POST) {
//            if (!empty($_FILES['sanctions_file'])) {
//                $file_open = $_FILES['sanctions_file']['tmp_name'];
//                $myfile = fopen($file_open, "r");
//                $flag = false;
//                $i = 0;
//                while (($fileop = fgetcsv($myfile)) !== false) {
//                    if ($flag) {
//
//                    }
//                    $flag = true;
//                    $i++;
//                }
//
//            }
//        }
        return $this->render('tranche-index');
    }


    public function actionFixesIndex()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request->post();
        if ($request) {
            if (!empty($_FILES['accounts_file'])) {
                $dispatchData = $this->csvToArray($_FILES['accounts_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $model = Loans::find()->where(['sanction_no' => $sanction])->one();
                    if (!empty($model) && $model != null) {
                        FixesHelper::fix_schedules_update($model);
                    } else {
                        $session->addFlash('error', 'Sanction no not found!');
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }
            }
        }
        return $this->render('fixes-index');

    }

    public function actionFixesHousingIndex()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request->post();
        if ($request) {
            if (!empty($_FILES['accounts_file'])) {
                $dispatchData = $this->csvToArray($_FILES['accounts_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $model = Loans::find()->where(['sanction_no' => $sanction])->one();
                    if (!empty($model) && $model != null) {
                        $disbursed_amount = $model->disbursed_amount;
                        $model->loan_amount = $disbursed_amount;
                        if($model->save()){
                            $lt = LoanTranches::find()->where(['loan_id'=>$model->id])->all();
                            $tr_charges_sum = 0;
                            if(!empty($lt) && $lt!=null){
                                foreach ($lt as $ltd){
                                    if($ltd->status !=6){
                                        \Yii::$app
                                            ->db
                                            ->createCommand()
                                            ->delete('loan_tranches', ['id' => $ltd->id])
                                            ->execute();
                                    }else{
                                        $tr_charges_sum += $ltd->tranch_charges_amount;
                                    }
                                }

                            }
                            $model->service_charges = $tr_charges_sum;
                            if($model->save()){
                                FixesHelper::ledger_regenerate($model);
                            }else{
                                var_dump($model->getErrors());
                                die();
                            }
                        }

                        $loanSchedules = Schedules::find()->where(['loan_id' => $model->id])->all();

                        if (!empty($loanSchedules) && $loanSchedules != null) {

                            foreach ($loanSchedules as $key => $schedules) {

                                $recoveries = Recoveries::find()->where(['schedule_id' => $schedules->id])->andWhere(['loan_id' => $model->id])->all();

                                if (!empty($recoveries) && $recoveries != null) {

                                    foreach ($recoveries as $recovery) {

                                        if ($recovery->charges_amount > 0) {

                                            $baseAmount = $recovery->amount + $recovery->charges_amount - ($schedules->charges_schdl_amount);
                                            $recovery->amount = $baseAmount;
                                            $recovery->charges_amount = $schedules->charges_schdl_amount;
                                            if ($recovery->save(false)) {
                                            } else {
                                                echo 'error';
                                                print_r($recovery->getErrors());
                                                die();
                                            }

                                        }
                                    }
                                }

                            }

                        }
                    } else {
                        $session->addFlash('error', 'Sanction no not found!');
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }
            }
        }
        return $this->render('fixes-housing-index');

    }

    public function actionMemberDocs()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        if (Yii::$app->request->post()) {
            $file_name_zip = 'nadra_document.zip';
            $tmp_zip_name = uniqid() . '.zip';
            $zip = new \ZipArchive();
            chdir(ImageHelper::getAttachmentPath() . '/uploads/doc_zip/');

            if (!empty($_FILES['cnic_file'])) {
                $dispatchData = $this->csvToArray($_FILES['cnic_file']['tmp_name']);

                foreach ($dispatchData as $data) {
                    $model = Members::find()->where(['cnic' => $data])->one();
                    $image = Images::findOne(['parent_id' => $model->id, 'parent_type' => 'members', 'image_type' => 'nadra_document']);
                    if(!empty($image) && $image!=null){
                        $attachment_path = ImageHelper::getAttachmentPath() . '/uploads/members/' . $model->id . '/' . $image->image_name;

                        if (file_exists($attachment_path)) {
                            if ($zip->open($tmp_zip_name, \ZipArchive::CREATE) === TRUE) {
                                $zip->addFile($attachment_path, $image->image_name);
                                $ret = $zip->close();
                            }
                        }
                    }
                }

                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename=' . $file_name_zip);
                header('Content-Length: ' . filesize($tmp_zip_name));
                readfile($tmp_zip_name);
                unlink($tmp_zip_name);
            }
            die();
        }

        return $this->render('doc-index');

    }

    public function actionCibGenerate()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        $session = Yii::$app->session;
        $request = Yii::$app->request->post();
        if ($request) {
            if (!empty($_FILES['cib_file'])) {
                $dispatchData = $this->csvToArray($_FILES['cib_file']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $modelLoan = Loans::find()->where(['sanction_no' => $sanction])->one();
                    if (!empty($modelLoan) && $modelLoan != null) {
                        $modelApp = Applications::find()->where(['id' => $modelLoan->application_id])->one();
                        if(!empty($modelApp) && $modelApp!=null){
                            $modelApp = ApplicationsCib::find()->where(['application_id' => $modelLoan->application_id])->one();
                            if(empty($modelApp) && $modelApp==null){
                                $modelOperation = Operations::find()->where(['application_id'=>$modelLoan->application_id])
                                    ->andWhere(['operation_type_id'=>3])
                                    ->select(['receipt_no','credit'])
                                    ->one();
                                if(!empty($modelOperation) && $modelOperation!=null){
                                    $receipt = $modelOperation->receipt_no;

                                    $modelCib = new ApplicationsCib();
                                    $modelCib->application_id = $modelLoan->application_id;
                                    $modelCib->cib_type_id = 1;
                                    $modelCib->fee = $modelOperation->credit;
                                    $modelCib->receipt_no = "$receipt";
                                    $modelCib->status = 3;
                                    $modelCib->type = 1;
                                    $modelCib->transfered = 2;
                                    $modelCib->created_by = 1;
                                    if(!$modelCib->save()){
                                        print_r($modelCib->getErrors());
                                        die('1');
                                    }

                                }else{
                                    $receipt = 'qwe'.$modelLoan->application_id;
                                    $modelCib = new ApplicationsCib();
                                    $modelCib->application_id = $modelLoan->application_id;
                                    $modelCib->cib_type_id = 1;
                                    $modelCib->fee = 14;
                                    $modelCib->receipt_no = "$receipt";
                                    $modelCib->status = 3;
                                    $modelCib->type = 1;
                                    $modelCib->transfered = 2;
                                    $modelCib->created_by = 1;
                                    if(!$modelCib->save()){
                                        print_r($modelCib->getErrors());
                                        die('2');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->render('cib-index');

    }

    public function actionTeamUpdateIndex()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request->post();
        if ($request) {
            if (!empty($_FILES['sanctions'])) {
                $dispatchData = $this->csvToArray($_FILES['sanctions']['tmp_name']);
                foreach ($dispatchData as $data) {
                    $sanction = $data['sanction_no'];
                    $model = Loans::find()->where(['sanction_no' => $sanction])->one();
                    if (!empty($model) && $model != null) {
                        $team = Teams::find()->where(['name'=>$request['team']])->andWhere(['branch_id'=>$model->branch_id])->one();
                        $field = Fields::find()->where(['name'=>$request['field']])->andWhere(['team_id'=>$team->id])->one();

                        $model->team_id = $team->id;
                        $model->field_id = $field->id;
                        if($model->save()){
                            $application = Applications::find()->where(['id'=>$model->application_id])->one();
                            if(!empty($application) && $application!=null){
                                $application->team_id = $team->id;
                                $application->field_id = $field->id;
                                if($application->save()){
                                }else{
                                    var_dump($application->getErrors());
                                    die();
                                }
                            }
                        }else{
                            var_dump($application->getErrors());
                            die();
                        }
                    } else {
                        $session->addFlash('error', 'Sanction no not found!');
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }
            }
        }
        return $this->render('team-update-index');

    }


}
