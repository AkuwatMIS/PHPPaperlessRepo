<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use common\models\ConfigRules;
use common\models\search\ConfigRulesSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\widgets\config\Config;

/**
 * ConfigRulesController implements the CRUD actions for ConfigRules model.
 */
class ConfigRulesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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
     * Lists all ConfigRules models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ConfigRulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $config_projects=ArrayHelper::map(ConfigurationsHelper::getConfigproject(),"id","name");
        $config_groups=ConfigurationsHelper::getConfiggroups();
        $config_parent_type =ConfigurationsHelper::getConfigparenttype();
        $config_priority=ConfigurationsHelper::getConfigpriority();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            "array"=>([
                'config_priority'=>$config_priority,
                'config_projects'=>$config_projects,
                'config_groups'=>$config_groups,
                'config_parent_type'=>$config_parent_type,
            ]),
        ]);
    }


    /**
     * Displays a single ConfigRules model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "ConfigRules #".$id,
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
     * Creates a new ConfigRules model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ConfigRules();
        $config_projects=ArrayHelper::map(ConfigurationsHelper::getConfigproject(),"id","name");
        $config_groups=ConfigurationsHelper::getConfiggroups();
        $config_parent_type =ConfigurationsHelper::getConfigparenttype();
        $config_priority=ConfigurationsHelper::getConfigpriority();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new ConfigRules",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        "array"=>([
                            'config_priority'=>$config_priority,
                            'config_projects'=>$config_projects,
                            'config_groups'=>$config_groups,
                            'config_parent_type'=>$config_parent_type,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->beforeSave(true) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new ConfigRules",
                    'content'=>'<span class="text-success">Create ConfigRules success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new ConfigRules",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        "array"=>([
                            'config_priority'=>$config_priority
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
            if ($model->load($request->post()) && $model->beforeSave(true) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {

                return $this->render('create', [
                    'model' => $model,
                    "array"=>([
                        'config_priority'=>$config_priority,
                        'config_projects'=>$config_projects,
                        'config_groups'=>$config_groups,
                        'config_parent_type'=>$config_parent_type,
                    ]),
                ]);
            }
        }
       
    }

    /**
     * Updates an existing ConfigRules model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $config_projects=ArrayHelper::map(ConfigurationsHelper::getConfigproject(),"id","name");
        $config_groups=ConfigurationsHelper::getConfiggroups();
        $config_parent_type =ConfigurationsHelper::getConfigparenttype();
        $config_priority=ConfigurationsHelper::getConfigpriority();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update ConfigRules #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        "array"=>([
                            'config_priority'=>$config_priority,
                            'config_projects'=>$config_projects,
                            'config_groups'=>$config_groups,
                            'config_parent_type'=>$config_parent_type,
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->beforeSave(true) && $model->save()){

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "ConfigRules #".$id,
                    'content'=>/*'<span class="text-success">'.$a.'</span>'.*/$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update ConfigRules #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        "array"=>([
                            'config_priority'=>$config_priority,
                            'config_projects'=>$config_projects,
                            'config_groups'=>$config_groups,
                            'config_parent_type'=>$config_parent_type,
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
            if ($model->load($request->post()) && $model->beforeSave(true) && $model->save()) {

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    "array"=>([
                        'config_priority'=>$config_priority,
                        'config_projects'=>$config_projects,
                        'config_groups'=>$config_groups,
                        'config_parent_type'=>$config_parent_type,
                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing ConfigRules model.
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
     * Delete multiple existing ConfigRules model.
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
     * Finds the ConfigRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ConfigRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ConfigRules::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
