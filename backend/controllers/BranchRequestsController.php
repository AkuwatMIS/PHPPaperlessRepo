<?php

namespace backend\controllers;

use common\components\Helpers\StructureHelper;
use Yii;
use common\models\BranchRequests;
use common\models\search\BranchRequestsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * BranchRequestsController implements the CRUD actions for BranchRequests model.
 */
class BranchRequestsController extends Controller
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
     * Lists all BranchRequests models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new BranchRequestsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'regions'=>$regions,
                'areas'=>$areas,
            ]),
        ]);
    }


    /**
     * Displays a single BranchRequests model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "BranchRequests #".$id,
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
     * Creates a new BranchRequests model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new BranchRequests();
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $cities = ArrayHelper::map(StructureHelper::getCities(),'id','name');
        $districts = ArrayHelper::map(StructureHelper::getDistricts(),'id','name');
        $divisions = ArrayHelper::map(StructureHelper::getDivisions(),'id','name');
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(),'id','name');
        $countries = ArrayHelper::map(StructureHelper::getCountries(),'id','name');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new BranchRequests",
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
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new BranchRequests",
                    'content'=>'<span class="text-success">Create BranchRequests success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new BranchRequests",
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
                        'cities'=>$cities,
                        'districts'=>$districts,
                        'divisions'=>$divisions,
                        'provinces'=>$provinces,
                        'countries'=>$countries,
                    ]),
                ]);
            }
        }
       
    }

    /**
     * Updates an existing BranchRequests model.
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
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update BranchRequests #".$id,
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
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "BranchRequests #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update BranchRequests #".$id,
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
                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing BranchRequests model.
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
     * Delete multiple existing BranchRequests model.
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
     * Finds the BranchRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BranchRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BranchRequests::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
