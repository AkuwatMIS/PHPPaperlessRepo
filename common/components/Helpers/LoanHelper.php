<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 5/30/2018
 * Time: 3:24 PM
 */

namespace common\components\Helpers;


use common\components\Parsers\ApiParser;
use common\models\Activities;
use common\models\ApplicationDetails;
use common\models\Applications;
use common\models\Branches;
use common\models\Groups;
use common\models\Lists;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Members;
use common\models\NadraVerisys;
use common\models\Operations;
use common\models\Projects;
use common\models\search\DuelistSearch;
use common\models\search\OverduelistSearch;
use common\models\search\PortfolioSearch;
use common\models\Visits;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use Yii;

class LoanHelper
{
    public static function getInfoByLoan($id)
    {
        $loan = Loans::find()->where(['loans.id' => $id/*,'loans.status'=>'processed'*/])->joinWith('application')->joinWith('application.member')->one();
        $application = ApiParser::parseApplication($loan['application']);
        $member = ApiParser::parseMember($loan['application']['member']);
        $loan_data = ApiParser::parseLoan($loan);
        $application = array_merge($application, ['loan' => $loan_data]);
        $data = array_merge($member, ['application' => $application]);
        return $data;
    }

    public static function getDisbursedInfoByLoan($id)
    {
        $loan = Loans::find()->where(['loans.id' => $id, 'loans.status' => 'collected'])->joinWith('application')->joinWith('application.member')->one();
        $application = [];
        $member = ApiParser::parseMemberBasic($loan['application']['member']);
        $loan_data = ApiParser::parseLoanBasic($loan);
        $application = array_merge($application, ['loan' => $loan_data]);
        $list_data = array_merge($member, ['application' => $application]);
        $details = DisbursementHelper::getDisbursementDetails($loan);
        $data = array_merge($list_data, ['disbursement_details' => $details]);
        return $data;
    }

    public static function getInfoByApplication($id)
    {
        $loan = Loans::find()->where(['loans.status' => 'processed', 'loans.application_id' => $id])->joinWith('application')->joinWith('application.member')->one();
        $application = ApiParser::parseApplication($loan['application']);
        $member = ApiParser::parseMember($loan['application']['member']);
        $loan_data = ApiParser::parseLoan($loan);
        $application = array_merge($application, ['loan' => $loan_data]);
        $data = array_merge($member, ['application' => $application]);
        return $data;
    }

    static public function getInstType()
    {
        return array('annually' => 'Annually', 'semi_annually' => 'Semi-Annually', 'quarterly' => 'Quarterly', 'monthly' => 'Monthly',
            'fortnightly' => 'Fortnightly', 'weekly' => 'Weekly', 'daily' => 'Daily', 'maturity' => 'Maturity');
    }

    public static function getInstallmentTypes()
    {
        $installments_types = ArrayHelper::map(Lists::find()->where(['list_name' => 'installments_types'])->all(), 'value', 'label');
        return $installments_types;
    }

    public static function getDisbursementStatus()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'disbursement_status'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriod()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getEhssasNujawan()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_eshssan_nujawan'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getAkhuwatEbm()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'akhuwat_ebm'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getApnichatapnaghar()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_apni_chat'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodLchs()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_aklchs'])->all(), 'value', 'label');
        return $disbursement_status;

    }
    public static function getLoanPeriodusaLchs()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_usalchs'])->all(), 'value', 'label');
        return $disbursement_status;

    }


    public static function getLoanPeriodPsa()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_psa'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodkamyab()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_kamyab'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodPmy()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'pm_youth_list'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodAkm()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_akm'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodRama()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_rama'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodkpkarobar()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'kp_kamyab_karobar'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getLoanPeriodScooty()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'electric_bike_financing'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getLoanPeriodppaf()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_ppaf'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getLoanPeriodAlflah()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'loan_period_alflah'])->all(), 'value', 'label');
        return $disbursement_status;
    }
    public static function getLoanPeriodpmiflFB()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'pmifl_for_business'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodPq()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'port_qasim'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    public static function getLoanPeriodMusharqa()
    {
        $disbursement_status = ArrayHelper::map(Lists::find()->where(['list_name' => 'akhuwat_musharqa'])->all(), 'value', 'label');
        return $disbursement_status;
    }

    static function DisbursmentSummary($params)
    {

        $cond = '';
        $cnd = '';
        $group_by_cond = 'loans.region_id';
        $columns_name = 'loans.id, loans.region_id as region_name,';
        if (!empty($params['LoansSearch']['date_disbursed'])) {

            //$date = explode(' - ', $params['LoansSearch']['date_disbursed']);
            $cond .= " && t.date_disbursed = '" . strtotime($params['LoansSearch']['date_disbursed']) . "'";
            $cnd .= " && loans.date_disbursed = '" . strtotime($params['LoansSearch']['date_disbursed']) . "'";
            //$cond .= " && date_disbursed between '".strtotime($date[0])."' and '".strtotime($date[1])."'";

        }
        if (!empty($params['LoansSearch']['region_id'])) {
            $cond .= " && loans.region_id = '" . $params['LoansSearch']['region_id'] . "'";
            $cnd .= " && loans.region_id = '" . $params['LoansSearch']['region_id'] . "'";
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'], $params['method'], $params['rbac_type']);
            $cnd .= Yii::$app->Permission->searchReportsFilters($params['controller'], $params['method'], $params['rbac_type']);
            if (isset($params['LoansSearch']['area_id']) && !empty($params['LoansSearch']['area_id'])) {
                $group_by_cond = 'loans.branch_id';
                $columns_name = 'loans.id, loans.region_id as region_name, loans.area_id as area_name, loans.branch_id as branch_name,';
                //$columns_name = 'loans.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            } else {
                $group_by_cond = 'loans.area_id';
                $columns_name = 'loans.id, loans.region_id as region_name, loans.area_id as area_name,';
                //$columns_name = 'loans.id, regions.name as region_name, areas.name as area_name,';
            }
        }
        if (isset($params['LoansSearch']['area_id']) && !empty($params['LoansSearch']['area_id'])) {
            $cond .= " && loans.area_id = '" . $params['LoansSearch']['area_id'] . "'";
            $cnd .= " && loans.area_id = '" . $params['LoansSearch']['area_id'] . "'";
        }
        if (isset($params['LoansSearch']['branch_id']) && !empty($params['LoansSearch']['branch_id'])) {
            $cond .= " && loans.branch_id = '" . $params['LoansSearch']['branch_id'] . "'";
            $cnd .= " && loans.branch_id = '" . $params['LoansSearch']['branch_id'] . "'";
        }
        if (!empty($params['LoansSearch']['project_ids'])) {
            $project_ids = '';
            foreach ($params['LoansSearch']['project_ids'] as $p) {
                $project_ids .= $p . ',';
            }
            $cond .= " && loans.project_id in (" . trim($project_ids, ',') . ")";
            $cnd .= " && loans.project_id in (" . trim($project_ids, ',') . ")";
        }
        /*if(!empty($params['LoansSearch']['crop_type'])){
            $cond .= " && borrowers.cropType = '".$params['LoansSearch']['crop_type']."'";
        }*/
        if (empty($params['LoansSearch']['region_id'])) {
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'], $params['method'], $params['rbac_type']);
        }
        $sql = "SELECT " . $columns_name . " (select COALESCE(count(loans.id),0) from loans where loans.deleted = 0  AND  loans.status not in ('not collected','pending') " . $cnd . ") as no_of_loans, COALESCE(sum(t.tranch_amount),0) as loan_amount from loan_tranches t  INNER JOIN loans on loans.id = t.loan_id
               
                where 1 and loans.status not in ('not collected','pending') and loans.deleted=0 " . $cond . " group by " . $group_by_cond . " ";
        /*$sql = "SELECT ".$columns_name." COALESCE(count(loans.id),0) as no_of_loans, COALESCE(sum(loan_amount),0) as loan_amount from loans
                inner join branches on branches.id = loans.branch_id
                inner join areas on areas.id = loans.area_id
                inner join regions on regions.id = loans.region_id
                where 1 and loans.status not in ('not collected','pending') and loans.deleted=0 ".$cond." group by ".$group_by_cond." ";*/

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

    static public function setSanctionNo($loan)
    {
        $branch = Branches::find()->where(['id' => $loan->branch_id, 'deleted' => 0])->one();
        $project = Projects::find()->where(['id' => $loan->project_id, 'deleted' => 0])->one();
        $last_loan = Loans::find()->where(['branch_id' => $branch->id, 'deleted' => 0])->andWhere(['like', 'sanction_no', '-' . $project->funding_line . '-'])->orderBy('br_serial desc')->one();

        $new_br_serial = (isset($last_loan->br_serial) ? (int)$last_loan->br_serial : 0) + 1;

        $sanction_no = $branch->code . '-' . $project->funding_line . '-' . str_pad($new_br_serial, 5, '0', STR_PAD_LEFT);

        $loan->br_serial = $new_br_serial;
        $loan->sanction_no = $sanction_no;
        return $loan;
    }

    static public function createLoan($loan)
    {
        $loan->setLoaninfo();
        $application = \common\models\Applications::find()->where(['id' => $loan->application_id])->one();
        $dob = $application->member->dob;
        $tdate = time();
        $age = date('Y', $tdate) - date('Y', $dob);
        $loan->age = $age;
        if ($loan->save()) {
            return $loan;
        } else {
            return $loan;
        }
    }

    static public function getTeams()
    {
        return array('Team-1' => 'Team-1', 'Team-2' => 'Team-2', 'Team-3' => 'Team-3');
    }

    static public function getDsbStatus()
    {
        return array('pending' => 'Pending', 'processed' => 'Processed', 'collected' => 'Collected', 'approved' => 'Approved', 'not collected' => 'Not Collected', 'loan completed' => 'Loan Completed');
    }

    static public function getStatus()
    {
        return array('collected' => 'Collected', 'not collected' => 'Not Collected');
    }

    static public function getAttendanceStatus()
    {
        return array('present' => 'Present', 'absent' => 'Absent');
    }

    static public function getTranchStatus()
    {
        return array(6 => 'Collected', 7 => 'Not Collected');
    }

    static public function getProjectCodeFromId($id)
    {
        $project = Projects::find()->where(['id' => $id])->one();
        return $project->code;
    }

    static public function getProjectChargesFromId($id)
    {
        $project = Projects::find()->where(['id' => $id])->one();
        return $project->charges_percent;
    }

    static public function getActivityNameFromId($id)
    {
        $activity = Activities::find()->where(['id' => $id])->one();
        return isset($activity->name) ? $activity->name : '';
    }

    static public function getLoanDetails($loan)
    {
        $loan_details[] = ApiParser::parseLoanBasic($loan);
        $tranches['tranches'] = ApiParser::parseLoanTranches($loan->tranches);
        $loan_details = array_merge($loan_details, $tranches);
        return $loan_details;

    }

    static public function saveTranches($model, $tranches)
    {
        foreach ($tranches as $tranch) {
            $tranch_model = new LoanTranches();
            $tranch_model->platform = isset($model->platform) ? $model->platform : 1;
            $tranch_model->attributes = $tranch;
            $tranch_model->loan_id = $model->id;
            if (!$tranch_model->save()) {
                return $tranch_model->getErrors();
            } else {
                ActionsHelper::insertActions('loan_tranches', $model->project_id, $tranch_model->id, $model->created_by);
            }
        }

        return true;
    }

    static public function getCompletionPercentage($id)
    {
        $percent = '';
        $visit = Visits::find()->where(['parent_id' => $id, 'parent_type' => 'application'])->orderBy('created_at desc')->one();
        if (isset($visit)) {
            $percent = $visit->percent;
        }

        return $percent;
    }

    static public function getIsShifted($id)
    {
        $is_shifted = 0;
        $visit = Visits::find()->where(['parent_id' => $id, 'parent_type' => 'application'])->orderBy('created_at desc')->one();
        if (isset($visit)) {
            $is_shifted = $visit->is_shifted;
        }

        return $is_shifted;
    }

    static public function absentLoan($tranch)
    {
        //tranch update
        $tranch->status = 3;
        $tranch->fund_request_id = 0;
        $tranch->cheque_no = '';
        $tranch->save();
        //loan actions//
        $loan_action = LoanActions::find()->where(['parent_id' => $tranch->loan_id, 'action' => 'takaful'])->one();
        if (!empty($loan_action)) {
            $loan_action->status = 0;
            $loan_action->save();
        }
        ///tranche actions//
        $loan_tr_actions = LoanTranchesActions::find()->where(['parent_id' => $tranch->id])->andWhere(['in', 'action', 'cheque_printing', 'fund_request', 'disbursement'])->all();
        foreach ($loan_tr_actions as $tranche_ac) {
            $tranche_ac->status = 0;
            $tranche_ac->save();
        }
        //remove takaful entry
        $takaf = Operations::find()->where(['loan_id' => $tranch->loan_id, 'operation_type_id' => 2])->one();
        if (!empty($takaf)) {
            $takaf->deleted = 1;
            $takaf->save();
        }
        return $tranch;
    }

    public static function updateLoanVerifyAcction($model)
    {
        if (in_array($model->project_id, StructureHelper::accountVerifyProjects())) {
            $account_model = $model->application->member->memberAccount;
            if (!empty($account_model) && $account_model->status == 1) {
                ActionsHelper::updateAction('loan', $model->id, 'account_verification');
            }
        }
    }

    public static function VerifyNadraVerysis($model)
    {
        $result = true;
         if ($model->application->application_date >= 1643673600) {
             $account_model = $model->application->member->nadraDoc;

             if ($model->application->created_at >= 1678534349) {
                 $nadraModel = NadraVerisys::find()->where(['application_id' => $model->application->id])
                     ->andWhere(['member_id' => $model->application->member->id])
                     ->andWhere(['status' => 1])
                     ->andWhere(['deleted' => 0])
                     ->one();

                 if (!empty($nadraModel) && $nadraModel != null) {
                     $result = true;
                 } else {
                     if(!empty($account_model) && $account_model!=null){
                         $result = true;
                     }else{
                         $result = false;
                     }
                 }
             } else {
                 if (!empty($account_model) && $account_model != null) {
                     $result = true;
                 } else {
                     $result = false;
                 }
             }
         } else {
             if (in_array($model->project_id, StructureHelper::kamyaabPakitanProjects())) {
                 $account_model = $model->application->member->nadraDoc;
                 if (!empty($account_model) && $account_model != null) {
                     $result = true;
                 } else {
                     $result = false;
                 }
             }
         }
        return $result;
    }

    public static function VerifyNadraVerysisTakaf($model)
    {
        $result = true;
        if ($model->application->application_date >= 1643673600) {
             $nadraModel = NadraVerisys::find()->where(['application_id' => $model->application->id])
                 ->andWhere(['member_id' => $model->application->member->id])
                 ->andWhere(['status' => 1])
                 ->andWhere(['deleted' => 0])
                 ->one();

             if (!empty($nadraModel) && $nadraModel != null) {
                 $result = true;
             } else {
                 $result = false;
             }
        } else {
            if (in_array($model->project_id, StructureHelper::kamyaabPakitanProjects())) {
                $account_model = $model->application->member->nadraDoc;
                 if (!empty($account_model) && $account_model != null) {
                     $result = true;
                 } else {
                     $result = false;
                 }
            }
        }
        return $result;
    }

    public static function PmtVerify($model)
    {
        $pmtVerify = ApplicationDetails::find()
            ->where(['parent_type' => 'member'])
            ->andWhere(['application_id' => $model->id])
            ->andWhere(['parent_id' => $model->member_id])
            ->andWhere(['status' => 1])
            ->one();
        return (!empty($pmtVerify) && $pmtVerify != null) ? $pmtVerify->poverty_score : 0;
    }

    public static function takafulOlp($model)
    {
        $connection = \Yii::$app->db;
        $schedule_amount = "select disbursed_amount as 'disbursed_amount' from loans where id = '" . $model . "'";
        $recovery_amount = "select COALESCE(sum(amount),0) as total_recover_amount from recoveries where loan_id = '" . $model . "'";
        $total_schedule_amount = $connection->createCommand($schedule_amount)->queryAll();
        $total_recovery_amount = $connection->createCommand($recovery_amount)->queryAll();
        $schedule = $total_schedule_amount[0]['disbursed_amount'];
        $recover = $total_recovery_amount[0]['total_recover_amount'];
        $olp = $schedule - $recover;
        return (!empty($olp) && $olp > 0) ? $olp : 0;
    }
}