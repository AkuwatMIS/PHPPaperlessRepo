<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use common\models\Areas;
use common\models\BranchProjectsMapping;
use common\models\mapping_models\BranchWithAccounts;
use common\models\mapping_models\BranchWithProjects;
use common\models\search\UsersSearch;
use common\models\TransferLogs;
use common\models\Users;
use frontend\modules\branch\Branch;
use Yii;
use common\models\Branches;
use common\models\search\BranchesSearch;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * BranchesController implements the CRUD actions for Branches model.
 */
class BranchesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else{
                        return Yii::$app->response->redirect(['site/main']);
                    }
                },
                'only' => ['index','view','create','update','_form','transfer'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','_form','transfer'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Branches models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BranchesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $cities = ArrayHelper::map(StructureHelper::getCities(),'id','name');
        $districts = ArrayHelper::map(StructureHelper::getDistricts(),'id','name');
        $divisions = ArrayHelper::map(StructureHelper::getDivisions(),'id','name');
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(),'id','name');
        $countries = ArrayHelper::map(StructureHelper::getCountries(),'id','name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'regions'=>$regions,
                'areas'=>$areas,
                'cities'=>$cities,
                'districts'=>$districts,
                'divisions'=>$divisions,
                'provinces'=>$provinces,
                'countries'=>$countries,
            ]),
        ]);
    }

    public function actionTransfer($branchId)
    {
        $request = Yii::$app->request;
        $model = Branches::find()->where(['id'=>$branchId])->one();
        $modelArea = Areas::find()->where(['id'=>$model->area_id])->one();
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $newArea = new Areas();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Branches #".$branchId,
                'content'=>$this->renderAjax('transfer', [
                    'model' => $model,
                    'areas' => $areas,
                    'modelArea' => $modelArea,
                    'newArea' => $newArea,
                    'array'=>([
                        'areas'=>ArrayHelper::map(StructureHelper::getAreas(),'id','name'),
                    ]),

                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['transfer','id'=>$branchId],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else if($model->load($request->post())) {
            $oldArea = Areas::find()->where(['id'=>$_POST['Branches']['area_id']])->one();
            $newArea = Areas::find()->where(['id'=>$_POST['Areas']['id']])->one();
            if($newArea){
                $model->updated_by = Yii::$app->user->getId();
                $model->created_by = Yii::$app->user->id;
                $model->region_id = $newArea->region_id;
                $model->area_id = $newArea->id;
                $model->updated_at = strtotime(date("Y-m-d"));
                if ($model->save()) {
                    $logs = New TransferLogs();
                    $logs->obj_type = 'branch';
                    $logs->transfer_from = $oldArea->id;
                    $logs->transfer_to = $newArea->id;
                    $logs->created_by = Yii::$app->user->id;
                    $logs->transfer_details = 'Branch is transferred from '.$oldArea->name.' to '.$newArea->name;
                    if($logs->save()){
                        StructureHelper::transferBranch($branchId,$oldArea,$newArea);
                        return $this->redirect('index');
                    }
                }else{
                    var_dump($model->getErrors());
                    die();
                }
            }else{
                return $this->render('transfer', [
                    'model' => $model,
                    'areas' => $areas,
                    'modelArea' => $modelArea,
                    'newArea' => $newArea,
                ]);
            }

        }else{
            return $this->render('transfer', [
                'model' => $model,
                'areas' => $areas,
                'modelArea' => $modelArea,
                'newArea' => $newArea
            ]);
        }

    }

    /**
     * Displays a single Branches model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $model_branchwitproject = new BranchWithProjects();
        $model_branchwitproject->loadProjects($id);
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');

        $model_branchwitaccount = new BranchWithAccounts();
        $model_branchwitaccount->loadAccounts($id);
//        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
        $accounts = [];

        $configurations = ConfigurationsHelper::getConfig($id,"branch");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("branch");
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Branches #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'array'=>([
                            'projects'=>$projects,
                            'accounts'=>$accounts,
                            'model_branchwitproject' => $model_branchwitproject,
                            'model_branchwitaccount' => $model_branchwitaccount,
                            'configurations'=>$configurations,
                            'global_configurations'=>$global_configurations
                        ]),

                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array'=>([
                    'projects'=>$projects,
                    'accounts'=>$accounts,
                    'model_branchwitproject' => $model_branchwitproject,
                    'model_branchwitaccount' => $model_branchwitaccount,
                    'configurations'=>$configurations,
                    'global_configurations'=>$global_configurations
                ]),
            ]);
        }
    }

    /**
     * Creates a new Branches model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Branches();
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $cities = ArrayHelper::map(StructureHelper::getCities(),'id','name');
        $districts = ArrayHelper::map(StructureHelper::getDistricts(),'id','name');
        $divisions = ArrayHelper::map(StructureHelper::getDivisions(),'id','name');
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(),'id','name');
        $countries = ArrayHelper::map(StructureHelper::getCountries(),'id','name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');

       $credit_division = ArrayHelper::map(StructureHelper::getCreditDivision(),'id','name');
//        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
        $accounts = [];
        $model_branchwitproject = new BranchWithProjects();
        $model_branchwitaccount = new BranchWithAccounts();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
           /* if($model->save()){
                $model_branchwitproject->saveProjects($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('create', [
                    'model' => $model,
                    'model_branchwitproject' => $model_branchwitproject,
                    'projects' => $projects,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'model_branchwitproject' => $model_branchwitproject,
                'projects' => $projects,
            ]);
        }*/


            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Branches",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'cities'=>$cities,
                            'districts'=>$districts,
                            'divisions'=>$divisions,
                            'provinces'=>$provinces,
                            'countries'=>$countries,
                            'projects'=>$projects,
                            'accounts'=>$accounts,
                            'credit_division'=>$credit_division,
                            'model_branchwitproject' => $model_branchwitproject,
                            'model_branchwitaccount' => $model_branchwitaccount,
                        ]),

                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) ){
                $model->assigned_to = Yii::$app->user->getId();
                $model->created_by = Yii::$app->user->getId();
                if($model->save()) {
                    $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                    $model_branchwitproject->saveProjects($model->id);
                    $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                    $model_branchwitaccount->saveAccounts($model->id);

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Branches",
                        'content' => '<span class="text-success">Create Branches success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else{
                    return [
                        'title'=> "Create new Branches",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'array'=>([
                                'regions'=>$regions,
                                'areas'=>$areas,
                                'cities'=>$cities,
                                'districts'=>$districts,
                                'divisions'=>$divisions,
                                'provinces'=>$provinces,
                                'countries'=>$countries,
                                'projects'=>$projects,
                                'accounts'=>$accounts,
                                'credit_division'=>$credit_division,
                                'model_branchwitproject' => $model_branchwitproject,
                                'model_branchwitaccount' => $model_branchwitaccount,
                            ]),

                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }

            }else{           
                return [
                    'title'=> "Create new Branches",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'cities'=>$cities,
                            'districts'=>$districts,
                            'divisions'=>$divisions,
                            'provinces'=>$provinces,
                            'countries'=>$countries,
                            'projects'=>$projects,
                            'accounts'=>$accounts,
                            'credit_division'=>$credit_division,
                            'model_branchwitproject' => $model_branchwitproject,
                            'model_branchwitaccount' => $model_branchwitaccount,
                        ]),

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

                $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                $model_branchwitproject->saveProjects($model->id);

                $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                $model_branchwitaccount->saveAccounts($model->id);


                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'array'=>([
                        'regions'=>$regions,
                        'areas'=>$areas,
                        'cities'=>$cities,
                        'districts'=>$districts,
                        'divisions'=>$divisions,
                        'provinces'=>$provinces,
                        'countries'=>$countries,
                        'projects'=>$projects,
                        'accounts'=>$accounts,
                        'credit_division'=>$credit_division,
                        'model_branchwitproject' => $model_branchwitproject,
                        'model_branchwitaccount' => $model_branchwitaccount,

                    ]),

                ]);
            }
        }
       
    }

    /**
     * Updates an existing Branches model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionImportFile()
    {

        $model = new Branches();
        $request = Yii::$app->request;
        if($request->isAjax){

            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet) {
                return [
                    'title' => "Upload Bulk #",
                    'content' => $this->renderAjax('_file_form'),

                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            }
        }else{
            if($_POST) {

                if (!empty($_FILES['file'])) {

                    $project_branch_mapping = $this->csvToArray($_FILES['file']['tmp_name']);
                    foreach ($project_branch_mapping as $mapping) {
                        $branch = Branches::find()->select('id')->where(['code' =>$mapping['code']])->asArray()->one();
                        $check = BranchProjectsMapping::find()->where(['branch_id'=>$branch['id'],'project_id'=>$mapping['project_id']])->one();
                        if(empty($check)) {
                            $map = new BranchProjectsMapping();
                            $map->branch_id = $branch['id'];
                            $map->project_id = $mapping['project_id'];
                            $map->account_id = $mapping['account_id'];
                            $map->assigned_to = 1;
                            $map->updated_by = 1;
                            $map->created_by = 1;
                            $map->created_at = strtotime(date('d-m-Y'));
                            $map->updated_at = strtotime(date('d-m-Y'));
                            $map->deleted = 0;

                            if(!$map->save()){
                                echo '<pre>';
                                var_dump($map->getErrors());
                                die();
                                //print_r($map->getErrors());die();
                            }
                        }

                    }
                    return $this->redirect(['index']);
                }
            } else {

                return $this->render('index');
            }
        }
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $cities = ArrayHelper::map(StructureHelper::getCities(),'id','name');
        $districts = ArrayHelper::map(StructureHelper::getDistricts(),'id','name');
        $divisions = ArrayHelper::map(StructureHelper::getDivisions(),'id','name');
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(),'id','name');
        $countries = ArrayHelper::map(StructureHelper::getCountries(),'id','name');
        $credit_division = ArrayHelper::map(StructureHelper::getCreditDivision(),'id','name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $model_branchwitproject = new BranchWithProjects();
        $model_branchwitproject->loadProjects($id);

//        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
        $accounts = [];
        $model_branchwitaccount = new BranchWithAccounts();
        $model_branchwitaccount->loadAccounts($id);

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Branches #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'cities'=>$cities,
                            'districts'=>$districts,
                            'divisions'=>$divisions,
                            'provinces'=>$provinces,
                            'countries'=>$countries,
                            'projects'=>$projects,
                            'credit_division'=>$credit_division,
                            'model_branchwitproject' => $model_branchwitproject,
                            'accounts'=>$accounts,
                            'model_branchwitaccount' => $model_branchwitaccount,

                        ]),

                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->created_by = Yii::$app->user->getId();

                if($model->save()) {
                    $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                    $model_branchwitproject->saveProjects($model->id);

                    $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                    $model_branchwitaccount->saveAccounts($model->id);

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Branches #" . $id,
                        'content' => $this->renderAjax('view', [
                            'model' => $model,
                            'array' => ([
                                'projects' => $projects,
                                'model_branchwitproject' => $model_branchwitproject,
                                'accounts' => $accounts,
                                'model_branchwitaccount' => $model_branchwitaccount,
                            ]),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                    ];
                } else{

                    return [
                        'title'=> "Update Branches #".$id,
                        'content'=>$this->renderAjax('update', [
                            'model' => $model,
                            'array'=>([
                                'regions'=>$regions,
                                'areas'=>$areas,
                                'cities'=>$cities,
                                'districts'=>$districts,
                                'divisions'=>$divisions,
                                'provinces'=>$provinces,
                                'countries'=>$countries,
                                'projects'=>$projects,
                                'credit_division'=>$credit_division,
                                'model_branchwitproject' => $model_branchwitproject,
                                'accounts'=>$accounts,
                                'model_branchwitaccount' => $model_branchwitaccount,
                            ]),

                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }
            }else{
                 return [
                    'title'=> "Update Branches #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'cities'=>$cities,
                            'districts'=>$districts,
                            'divisions'=>$divisions,
                            'provinces'=>$provinces,
                            'countries'=>$countries,
                            'projects'=>$projects,
                            'credit_division'=>$credit_division,
                            'model_branchwitproject' => $model_branchwitproject,
                            'accounts'=>$accounts,
                            'model_branchwitaccount' => $model_branchwitaccount,
                        ]),

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
                $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                $model_branchwitproject->saveProjects($model->id);

                $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                $model_branchwitaccount->saveAccounts($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'array'=>([
                        'regions'=>$regions,
                        'areas'=>$areas,
                        'cities'=>$cities,
                        'districts'=>$districts,
                        'divisions'=>$divisions,
                        'provinces'=>$provinces,
                        'countries'=>$countries,
                        'projects'=>$projects,
                        'credit_division'=>$credit_division,
                        'model_branchwitproject' => $model_branchwitproject,
                        'accounts'=>$accounts,
                        'model_branchwitaccount' => $model_branchwitaccount,
                    ]),

                ]);
            }
        }
    }
    public function actionUsageReport()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 300);
            $this->layout = 'csv';
            $headers = array('Region', 'Area', 'Branch', 'No of Members','No of Social Appraisals','No of Business Appraisals', 'No of Applications','No of Verifications', 'No of Groups', 'No of Loans', 'No of Fund Requests','No of Disbursements','No of Recoveries');
            $groups = array();
            $searchModel = new BranchesSearch();
            $query = $searchModel->searchusagereport($_GET,true);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['region_id'] = $g['region']['name'];
                $groups[$i]['area_id'] = $g['area']['name'];
                $groups[$i]['name'] = $g['name'];
                $groups[$i]['no_of_members'] = $g['no_of_members'];
                $groups[$i]['no_of_applications'] = $g['no_of_applications'];
                $groups[$i]['no_of_social_appraisals'] = $g['no_of_social_appraisals'];
                $groups[$i]['no_of_business_appraisals'] = $g['no_of_business_appraisals'];
                $groups[$i]['no_of_verifications'] = $g['no_of_verifications'];
                $groups[$i]['no_of_groups'] = $g['no_of_groups'];
                $groups[$i]['no_of_loans'] = $g['no_of_loans'];
                $groups[$i]['no_of_fund_requests'] = $g['no_of_fund_requests'];
                $groups[$i]['no_of_disbursements'] = $g['no_of_disbursements'];
                $groups[$i]['no_of_recoveries'] = $g['no_of_recoveries'];
                $i++;
            }
            ExportHelper::ExportCSV('Usage-Report.csv', $headers, $groups);
            die();
        }
        $searchModel = new BranchesSearch();
        $dataProvider = $searchModel->searchusagereport(Yii::$app->request->queryParams);
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        return $this->render('usage_report/userreport', [
            'dataProvider' => $dataProvider,
            'searchModel'=>$searchModel,
            'regions'=>$regions,
            'areas'=>$areas,
            'branches'=>$branches,
        ]);
    }
    /**
     * Delete an existing Branches model.
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
     * Delete multiple existing Branches model.
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

    /**
     * Finds the Branches model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Branches the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Branches::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = array();
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header) {
                    $header = $row;
                }else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
