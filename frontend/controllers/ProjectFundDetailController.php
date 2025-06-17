<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\StructureHelper;
use common\models\DisbursementDetails;
use common\models\Funds;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Projects;
use common\models\Transactions;
use Yii;
use common\models\ProjectFundDetail;
use common\models\search\ProjectFundDetailSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Request;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * ProjectFundDetailController implements the CRUD actions for ProjectFundDetail model.
 */
class ProjectFundDetailController extends Controller
{
    /**
     * @inheritdoc
     */

    public $rbac_type = 'frontend';

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
     * Lists all ProjectFundDetail models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new ProjectFundDetailSearch();

        $bank_names = ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        if (isset($_GET['export_summary']) && $_GET['export_summary'] == 'export_summary') {
            $this->layout = 'csv';
            $headers = ['Batch No', 'Name', 'Disbursement Source', 'Project', 'No Of Loans', 'Fund Batch Amount', 'Allocation Date', 'Transaction Mode', 'Transaction No', 'Received At', 'Status'];
            $groups = array();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['batch_no'] = isset($g['batch_no'])?$g['batch_no']:'';
                $groups[$i]['name'] = isset($g['fund']['name'])?$g['fund']['name']:'';
                $groups[$i]['disbursement_source'] = isset($g['disbursement_source'])?$g['disbursement_source']:'';
                $groups[$i]['project_id'] = isset($g['project']['name'])?$g['project']['name']:'';
                $groups[$i]['no_of_loans'] = isset($g['no_of_loans'])?$g['no_of_loans']:'';
                $groups[$i]['fund_batch_amount'] = isset($g['fund_batch_amount'])?number_format($g['fund_batch_amount']):'';
                $groups[$i]['allocation_date'] = date('Y-m-d',isset($g['allocation_date'])?$g['allocation_date']:0);
                $groups[$i]['txn_mode'] = isset($g['transaction']['txn_mode'])?$g['transaction']['txn_mode']:'';
                $groups[$i]['txn_no'] = isset($g['transaction']['txn_no'])?$g['transaction']['txn_no']:'';
                $groups[$i]['received_at'] = date('Y-m-d',$g['transaction']['received_at']);
                $groups[$i]['status'] = isset($g['status'])?\common\components\Helpers\StatusHelper::projectFundDetailStatus($g['status']):'';

                $i++;
            }
            ExportHelper::ExportCSV('batches_summary.csv',$headers,$groups);
            die();
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $batch_id = $_GET['id'];
            $headers = ['Fund Source Name', 'Batch No', 'Region', 'Area', 'Branch', 'Sanction No', 'CNIC', 'Memeber Name', 'Member Bank', 'Member Account', 'Tranche Amount'];
            $disb_detail = array();

            $query = LoanTranches::find()->where(['batch_id' => $batch_id])->all();
            foreach ($query as $i => $g) {
                $disb_detail[$i]['fund_source'] = isset($g->batch->fund->name) ? $g->batch->fund->name : '';
                $disb_detail[$i]['batch_no'] = isset($g->batch->batch_no) ? $g->batch->batch_no : '';
                $disb_detail[$i]['region'] = isset($g->loan->region) ? $g->loan->region->name : '';
                $disb_detail[$i]['area'] = isset($g->loan->area) ? $g->loan->area->name : '';
                $disb_detail[$i]['branch'] = isset($g->loan->branch) ? $g->loan->branch->name : '';
                $disb_detail[$i]['sanction_no'] = isset($g->loan->sanction_no) ? $g->loan->sanction_no : '';
                $disb_detail[$i]['cnic'] = isset($g->loan->application->member) ? $g->loan->application->member->cnic : '';
                $disb_detail[$i]['full_name'] = isset($g->loan->application->member) ? $g->loan->application->member->full_name : '';
                $disb_detail[$i]['bank_name'] = isset($g->publish->bank_name) ? $g->publish->bank_name : '';
                $disb_detail[$i]['account_no'] = isset($g->publish->account_no) ? number_format($g->publish->account_no) : '';
                $disb_detail[$i]['tranch_amount'] = isset($g->tranch_amount) ? number_format($g->tranch_amount) : '';
            }

            ExportHelper::ExportCSV('Fund Batch Detail', $headers, $disb_detail);
            die();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'projects' => $projects,
        ]);
    }

    public function actionApproveBatch($id){
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "batch Confirmation",
                'content' => $this->renderAjax('batch_confirmation', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

            ];
        }
    }
    public function actionEditLoanBatchNo($id) {

        $model = LoanTranches::find()->where(['id'=> $id])->one();
        $batches_names = ArrayHelper::map(ProjectFundDetail::find()->orderBy('id desc')->asArray()->all(), 'id', 'batch_no');
        //$batches_names = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isAjax) {
            return [
                'title' => "Loan Batch Update",
                'content' => $this->renderAjax('loan_batch_update', [
                    'model' => $model,
                    'batches_names' => $batches_names,
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
            ];
            }
        } if ($model->load($request->post())) {

            $batch_id = $request->post()['LoanTranches']['batch_id'];
            $disb_tranches = DisbursementDetails::find()->where(['tranche_id'=>$id,'deleted'=>0/*,'status'=>6*/])->one();
            if(($disb_tranches->status == 6)){
                $disb_tranches->status = 0;
                $disb_tranches->save(false);
            }

            $remove_loan = ProjectFundDetail::find()->where(['id'=>$model->batch_id])->one();
            $add_loan = ProjectFundDetail::find()->where(['id'=>$batch_id])->one();
            if(isset($remove_loan) && isset($add_loan)) {
                $remove_loan->no_of_loans = $remove_loan->no_of_loans - 1;
                $remove_loan->fund_batch_amount = $remove_loan->fund_batch_amount - $model->tranch_amount;
                $remove_loan->save(false);
                $add_loan->no_of_loans = $add_loan->no_of_loans + 1;
                $add_loan->fund_batch_amount = $add_loan->fund_batch_amount + $model->tranch_amount;
                $add_loan->save(false);
                $model->batch_id = $batch_id;
                //$model->save(false);
                if (!$model->save(false)) {
                    Yii::$app->session->setFlash('error', "Some internal issues occurred while updating, please contact admin!");
                    return $this->redirect(['view?id='.$model->batch_id]);
                }

            } else {
                Yii::$app->session->setFlash('error', "Unable to find batch detail,please contact admin!");
                return $this->redirect(['view?id='.$model->batch_id]);
            }
            return $this->redirect(['view?id='.$model->batch_id]);
        }
    }

    public function actionUpdateBatch()
    {
        $request = Yii::$app->request;
        $batch_no = $request->post()['ProjectFundDetail']['batch_no'];
        $batchModel = ProjectFundDetail::find()->where(['batch_no'=>$batch_no])->one();

        if (!empty($batchModel) && $batchModel != null) {

            $batchModel->status = 1;
            if ($batchModel->save()) {
                $fund = Funds::find()->where(['id'=>$batchModel->fund_id])->one();
                $setTo = $fund->email;
                $message = Yii::$app->mailer->compose('fund-line')
                    ->setFrom('mishelpdesk@akhuwat.org.pk')
                    ->setTo($setTo)
                    ->setSubject('This is system generated email to verify checks.');
                if (!($message->send())) {
                    Yii::$app->session->setFlash('error', "Some internal issue, please contact admin");
                    return $this->redirect(Yii::$app->request->referrer);
                } else {
                    Yii::$app->session->setFlash('success', "Batch Approved successfully!");
                    return $this->redirect(Yii::$app->request->referrer);
                }
            } else {
                Yii::$app->session->setFlash('error', "Some internal issue, please contact admin");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }


    }

    public function actionRejectBatch()
    {
        $request = Yii::$app->request;
        $batch_no = $request->post()['ProjectFundDetail']['batch_no'];
        $batchModel = ProjectFundDetail::find()->where(['batch_no'=>$batch_no])->one();
        if (!empty($batchModel) && $batchModel != null) {

            $batchModel->status = 3;
            if ($batchModel->save()) {
                $fund = Funds::find()->where(['id'=>$batchModel->fund_id])->one();
                if(!empty($fund) && $fund!=null){
                    $fund->fund_received = ($fund->fund_received)-($batchModel->fund_batch_amount);
                    if($fund->save()){
                        Yii::$app->db->createCommand('
                                      UPDATE disbursement_details inner join loan_tranches on loan_tranches.id = disbursement_details.tranche_id SET disbursement_details.status = 0 where disbursement_details.status = 6 and loan_tranches.batch_id = ' . $batchModel->id . '
                                      ')->execute();
                        Yii::$app->db->createCommand('
                                      UPDATE loan_tranches SET batch_id = null where batch_id = ' . $batchModel->id . '
                                      ')->execute();
                        Yii::$app->session->setFlash('success', "Batch Rejected successfully!");
                        return $this->redirect(Yii::$app->request->referrer);
                    }else{
                        Yii::$app->session->setFlash('success', "Batch Rejection failed, please contact admin!");
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }


                $message = Yii::$app->mailer->compose('fund-line')
                    ->setFrom('mishelpdesk@akhuwat.org.pk')
                    ->setTo('asif.itbeam@gmail.com')
                    ->setSubject('This is system generated email to verify checks.');
                if (!($message->send())) {
                    Yii::$app->session->setFlash('error', "Some internal issue, please contact admin");
                    die('here');
                } else {
                    Yii::$app->session->setFlash('success', "Batch Rejected successfully!");
                    return $this->redirect(Yii::$app->request->referrer);
                }
            } else {
                Yii::$app->session->setFlash('error', "Some internal issue, please contact admin");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }


    }

    public function actionExportBatch()
    {

        $searchModel = new ProjectFundDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);

    }

    /**
     * Displays a single ProjectFundDetail model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new ProjectFundDetailSearch();
        $dataProvider = $searchModel->searchList($id,Yii::$app->request->queryParams);

        return $this->render('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        /*$request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Transaction Detail ",
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

            ];
        } else {
            return $this->render('show', [
                'model' => $this->findModel($id),
            ]);
        }*/
    }

    /**
     * Creates a new ProjectFundDetail model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ProjectFundDetail();
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $funds = ArrayHelper::map(Funds::find()->where(['status' => 1])->all(), 'id', 'name');

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new ProjectFundDetail",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'projects' => $projects,
                        'funds' => $funds,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->receive_date = strtotime($model->receive_date);
                $model->status = 1;
                if ($model->save()) {

                } else {
                    print_r($model->getErrors());
                    die();
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new ProjectFundDetail",
                    'content' => '<span class="text-success">Create ProjectFundDetail success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];

            } else {
                return [
                    'title' => "Create new ProjectFundDetail",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'projects' => $projects,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'projects' => $projects,
                ]);
            }
        }

    }
    /**
     * Updates an existing ProjectFundDetail model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $funds = ArrayHelper::map(Funds::find()->where(['status' => 1])->all(), 'id', 'name');

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {

                $fundLine = ArrayHelper::map(Funds::find()->where(['id' => $model->fund_id])->all(), 'id', 'name');
                return [
                    'title' => "Fund Received Detail",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'projects' => $projects,
                        'funds' => $funds,
                        'fundLine' => $fundLine,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {

                //$currentDate  =  strtotime(date('Y-m-d'));
                //$previousDate =  strtotime(date('Y-m-d', strtotime('-30 days')));

                /*if((strtotime($model->received_date) < $previousDate) || (strtotime($model->received_date) > $currentDate)){
                    Yii::$app->session->setFlash('error', "Past 30 and future days are not allowed, please chose between.!");
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'title' => "Fund Detail ",
                        'content' => $this->renderAjax('view', [
                            'model' => $this->findModel($id),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                    ];
                }*/


                $model->status = 2;
                $txn = $request->post()['ProjectFundDetail'];
                
                $extTrnx = Transactions::find()
//                    ->where(['txn_no'=>$txn['txn_no']])
                    ->where(['parent_table'=>'project_fund_detail'])
                    ->andWhere(['parent_id'=>$model->id])
                    ->one();

                if(!empty($extTrnx) && $extTrnx!=null){
                    $extTrnx->parent_id = $model->id;
                    $extTrnx->parent_table = "project_fund_detail";
                    $extTrnx->txn_mode = $txn['txn_mode'];
                    $extTrnx->txn_no = $txn['txn_no'];
                    $extTrnx->received_at = strtotime($model->received_date);
                    if(!$extTrnx->save(false)){
                        print_r($extTrnx->getErrors());
                        die();
                    }
                }else{
                    $transaction = new Transactions();
                    $txn = $request->post()['ProjectFundDetail'];
                    $transaction->parent_id = $model->id;
                    $transaction->parent_table = "project_fund_detail";
                    $transaction->txn_mode = $txn['txn_mode'];
                    $transaction->txn_no = $txn['txn_no'];
                    $transaction->received_at = strtotime($model->received_date);
                    if(!$transaction->save(false)){
                        print_r($transaction->getErrors());
                        die();
                    }
                }


                Yii::$app->db->createCommand('
                                      UPDATE disbursement_details inner join loan_tranches on loan_tranches.id = disbursement_details.tranche_id SET disbursement_details.status = 5 where disbursement_details.status = 6 and loan_tranches.batch_id = ' . $model->id . '
                                      ')->execute();

                if (!$model->save(false)) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'title' => "Fund Detail ",
                        'content' => $this->renderAjax('view', [
                            'model' => $this->findModel($id),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                    ];
                }else{
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'title' => "Fund Detail ",
                        'content' => $this->renderAjax('view', [
                            'model' => $this->findModel($id),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                    ];
                }

            } else {
                $fundLine = ArrayHelper::map(Funds::find()->where(['id' => $model->fund_id])->all(), 'id', 'name');
                return [
                    'title' => "Update Fund Received Detail #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'projects' => $projects,
                        'funds' => $funds,
                        'fundLine' => $fundLine,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $model->status = 2;
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', "Some internal issues occurred while updating, please contact admin!");
                    return $this->redirect(['index']);
                }else{
                    $txn = $request->post()['ProjectFundDetail'];
                    $extTrnx = Transactions::find()->where(['txn_no'=>$txn['txn_no']])
                        ->andWhere(['parent_table'=>'project_fund_detail'])
                        ->andWhere(['parent_id'=>$model->id])
                        ->one();
                    if(!empty($extTrnx) && $extTrnx!=null){
                        $extTrnx->parent_id = $model->id;
                        $extTrnx->parent_table = "project_fund_detail";
                        $extTrnx->txn_mode = $txn['txn_mode'];
                        $extTrnx->txn_no = $txn['txn_no'];
                        $extTrnx->received_at = strtotime($model->received_date);
                        if(!$extTrnx->save(false)){
                            print_r($extTrnx->getErrors());
                            die();
                        }
                    }else{
                        $transaction = new Transactions();
                        $txn = $request->post()['ProjectFundDetail'];
                        $transaction->parent_id = $model->id;
                        $transaction->parent_table = "project_fund_detail";
                        $transaction->txn_mode = $txn['txn_mode'];
                        $transaction->txn_no = $txn['txn_no'];
                        $transaction->received_at = strtotime($model->received_date);
                        if(!$transaction->save(false)){
                            print_r($transaction->getErrors());
                            die();
                        }
                    }
                }

            } else {
                return $this->render('update', [
                    'model' => $model,
                    'projects' => $projects,
                    'funds' => $funds,
                ]);
            }
        }
    }

    /**
     * Delete an existing ProjectFundDetail model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemoveBatch($id)
    {
        $request = Yii::$app->request;
        $loan_tranches = LoanTranches::find()->where(['id'=>$id])->one();
        $disb_tranches = DisbursementDetails::find()->where(['tranche_id'=>$id,'deleted'=>0/*,'status'=>6*/])->one();
        if(($disb_tranches->status == 6)){
            $disb_tranches->status = 0;
            $disb_tranches->save(false);
        }
        $batches = ProjectFundDetail::find()->where(['id'=>$loan_tranches->batch_id])->one();
        if(isset($batches)) {
            $batches->no_of_loans = $batches->no_of_loans - 1;
            $batches->fund_batch_amount = $batches->fund_batch_amount - $loan_tranches->tranch_amount;
            $batches->save(false);

            $loan_tranches->batch_id = null;
            $loan_tranches->save(false);

        }


        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    /**
     * Delete multiple existing ProjectFundDetail model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the ProjectFundDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectFundDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectFundDetail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
