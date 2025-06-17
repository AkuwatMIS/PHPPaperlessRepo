<?php

namespace frontend\controllers;

use common\components\Helpers\AcagHelper;
use common\components\Helpers\FixesHelper;
use Yii;
use common\models\Designations;
use common\models\DisbursementDetails;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\search\GlobalsSearch;
use common\models\search\TemporaryDisbursementRejectedSearch;
use common\models\TemporaryDisbursementRejected;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
/**
 * DisbursementRejectedController implements the CRUD actions for DisbursementRejected model.
 */
class TemporaryDisbursementRejectedController extends Controller
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
        $searchModel = new TemporaryDisbursementRejectedSearch();
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
                    'types' => $types
                ]);
            } else {
                return $this->render('index', [
                    'designation' => $designation,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'memberModel' => $memberModel,
                    'types' => $types
                ]);
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'designation' => $designation,
            'dataProvider' => $dataProvider,
            'memberModel' => $memberModel,
            'types' => $types
        ]);
    }

    /**
     * Displays a single TemporaryDisbursementRejected model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRejectDisbursedLoan($id)
    {
        $model = new TemporaryDisbursementRejected();
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $tranche = LoanTranches::find()->where(['loan_id' => $id])
                    ->andWhere(['>','date_disbursed', 0])
                    ->andWhere(['>','disbursement_id', 0])
                    ->orderBy([
                        'id' => SORT_DESC,
                    ])
                    ->one();
                if ($tranche) {
                    $disbursement_detail = DisbursementDetails::find()
                        ->where(['tranche_id' => $tranche->id])
                        ->andWhere(['!=','status' , 2])
                        ->one();
                    if ($disbursement_detail) {
                        return [
                            'title' => "",
                            'content' => $this->renderAjax('create', [
                                'model' => $model,
                                'disbursement_detail_id' => $disbursement_detail->id,
                                'tranche_no' => $tranche->tranch_no,
                            ]),
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                        ];
                    }else{
                        return [
                            'title' => "",
                            'content' => $this->renderAjax('create_error', [
                            ]),
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                        ];
                    }
                }
            } elseif ($model->load(Yii::$app->request->post())) {
                $rejectedLoan = TemporaryDisbursementRejected::find()
                    ->where(['disbursement_detail_id' => $_POST['disbursement_detail_id']])
                    ->andWhere(['is_verified' => 0])
                    ->one();
                if ($rejectedLoan) {
                    Yii::$app->session->setFlash('pending', 'Loan is already Submitted for rejection and pending for verification.');
                    return $this->redirect('index');
                } else {
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
                return $this->render('create', [
                    'model' => $model,
                    'disbursement_detail_id' => 0
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'disbursement_detail_id' => 0
            ]);
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
     * Creates a new TemporaryDisbursementRejected model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TemporaryDisbursementRejected();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TemporaryDisbursementRejected model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReview($id)
    {
        $model = $this->findModel($id);
        $model->status = 2;
        $model->review_by = Yii::$app->user->getId();
        $model->review_at = strtotime('now');
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionUpdate($id)
    {
        $model = TemporaryDisbursementRejected::find()
            ->where(['id' => $id])
            ->andWhere(['status' => 2])
            ->one();
        if ($model) {
            $loan = Loans::find()->where(['id' => $model->disbursement->tranch->loan->id])->one();
            if (isset($loan) && !empty($loan)) {
                $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])
                    ->andWhere(['tranch_no'=> $model->tranche_no])
                    ->one();
                if(in_array($tranche_reject->status,[6,8])){
                    if($tranche_reject->tranch_no == 1){
                        $loan->disbursed_amount = 0;
                        $loan->date_disbursed = 0;
                        $loan->status = 'pending';
                    }else{
                        $loan->disbursed_amount = $loan->disbursed_amount-$tranche_reject->tranch_amount;
                    }
                    if($loan->save()){
                        $tranche_reject->status = 3;
                        $tranche_reject->cheque_no = 0;
                        $tranche_reject->fund_request_id = 0;
                        $tranche_reject->attendance_status = 'info_not_available';
                        $tranche_reject->date_disbursed = 0;
                        $tranche_reject->disbursement_id = 0;
                        if ($tranche_reject->save(false)) {
                            $modelDisbursementDetail = DisbursementDetails::find()
                                ->where(['id' => $model->disbursement_detail_id])
                                ->one();
                            $modelDisbursementDetail->status=2;
                            if($modelDisbursementDetail->save()){
                                $model->status = 1;
                                $model->is_verified = 1;
                                $model->verified_by = Yii::$app->user->getId();
                                $model->verfied_at = strtotime('now');
                                if($model->save()){
                                    if($tranche_reject->tranch_no == 1){
                                        $connection = \Yii::$app->db;
                                        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                        $connection->createCommand($schdl_delete)->execute();
                                        if($loan->project_id == 132){
                                            $status = 'Loan In Process';
                                            $statusReason = 'Loan In Process';
                                            AcagHelper::actionPush($loan->application, $status, $statusReason, 0, date('Y-m-d'), 0, $loan);
                                        }
                                    }else{
                                        FixesHelper::ledger_regenerate($loan);
                                    }
                                    Yii::$app->session->setFlash('success', 'Temporary Disbursement Rejected Successfully!');
                                    return $this->redirect('index');
                                }else{
                                    Yii::$app->session->setFlash('danger', 'Temporary Disbursement Rejected Status not updated!');
                                    return $this->redirect('index');
                                }
                            }else{
                                Yii::$app->session->setFlash('danger', 'Disbursement Status not updated!');
                                return $this->redirect('index');
                            }
                        }else{
                            Yii::$app->session->setFlash('danger', 'Failed! please contact admin');
                            return $this->redirect('index');
                        }
                    }
                }else{
                    $tranche_reject->status = 3;
                    $tranche_reject->cheque_no = 0;
                    $tranche_reject->fund_request_id = 0;
                    $tranche_reject->attendance_status = 'info_not_available';
                    $tranche_reject->date_disbursed = 0;
                    $tranche_reject->disbursement_id = 0;
                    if($tranche_reject->save(false)){
                        $model->status = 1;
                        $model->is_verified = 1;
                        $model->verified_by = Yii::$app->user->getId();
                        $model->verfied_at = strtotime('now');
                        if($model->save()){
                            if($tranche_reject->tranch_no == 1){
                                $loan->date_disbursed = 0;
                                $loan->save(false);
                            }
                            $modelDisbursementDetail = DisbursementDetails::find()
                                ->where(['id' => $model->disbursement_detail_id])
                                ->one();
                            $modelDisbursementDetail->status=2;
                            if($modelDisbursementDetail->save()){
                                Yii::$app->session->setFlash('success', 'Temporary Disbursement Rejected Successfully!');
                                return $this->redirect('index');
                            }else{
                                Yii::$app->session->setFlash('danger', 'Disbursement Status not updated!');
                                return $this->redirect('index');
                            }
                        }else{
                            Yii::$app->session->setFlash('success', 'Temporary Disbursement Rejected Status not updated!');
                            return $this->redirect('index');
                        }
                    }else{
                        Yii::$app->session->setFlash('danger', 'Failed! please contact admin');
                        return $this->redirect('index');
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('pending', 'Temporary Disbursement Rejection is under review!.');
            return $this->redirect(['index']);
        }
    }

    /**
     * Deletes an existing TemporaryDisbursementRejected model.
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
        $model->deleted = 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the TemporaryDisbursementRejected model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TemporaryDisbursementRejected the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TemporaryDisbursementRejected::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
