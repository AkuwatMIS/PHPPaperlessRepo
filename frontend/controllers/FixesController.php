<?php

namespace frontend\controllers;


use common\components\Helpers\AcagHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\StructureHelper;
use common\models\Applications;
use common\models\DisbursementDetails;
use common\models\LedgerRegenerateLogs;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Members;
use common\models\MembersAccount;
use common\models\Recoveries;
use common\models\Schedules;
use Yii;
use common\models\search\LoansSearch;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\components\Helpers\ExportHelper;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;

/**
 * LoansController implements the CRUD actions for Loans model.
 */
class FixesController extends Controller
{
    public $rbac_type = 'frontend';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Loans models.
     * @return mixed
     */
    public function actionFixes($id = 0)
    {
        if ($id != 0) {
            $loan = Loans::findOne(['id' => $id]);
            if ($loan->status == 'grant') {
            } else {
                FixesHelper::fix_schedules_update($loan);
            }
            die();
        } else {
            $loan_ids = array(
                1315526, 1507265
            );
            foreach ($loan_ids as $loan_id) {
                $loan = Loans::findOne(['id' => $loan_id]);
                if ($loan->status == 'grant') {
                } else {
                    FixesHelper::fix_schedules_update($loan);
                }
            }
        }

    }

    public function actionLedgerGenerate($id)
    {
        $session = Yii::$app->session;
        $loan = Loans::find()->where(['id' => $id])->andWhere(['!=', 'status', 'not collected'])->one();
        if (isset($loan) && !empty($loan)) {
            if ($loan->status == 'grant') {
            } else {
                FixesHelper::ledger_regenerate($loan);
            }
            $session->addFlash('success', 'Ledger generated successfully!');
            return $this->redirect('/fixes/fixes-loans?id=' . $id);
        } else {
            throw new BadRequestHttpException('Ledger not generate against cancelled loan.');
            /* print_r('Ledger not generate against cancelled loan.');
             die();*/
        }
    }

    public function actionUpdateCheque($id)
    {
        $flag_ledger = false;
        $err = true;
        $request = Yii::$app->request->post();
        $model = LoanTranches::findOne(['id' => $request['LoanTranches']['id']]);
        $date = $model->date_disbursed;
        $model->load($request);
        $model->cheque_date = strtotime($model->cheque_date);
        if (isset($request['LoanTranches']['date_disbursed'])) {
            if (date('Y-m', strtotime($model->date_disbursed)) == date('Y-m') && date('Y-m') == date('Y-m', $date)) {
                $model->date_disbursed = strtotime($model->date_disbursed);
                $flag_ledger = true;
                if ($model->tranch_no == 1) {
                    $loan_model = Loans::findOne(['id' => $model->loan_id]);
                    $loan_model->date_disbursed = ($model->date_disbursed);
                    $loan_model->save();
                }
            } else {
                $err = false;
                $model->date_disbursed = $date;
                $model->addError('date_disbursed', 'Disbursement date can be changed only in current month');
            }
        }
        if ($err == true && $model->save()) {

            if ($flag_ledger) {
                $ledger_regenerate = new LedgerRegenerateLogs();
                $ledger_regenerate->loan_id = $model->loan_id;
                $ledger_regenerate->reason = 'Disbursement Date Update';
                $ledger_regenerate->save();
                //FixesHelper::ledger_regenerate($model);
            }
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
            $response['data']['date_disbursed'] = date('Y-m-d', $model->date_disbursed);
        } else {
            $response['status_type'] = "error";
            $response['errors'] = $model->getErrors();
            $response['data']['date_disbursed'] = date('Y-m-d', $model->date_disbursed);
        }
        return json_encode($response);

    }

    public function actionFixesLoans($id = 0)
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request->post();

        if ($request) {
            $model = Loans::find()->where(['sanction_no' => $request['Loans']['sanction_no']])->andWhere(['in', 'status', ['collected', 'loan completed']]);
            Yii::$app->Permission->getSearchFilterQuery($model, 'loans', 'index', $this->rbac_type);
            $model = $model->one();
            if (!empty($model)) {
                return $this->render('index', [
                    'model' => $model,
                ]);
            } else {
                $session->addFlash('error', 'Sanction no not found!');
                return $this->redirect('/fixes/fixes-loans');
            }
        }
        if ($id != 0) {
            $model = Loans::findOne(['id' => $id]);
            return $this->render('index', [
                'model' => $model,
            ]);
        } else {
            $model = new Loans();
            return $this->render('index', [
                'model' => $model,
            ]);
        }

    }

    public function actionUpdateLoan($id = 0)
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request->post();

        if ($request) {
            $model = Loans::find()->where(['sanction_no' => $request['Loans']['sanction_no']]);
//                ->andWhere(['in','status',['collected','pending','loan completed']]);
            Yii::$app->Permission->getSearchFilterQuery($model, 'loans', 'index', $this->rbac_type);
            $model = $model->one();
            if (!empty($model)) {
                return $this->render('update-loan', [
                    'model' => $model,
                ]);
            } else {
                $session->addFlash('error', 'Sanction no not found!');
                return $this->redirect('/fixes/update-loan');
            }
        }
        if ($id != 0) {
            $model = Loans::findOne(['id' => $id]);
            return $this->render('update-loan', [
                'model' => $model,
            ]);
        } else {
            $model = new Loans();
            return $this->render('update-loan', [
                'model' => $model,
            ]);
        }

    }


    public function actionRejectLoan($id)
    {
        $request = Yii::$app->request->post();
        $session = Yii::$app->session;
        $loan = Loans::find()->where(['id' => $id])->andWhere(['in', 'status', ['pending', 'collected', 'loan completed', 'not collected', 'grant']])->one();
        if (isset($loan) && !empty($loan)) {
            if (empty($loan->recovery)) {
                if (isset($request['Loans'])) {
                    if ($request['Loans']['status'] == 'permanent_reject') {
                        if (in_array($loan->project_id, StructureHelper::trancheProjectsReject())) {
                            $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no asc')->all();
                            if (count($tranch_reject) > 1) {
                                $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                                if ($tranche_reject[0]->status > 0) {
                                    $tranche_reject[0]->status = 9;
                                    $tranche_reject[0]->date_disbursed = 0;
                                    $tranche_reject[0]->disbursement_id = 0;
                                    if ($tranche_reject[0]->save(false)) {
                                        $loan->disbursed_amount = $tranche_reject[1]->tranch_amount;
                                        $loan->date_disbursed = $tranche_reject[1]->date_disbursed;
                                        $loan->disbursement_id = $tranche_reject[1]->disbursement_id;
                                        if ($loan->save(false)) {
                                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranche_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                            foreach ($disbursement_details as $d) {
                                                $d->status = 2;
                                                $d->updated_by = 1;
                                                if (!$d->save(false)) {
                                                    print_r($d->getErrors());
                                                    die();
                                                }
                                            }
                                            if ($loan->status != 'grant') {

                                                FixesHelper::ledger_regenerate($loan);
                                            }
                                            $loan->status = 'rejected';
                                            $loan->reject_reason = $request['Loans']['reject_reason'];
                                            $loan->save(false);

                                            if ($loan->project_id == 132) {
                                                AcagHelper::actionPush($loan->application, 'Loan Rejected', $loan->reject_reason, $loan->loan_amount, date('Y-m-d'), 0, $loan);
                                            }
                                        }
                                        $session->addFlash('success', 'Loan Permanently rejected successfully.');
                                    } else {
                                        $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                                    }
                                }
                            } else {
                                if ($tranch_reject[0]->status > 0) {
                                    $tranch_reject[0]->status = 9;
                                    $tranch_reject[0]->date_disbursed = 0;
                                    $tranch_reject[0]->disbursement_id = 0;
                                    if ($tranch_reject[0]->save(false)) {
                                        $loan->status = 'rejected';
                                        $loan->reject_reason = $request['Loans']['reject_reason'];
                                        $loan->disbursed_amount = 0;
                                        $loan->date_disbursed = 0;
                                        $loan->disbursement_id = 0;
                                        if ($loan->save(false)) {
                                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                            foreach ($disbursement_details as $d) {
                                                $d->status = 2;
                                                $d->updated_by = 1;
                                                if (!$d->save(false)) {
                                                    print_r($d->getErrors());
                                                    die();
                                                }
                                            }
                                            $connection = \Yii::$app->db;
                                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                            $connection->createCommand($schdl_delete)->execute();

                                            if ($loan->project_id == 132) {
                                                AcagHelper::actionPush($loan->application, 'Loan Rejected', $loan->reject_reason, $loan->loan_amount, date('Y-m-d'), 0, $loan);
                                            }
                                        }
                                        $session->addFlash('success', 'Loan Permanently rejected successfully.');
                                    } else {
                                        $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                                    }
                                }
                            }
                        } else {
                            $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->one();
                            if ($tranch_reject->status > 0) {
                                $tranch_reject->status = 9;
                                $tranch_reject->date_disbursed = 0;
                                $tranch_reject->disbursement_id = 0;
                                if ($tranch_reject->save(false)) {
                                    $loan->status = 'rejected';
                                    $loan->reject_reason = $request['Loans']['reject_reason'];
                                    $loan->disbursed_amount = 0;
                                    $loan->date_disbursed = 0;
                                    $loan->disbursement_id = 0;
                                    if ($loan->save(false)) {
                                        $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                        foreach ($disbursement_details as $d) {
                                            $d->status = 2;
                                            $d->updated_by = 1;
                                            if (!$d->save(false)) {
                                                print_r($d->getErrors());
                                                die();
                                            }
                                        }
                                        $connection = \Yii::$app->db;
                                        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                        $connection->createCommand($schdl_delete)->execute();
                                    }
                                    $session->addFlash('success', 'Loan Permanently rejected successfully.');
                                } else {
                                    $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                                }
                            }
//                            $loan->status = 'rejected';
//                            $loan->disbursed_amount = 0;
//                            if ($loan->save()) {
//                                $session->addFlash('success', 'Loan Permanently rejected successfully.');
//                            } else {
//                                $session->addFlash('error', 'Loan not Permanently rejected successfully.');
//                            }
                        }


                        $application = Applications::find()->where(['id' => $loan->application_id])->one();
                        if (!empty($application) && $application != null) {
                            $application->status = 'rejected';
                            $application->save(false);
                        }

                    } elseif ($request['Loans']['status'] == 'ready_for_disbursement') {
                        $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();
                        $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                        $tranche_reject_count = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->count();
                        $amount_sum = 0;
                        $trancheCount = 0;
                        foreach ($tranche_reject as $key => $amount) {
                            if ($tranche_reject_count > 1) {
                                $trancheCount +=1;
                                if ($amount[$key] == 0) {
                                } else {
                                    $amount_sum += $amount->tranch_amount;
                                }
                            } else {
                                $amount_sum += $amount->tranch_amount;
                            }
                        }
                        if (in_array($loan->project_id, StructureHelper::trancheProjectsReject()) && $loan->loan_amount > 200000) {
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
                                if($trancheCount > 0){
                                    $loan->disbursed_amount = $amount_sum;
                                }else{
                                    $loan->status = 'pending';
                                    $loan->date_disbursed = 0;
                                    $loan->disbursed_amount = 0;
                                }
                            } else {
                                $loan->disbursed_amount = $amount_sum;
//                                $loan->disbursed_amount=$loan_tranches[0]->tranch_amount;
                            }
                            $loan->save(false);
                            //update loan actions if housing loan
                            if (in_array($loan->project_id, StructureHelper::trancheProjectsReject())) {
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
                                $publish->save(false);
                            }
                            //update tranche actions
                            $tranche_actions = LoanTranchesActions::find()->where(['in', 'action', ['disbursement']])->andWhere(['parent_id' => $ist_tranch->id])->all();
                            foreach ($tranche_actions as $l_act) {
                                $l_act->status = 0;
                                $l_act->save(false);
                            }
                            //update tranche
                            $ist_tranch->status = 4;
                            $ist_tranch->disbursement_id = 0;
                            $ist_tranch->date_disbursed = 0;
                            $ist_tranch->attendance_status = 'info_not_available';
                            $ist_tranch->save(false);
                            if ($loan->project_id == 132) {
                                $status = 'Loan In Process';
                                $statusReason = 'Loan In Process';
                                AcagHelper::actionPush($loan->application, $status, $statusReason, $loan->loan_amount, date('Y-m-d'), 0, $loan);
                            }
                            //delete schedules if created
                            if ($ist_tranch->tranch_no == 1) {
                                $connection = \Yii::$app->db;
                                $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                $connection->createCommand($schdl_delete)->execute();
                            }
                            $session->addFlash('success', 'Loan added to ready for disbursement stage successfully.');
                        } else {
                            $session->addFlash('error', 'Loan is already at the priviouse stage than disbursement');
                        }
                    } elseif ($request['Loans']['status'] == 'ready_for_fund_request') {
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
                            if (!$tranche_rejected[0]->save(false)) {
                                print_r($tranche_rejected[0]->getErrors());
                            }

                            $tranche_action = LoanTranchesActions::find()->where(['parent_id' => $tranche_rejected[0]->id])->andWhere(['in', 'action', ['cheque_printing', 'disbursement', 'fund_request']])->andWhere(['status' => 1])->all();
                            foreach ($tranche_action as $t_action) {
                                $t_action->status = 0;
                                $t_action->updated_by = 1;
                                if (!$t_action->save(false)) {
                                    print_r($t_action->getErrors());
                                }
                            }
                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranche_rejected[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                            foreach ($disbursement_details as $d) {
                                $d->status = 2;
                                $d->updated_by = 1;
                                if (!$d->save(false)) {
                                    print_r($d->getErrors());
                                    die();
                                }
                            }

                            $loan->status = 'pending';
                            $loan->date_disbursed = 0;
                            $loan->disbursement_id = 0;
                            $loan->disbursed_amount = 0;

                            if (!$loan->save(false)) {
                                print_r($loan->getErrors());
                            }
                            $connection = \Yii::$app->db;
                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                            $connection->createCommand($schdl_delete)->execute();
                            $loan_actions = LoanActions::find()->where(['parent_id' => $loan->id])->andWhere(['in', 'action', ['cheque_printing', 'takaful', 'disbursement']])->andWhere(['status' => 1])->all();
                            foreach ($loan_actions as $l_action) {
                                $l_action->status = 0;
                                $l_action->updated_by = 1;
                                if (!$l_action->save(false)) {
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
                            if (!$loan_tranches[0]->save(false)) {
                                print_r($loan_tranches[0]->getErrors());
                            }

                            $tranche_action = LoanTranchesActions::find()->where(['parent_id' => $loan_tranches[0]->id])->andWhere(['in', 'action', ['cheque_printing', 'disbursement', 'fund_request']])->andWhere(['status' => 1])->all();
                            foreach ($tranche_action as $t_action) {
                                $t_action->status = 0;
                                $t_action->updated_by = 1;
                                if (!$t_action->save(false)) {
                                    print_r($t_action->getErrors());
                                }
                            }
                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $loan_tranches[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                            foreach ($disbursement_details as $d) {
                                $d->status = 2;
                                $d->updated_by = 1;
                                if (!$d->save(false)) {
                                    print_r($d->getErrors());
                                    die();
                                }
                            }

                            $loan->status = 'pending';
                            $loan->date_disbursed = 0;
                            $loan->disbursement_id = 0;
                            $loan->disbursed_amount = 0;
                            if (!$loan->save(false)) {
                                print_r($loan->getErrors());
                            }
                            if ($loan->project_id == 132) {
                                $status = 'Loan In Process';
                                $statusReason = 'Loan In Process';
                                AcagHelper::actionPush($loan->application, $status, $statusReason, $loan->loan_amount, date('Y-m-d'), 0, $loan);
                            }
                            $connection = \Yii::$app->db;
                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                            $connection->createCommand($schdl_delete)->execute();
                            $loan_actions = LoanActions::find()->where(['parent_id' => $loan->id])->andWhere(['in', 'action', ['cheque_printing', 'takaful', 'disbursement']])->andWhere(['status' => 1])->all();
                            foreach ($loan_actions as $l_action) {
                                $l_action->status = 0;
                                $l_action->updated_by = 1;
                                if (!$l_action->save(false)) {
                                    print_r($l_action->getErrors());
                                }
                            }
                        }
                    }

                }
            } else {
                $session->addFlash('error', 'Action can not be done.because recovery is posted against this sanction no.');
            }
            return $this->redirect('/fixes/update-loan?id=' . $id);
        } else {
            throw new BadRequestHttpException('Action can not be performed against this loan.');
        }
    }

    public function actionRejectLoanOld($id)
    {
        $request = Yii::$app->request->post();
        $session = Yii::$app->session;
        $loan = Loans::find()->where(['id' => $id])->andWhere(['in', 'status', ['pending', 'collected', 'loan completed']])->one();
        if (isset($loan) && !empty($loan)) {
            if (empty($loan->recovery)) {
                if (isset($request['Loans'])) {
                    if ($request['Loans']['status'] == 'permanent_reject') {
                        if (in_array($loan->project_id, StructureHelper::trancheProjects())) {
                            $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();
                            if (count($tranch_reject) > 1) {
                                if ($tranch_reject[1]->status > 0) {
                                    $tranch_reject[1]->status = 9;
                                    $tranch_reject[1]->date_disbursed = 0;
                                    $tranch_reject[1]->disbursement_id = 0;
                                    if ($tranch_reject[1]->save()) {
                                        $loan->date_disbursed = 0;
                                        $loan->save(false);
                                        if ($loan->status != 'grant') {

                                            FixesHelper::ledger_regenerate($loan);
                                        }
                                        $session->addFlash('success', 'Loan Permanently rejected successfully.');
                                    } else {
                                        $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                                    }
                                } else {
                                    $loan->status = 'rejected';
                                    $loan->disbursed_amount = 0;
                                    if ($loan->save()) {
                                        $connection = \Yii::$app->db;
                                        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                        $connection->createCommand($schdl_delete)->execute();
                                        $tranch_reject[0]->date_disbursed = 0;
                                        $tranch_reject[0]->status = 9;
                                        if ($tranch_reject[0]->save()) {
                                            $disbursement_reject = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject[0]->id])->one();
                                            $disbursement_reject->status = 2;
                                            $disbursement_reject->save();
                                        }

                                        $session->addFlash('success', 'Loan Permanently rejected successfully.');
                                    } else {
                                        $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                                    }
                                }
                            }
                        } else {
                            $loan->status = 'rejected';
                            $loan->disbursed_amount = 0;
                            $loan->date_disbursed = 0;

                            $connection = \Yii::$app->db;
                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                            $connection->createCommand($schdl_delete)->execute();

                            if ($loan->save()) {
                                $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->one();
                                if ($tranche_reject) {
                                    $tranche_reject->date_disbursed = 0;
                                    $tranche_reject->status = 9;
                                    if ($tranche_reject->save()) {
                                        $disbursement_reject = DisbursementDetails::find()->where(['tranche_id' => $tranche_reject->id])->one();
                                        if ($disbursement_reject) {
                                            $disbursement_reject->status = 2;
                                            $disbursement_reject->save();
                                        }
                                    }
                                }

                                $session->addFlash('success', 'Loan Permanently rejected successfully.');
                            } else {
                                $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                            }
                        }

                    } elseif ($request['Loans']['status'] == 'ready_for_disbursement') {
                        $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();
                        if (in_array($loan->project_id, StructureHelper::trancheProjects()) && $loan->loan_amount > 200000) {
                            if ($loan_tranches[1]->status > 0) {
                                $ist_tranch = $loan_tranches[1];
                            } else {
                                $ist_tranch = $loan_tranches[0];
                            }
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
                                $loan->disbursed_amount = $loan_tranches[0]->tranch_amount;
                            }
                            $loan->save();
                            //update loan actions if housing loan
                            if (in_array($loan->project_id, StructureHelper::trancheProjects())) {
                                if ($ist_tranch->tranch_no == 1) {
                                    //change account verification status change
                                    /*$loan_actions = LoanActions::find()->where(['in', 'action', ['account_verification']])->andWhere(['parent_id' => $loan->id])->all();
                                    foreach ($loan_actions as $l_act) {
                                        $l_act->status = 0;
                                        if (!$l_act->save()) {
                                            print_r($l_act->getErrors());
                                            die();
                                        }
                                    }*/
                                    $loan_actions = LoanActions::find()->where(['action' => 'account_verification', 'parent_id' => $loan->id])->one();
                                    if (!empty($loan_actions)) {
                                        $loan_actions->status = 0;
                                        if (!$loan_actions->save()) {
                                            print_r($loan_actions->getErrors());
                                            die();
                                        }
                                    }
                                    //account no verified status change
                                    $account_no = MembersAccount::find()->where(['member_id' => $loan->application->member_id])->andWhere(['is_current' => 1, 'status' => 1])->one();
                                    if (!empty($account_no)) {
                                        $account_no->status = 0;
                                        $account_no->save();
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
                            $session->addFlash('success', 'Loan added to ready for disbursement stage successfully.');
                        } else {
                            $session->addFlash('error', 'Loan is already at the priviouse stage than disbursement');
                        }
                    } elseif ($request['Loans']['status'] == 'ready_to_fund_request') {
                        $loan = Loans::find()->where(['sanction_no' => $loan->sanction_no])->andWhere(['in', 'status', ['pending', 'collected', 'loan completed']])->one();
                        if (isset($loan) && !empty($loan)) {
                            if (empty($loan->recovery)) {
                                $loan_tranches = LoanTranches::find()->where(['loan_id' => $loan->id])->orderBy('tranch_no asc')->all();

                                $ist_tranch = $loan_tranches[0];

                                if ($ist_tranch->status > 4) {
                                    //update loan
                                    if ($ist_tranch->tranch_no == 1) {
                                        $loan->status = 'pending';
                                        $loan->date_disbursed = 0;
                                        $loan->disbursed_amount = 0;
                                    } else {
                                        $loan->disbursed_amount = $loan_tranches[0]->tranch_amount;
                                    }
                                    $loan->updated_by = 0;
                                    $loan->save();
                                    //update loan actions if housing loan
                                    if ($ist_tranch->tranch_no == 1) {

                                        $loan_actions = LoanActions::find()->where(['in', 'action', ['account_verification', 'takaful']])->andWhere(['parent_id' => $loan->id])->one();
                                        if (!empty($loan_actions)) {
                                            $loan_actions->status = 0;
                                            $loan_actions->updated_by = 0;
                                            if (!$loan_actions->save()) {
                                                print_r($loan_actions->getErrors());
                                                die();
                                            }
                                        }
                                        //account no verified status change
                                    }
                                    //publish entry delete
                                    $publish = DisbursementDetails::find()->where(['tranche_id' => $ist_tranch->id])->andWhere(['deleted' => 0])->one();
                                    if (!empty($publish)) {
                                        $publish->status = 2;
                                        $publish->updated_by = 2;
                                        $publish->save();
                                    }
                                }
                                //update tranche actions
                                $tranche_actions = LoanTranchesActions::find()->where(['in', 'action', ['fund_request', 'takaful', 'disbursement', 'cheque_printing']])->andWhere(['parent_id' => $ist_tranch->id])->all();
                                foreach ($tranche_actions as $l_act) {
                                    $l_act->status = 0;
                                    $l_act->updated_by = 0;
                                    $l_act->save();
                                }
                                //update tranche
                                $ist_tranch->status = 3;
                                $ist_tranch->disbursement_id = 0;
                                $ist_tranch->fund_request_id = 0;
                                $ist_tranch->date_disbursed = 0;
                                $ist_tranch->updated_by = 0;
                                $ist_tranch->attendance_status = 'info_not_available';
                                $ist_tranch->save();
                                //delete schedules if created
                                if ($ist_tranch->tranch_no == 1) {
                                    $connection = \Yii::$app->db;
                                    $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                    $connection->createCommand($schdl_delete)->execute();
                                }


                            }
                        }

                    }
                }
            } else {
                $session->addFlash('error', 'Action can not be done.because recovery is posted against this sanction no.');
            }
            return $this->redirect('/fixes/update-loan?id=' . $id);
        } else {
            throw new BadRequestHttpException('Action can not be performed against this loan.');
        }
    }

    public function actionFixesSchedules($id)
    {
        $session = Yii::$app->session;
        $session->addFlash('success', 'Fixes Run Successfully');
        $model = Loans::findOne(['id' => $id]);
        if ($model->status == 'grant') {
        } else {
            FixesHelper::fix_schedules_update($model);
        }
        return $this->redirect('/fixes/fixes-loans?id=' . $id);
    }

    public function actionHousingLedger($id)
    {
        $model = Loans::findOne(['id' => $id]);
        if (!empty($model) && $model->status != 'grant') {
            FixesHelper::ledger_regenerate_closing_loan($model);
        }
        $scheduleModel = Schedules::find()
            ->where(['loan_id' => $id])
            ->andWhere(['<=', 'due_date', strtotime(date("Y-m-10"))])
            ->all();

        $recoveriesModel = Recoveries::find()
            ->where(['loan_id' => $id])
            ->andWhere(['deleted' => 0])
            ->andWhere(['<=', 'receive_date', strtotime(date("Y-m-d"))])
            ->all();

        $schdl_amnt = 0;
        $charges_schdl_amount = 0;
        $charges_schdl_amnt_tax = 0;

        $credit = 0;
        $charges_credit = 0;
        $credit_tax = 0;

        foreach ($scheduleModel as $schedule) {

            $schdl_amnt = $schdl_amnt + $schedule->schdl_amnt;
            $charges_schdl_amount = $charges_schdl_amount + $schedule->charges_schdl_amount;
            $charges_schdl_amnt_tax = $charges_schdl_amnt_tax + $schedule->charges_schdl_amnt_tax;
        }

        foreach ($recoveriesModel as $recovery) {
            $credit = $credit + $recovery->amount;
            $charges_credit = $charges_credit + $recovery->charges_amount;
            $credit_tax = (int)$credit_tax + (int)$recovery->credit_tax;
        }

        $olp = $model->disbursed_amount - $credit;
        $rent = $charges_schdl_amount - $charges_credit;
        $tax = $charges_schdl_amnt_tax - $credit_tax;
        $payable = $olp + $rent + $tax;

        $session = Yii::$app->session;
        $session->addFlash('success', 'Ledger updated successfully!');
        return $this->render('ledger_detail_index', [
            'model' => $model,
            'olp' => $olp,
            'rent' => $rent,
            'tax' => $tax,
            'payable' => $payable
        ]);
    }

    public function actionHousingLedgerRupee($id)
    {
        $loan = Loans::findOne(['id' => $id]);
        if (!empty($loan) && $loan->status != 'grant') {
            FixesHelper::ledger_regenerate_housing_rupee_diff($loan);
        }
    }

    public function actionSplit()
    {
        //print_r(Members::find()->limit(10)->all());
        //die();
        $exporter = new CsvGrid([
            'query' => Members::find(),
            'maxEntriesPerFile' => 1000,
            'resultConfig' => [
                'forceArchive' => true // always archive the results
            ],
        ]);
        $exporter->export()->saveAs('exports/archive-file.zip'); // output ZIP archive!
    }
}
