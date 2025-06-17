<?php

namespace frontend\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\FundRequestHelper;
use common\components\Helpers\StructureHelper;
use common\models\FundRequestsDetails;
use common\models\GroupActions;
use common\models\Loans;
use common\models\LoansDisbursement;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Model;
use common\models\Projects;
use common\models\search\LoansSearch;
use common\models\search\LoanTranchesSarch;
use common\models\search\LoanTranchesSearch;
use common\models\Users;
use phpDocumentor\Reflection\Project;
use Yii;
use common\models\FundRequests;
use common\models\search\FundRequestsSearch;

use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\filters\AccessControl;

/**
 * FundRequestsController implements the CRUD actions for FundRequests model.
 */
class FundRequestsController extends Controller
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
     * Lists all FundRequests models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FundRequestsSearch();
        $key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $count = $searchModel->searchCount(Yii::$app->request->queryParams);
        /*$dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        Yii::$app->Permission->getSearchFilterQuery($count, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $count_as_status = array_column($count->asArray()->all(), 'count(fund_requests.id)','status'  );
        /*if(Yii::$app->user->identity->role->item_name == 'RA')
        {
            $dataProvider->query->andFilterWhere(["fund_requests.status" => 'approved']);
        }*/
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$areas = Yii::$app->Permission->getAreaListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$branches = Yii::$app->Permission->getBranchListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'count_as_status' => $count_as_status,
            'regions' => $regions
            //'areas' => $areas,
            //'branches' => $branches,
        ]);
    }

    public function actionUpdateStatus($id,$status)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->status = $status;
        $model->approved_by = Yii::$app->user->getId();
        $model->approved_on = time();
        if($model->save()) {
            if($status=='rejected') {
                $tranches = LoanTranches::find()
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->andFilterWhere(['loan_tranches.fund_request_id' => $model->id])
                    ->all();
                foreach ($tranches as $t) {
                    $t->fund_request_id = 0;
                    $t->status = 3;
                    $t->save();
                    $actions = LoanTranchesActions::find()->where(['parent_id' => $t->id])->andWhere(['in', 'action', ['fund_request', 'cheque_printing']])->all();
                    foreach ($actions as $act) {
                        $act->status = 0;
                        $act->save();
                    }
                }
            }
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionApproval($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "",
                    'content'=>$this->renderAjax('_form_approval', [
                        'model' => $model,
                    ]),
                ];
            } else if($model->load($request->post()) ) {
                if($model->save()) {
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Update Status",
                        'content'=>'<span class="text-success"> Status Updated Successfully. </span>',
                    ];
                }
                else {
                    return [
                        'title'=> "",
                        'content'=>$this->renderAjax('_form_approval', [
                            'model' => $model,
                        ]),
                    ];
                }
            } else {
                return [
                    'title'=> "",
                    'content'=>$this->renderAjax('_form_approval', [
                        'model' => $model,
                    ]),
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())){
                if( $model->save()) {
                    return $this->redirect(['index']);
                } else {
                    return $this->render('_form_approval', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('_form_approval',[
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Displays a single FundRequests model.
     * @param integer $id
     * @return mixed
     */
    public function actionProcessed($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $params= Yii::$app->request->queryParams;
        $params['LoanTranchesSearch']['fund_request_id']=$id;
        $searchModeLoans=new LoanTranchesSearch();
        $loans=$searchModeLoans->search($params);
        $fund_req_detail=FundRequestsDetails::find()->where(['fund_request_id'=>$model->id])->all();
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects=ArrayHelper::map(Projects::find()->all(),'name','name');
        /*if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "FundRequests #" . $id,
                'content' => $this->renderAjax('show', [
                    'model' => $this->findModel($id),
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects,
                    'fund_request_detail'=>$fund_req_detail
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {*/

            $fund_request_detail = array();
            if ($model->load($request->post())) {

                $flag = false;
                $total_approved_amount = 0;
                //$models_fund_request_detail = Model::createMultiple(FundRequestsDetails::classname());
                //Model::loadMultiple($models_fund_request_detail, Yii::$app->request->post()['FundRequestsDetails']);
                foreach (Yii::$app->request->post()['FundRequestsDetails'] as $f) {
                    $f2 = FundRequestsDetails::findOne($f['id']);
                    $f2->status = $f['status'];
                    if($f2->status=='fund available'){
                        $f2->cheque_no = $f['cheque_no'];
                        $f2->payment_method_id = $f['payment_method_id'];
                        $f2->total_approved_amount = $f['total_requested_amount'];
                        $total_approved_amount +=  $f2->total_approved_amount;
                        $tranches_payment=LoanTranches::find()
                            ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                            ->andFilterWhere(['loan_tranches.fund_request_id'=>$model->id])
                            ->andFilterWhere(['loans.project_id'=>$f2->project_id])->all();
                        foreach ($tranches_payment as $t){
                            $pay=LoansDisbursement::find()->where(['tranche_id'=>$t->id])->one();
                            if(empty($pay)){
                                $pay=new LoansDisbursement();
                            }
                            $pay->loan_id=$t->loan_id;
                            $pay->tranche_id=$t->id;
                            $pay->payment_method_id=  $f2->payment_method_id;
                            $pay->save();
                            if(!in_array($pay->payment_method_id,StructureHelper::ChequeFlow())){
                                $t->cheque_no='1234';
                                $t->cheque_date=time();
                                $t->save();
                                $action=LoanTranchesActions::find()->where(['parent_id'=>$t->id])->andWhere(['in','action',['cheque_printing']])->one();
                                $action->status=1;
                                $action->save();
                            }
                        }
                    }
                    else{
                        $f2->status='fund not available';
                        $tranches=LoanTranches::find()
                            ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                            ->andFilterWhere(['loan_tranches.fund_request_id'=>$model->id])
                            ->andFilterWhere(['loans.project_id'=>$f2->project_id])->all();
                        foreach ($tranches as $t){
                            $t->fund_request_id=0;
                            $t->status=3;
                            $t->save();
                            $actions=LoanTranchesActions::find()->where(['parent_id'=>$t->id])->andWhere(['in','action',['fund_request','cheque_printing']])->all();
                            foreach ($actions as $act){
                                $act->status=0;
                                $act->save();
                            }
                        }
                    }
                    if ($f2->save()) {
                        $flag = true;
                    } else {
                        return $this->render('processed', [
                            'model' => $this->findModel($id),
                            'searchModelLoans'=>$searchModeLoans,
                            'dataProviderLoans'=>$loans,
                            'projects'=>$projects,
                            'fund_request_detail'=>$fund_req_detail,
                            'errors'=>$f2

                        ]);
                    }
                }
                if ($flag) {
                    /*echo '<pre>';
                    print_r($model);
                    die('here');*/
                    $model->approved_amount = $total_approved_amount;
                    if ($model->save()) {
                        /*$loans = Loans::find()->where(['fund_request_id'=>$model->id])->all();
                        foreach ($loans as $loan){
                            $loan->status = 'processed';
                        }*/
                        return $this->redirect(['show', 'id' => $model->id]);
                    } else {
                        return $this->render('processed', [
                            'model' => $model,
                            'searchModelLoans'=>$searchModeLoans,
                            'dataProviderLoans'=>$loans,
                            'projects'=>$projects,
                            'fund_request_detail'=>$fund_req_detail

                        ]);
                       //print_r($model->getErrors());
                        //die();
                    }
                }

            } else {
                return $this->render('processed', [
                    'model' => $model,
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects,
                    'fund_request_detail'=>$fund_req_detail

                ]);
            }
        //}
    }

    /**
     * Displays a single FundRequests model.
     * @param integer $id
     * @return mixed
     */
    public function actionRemoveLoan($id)
    {
        $request = Yii::$app->request->post();
        if($request){
            $tranche=LoanTranches::find()->where(['id'=>$request['LoanTranches']['id']])->one();
            $transaction = Yii::$app->db->beginTransaction();
            $fund_request=FundRequests::find()->where(['id'=>$id])->one();
            $fund_request->requested_amount=$fund_request->requested_amount-$tranche->tranch_amount;
            $fund_request->total_loans=$fund_request->total_loans-1;
            $fund_request_detail=FundRequestsDetails::find()->where(['project_id'=>$tranche->loan->project_id,'fund_request_id'=>$fund_request->id])->one();
            $fund_request_detail->total_requested_amount=$fund_request_detail->total_requested_amount-$tranche->tranch_amount;
            $fund_request_detail->total_loans=$fund_request_detail->total_loans-1;
            $tranche->status=3;
            $tranche->fund_request_id=0;
            if ($fund_request->save()) {
                if ($fund_request_detail->save()) {
                    if ($tranche->save()) {
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } else {
                    $transaction->rollBack();
                }
            } else {
                $transaction->rollBack();
            }
        }
        return $this->redirect(['view','id'=>$id]);
    }
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $params= Yii::$app->request->queryParams;
        $params['LoanTranchesSearch']['fund_request_id']=$id;
        $searchModeLoans=new LoanTranchesSearch();
        $loans=$searchModeLoans->search($params);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$projects=ArrayHelper::map(Projects::find()->all(),'name','name');
       /* if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "FundRequests #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            if ($model->load($request->post())) {
                if($model->status=='rejected'){
                    $tranches=LoanTranches::find()
                        ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                        ->andFilterWhere(['loan_tranches.fund_request_id'=>$model->id])->all();
                    foreach ($tranches as $t){
                        $t->fund_request_id=0;
                        $t->status=3;
                        $t->save();
                        $actions=LoanTranchesActions::find()->where(['parent_id'=>$t->id])->andWhere(['in','action',['fund_request','cheque_printing']])->all();
                        foreach ($actions as $act){
                            $act->status=0;
                            $act->save();
                        }
                    }
                }
                if ($model->save()) {
                    return $this->redirect(['view',
                        'id' => $model->id,
                        'searchModelLoans'=>$searchModeLoans,
                        'dataProviderLoans'=>$loans,
                        'projects'=>$projects
                    ]);
                }
            } else {*/
                return $this->render('view', [
                    'model' => $model,
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects
                ]);
            //}
        //}
    }

    /**
     * Creates a new FundRequests model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new FundRequests();
        $fund_requests_details = array();
        $fund_request_detail = array();
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new FundRequests",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'regions' => $regions,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new FundRequests",
                    'content' => '<span class="text-success">Create FundRequests success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new FundRequests",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'regions' => $regions,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */


            $params = Yii::$app->request->queryParams;
            if (isset($params['FundRequests'])) {
                $model->load($request->get());
                $fund_request_detail = FundRequestHelper::getFundRequest($params['FundRequests']['branch_id']);

            }
            foreach ($fund_request_detail as $d) {
                $fund_requests_details[] = new FundRequestsDetails();
            }

            if ($model->load($request->post())) {
                Model::loadMultiple($fund_requests_details, Yii::$app->request->post());
                if ($model->save()) {
                    foreach ($fund_requests_details as $f) {
                        $f->fund_request_id = $model->id;
                        $f->status='pending';
                        if ($f->save()) {

                        }
                    }
                    //$loans = Loans::find()->where(['fund_request_id' => 0,'status' => "approved", 'branch_id' => $model->branch_id])/*->andFilterWhere(['!=', 'cheque_no', 0])*/->all();
                    $loans = LoanTranches::find()
                        ->joinWith('loan')
                        ->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
                        //->where(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
                        //->where(['in','loans.project_id',StructureHelper::trancheProjects()])
                        ->where(['in','loans.status' ,["pending","collected"]])
                        ->andWhere(['loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0,'loan_tranches.status' => 4, 'loans.branch_id' => $model->branch_id])->all();
                    /*$loans = LoanTranches::find()
                        ->joinWith('loan')
                        ->where(['in','loans.status' ,["pending","collected"]])
                        ->andWhere(['in','loan_tranches.platform' ,[0,1]])
                        ->andWhere(['loan_tranches.fund_request_id' => 0,'loan_tranches.status' => 4, 'loans.branch_id' => $model->branch_id])
                        ->where(['in','loans.project_id',StructureHelper::trancheProjects()])
                        ->orWhere(['and',['=','loans.platform',2],['in','loans.status' ,["pending","collected"]],['loan_tranches.status' => 4, 'loans.branch_id' => $model->branch_id,'loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0]])
                        ->all();*/

                    foreach ($loans as $l) {
                        $l->fund_request_id = $model->id;
                        if ($l->save()) {
                            ActionsHelper::updateAction('tranche',$l->id,'fund_request');
                        } else {
                            print_r($l->getErrors());
                            die();
                        }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                }

            } else {
                return $this->render('create', [
                    'model' => $model,
                    'regions' => $regions,
                    'fund_requests_details' => $fund_requests_details,
                    'fund_request_detail' => $fund_request_detail
                ]);
            }
        }

    }

    /**
     * Updates an existing FundRequests model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update FundRequests #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "FundRequests #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update FundRequests #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
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
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing FundRequests model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
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
     * Delete multiple existing FundRequests model.
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
     * Finds the FundRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FundRequests::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionShow($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $params= Yii::$app->request->queryParams;
        $params['LoanTranchesSearch']['fund_request_id']=$id;
        $searchModeLoans=new LoanTranchesSearch();
        $loans=$searchModeLoans->search($params);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$projects = ArrayHelper::map(Projects::find()->all(),'id','name');

        /*if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "FundRequests #" . $id,
                'content' => $this->renderAjax('show', [
                    'model' => $this->findModel($id),
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {*/
            if ($model->load($request->post())) {
                if ($model->save()) {
                    return $this->redirect(['show',
                        'id' => $model->id,
                        'searchModelLoans'=>$searchModeLoans,
                        'dataProviderLoans'=>$loans,
                        'projects'=>$projects
                    ]);
                }
            } else {
                return $this->render('show', [
                    'model' => $model,
                    'searchModelLoans'=>$searchModeLoans,
                    'dataProviderLoans'=>$loans,
                    'projects'=>$projects
                ]);
            }
        //}
    }
}
