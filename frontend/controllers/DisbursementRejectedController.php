<?php

namespace frontend\controllers;

use common\components\Helpers\AcagHelper;
use common\components\Helpers\ImageHelper;
use common\models\Designations;
use common\models\DisbursementDetails;
use common\models\Disbursements;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Recoveries;
use common\models\search\GlobalsSearch;
use Yii;
use common\models\DisbursementRejected;
use common\models\search\DisbursementRejectedSearch;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\FixesHelper;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
/**
 * DisbursementRejectedController implements the CRUD actions for DisbursementRejected model.
 */
class DisbursementRejectedController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
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
     * Lists all DisbursementRejected models.
     * @return mixed
     */
    public function actionIndex()
    {
        $designation = Designations::find()->where(['id'=>\Yii::$app->user->identity->designation_id])->one();
        $searchModel = new DisbursementRejectedSearch();
        $projectList = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $types = array('sanction_no' => ' Sanction No', 'borrower_cnic' => ' CNIC');
        $memberModel = new GlobalsSearch();
        if (!empty(Yii::$app->request->queryParams)) {
            $params = Yii::$app->request->queryParams;
            if (isset($params['GlobalsSearch']['sanction_no'])) {
                $memberModel = new GlobalsSearch();
                $memberProvider = $memberModel->search(Yii::$app->request->queryParams);
                $modelLoan = Loans::find()->where(['sanction_no' => $params['GlobalsSearch']['sanction_no']])->one();
                return $this->render('index', [
                    'designation' => $designation,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'memberModel' => $memberModel,
                    'memberProvider' => $memberProvider,
                    'modelLoan' => $modelLoan,
                    'projectArray' => $projectList,
                    'types' => $types
                ]);
            } else {
                return $this->render('index', [
                    'designation' => $designation,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'memberModel' => $memberModel,
                    'projectArray' => $projectList,
                    'types' => $types
                ]);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'designation' => $designation,
            'dataProvider' => $dataProvider,
            'memberModel' => $memberModel,
            'projectArray' => $projectList,
            'types' => $types
        ]);
    }

    /**
     * Displays a single DisbursementRejected model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRejectDisbursedLoan($id)
    {
        $model = new DisbursementRejected();
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $tranche = LoanTranches::find()->where(['loan_id' => $id])
                    ->andWhere(['<>','date_disbursed', 0])
                    ->andWhere(['<>','disbursement_id', 0])
                    ->orderBy([
                        'id' => SORT_DESC,
                    ])
                    ->one();
                if ($tranche) {
                    $loanDataId = Loans::find()->where(['id' => $id])->one();
                    $disbursement_detail = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->select(['id'])->one();
                    if ($disbursement_detail && $loanDataId) {
                        return [
                            'title' => "",
                            'content' => $this->renderAjax('create', [
                                'model' => $model,
                                'disbursement_detail_id' => $disbursement_detail->id,
                                'project_id' => $loanDataId->project_id,
                            ]),
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                        ];
                    }else{
                        if($loanDataId->status == 'collected'){
                            return [
                                'title' => "",
                                'content' => $this->renderAjax('create', [
                                    'model' => $model,
                                    'disbursement_detail_id' => $loanDataId->id,
                                    'project_id' => $loanDataId->project_id,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                            ];
                        }
                        return [
                            'title' => "",
                            'content' => $this->renderAjax('create_error', [
                            ]),
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                        ];
                    }
                }
            } elseif ($model->load(Yii::$app->request->post())) {
                $rejectedLoan = DisbursementRejected::find()
                    ->where(['disbursement_detail_id' => $_POST['disbursement_detail_id']])
                    ->andWhere(['is_deleted' => 0])
                    ->andWhere(['is_verified' => 0])
                    ->one();
                if ($rejectedLoan) {
                    Yii::$app->session->setFlash('pending', 'Loan is already Submitted for rejection and pending for verification.');
                    return $this->redirect('index');
                } else {
                    $loan = Loans::find()->where(['id'=>$id])->one();
                    $model->disbursement_detail_id = $_POST['disbursement_detail_id'];
                    $model->project_id = $_POST['project_id'];
                    $model->borrower_name = $loan->application->member->full_name;
                    $model->borrower_cnic = $loan->application->member->cnic;
                    $model->sanction_no = $loan->sanction_no;
                    $model->loan_amount = $loan->loan_amount;
                    $model->deposit_amount = $_POST['DisbursementRejected']['deposit_amount'];
                    $model->deposit_bank = $_POST['DisbursementRejected']['deposit_bank'];
                    $model->deposit_slip_no = $_POST['DisbursementRejected']['deposit_slip_no'];
                    $model->reject_reason = $_POST['DisbursementRejected']['reject_reason'];
                    $model->deposit_date = strtotime($_POST['DisbursementRejected']['deposit_date']);
                    $model->is_verified = 0;
                    $model->created_by = Yii::$app->user->getId();
                    $model->created_at = strtotime('now');

                    $model->file = UploadedFile::getInstance($model, 'file');
                    $imageName = uniqid() . '.' . $model->file->extension;
                    $path = Yii::getAlias('@frontend/web/uploads/') . $imageName;
                    if ($model->file->saveAs($path)) {
                        $model->file_path = $imageName;
                    }

                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Submitted for verification!.');
                        return $this->redirect('index');
                    } else {
                        Yii::$app->session->setFlash('danger', 'Failed to reject loan please contact team.');
                        return $this->redirect('index');
                    }
                }

            } else {
                return [
                    'title' => "",
                    'content' => $this->renderAjax('create_error', [
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            }
        } else {
            return [
                'title' => "",
                'content' => $this->renderAjax('create_error', [
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
            ];
        }
    }

    public function actionView($id)
    {
        $designation = Designations::find()->where(['id'=>\Yii::$app->user->identity->designation_id])->one();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'designation' => $designation,
        ]);
    }

    /**
     * Creates a new DisbursementRejected model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DisbursementRejected();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DisbursementRejected model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReview($id)
    {
        $model = $this->findModel($id);
        $model->status = 2;
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionUpdate($id)
    {
        $disbursementRejected = DisbursementRejected::find()
            ->where(['id' => $id, 'status' => 2])
            ->one();

        if (!$disbursementRejected) {
            Yii::$app->session->setFlash('pending', 'Loan rejection is under review!');
            return $this->redirect(['index']);
        }

        // Resolve loan either via disbursement > tranch > loan OR directly via loan_id in disbursementRejected
        $loan = null;
        if ($disbursementRejected->disbursement && $disbursementRejected->disbursement->tranch) {
            $loan = Loans::find()->where(['id' => $disbursementRejected->disbursement->tranch->loan->id])->one();
        } elseif (!empty($disbursementRejected->disbursement_detail_id)) {
            $loan = Loans::find()->where(['id' => $disbursementRejected->disbursement_detail_id])->one();
        }

        if (!$loan) {
            Yii::$app->session->setFlash('error', 'Loan not found.');
            return $this->redirect(['index']);
        }

        $tranches = LoanTranches::find()
            ->where(['loan_id' => $loan->id])
            ->andWhere(['>', 'status', 0])
            ->orderBy('tranch_no ASC')
            ->all();

        if (empty($tranches)) {
            Yii::$app->session->setFlash('error', 'Loan tranches not found.');
            return $this->redirect(['index']);
        }

        foreach ($tranches as $tranche) {
            $tranche->status = 9;
            $tranche->date_disbursed = 0;
            $tranche->disbursement_id = 0;
            if (!$tranche->save(false)) {
                Yii::$app->session->setFlash('error', 'Loan tranche update failed.');
                return $this->redirect(['index']);
            }
        }

        $recoveriesList = Recoveries::find()
            ->where(['loan_id' => $loan->id])
            ->all();

        foreach ($recoveriesList as $recovery) {
            $recovery->deleted = 1;
            $recovery->save(false);
        }


        // Update loan
        $loan->status = 'rejected';
        $loan->disbursed_amount = 0;
        $loan->date_disbursed = 0;
        $loan->disbursement_id = 0;
        $loan->reject_reason = $disbursementRejected->reject_reason;

        if (!$loan->save(false)) {
            Yii::$app->session->setFlash('error', 'Loan rejection failed!');
            return $this->redirect(['index']);
        }

        // Push status if applicable
        if ($loan->project_id == 132) {
            AcagHelper::actionPush(
                $loan->application,
                'Loan Rejected',
                $loan->reject_reason,
                0,
                date('Y-m-d'),
                0,
                $loan
            );
        }

        // Update disbursement detail if exists
        if (!empty($disbursementRejected->disbursement_detail_id)) {
            $disbursementDetail = DisbursementDetails::findOne($disbursementRejected->disbursement_detail_id);
            if ($disbursementDetail) {
                $disbursementDetail->status = 2;
                $disbursementDetail->updated_by = Yii::$app->user->id;
                $disbursementDetail->save(false);
            }
        }

        // Update rejection record
        $disbursementRejected->status = 1;
        $disbursementRejected->is_verified = 1;
        $disbursementRejected->verified_by = Yii::$app->user->id;
        $disbursementRejected->verfied_at = time();
        $disbursementRejected->save(false);

        // Delete existing schedule records
        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS=0; DELETE FROM schedules WHERE loan_id = :loan_id")
            ->bindValue(':loan_id', $loan->id)
            ->execute();

        Yii::$app->session->setFlash('success', 'Loan permanently rejected successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing DisbursementRejected model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime('now');
        $model->is_deleted = 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the DisbursementRejected model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DisbursementRejected the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DisbursementRejected::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
