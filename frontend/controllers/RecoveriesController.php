<?php

namespace frontend\controllers;

use common\components\Helpers\CacheHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\RecoveriesHelper;
use common\models\Loans;
use common\models\Model;
use common\models\Projects;
use common\models\Schedules;
use Yii;
use common\models\Recoveries;
use common\models\search\RecoveriesSearch;
use common\components\RbacHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\components\Helpers\StructureHelper;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;
/**
 * RecoveriesController implements the CRUD actions for Recoveries model.
 */
class RecoveriesController extends Controller
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
                    //'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Recoveries models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;

        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {

            $recv_date = date('Y-m-d');
            $params['RecoveriesSearch']['receive_date'] =  date("Y-m-d H:i:s",strtotime("midnight", strtotime(date('d-m-Y')))). ' - ' . date('Y-m-d H:i:s',strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
            //$params['RecoveriesSearch']['receive_date'] = date('Y-m-01', strtotime($recv_date)) . ' - ' . date('Y-m-d');
        }
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $headers=[];
            $this->layout = 'csv';
            for ($i=0;$i<10;$i++){
                array_push($headers,array_keys($_GET['RecoveriesSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new RecoveriesSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();


            foreach ($data as $g){


                $groups[$i]['region_id'] = $g['region']['name'];
                $groups[$i]['area_id'] = $g['area']['name'];
                $groups[$i]['branch_id'] = $g['branch']['name'];
                $groups[$i]['sanction_no'] = $g['loan']['sanction_no'];
                $groups[$i]['member_name'] = $g['application']['member']['full_name'];
                $groups[$i]['member_cnic'] = $g['application']['member']['cnic'];
                $groups[$i]['receive_date'] = date('Y-M-d',($g['receive_date']));
                $groups[$i]['amount'] = $g['amount'];
                $groups[$i]['receipt_no'] = $g['receipt_no'];
                $groups[$i]['project_id'] = $g['project']['name'];
                $i++;
            }

            ExportHelper::ExportCSV('Recovery-Report('.$params['RecoveriesSearch']['receive_date'].').csv',$headers,$groups);
           die();
        }
        $searchModel = new RecoveriesSearch();
        $searchModel->load($params);
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        if(empty(Yii::$app->request->queryParams)){
            $dataProvider = array();
        }else{
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
//        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches'=>$branches,
            'regions'=>$regions,
            'areas'=>$areas,
            'projects'=>$projects,
            // 'months'=>$months,
            //'years'=>$years,

        ]);
    }

    public function actionHousing()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;

        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {

            $recv_date = date('Y-m-d');
            $params['RecoveriesSearch']['receive_date'] =  date("Y-m-d H:i:s",strtotime("midnight", strtotime(date('d-m-Y')))). ' - ' . date('Y-m-d H:i:s',strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
            //$params['RecoveriesSearch']['receive_date'] = date('Y-m-01', strtotime($recv_date)) . ' - ' . date('Y-m-d');
        }
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $headers=[];
            $this->layout = 'csv';
            for ($i=0;$i<11;$i++){
                array_push($headers,array_keys($_GET['RecoveriesSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new RecoveriesSearch();
            $query = $searchModel->searchHousing($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();


            foreach ($data as $g){
                $groups[$i]['region_id'] = $g['region']['name'];
                $groups[$i]['area_id'] = $g['area']['name'];
                $groups[$i]['branch_id'] = $g['branch']['name'];
                $groups[$i]['sanction_no'] = $g['loan']['sanction_no'];
                $groups[$i]['member_name'] = $g['application']['member']['full_name'];
                $groups[$i]['member_cnic'] = $g['application']['member']['cnic'];
                $groups[$i]['receive_date'] = date('Y-M-d',($g['receive_date']));
                $groups[$i]['amount'] = $g['amount'];
                $groups[$i]['charges_amount'] = $g['charges_amount'];
                $groups[$i]['credit_tax'] = $g['credit_tax'];
                $groups[$i]['receipt_no'] = $g['receipt_no'];
                $groups[$i]['project_id'] = $g['project']['name'];
                $i++;
            }

            ExportHelper::ExportCSV('Recovery-Report('.$params['RecoveriesSearch']['receive_date'].').csv',$headers,$groups);
            die();
        }
        $searchModel = new RecoveriesSearch();
        $searchModel->load($params);
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/

        $dataProvider = $searchModel->searchHousing(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(Projects::find()->where(['in','id',[52,61,62,64,67,76,77,83,90,103,97,98,100,109,110,113,118,119,114,127,132,131]])->asArray()->all(), 'id', 'name');
//        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);


        return $this->render('housing/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches'=>$branches,
            'regions'=>$regions,
            'areas'=>$areas,
            'projects'=>$projects,
            // 'months'=>$months,
            //'years'=>$years,

        ]);
    }
    /**
     * Displays a single Recoveries model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Recoveries #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Recoveries model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Recoveries();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Recoveries",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
//                    if (in_array($model->project_id, [77, 78, 79])) {
//                        FixesHelper::addRecoveryForFund($model->id);
//                    }
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Recoveries",
                    'content'=>'<span class="text-success">Create Recoveries success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Recoveries",
                    'content'=>$this->renderAjax('create', [
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
                FixesHelper::addRecoveryForFund($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Recoveries model.
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
                    'title'=> "Update Recoveries #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Recoveries #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Recoveries #".$id,
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
            if ($model->load($request->post())) {

                $model->receive_date = strtotime($model->receive_date);
                /*echo '<pre>';
                print_r($model->save(false));
                print_r($model->getErrors());
                die();*/
                if($model->save(false)){
                    $loan = Loans::findOne(['id'=>$model->loan_id]);
                    FixesHelper::fix_schedules_update($loan);
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    /*print_r($model->getErrors());
                    die();*/
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Recoveries model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionAddBulk($n = 1)
    {
        $request = Yii::$app->request;
        $model = new Recoveries();
        $modelsRecovery = array_fill(0, $n, $model);
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
        } else {
            if (Yii::$app->request->post()) {
                $modelsRecovery = Model::createMultiple(Recoveries::classname());
                Model::loadMultiple($modelsRecovery, Yii::$app->request->post());

                foreach ($modelsRecovery as $modelRecovery) {
                    $modelRecovery->receive_date = strtotime($modelRecovery->receive_date);
                    if ($modelRecovery->save()) {
                        if(in_array($modelRecovery->project_id,[77])){
                            $controller = new \console\controllers\FixesController(Yii::$app->controller->id, Yii::$app);
                            $controller->actionLedgerReGeneratesKppSingle($modelRecovery->loan_id);
                        }
                    } else {

                    }
                }
            }
            return $this->render('add_bulk_recoveries', [
                'modelsRecovery' => (empty($modelsRecovery)) ? [new Recoveries] : $modelsRecovery,
                'branches' => Yii::$app->Permission->getBranchListCodeWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type),
            ]);
        }

    }
    public function actionAddRecoveryTax($n = 1)
    {
        $mesg = '';
        $request = Yii::$app->request;
        $model = new Recoveries();
        $modelsRecovery = array_fill(0, $n, $model);
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
        } else {
            if (Yii::$app->request->post()) {
                $modelsRecovery = Model::createMultiple(Recoveries::classname());
                Model::loadMultiple($modelsRecovery, Yii::$app->request->post());

                foreach ($modelsRecovery as $modelRecovery) {
                    $lastSchedule = Recoveries::find()
                        ->select('recoveries.schedule_id')
                        ->join('inner join', 'loans', 'loans.id=recoveries.loan_id')
                        ->where(['=','loans.sanction_no',$modelRecovery->sanction_no])
                        ->orderBy('recoveries.id desc')
                        ->one();

                    $modelRecovery->receive_date = strtotime($modelRecovery->receive_date);
                    $modelRecovery->schedule_id = $lastSchedule->id;
                    $modelRecovery->amount = 0;

                    if ($modelRecovery->save()) {
//                        $mesg = 'Tax save successfully.';
                    }
                }

//                if(!empty($mesg)){
//                    Yii::$app->session->setFlash('success', "Tax save successfully.");
//                }
            }
            return $this->render('add_recoveries_tax', [
                'modelsRecovery' => (empty($modelsRecovery)) ? [new Recoveries] : $modelsRecovery,
                'branches' => Yii::$app->Permission->getBranchListCodeWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type),
            ]);
        }

    }
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
    
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if(!in_array($model->source, ['branch','1','cc']))
        {
            $model->addError('source','You are not allowed to delete bank recoveries.');
        }
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if($model->save(false)){
            /*echo '<pre>';
            print_r($model->getErrors());
            die();*/
            $loan = Loans::findOne(['id'=>$model->loan_id]);
            FixesHelper::fix_schedules_update($loan);
//            if(in_array($model->project_id,[77,78,79])){
//                FixesHelper::deleteRecoveryForFund($id);
//            }
        }
        /*echo '<pre>';
        print_r($model->getErrors());
        die();*/
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
     * Delete multiple existing Recoveries model.
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
            $model->deleted = 1;
            $model->deleted_by = Yii::$app->user->getId();
            $model->deleted_at = strtotime(date('Y-m-d'));
            $model->save();
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

    /**
     * Finds the Recoveries model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Recoveries the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Recoveries::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionRecoverySummary()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;
        $params = Yii::$app->request->post();
        if(isset($params2['export'])&&$params2['export']=='export'){
            $params=$params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $crop_types = array('rabi'=>'Rabi','kharif'=>'Kharif');
            $searchModel = new RecoveriesSearch();
            $searchModel->load($params);
            $dataProvider = RecoveriesHelper::recoverySummary($params);
            $models = $dataProvider->getModels();
            $headers=[];

            if(isset($models[0]) && $models[0]!=null){

                foreach ($models[0] as $key=>$headings){

                 array_push($headers,$key);
                }
            }

            foreach($models as $model){
                if(isset($model['region_name'])){
                    $array = \common\components\Helpers\StructureHelper::getStructureList('regions', 'id', $model['region_name']);
                    $model['region_name']=isset($array['0']['name'])?$array['0']['name']:'Not Set';
                }
                if(isset($model['area_name'])){
                    $array = \common\components\Helpers\StructureHelper::getStructureList('areas', 'id', $model['area_name']);
                    $model['area_name']=isset($array['0']['name'])?$array['0']['name']:'Not Set';
                }
                if(isset($model['branch_name'])){
                    $array = \common\components\Helpers\StructureHelper::getStructureList('branches', 'id', $model['branch_name']);
                    $model['branch_name']=isset($array['0']['code'])?$array['0']['code']:'Not Set';
                }
                $data[]=$model;
            }
            ExportHelper::ExportCSV('Recovery-Summary-Report.csv',$headers,$data);
            die();
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $crop_types = array('rabi'=>'Rabi','kharif'=>'Kharif');
        $searchModel = new RecoveriesSearch();

        if (empty($params['RecoveriesSearch']['receive_date'])) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d');
            $params['RecoveriesSearch']['receive_date'] = $from_date/* . ' - ' . $to_date*/;
        }
        else{
            if(strpos($params['RecoveriesSearch']['receive_date'], ' - ') == false) {
                $params['RecoveriesSearch']['receive_date'] = $params['RecoveriesSearch']['receive_date']/* . ' - ' . $params['RecoveriesSearch']['receive_date']*/;
            }
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $searchModel->load($params);


        $dataProvider = RecoveriesHelper::recoverySummary($params);

        $total = array();
        $total_credit = $total_mdp = $total_loans = 0;
        $models = $dataProvider->getModels();

        foreach ($models as $m) {
            $total_credit += $m['amount'];
            //$total_mdp += $m['mdp'];
            $total_loans += $m['no_of_loans'];
        }
        $total['amount'] = $total_credit;
       // $total['mdp'] = $total_mdp;
        $total['no_of_loans'] = $total_loans;
        $groups=array();
        $i=1;
        foreach ($models as $g){
            $groups[$i]['amount'] = $g['amount'];
            $groups[$i]['no_of_loans'] = $g['no_of_loans'];
            $i++;
        }
        return $this->render('recovery_summary/recovery_summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
            //'projects' => $projects,
            'crop_types' => $crop_types,
            'total' => $total,
        ]);
    }

    public function actionLogs($id = null ,$field = null)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if($request->isAjax) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return $this->renderAjax('logs', [
                    'id' => $id,
                    'field' => $field,
                ]);
                /*return [
                    'title' => "Log Recovery #" . $id,
                    'header'=> [
                        'close' =>['display'=> 'none'],
                    ],
                    'content' => $this->renderAjax('logs', [
                        'id' => $id,
                        'field' => $field,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]).
                        Html::a('Back',['view','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];*/
            }
        }

        return $this->render('logs', [
            'id' => $id,
            'field' => $field,
        ]);
    }
}
