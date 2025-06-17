<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\StructureHelper;
use common\models\Branches;
use common\models\Regions;
use common\models\TransferLogs;
use Yii;
use common\models\Areas;
use common\models\search\AreasSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * AreasController implements the CRUD actions for Areas model.
 */
class AreasController extends Controller
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
     * Lists all Areas models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new AreasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'regions'=>$regions
            ]),
        ]);
    }

    public function actionRegionTransfer($areaId)
    {
        $request = Yii::$app->request;
        $model = Areas::find()->where(['id'=>$areaId])->one();
        $modelRegion = Regions::find()->where(['id'=>$model->region_id])->one();
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $newRegion = new Regions();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Areas #".$areaId,
                'content'=>$this->renderAjax('transfer', [
                    'model' => $model,
                    'regions' => $regions,
                    'modelRegion' => $modelRegion,
                    'newRegion' => $newRegion,
                    'array'=>([
                        'areas'=>ArrayHelper::map(StructureHelper::getAreas(),'id','name'),
                    ]),

                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['transfer','id'=>$areaId],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else if($model->load($request->post())) {
            $oldRegions = Regions::find()->where(['id'=>$_POST['Areas']['region_id']])->one();
            $newRegions = Regions::find()->where(['id'=>$_POST['Regions']['id']])->one();
            if($newRegions){
                $model->updated_by = Yii::$app->user->getId();
                $model->region_id = $newRegions->id;
                $model->updated_at = strtotime(date("Y-m-d"));
                if ($model->save(false)) {
                    $logs = New TransferLogs();
                    $logs->obj_type = 'Area';
                    $logs->transfer_from = $oldRegions->id;
                    $logs->transfer_to = $newRegions->id;
                    $logs->created_by = Yii::$app->user->getId();
                    $logs->transfer_details = 'Area is transferred from '.$oldRegions->name.' to '.$newRegions->name;
                    if($logs->save()){
                        StructureHelper::transferArea($areaId,$oldRegions,$newRegions);
                        return $this->redirect('index');
                    }
                }
            }else{
                return $this->render('transfer', [
                    'model' => $model,
                    'regions' => $regions,
                    'modelRegion' => $modelRegion,
                    'newRegion' => $newRegion
                ]);
            }

        }else{
            return $this->render('transfer', [
                'model' => $model,
                'regions' => $regions,
                'modelRegion' => $modelRegion,
                'newRegion' => $newRegion
            ]);
        }

    }
    /**
     * Displays a single Areas model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $configurations = ConfigurationsHelper::getConfig($id,"area");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("area");
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Areas #".$id,
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
     * Creates a new Areas model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Areas();
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Areas",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Areas",
                    'content'=>'<span class="text-success">Create Areas success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Areas",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions
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
                        'regions'=>$regions
                    ]),
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Areas model.
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

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Areas #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Areas #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Areas #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'regions'=>$regions
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
                        'regions'=>$regions
                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing Areas model.
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
     * Delete multiple existing Areas model.
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
     * Finds the Areas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Areas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Areas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
