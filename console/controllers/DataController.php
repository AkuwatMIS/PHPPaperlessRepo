<?php
namespace console\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\SchedulesHelper;
use common\models\Actions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Branches;
use common\models\Donations;
use common\models\DynamicReports;
use common\models\GroupActions;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\ProgressReports;
use common\models\Schedules;
use common\models\Users;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;
use function GuzzleHttp\Promise\all;


class DataController extends Controller
{

    public function actionSetLoanTranche()
    {
        $status = ['pending' => 3,'approved' => 4,'processed' => 5,'collected' => 6,'not collected' => 7,'loan completed' => 8];
        $loans = Loans::find()->all();
        foreach ($loans as $loan)
        {
           $tranche_model = LoanTranches::find()->where(['loan_id' => $loan->id])->one();
           if(!isset($tranche_model))
           {
               $tranche_model = new LoanTranches();
               $tranche_model->loan_id = $loan->id;
               $tranche_model->tranch_no = 1;
           }

           $tranche_model->tranch_amount = $loan->loan_amount;
           $tranche_model->date_disbursed = $loan->date_disbursed;
           $tranche_model->disbursement_id = $loan->disbursement_id;
           $tranche_model->cheque_no = $loan->cheque_no;
           $tranche_model->cheque_date = $loan->cheque_dt;
           $tranche_model->fund_request_id = $loan->fund_request_id;
           $tranche_model->attendance_status = $loan->attendance_status;
           $tranche_model->deleted = $loan->deleted;
           $tranche_model->platform = $loan->platform;
           $tranche_model->created_by = $loan->created_by;
           $tranche_model->updated_by = $loan->updated_by;
           $tranche_model->created_at = $loan->created_at;
           $tranche_model->updated_at = $loan->updated_at;
           $tranche_model->status = $status[$loan->status];
           $tranche_model->save();

           ActionsHelper::insertActions('loan_tranches',$loan->project_id,$tranche_model->id,$loan->created_by);

           if($loan->disbursement_id > 0) {
               $loan->disbursed_amount = $loan->loan_amount;
           }
           $loan->save();
        }
    }

    public function actionFundRequestActionMove()
    {
        $group_actions = GroupActions::find()->where(['action' => 'fund_request','status' => 1])->all();
        foreach ($group_actions as $grp_action)
        {
            $loan_tranches = LoanTranches::find()->joinWith('loan')->where(['group_id' => $grp_action->parent_id])->all();
            foreach ($loan_tranches as $loan_tranche)
            {

                $action = LoanTranchesActions::find()->where(['action' => 'fund_request', 'parent_id' => $loan_tranche->id])->one();
                $action->status = $grp_action->status;
                $action->user_id = $grp_action->user_id;
                $action->expiry_date = $grp_action->expiry_date;
                $action->created_by = $grp_action->created_by;
                $action->updated_by = $grp_action->updated_by;
                $action->created_at = $grp_action->created_at;
                $action->updated_at = $grp_action->updated_at;
                $action->save();
            }
        }
    }

    public function actionLoansAcionsMove()
    {
        $loan_actions = LoanActions::find()->where(['status' => 1])->andWhere(['in','action',['cheque_printing','disbursement']])->all();
        foreach ($loan_actions as $loan_action)
        {
            $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan_action->parent_id])->one();

            $action = LoanTranchesActions::find()->where(['action' => $loan_action->action, 'parent_id' => $loan_tranche->id])->one();

            $action->status = $loan_action->status;
            $action->user_id = $loan_action->user_id;
            $action->expiry_date = $loan_action->expiry_date;
            $action->created_by = $loan_action->created_by;
            $action->updated_by = $loan_action->updated_by;
            $action->created_at = $loan_action->created_at;
            $action->updated_at = $loan_action->updated_at;
            $action->save();
        }
    }
    public function actionUpdateSchedules()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [15]])->all();
        foreach ($dynamic_reports as $report) {
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'schedule' . '/' . $report->uploaded_file;
            //$file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/' . $file_name;
            $myfile = fopen($file_path, "r");
            $flag = false;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $l = Loans::find()->where(['sanction_no' => $fileop[0]])->one();
                    if (!empty($l)) {
                        $schedules = Schedules::find()->where(['loan_id' => $l->id])->andWhere(['between', 'due_date', '1585699200', '1588291199'])->one();
                        if (!empty($schedules)) {
                            # update schedule amount/due amount to zero
                            $due_amount = 0;
                            $due_amount = $schedules->schdl_amnt;
                            $schedules->due_amnt = 0;
                            $schedules->schdl_amnt = 0;
                            $schedules->updated_by = 1;
                            $schedules->save();
                            # create new schedule with remaining amount at the end
                            SchedulesHelper::adjust_last_schedule($l, $due_amount);
                            # fix loan and schedules
                            FixesHelper::fix_schedules_update_deffer($l);
                            # update loan expiry
                            FixesHelper::update_loan_expiry($l);
                        }
                    }
                }
                $flag = true;
            }
            $report->status=1;
            $report->save();
        }
    }
}