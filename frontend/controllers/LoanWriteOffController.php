<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use common\models\Loans;
use common\models\Recoveries;
use common\models\search\LoansSearch;
use Yii;
use common\models\LoanWriteOff;
use common\models\search\LoanWriteOffSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * LoanWriteOffController implements the CRUD actions for LoanWriteOff model.
 */
class LoanWriteOffController extends Controller
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
                    //'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all LoanWriteOff models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LoanWriteOffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }

    public function actionWriteOffExport()
    {
        if (isset($_POST['LoanWriteOffSearch']) && $_POST['LoanWriteOffSearch'] != '') {
            $this->layout = 'csv';
            $headersRecovery = (['Region', 'Area', 'Branch', 'Branch code', 'Project', 'Sanction No', 'Borrower Name', 'Gender', 'Account title', 'Account Number',
                'Voucher', 'Write off Cheque', 'Write off Deposit slip', 'Recovery', 'Type', 'Diseased person', 'Relation','Status']);

            $headersFuneral = (['Region', 'Area', 'Branch', 'Branch code', 'Project', 'Sanction No', 'Borrower Name', 'Gender',
                'Voucher', 'Funeral Cheque', 'Funeral', 'Name of Heir/Guardian',
                'CNIC of Heir/Guardian','Status']);
            $write_off_recovery = array();
            $write_off_funeral = array();
            $searchModel = new LoanWriteOffSearch();
            if ($_POST['LoanWriteOffSearch']['type'] == 0) {
                $queryRecovery = $searchModel->searchWriteOff($_POST);
                if (!empty($queryRecovery) && $queryRecovery != null) {
                    $i = 0;
                    foreach ($queryRecovery as $g) {
                        if ($g['who_will_work'] == 'self') {
                            $Diseased_person = $g['member_name'];
                        } else {
                            $Diseased_person = $g['other_name'];
                        }

                        if ($g['status'] == 1) {
                            $status = 'Approved';
                        } elseif ($g['status'] == 2) {
                            $status = 'Rejected';
                        } elseif ($g['status'] == 0) {
                            $status = 'Pending';
                        }

                        $write_off_recovery[$i]['Region'] = ($g['region_name']) ? $g['region_name'] : '';
                        $write_off_recovery[$i]['Area'] = ($g['area_name']) ? $g['area_name'] : '';
                        $write_off_recovery[$i]['Branch'] = ($g['branch_name']) ? $g['branch_name'] : '';
                        $write_off_recovery[$i]['Branch code'] = isset($g['branch_code']) ? $g['branch_code'] : '';
                        $write_off_recovery[$i]['Project'] = ($g['project_name']) ? $g['project_name'] : '';
                        $write_off_recovery[$i]['Sanction No'] = ($g['sanction_no']) ? $g['sanction_no'] : '';
                        $write_off_recovery[$i]['Borrower Name'] = ($g['member_name']) ? $g['member_name'] : '';
                        $write_off_recovery[$i]['Gender'] = ($g['gender']) ? $g['gender'] : '';
                        $write_off_recovery[$i]['Account title'] = ($g['account_title']) ? $g['account_title'] : '';
                        $write_off_recovery[$i]['Account Number'] = ($g['account_no']) ? $g['account_no'] : '';
                        $write_off_recovery[$i]['Voucher'] = isset($g['voucher_no']) ? $g['voucher_no'] : '';
                        $write_off_recovery[$i]['Write off Cheque'] = ($g['cheque_no']) ? $g['cheque_no'] : '';
                        $write_off_recovery[$i]['Write off Deposit slip'] = ($g['deposit_slip_no']) ? $g['deposit_slip_no'] : '';
                        $write_off_recovery[$i]['Recovery'] = ($g['amount']) ? $g['amount'] : '';
                        $write_off_recovery[$i]['Reason'] = ($g['reason']) ? $g['reason'] : '';
                        $write_off_recovery[$i]['Diseased person'] = $Diseased_person;
                        $write_off_recovery[$i]['Relation'] = ($g['relation']) ? $g['relation'] : '';
                        $write_off_recovery[$i]['Status'] = $status;
                        $i++;
                    }
                    ExportHelper::ExportCSV('write_off_recovery', $headersRecovery, $write_off_recovery);
                    die();
                } else {
                    ExportHelper::ExportCSV('write_off_recovery', $headersRecovery, $write_off_recovery);
                    die();
                }
            } else {
                $queryFuneral = $searchModel->searchWriteOff($_POST);
                if (!empty($queryFuneral) && $queryFuneral != null) {
                    $i = 0;
                    foreach ($queryFuneral as $g) {
                        if ($g['status'] == 1) {
                            $status = 'Approved';
                        } elseif ($g['status'] == 2) {
                            $status = 'Rejected';
                        } elseif ($g['status'] == 0) {
                            $status = 'Pending';
                        }

                        $write_off_funeral[$i]['Region'] = ($g['region_name']) ? $g['region_name'] : '';
                        $write_off_funeral[$i]['Area'] = ($g['area_name']) ? $g['area_name'] : '';
                        $write_off_funeral[$i]['Branch'] = ($g['branch_name']) ? $g['branch_name'] : '';
                        $write_off_funeral[$i]['Branch code'] = isset($g['branch_code']) ? $g['branch_code'] : '';
                        $write_off_funeral[$i]['Project'] = ($g['project_name']) ? $g['project_name'] : '';
                        $write_off_funeral[$i]['Sanction No'] = ($g['sanction_no']) ? $g['sanction_no'] : '';
                        $write_off_funeral[$i]['Borrower Name'] = ($g['member_name']) ? $g['member_name'] : '';
                        $write_off_funeral[$i]['Gender'] = ($g['gender']) ? $g['gender'] : '';
                        $write_off_funeral[$i]['Voucher'] = isset($g['voucher_no']) ? $g['voucher_no'] : '';
                        $write_off_funeral[$i]['Funeral Cheque'] = ($g['cheque_no']) ? $g['cheque_no'] : '';
                        $write_off_funeral[$i]['Funeral'] = ($g['amount']) ? $g['amount'] : '';
                        $write_off_funeral[$i]['Name of Heir/Guardian'] = ($g['other_name']) ? $g['other_name'] : '';
                        $write_off_funeral[$i]['CNIC of Heir/Guardian'] = ($g['other_cnic']) ? $g['other_cnic'] : '';
                        $write_off_funeral[$i]['Status'] = $status;
                        $i++;
                    }
                    ExportHelper::ExportCSV('write_off_funeral', $headersFuneral, $write_off_funeral);
                    die();
                } else {
                    ExportHelper::ExportCSV('write_off_funeral', $headersFuneral, $write_off_funeral);
                    die();
                }
            }


        }
    }

    /**
     * Displays a single LoanWriteOff model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Write Off Detail #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
            ];
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LoanWriteOff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionGetMemberInfo()
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */

            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = $request->post();
            if (substr_count($data['sanc_no'], '-') == 2) {
                $ar = explode("-", $data['sanc_no']);
                if (ctype_digit($ar[0]) && ctype_digit($ar[2])) {
                    $recoveries = new Recoveries();
                    $ret = $recoveries->getMemberForRecovery($data['sanc_no']);
                    if ($ret) {

                    } else {
                        $ret['error'] = 'No record found.';
                    }
                } else {
                    $ret['error'] = 'Invalid sanction number.';
                }
            } else {
                $ret['error'] = 'Invalid sanction number.';
            }
            header("Content-type: application/json");
            // echo json_encode($ret);
            $this->asJson($ret);
            Yii::$app->end();
        }

    }

    public function actionCreate()
    {
        $model = new LoanWriteOff();

        if ($model->load(Yii::$app->request->post())) {
            $loan = Loans::find()->where(['sanction_no' => $model->sanction_no])->one();
            $model->loan_id = $loan->id;
            $model->loan_id = $loan->id;
            $model->write_off_date = strtotime($model->write_off_date);;
            if (!$model->save()) {
                return $this->render('create', [
                    'model' => $model,
                ]);
            } else {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LoanWriteOff model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);;

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Write Off Approve/Reject #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            } else if ($model->load($request->post())) {
                $loan = Loans::find()->where(['id' => $model->loan_id])->one();
                if ($model->type == 0) {
                    if ($loan->balance >= $model->amount) {
                        if ($model->save(false)) {
                            if (!empty($loan) && $loan != null && $model->type == 0 && $model->status == 1) {
                                $recovery = new Recoveries();
                                $recovery->sanction_no = $loan->sanction_no;
                                $recovery->receive_date = $model->write_off_date;
                                $recovery->receipt_no = str_replace("-", "", $loan->sanction_no);
                                $recovery->amount = $model->amount;
                                if ($recovery->save()) {
                                    $model->recovery_id = $recovery->id;
                                    $model->save(false);
                                    Yii::$app->session->setFlash('success', "Write Off success.");
                                    return $this->redirect(['index']);
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Loan not found, please contact admin.");
                                return $this->redirect(['index']);
                            }
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "You are not allowed to Approve , Loan balance is less than Write Off Amount.");
                        return $this->redirect(['index']);
                    }
                } else {
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', "Write Of rejected.");
                        return $this->redirect(['index']);
                    } else {
                        print_r($model->getErrors());
                        die();
                    }
                }
            } else {
                return [
                    'title' => "Write Off Approve/Reject #" . $id,
                    'content' => $this->renderAjax('update', [
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $loan = Loans::find()->where(['id' => $model->loan_id])->one();
                if ($model->type == 0) {
                    if ($loan->balance >= $model->amount) {
                        if ($model->save(false)) {
                            if (!empty($loan) && $loan != null && $model->type == 0 && $model->status == 1) {
                                $recovery = new Recoveries();
                                $recovery->sanction_no = $loan->sanction_no;
                                $recovery->receive_date = $model->write_off_date;
                                $recovery->receipt_no = str_replace("-", "", $loan->sanction_no);
                                $recovery->amount = $model->amount;
                                if ($recovery->save()) {
                                    $model->recovery_id = $recovery->id;
                                    $model->save(false);
                                    Yii::$app->session->setFlash('success', "Write Off success.");
                                    return $this->redirect(['index']);
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Loan not found, please contact admin.");
                                return $this->redirect(['index']);
                            }
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "You are not allowed to Approve , Loan balance is less than Write Off Amount.");
                        return $this->redirect(['index']);
                    }
                } else {
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', "Write Of rejected.");
                        return $this->redirect(['index']);
                    } else {
                        print_r($model->getErrors());
                        die();
                    }
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionBulkUpdate()
    {


        $request = Yii::$app->request;

        $model = new LoanWriteOff();

        if ($request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {


            } else {

                $idArray =  $request->post()['pks'];

                return [
                    'title' => "Write Off Approve/Reject #", //. $id,
                    'content' => $this->renderAjax('update_bulk', [
                        'model' => $model,
                        'idArray' => $idArray,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            }
        } else {

            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $ids = explode(',',$request->post()['id']);
               $status =  $request->post()['LoanWriteOff']['status'];
                $models = LoanWriteOff::find()->where(['in', 'id', $ids])->all();
                foreach ($models as $model) {
                    $model->status = $status;
                $loan = Loans::find()->where(['id' => $model->loan_id])->one();
                if ($model->type == 0) {
                    if ($loan->balance >= $model->amount) {
                        if ($model->save(false)) {
                            if (!empty($loan) && $loan != null && $model->type == 0 && $model->status == 1) {
                                $recovery = new Recoveries();
                                $recovery->sanction_no = $loan->sanction_no;
                                $recovery->receive_date = $model->write_off_date;
                                $recovery->receipt_no = str_replace("-", "", $loan->sanction_no);
                                $recovery->amount = $model->amount;
                                if ($recovery->save()) {
                                    $model->recovery_id = $recovery->id;
                                    $model->save(false);
                                    Yii::$app->session->setFlash('success', "Write Off success.");
                                    //return $this->redirect(['index']);
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Loan not found, please contact admin.");
                                //return $this->redirect(['index']);
                            }
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "You are not allowed to Approve , Loan balance is less than Write Off Amount.");
                        //return $this->redirect(['index']);
                    }
                } else {
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', "Write Of rejected.");
                        //return $this->redirect(['index']);
                    } else {
                        print_r($model->getErrors());
                        die();
                    }
                }
                }
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Deletes an existing LoanWriteOff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LoanWriteOff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LoanWriteOff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LoanWriteOff::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
