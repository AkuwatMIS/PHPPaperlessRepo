<?php

namespace frontend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ProgressReportHelper;
use common\components\Helpers\StructureHelper;
use common\components\RbacHelper;
use common\models\Fields;
use common\models\mapping_models\BranchWithAccounts;
use common\models\mapping_models\BranchWithProjects;
use common\models\search\UsersSearch;
use common\models\UserStructureMapping;
use Yii;
use common\models\Branches;
use common\models\Teams;
use common\models\search\BranchesSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * BranchesController implements the CRUD actions for Branches model.
 */
class BranchesController extends Controller
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
     * Lists all Branches models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = array_keys($_GET['BranchesSearch']);
            $groups = array();
            $searchModel = new BranchesSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilter($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['name'] = $g['name'];
                $groups[$i]['type'] = $g['type'];
                $groups[$i]['short_name'] = $g['short_name'];
                $groups[$i]['region_id'] = isset($g->region->name)?$g->region->name:'';
                $groups[$i]['area_id'] =isset($g->area->name)?$g->area->name:'';
                $groups[$i]['city_id'] = isset($g->city->name)?$g->city->name:'';
                $groups[$i]['district_id'] =isset($g->district->name)?$g->district->name:'';
                $groups[$i]['division_id'] = isset($g->division->name)?$g->division->name:'';
                $groups[$i]['province_id'] = isset($g->province->name)?$g->province->name:'';
                $groups[$i]['country_id'] =isset($g->country->name)?$g->country->name:'';
                $groups[$i]['status'] = $g['status'];
                $i++;
            }
            ExportHelper::ExportCSV('branches.csv',$headers,$groups);
            die();
        }
        $searchModel = new BranchesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
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
        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
        $branch_projects = StructureHelper::BranchProjects($id);
        $branch_accounts = StructureHelper::BranchAccounts($id);
        $progress = ProgressReportHelper::getProgressOfBranch($id);
        //$configurations = ConfigurationsHelper::getConfig($id,"branch");
        //$global_configurations = ConfigurationsHelper::getConfigGlobal("branch");
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Branches #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'array'=>([
                            'projects'=>$projects,
                            'accounts'=>$accounts,
                            'branch_projects'=>$branch_projects,
                            'branch_accounts'=>$branch_accounts,
                            'progress'=>$progress,
                            'model_branchwitproject' => $model_branchwitproject,
                            'model_branchwitaccount' => $model_branchwitaccount,
                            //'configurations'=>$configurations,
                            //'global_configurations'=>$global_configurations
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
                    'branch_projects'=>$branch_projects,
                    'branch_accounts'=>$branch_accounts,
                    'progress'=>$progress,
                    'model_branchwitproject' => $model_branchwitproject,
                    'model_branchwitaccount' => $model_branchwitaccount,
                    //'configurations'=>$configurations,
                    //'global_configurations'=>$global_configurations
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
        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
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
            }else if($model->load($request->post()) && $model->save()){
                $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                $model_branchwitproject->saveProjects($model->id);
                $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                $model_branchwitaccount->saveAccounts($model->id);

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Branches",
                    'content'=>'<span class="text-success">Create Branches success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
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

        $accounts = ArrayHelper::map(StructureHelper::getAccounts(),'id','acc_no');
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
            }else if($model->load($request->post()) && $model->save()){
                $model_branchwitproject->project_ids = Yii::$app->request->post()['BranchWithProjects']['project_ids'];
                $model_branchwitproject->saveProjects($model->id);

                $model_branchwitaccount->account_ids = Yii::$app->request->post()['BranchWithAccounts']['account_ids'];
                $model_branchwitaccount->saveAccounts($model->id);

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Branches #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                        'array'=>([
                            'projects'=>$projects,
                            'model_branchwitproject' => $model_branchwitproject,
                            'accounts'=>$accounts,
                            'model_branchwitaccount' => $model_branchwitaccount,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
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
    public function actionAddTeams($id=1)
    {
        $model = $this->findModel($id);
        $teams=new Teams();
        $fields=new Fields();
        return $this->render('add_teams', [
            'model'=>$model,
            'teams'=>$teams,
            'fields'=>$fields,
        ]);

    }

    public function actionTeams()
    {
        $post = Yii::$app->request->post();
        $teams_count = Teams::find()->where(['branch_id' => $post['Teams']['branch_id']])->count()+1;
        $team = new Teams();
        $team->name = "Team" . $teams_count;
        $team->branch_id = $post['Teams']['branch_id'];
        $team->beforeSave(true);
        if ($team->save()) {
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        } else {
            $response['status_type'] = "error";
            $response['errors'] = $team->getErrors();
        }
        return json_encode($response);
    }

    public function actionFields()
    {
        $post = Yii::$app->request->post();
        $fields_count = Fields::find()->where(['team_id' => $post['Fields']['team_id']])->count()+1;
        $field = new Fields();
        $field->name = "Field" . $fields_count;
        $field->team_id = $post['Fields']['team_id'];
        $field->beforeSave(true);
        if ($field->save()) {
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        } else {
            $response['status_type'] = "error";
            $response['errors'] = $field->getErrors();
        }
        return json_encode($response);
    }
    public function actionDeleteField()
    {
        $post = Yii::$app->request->post();
        $field = Fields::find()->where(['id' => $post['Fields']['id']])->one();
        if ($field->delete()) {
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        } else {
            $response['status_type'] = "error";
            $response['errors'] = $field->getErrors();
        }
        return json_encode($response);
    }
    public function actionDeleteTeam()
    {
        $post = Yii::$app->request->post();
        $team = Teams::find()->where(['id' => $post['Teams']['id']])->one();
        if ($team->delete()) {
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        } else {
            $response['status_type'] = "error";
            $response['errors'] = $team->getErrors();
        }
        return json_encode($response);
    }
    public function actionUserlist($field_id)
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        if (isset($_POST['keylist'])) {
            $teams=new Teams();
            $fields=new Fields();
            print_r($_POST['keylist']);
          $field=Fields::find()->where(['id'=>$field_id])->one();

          $field->assigned_to=$_POST['keylist'][0];
            //$field->assigned_to=199;

            if($field->save()){}
            else{

                print_r($field->getErrors());
                die();
            }

            /*print_r($field->getErrors());
            die();*/


            return $this->redirect(['add-teams']);
        }
        else {
            return $this->render('userlist', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'field_id'=>$field_id
            ]);
        }
    }
    public function actionQuickUpdate($id)
    {

        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['quick-view', 'id' => $model->id]);
        } else {
            return $this->render('mybranches/update', [
                'model' => $model,
            ]);
        }
    }
    public function actionQuickView($id)
    {
        return $this->render('mybranches/view', [
            'model' => $this->findModel($id),
        ]);
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
    public function actionBranchWiseProgress(){

        $params= Yii::$app->request->queryParams;
        if(isset($params['BranchesSearch']['date'])){
            $date=$params['BranchesSearch']['date'];
        }
        else{
            $date=date('Y-m-d');
        }
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $headers=[];
            $this->layout = 'csv';
            $headers=array("Up to date","Sr.No","Branch Code","Branch Name","Region","Area","District","Branch Manager","Cell Phone #","Address","Province","City","Longitude","Latitude","Total Benifitting Families",
                "Total loans disbursed to Male","Total loans disbursed to Female","Amount Disbursed PKR","Percentage Recovery","Active Loans","Outstanding Loan Portfolio PKR");
            $groups = array();
            $searchModel = new BranchesSearch();
            $query = $searchModel->searchprogress($_GET,true);
            RbacHelper::getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id);
            $i=1;
            foreach ($query as $g){
                $groups[$i]['up_to_date'] = $date;
                $groups[$i]['serial_no'] = $i;
                $groups[$i]['code'] = $g['code'];
                $groups[$i]['name'] = $g['name'];
                $groups[$i]['region_name'] = $g['region_name'];
                $groups[$i]['area_name'] = $g['area_name'];
                $groups[$i]['district_name'] = $g['district_name'];
                $groups[$i]['branch_manager'] = '';
                $groups[$i]['mobile'] = $g['mobile'];
                $groups[$i]['address'] = $g['address'];
                $groups[$i]['province_name'] = $g['province_name'];
                $groups[$i]['city_name'] = $g['city_name'];
                $groups[$i]['longitude'] = $g['longitude'];
                $groups[$i]['latitude'] = $g['latitude'];
                $groups[$i]['no_of_loans'] = $g['no_of_loans'];
                $groups[$i]['male_loans'] = $g['male_loans'];
                $groups[$i]['female_loans'] = $g['female_loans'];
                $groups[$i]['amount_disbursed'] = $g['amount_disbursed'];
                $groups[$i]['percentage_recovery'] = $g['percentage_recovery'];
                $groups[$i]['active_loans'] = $g['active_loans'];
                $groups[$i]['olp'] = $g['olp'];

                $i++;
            }
            ExportHelper::ExportCSV('Branch-Wise-Progress-Report.csv',$headers,$groups);
            die();
        }
        $searchModel = new BranchesSearch();


        return $this->render('_branch_wise_progress', [
            'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,

        ]);
    }
}
