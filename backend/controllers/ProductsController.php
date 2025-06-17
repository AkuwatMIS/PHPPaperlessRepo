<?php

namespace backend\controllers;

use common\components\Helpers\StructureHelper;
use common\models\mapping_models\ProductWithActivities;
use Yii;
use common\models\Products;
use common\models\search\ProductsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
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
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Products model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $model_productwithactivity = new ProductWithActivities();
        $model_productwithactivity->loadActivities($id);
        $activities = ArrayHelper::map(StructureHelper::getActivities(),'id','name');
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Products #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array'=>([
                    'activities'=>$activities,
                    'model_productwithactivity' => $model_productwithactivity,
                ]),
            ]);
        }
    }

    /**
     * Creates a new Products model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Products();
        $activities = ArrayHelper::map(StructureHelper::getActivities(),'id','name');
        $model_productwithactivity = new ProductWithActivities();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Products",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                $model_productwithactivity->activity_ids = Yii::$app->request->post()['ProductWithActivities']['activity_ids'];
                $model_productwithactivity->saveActivities($model->id);
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Products",
                    'content'=>'<span class="text-success">Create Products success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Products",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
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
                $model_productwithactivity->activity_ids = Yii::$app->request->post()['ProductWithActivities']['activity_ids'];
                $model_productwithactivity->saveActivities($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'array'=>([
                        'activities'=>$activities,
                        'model_productwithactivity' => $model_productwithactivity,
                    ]),
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Products model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $activities = ArrayHelper::map(StructureHelper::getActivities(),'id','name');
        $model_productwithactivity = new ProductWithActivities();
        $model_productwithactivity->loadActivities($id);
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Products #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                $model_productwithactivity->activity_ids = Yii::$app->request->post()['ProductWithActivities']['activity_ids'];
                $model_productwithactivity->saveActivities($model->id);
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Products #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Products #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'activities'=>$activities,
                            'model_productwithactivity' => $model_productwithactivity,
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
                $model_productwithactivity->activity_ids = Yii::$app->request->post()['ProductWithActivities']['activity_ids'];
                $model_productwithactivity->saveActivities($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'array'=>([
                        'activities'=>$activities,
                        'model_productwithactivity' => $model_productwithactivity,
                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing Products model.
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
     * Delete multiple existing Products model.
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
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
