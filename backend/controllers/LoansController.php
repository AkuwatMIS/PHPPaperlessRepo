<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use common\models\Loans;
use common\models\search\LoansSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * LoansController implements the CRUD actions for Loans model.
 */
class LoansController extends Controller
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
                'only' => ['index','view','create','update','_form'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','_form'],
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
     * Lists all Loans models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 300);
        $searchModel = new LoansSearch();
        $params = Yii::$app->request->queryParams;
        $params['LoansSearch']['disb_date'] =strtotime(date("Y-m-d",strtotime("-8 Months")));
        $dataProvider = $searchModel->search($params);
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'projects'=>$projects,
                'regions'=>$regions,
                'areas'=>$areas,
                'branches'=>$branches
            ]),
        ]);
    }
    public function actionIndexSearch()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 300);
        $searchModel = new LoansSearch();
        $params = Yii::$app->request->queryParams;
        $params['LoansSearch']['disb_date'] =strtotime(date("Y-m-d",strtotime("-8 Months")));
        if(empty(Yii::$app->request->queryParams)){
            $dataProvider=array();
        }else{
            $dataProvider = $searchModel->search($params);
        }
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        return $this->render('index_search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'projects'=>$projects,
                'regions'=>$regions,
                'areas'=>$areas,
                'branches'=>$branches
            ]),
        ]);
    }



    /**
     * Displays a single Loans model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $configurations = ConfigurationsHelper::getConfig($id,"loan");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("loan");
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Loans #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'array'=>([
                            'configurations'=>$configurations,
                            'global_configurations'=>$global_configurations,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array'=>([
                    'configurations'=>$configurations,
                    'global_configurations'=>$global_configurations,
                ]),
            ]);
        }
    }

    /**
     * Creates a new Loans model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Loans();
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $products = ArrayHelper::map(StructureHelper::getProducts(),'id','name');
        $activities = ArrayHelper::map(StructureHelper::getActivities(),'id','name');

        $is_lock=array("0"=>"unlocked","1"=>"locked");


        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Loans",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'branches'=>$branches,
                            'projects'=>$projects,
                            'products'=>$products,
                            'activities'=>$activities,
                            'is_lock'=>$is_lock,

                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Loans",
                    'content'=>'<span class="text-success">Create Loans success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Loans",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'branches'=>$branches,
                            'projects'=>$projects,
                            'products'=>$products,
                            'activities'=>$activities,
                            'is_lock'=>$is_lock,

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
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'array'=>([
                        'regions'=>$regions,
                        'areas'=>$areas,
                        'branches'=>$branches,
                        'projects'=>$projects,
                        'products'=>$products,
                        'activities'=>$activities,
                        'is_lock'=>$is_lock,

                    ]),
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Loans model.
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
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $products = ArrayHelper::map(StructureHelper::getProducts(),'id','name');
        $activities = ArrayHelper::map(StructureHelper::getActivities(),'id','name');
        $is_lock=array("0"=>"unlocked","1"=>"locked");
        $configurations = ConfigurationsHelper::getConfig($id,"loan");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("loan");
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Loans #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'branches'=>$branches,
                            'projects'=>$projects,
                            'products'=>$products,
                            'activities'=>$activities,
                            'is_lock'=>$is_lock,

                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) ){
                if (!is_numeric($model->date_disbursed)) {
                    $model->date_disbursed = strtotime($model->date_disbursed);
                }
                if($model->save()) {

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Loans #" . $id,
                        'content' => $this->renderAjax('view', [
                            'model' => $this->findModel($id),
                            'array' => ([
                                'configurations' => $configurations,
                                'global_configurations' => $global_configurations,
                            ]),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                    ];
                } else{
                    return [
                        'title'=> "Update Loans #".$id,
                        'content'=>$this->renderAjax('update', [
                            'model' => $model,
                            'array'=>([
                                'regions'=>$regions,
                                'areas'=>$areas,
                                'branches'=>$branches,
                                'projects'=>$projects,
                                'products'=>$products,
                                'activities'=>$activities,
                                'is_lock'=>$is_lock,

                            ]),
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }
            }else{
                 return [
                    'title'=> "Update Loans #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions,
                            'areas'=>$areas,
                            'branches'=>$branches,
                            'projects'=>$projects,
                            'products'=>$products,
                            'activities'=>$activities,
                            'is_lock'=>$is_lock,

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
            if ($model->load($request->post())/* && $model->save()*/) {
                if (!is_numeric($model->date_disbursed)) {
                    $model->date_disbursed = strtotime($model->date_disbursed);
                }
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else {
                    return $this->render('update', [
                        'model' => $model,
                        'array' => ([
                            'regions' => $regions,
                            'areas' => $areas,
                            'branches' => $branches,
                            'projects' => $projects,
                            'products' => $products,
                            'activities' => $activities,
                            'is_lock' => $is_lock,

                        ]),
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'array'=>([
                        'regions'=>$regions,
                        'areas'=>$areas,
                        'branches'=>$branches,
                        'projects'=>$projects,
                        'products'=>$products,
                        'activities'=>$activities,
                        'is_lock'=>$is_lock,

                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing Loans model.
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
     * Delete multiple existing Loans model.
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
     * Finds the Loans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loans::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
