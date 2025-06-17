<?php

namespace frontend\controllers;

use common\components\Helpers\AcagHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\StructureHelper;
use common\components\RbacHelper;
use common\models\Applications;
use common\models\DisbursementDetails;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Operations;
use common\models\search\LoansSearch;
use kartik\form\ActiveForm;
use Ratchet\NullComponent;
use Yii;
use common\models\Disbursements;
use common\models\search\DisbursementsSearch;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\filters\AccessControl;

/**
 * DisbursementsController implements the CRUD actions for Disbursements model.
 */
class DisbursementsController extends Controller
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
     * Lists all Disbursements models.
     * @return mixed
     */
    public function actionDisbursements()
    {
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('disbursements', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Lists all Disbursements models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = array_keys($_GET['DisbursementsSearch']);
            $groups = array();
            $searchModel = new DisbursementsSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilter($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['region_id'] = isset($g->region->name)?$g->region->name:'';
                $groups[$i]['area_id'] =isset($g->area->name)?$g->area->name:'';
                $groups[$i]['branch_id'] = isset($g->branch->name)?$g->branch->name:'';
                $groups[$i]['disbursement_date'] = date('Y-m-d',$g['date_disbursed']);
                $groups[$i]['venue'] = $g['venue'];
                $i++;
            }
            ExportHelper::ExportCSV('Disbursements.csv',$headers,$groups);
            die();
        }
        $searchModel = new DisbursementsSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions_by_id,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
        ]);
    }
    /**
     * Displays a single Applications model.
     * @param integer $id
     * @return mixed
     */
    public function actionDisbursementDetails($id)
    {
        $this->layout = 'main_simple_js';
        return $this->render('disbursementDetails', [
            'model' => LoanTranches::find()->where(['disbursement_id'=>$id])->all(),
        ]);
    }

    /**
     * Displays a single Disbursements model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $disbursement=$this->findModel($id);
        $disb_amount=0;
        foreach($disbursement->tranches as $loan){
            $disb_amount+=$loan->tranch_amount;
        }
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Disbursements #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'disb_amount'=>$disb_amount
            ]);
        }
    }

    /**
     * Creates a new Disbursements model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   /* public function actionValidate()
    {
        $model = new Disbursements();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }*/

    public function actionSaveDisbursement()
    {
        $request = Yii::$app->request;
        $model = new Disbursements();
        $response = array();
        if($model->load($request->post())){
            if($model->save()){
                $response['status_type']="success";
                $response['data']['id']=$model->id;
                $response['data']['date_disbursed']=$model->date_disbursed;
            }else{
                $response['status_type']="error";
                $response['errors']=$model->getErrors();
            }
        }
        return json_encode($response);
    }
    public function actionSaveDisbursementLoans($id)
    {

        $request = Yii::$app->request;
        $model = LoanTranches::findOne($id);
        $response = array();
        $operation = new Operations();

        if (!empty($model)) {

            if ($model->load($request->post())) {
                if ($model->status == 6) {
                    $transaction = Yii::$app->db->beginTransaction();

                    if ($model->save()) {

                        $operation->load($request->post());
                        if (isset($request->post()['Operations']['credit']) && !empty($request->post()['Operations']['credit']) && isset($request->post()['Operations']['receipt_no']) && !empty($request->post()['Operations']['receipt_no'])) {

                            $operation->receive_date = strtotime($operation->receive_date);
                            if ($operation->save()) {
                                $loan_update=Loans::findOne($model->loan_id);
                                $loan_update->status='collected';
                                if($model->tranch_no==1){
                                    $loan_update->date_disbursed=strtotime('now');
                                }
                                $loan_tranches = LoanTranches::find()
                                    ->where(['loan_id' => $model->loan_id])
                                    ->andWhere(['status' => 6])
                                    ->all();
                                $amount_sum = 0;
                                foreach ($loan_tranches as $key => $amount) {
                                    $amount_sum += $amount->tranch_amount;
                                }

                                $AmountDisbursed = $amount_sum;

                                $loan_update->disbursed_amount=$AmountDisbursed;
                                $loan_update->save();

                                ActionsHelper::updateAction('loan',$loan_update->id,'takaful');
                                $transaction->commit();
                                ActionsHelper::updateAction('tranche',$model->id,'disbursement');
                                if($model->tranch_no==1){
                                    DisbursementHelper::GenerateSchedule($loan_update);
                                }else{
                                    FixesHelper::ledger_regenerate($loan_update);
                                }
                                //FixesHelper::update_loan_expiry($model);

                                $response['status_type'] = "success";
                                $response['data']['message'] = "Saved";
                            }
                            else{
                                $transaction->rollBack();
                                $response['status_type'] = "error";
                                $response['errors'] = $operation->getErrors();
                            }
                        }
                        else{
                            $transaction->commit();
                            $loan_update=Loans::findOne($model->loan_id);
                            $loan_update->status='collected';
                            if($model->tranch_no==1){
                                $loan_update->date_disbursed=strtotime('now');
                            }
                            $loan_update->disbursed_amount=$loan_update->disbursed_amount+$model->tranch_amount;
                            $loan_update->save();
                            if($model->tranch_no==1){
                                DisbursementHelper::GenerateSchedule($loan_update);
                            }else{
                                FixesHelper::ledger_regenerate($loan_update);
                            }
                            //FixesHelper::update_loan_expiry($model);
                            ActionsHelper::updateAction('tranche',$model->id,'disbursement');
                            $response['status_type'] = "success";
                            $response['data']['message'] = "Saved";
                        }
                    }
                } else {
                    $model->disbursement_id = 0;
                    $model->date_disbursed = 0;
                    if ($model->save()) {
                        $loan_update=Loans::findOne($model->loan_id);
                        $loan_update->status='not collected';
                        $loan_update->save();
                        ActionsHelper::updateAction('tranche',$model->id,'disbursement');
                        $response['status_type'] = "success";
                        $response['data']['message'] = "Saved";
                    }
                }
            } else {
                $response['status_type'] = "error";
                $response['errors'] = $model->getErrors();
            }
        } else {
            $response['status_type'] = "error";
            $response['errors'] = "Record not found";
        }
        return json_encode($response);
    }

    public function actionCreate()
    {
        if(Yii::$app->request->get()) {
            //$request = Yii::$app->getRequest();
            $model = new Disbursements();
            $model->load(Yii::$app->request->get());
            $loans_search = new LoansSearch();
            $region_area=StructureHelper::getRegionAreaFromBranch($model->branch_id);
            $model->region_id = $region_area['region_id'];
            $model->area_id = $region_area['area_id'];
            $loans = $loans_search->search_dibursement_list(Yii::$app->request->queryParams,$model->branch_id);
            $pages = new Pagination(['totalCount' => $loans->getTotalCount(), 'pageSize' => $loans->getCount()]);
            // Yii::$app->Permission->getSearchFilter($loans,'loans',  Yii::$app->controller->action->id,$this->rbac_type);
            $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

            return $this->render('create', [
                'model' => $model,
                'loans' => $loans,
                'pagination' => $pages,
                'regions_by_id' => $regions_by_id,
                'loans_search' => $loans_search,
                'branches'=>$branches
            ]);
        }
        else{
            //$request = Yii::$app->getRequest();
            $model = new Disbursements();
            $loans_search = new LoansSearch();
            $loans = $loans_search->search_dibursement_list(Yii::$app->request->queryParams,$model->branch_id);
            $pages = new Pagination(['totalCount' => $loans->getTotalCount(), 'pageSize' => $loans->getCount()]);
            // Yii::$app->Permission->getSearchFilter($loans,'loans',  Yii::$app->controller->action->id,$this->rbac_type);
            $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

            return $this->render('create', [
                'model' => $model,
                'loans' => $loans,
                'pagination' => $pages,
                'regions_by_id' => $regions_by_id,
                'loans_search' => $loans_search,
                'branches'=>$branches
            ]);
        }
    }

    /**
     * Updates an existing Disbursements model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Disbursements #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Disbursements #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Disbursements #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
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
     * Delete an existing Disbursements model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing Disbursements model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }
    public function actionAttendanceDisb()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 500);
        if(Yii::$app->request->get()) {
            //$request = Yii::$app->getRequest();
            $model = new Disbursements();
            $model->load(Yii::$app->request->get());
            $loans_search = new LoansSearch();
            $region_area=StructureHelper::getRegionAreaFromBranch($model->branch_id);
            $model->region_id = $region_area['region_id'];
            $model->area_id = $region_area['area_id'];
            $loans = $loans_search->search_dibursement_list_attendence(Yii::$app->request->queryParams,$model->branch_id);

            $pages = new Pagination(['totalCount' => $loans->getTotalCount(), 'pageSize' => $loans->getCount()]);
            // Yii::$app->Permission->getSearchFilter($loans,'loans',  Yii::$app->controller->action->id,$this->rbac_type);
            $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            return $this->render('attendence_disbursement_group', [
                'model' => $model,
                'loans' => $loans,
                'pagination' => $pages,
                'regions_by_id' => $regions_by_id,
                'loans_search' => $loans_search,
                'branches'=>$branches
            ]);
        }
        else{
            //$request = Yii::$app->getRequest();
            $model = new Disbursements();
            $loans_search = new LoansSearch();
            //$loans = $loans_search->search_dibursement_list_attendence(Yii::$app->request->queryParams,$model->branch_id);
            //$pages = new Pagination(['totalCount' => $loans->getTotalCount(), 'pageSize' => $loans->getCount()]);
            // Yii::$app->Permission->getSearchFilter($loans,'loans',  Yii::$app->controller->action->id,$this->rbac_type);
            $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            return $this->render('attendence_disbursement_group', [
                'model' => $model,
                //'loans' => $loans,
                //'pagination' => $pages,
                'regions_by_id' => $regions_by_id,
                'loans_search' => $loans_search,
                'branches'=>$branches
            ]);
        }
    }
    public function actionSaveAttendanceLoans($id)
    {
        $request = Yii::$app->request;
        $model = Loans::findOne($id);
        $tranch = LoanTranches::find()->where(['id'=>$request->post()['LoanTranches']['id']])->one();
        $tranch->load($request->post());
        if($tranch->attendance_status=='present'){
            $tranch->status=5;
            $tranch->save();
            /*if($tranch->tranch_no==1 && $model->status=='not collected'){
                $model->status='pending';
                $model->save();
            }*/
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
            $response['data']['status'] = "collected";
            $response['data']['group'] = isset($model->group->grp_no)?$model->group->grp_no:'';
        }
        else if($tranch->attendance_status=='absent'){
            if($tranch->tranch_no==1){
                $model->status='not collected';
                $model->save();
                $result=LoanHelper::absentLoan($tranch);
            }
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
            $response['data']['status'] = "not collected";
        }
        return json_encode($response);
    }
    public function actionAddDisburse()
    {
        $response=[];
        $loan_ids=[];
        $group_no='';
        $request=Yii::$app->request->post();
        foreach($request['Loans'] as $loan){
            $loan=Loans::find()->where(['id'=>$loan['id']])->andWhere(['not in','status',['not collected'/*,'collected'*/]])->one();
            $tranch=LoanTranches::find()->where(['loan_id'=>$loan['id'],'attendance_status'=>'present','status'=>5])->one();
            if(!empty($tranch)){
                $group_no=$tranch->loan->group->grp_no;
                $tranch->status=7;
                $tranch->save();
                array_push($loan_ids,$tranch->loan_id);
            }
        }
        $response['status_type'] = "success";
        $response['data']['message'] = "Saved";
        $response['data']['loan_ids'] = $loan_ids;
        $response['data']['group'] = $group_no;
        return json_encode($response);
    }
    public function actionSaveDisbursementAll()
    {
        $request = Yii::$app->request;
        $model = new Disbursements();
        $response = array();
        if($model->load($request->post())){
            if($model->save()){
                $tranches=LoanTranches::find()
                    ->join('inner join','loans','loans.id=loan_tranches.loan_id')
                    ->where(['loan_tranches.disbursement_id' => 0])
                    ->andWhere(['loans.deleted' => 0, 'loan_tranches.status' => 7])
                    //->andWhere(['loans.status' => 'processed'])
                    ->andWhere(['loans.branch_id' => $model->branch_id])->all();
                foreach ($tranches as $tranch){
                    $payment_method=isset($tranch->payment->payment_method_id)?$tranch->payment->payment_method_id:1;
                    if(!in_array($payment_method,StructureHelper::ChequeFlow())/* && !in_array($tranch->loan->project_id,[52,61,62,60])*/){
                        $tranch->status='8';
                        $tranch->disbursement_id=$model->id;
                        $tranch->date_disbursed=$model->date_disbursed;
                        $loan_update=Loans::findOne($tranch->loan_id);
                        if($tranch->tranch_no==1){
                            $loan_update->date_disbursed=$model->date_disbursed;
                        }
                        //$loan_update->disbursed_amount=$loan_update->disbursed_amount+$tranch->tranch_amount;
                        $loan_update->save();
                        $tranch->save();
                        ActionsHelper::updateAction('tranche',$tranch->id,'disbursement');
                    }else{
                        $tranch->status='6';
                        $tranch->disbursement_id=$model->id;
                        $tranch->date_disbursed=$model->date_disbursed;
                        $tranch->save();
                        $loan_update=Loans::findOne($tranch->loan_id);
                        $applicationCheck = Applications::find()->where(['id'=>$loan_update->application_id])->one();

                        if(!empty($applicationCheck) && $applicationCheck!=null && $applicationCheck->project_id==108){
                            $loan_update->status='grant';
                        }else{
                            $loan_update->status='collected';
                        }

                        if($tranch->tranch_no==1){
                            $loan_update->date_disbursed=$model->date_disbursed;
                        }
                        $loan_tranches = LoanTranches::find()
                            ->where(['loan_id' => $tranch->loan_id])
                            ->andWhere(['status' => 6])
                            ->all();
                        $amount_sum = 0;
                        foreach ($loan_tranches as $key => $amount) {
                            $amount_sum += $amount->tranch_amount;
                        }

                        $AmountDisbursed = $amount_sum;

                        $loan_update->disbursed_amount=$AmountDisbursed;
                        $loan_update->save();
                        ActionsHelper::updateAction('tranche',$tranch->id,'disbursement');
                        if($loan_update->status != 'grant'){
                            if($tranch->tranch_no==1){
                                DisbursementHelper::GenerateSchedule($loan_update);
                                FixesHelper::update_loan_expiry($loan_update);
                            }else{
                                FixesHelper::ledger_regenerate($loan_update);
                            }
                        }
                    }
                }
                $response['status_type']="success";
                $response['data']['id']=$model->id;
                $response['data']['date_disbursed']=$model->date_disbursed;
            }else{
                $response['status_type']="error";
                $response['errors']=$model->getErrors();
            }
        }
        return json_encode($response);
    }
    public function actionPublishLoan()
    {
        $request=Yii::$app->request;

        if (isset($request->post()['export']) && $request->post()['export'] == 'export') {
            $this->layout = 'csv';
            $headers = ['Full Name','Cnic','Parentage','Tranche Amount','Sanction No','Grp No','Project','Bank Name','Account Title','Account No'];
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->search_publish_loan_list($_GET,true);
            //Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['full_name'] = isset($g['loan']['application']['member']['full_name'])?$g['loan']['application']['member']['full_name']:'';
                $groups[$i]['cnic'] = isset($g['loan']['application']['member']['cnic'])?$g['loan']['application']['member']['cnic']:'';
                $groups[$i]['parentage'] = isset($g['loan']['application']['member']['parentage'])?$g['loan']['application']['member']['parentage']:'';
                $groups[$i]['tranch_amount'] = isset($g['tranch_amount'])?$g['tranch_amount']:'';
                $groups[$i]['sanction_no'] = isset($g['loan']['sanction_no'])?$g['loan']['sanction_no']:'';
                $groups[$i]['grp_no'] = isset($g['loan']['group']['grp_no'])?$g['loan']['group']['grp_no']:'';
                $groups[$i]['project'] = isset($g['loan']['project']['name'])?$g['loan']['project']['name']:'';
                $groups[$i]['bank_name'] = isset($g['loan']['application']['member']['memberAccount']['bank_name'])?$g['loan']['application']['member']['memberAccount']['bank_name']:'';
                $groups[$i]['title'] = isset($g['loan']['application']['member']['memberAccount']['title'])?$g['loan']['application']['member']['memberAccount']['title']:'';
                $groups[$i]['Account No'] = isset($g['loan']['application']['member']['memberAccount']['account_no'])?"'".$g['loan']['application']['member']['memberAccount']['account_no']."'":'';
                $i++;
            }
            ExportHelper::ExportCSV('Publish Loans.csv',$headers,$groups);
            die();
        }else if($request->post() && isset($request->post()['selection']) ){
            foreach ($request->post()['selection'] as $id){
                $tranch=LoanTranches::findOne($id);
                $loansModel =  $tranch->loan;
                $disb_Detail=DisbursementDetails::find()->where(['tranche_id'=>$tranch->id])->andWhere(['deleted'=>0])->orderBy('id desc')/*->andWhere(['in','status',[0,1,3]])*/->one();
                if(empty($disb_Detail) && $disb_Detail == null){
                    $disb_Detail=new DisbursementDetails();
                    $disb_Detail->tranche_id=$tranch->id;
                    $disb_Detail->payment_method_id=isset($tranch->payment->payment_method_id)?$tranch->payment->payment_method_id:0;
                    $disb_Detail->account_no=$tranch->loan->application->member->memberAccount->account_no;
                    $disb_Detail->bank_name=$tranch->loan->application->member->memberAccount->bank_name;
                    $disb_Detail->transferred_amount=$tranch->tranch_amount;
                    $disb_Detail->disbursement_id=$tranch->disbursement_id;
                    $disb_Detail->tranche_id=$tranch->id;
                    if($disb_Detail->save()){
                        if($loansModel->project_id == 132 && $tranch->tranch_no == 1){
                            $status = 'Loan Approved';
                            $statusReason = 'Loan Approved';
                            AcagHelper::actionPush($loansModel->application,$status,$statusReason,$loansModel->loan_amount,date('Y-m-d'),0,$loansModel);
                        }
                    }
                }elseif (!empty($disb_Detail) && $disb_Detail->status==2){
                    $disb_Detail->deleted=1;
                    if($disb_Detail->save()){
                        $disb_Detail=new DisbursementDetails();
                        $disb_Detail->tranche_id=$tranch->id;
                        $disb_Detail->payment_method_id=isset($tranch->payment->payment_method_id)?$tranch->payment->payment_method_id:0;
                        $disb_Detail->account_no=$tranch->loan->application->member->memberAccount->account_no;
                        $disb_Detail->bank_name=$tranch->loan->application->member->memberAccount->bank_name;
                        $disb_Detail->transferred_amount=$tranch->tranch_amount;
                        $disb_Detail->disbursement_id=$tranch->disbursement_id;
                        $disb_Detail->tranche_id=$tranch->id;
                        if($disb_Detail->save()){
                            if($loansModel->project_id == 132 && $tranch->tranch_no == 1){
                                $status = 'Loan Approved';
                                $statusReason = 'Loan Approved';
                                AcagHelper::actionPush($loansModel->application,$status,$statusReason,$loansModel->loan_amount,date('Y-m-d'),0,$loansModel);
                            }
                        }
                    }
                }

            }
            $session = Yii::$app->session;
            $session->addFlash('success', 'Selected Loans published successfully.');
        }
        $searchModel = new LoansSearch();
        if(!isset(Yii::$app->request->queryParams['LoansSearch']['branch_id']) || empty(Yii::$app->request->queryParams['LoansSearch']['branch_id'])){
            $dataProvider=[];
        }else {
            $dataProvider = $searchModel->search_publish_loan_list(Yii::$app->request->queryParams);
        }
        //Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        return $this->render('publish-loan/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }
    /**
     * Finds the Disbursements model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Disbursements the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Disbursements::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
