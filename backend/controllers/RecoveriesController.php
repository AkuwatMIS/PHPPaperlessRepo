<?php

namespace backend\controllers;

use common\components\Helpers\FixesHelper;
use common\components\Helpers\StructureHelper;
use common\models\Loans;
use common\models\Schedules;
use Yii;
use common\models\Recoveries;
use common\models\search\RecoveriesSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * RecoveriesController implements the CRUD actions for Recoveries model.
 */
class RecoveriesController extends Controller
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
     * Lists all Recoveries models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204802423532M');
        ini_set('max_execution_time', 500);
        $searchModel = new RecoveriesSearch();
        $params = Yii::$app->request->queryParams;
        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {

            $recv_date = date('Y-m-d');
            $params['RecoveriesSearch']['receive_date'] =  date("Y-m-01 H:i:s"). ' - ' . date('Y-m-d H:i:s',strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
            //$params['RecoveriesSearch']['receive_date'] = date('Y-m-01', strtotime($recv_date)) . ' - ' . date('Y-m-d');
        }
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
        ini_set('memory_limit', '204802423532M');
        ini_set('max_execution_time', 500);
        $searchModel = new RecoveriesSearch();
        $params = Yii::$app->request->queryParams;
        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {

            $recv_date = date('Y-m-d');
            $params['RecoveriesSearch']['receive_date'] =  date("Y-m-01 H:i:s"). ' - ' . date('Y-m-d H:i:s',strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
            //$params['RecoveriesSearch']['receive_date'] = date('Y-m-01', strtotime($recv_date)) . ' - ' . date('Y-m-d');
        }
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
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $oldRecoveryAmount = $model->amount;
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
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->receive_date = strtotime($model->receive_date);
                /*echo '<pre>';
                print_r($model->save(false));
                print_r($model->getErrors());
                die();*/
                if($model->save(false)){
                    $loan = Loans::findOne(['id'=>$model->loan_id]);
                    FixesHelper::fix_schedules_update($loan);
//                    if (in_array($model->project_id, [77, 78, 79])) {
//                        FixesHelper::updateRecoveryForFund($id,$oldRecoveryAmount);
//                    }

                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Recoveries #".$id,
                        'content'=>$this->renderAjax('view', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                    ];
                } else {
                    return [
                        'title'=> "Update Recoveries #".$id,
                        'content'=>$this->renderAjax('update', [
                            'model' => $model,
                            'projects' => $projects,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }

            }else{
                 return [
                    'title'=> "Update Recoveries #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) /*&& $model->save()*/) {
                $model->receive_date = strtotime($model->receive_date);
                if($model->save(false)) {
                    $loan = Loans::findOne(['id' => $model->loan_id]);
                    FixesHelper::fix_schedules_update($loan);
//                    if (in_array($model->project_id, [77, 78, 79])) {
//                        FixesHelper::updateRecoveryForFund($id,$oldRecoveryAmount);
//                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else{
                    return $this->render('update', [
                        'model' => $model,
                        'projects' => $projects,
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'projects' => $projects,
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
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->deleted = 1;
        //$model->delete();
        $model->save(false);

        $loan = Loans::findOne(['id'=>$model->loan_id]);
        FixesHelper::fix_schedules_update($loan);

//        if (in_array($model->project_id, [77, 78, 79])) {
//            FixesHelper::deleteRecoveryForFund($id);
//        }
//        if($model->branch_id == 814) {
            $schedule = Schedules::find()->where(['id' => $model->schedule_id])->one();
            $schedule->credit_tax = 0;
            if($schedule->save(false)){
            }
//        }
        //die();
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
}
