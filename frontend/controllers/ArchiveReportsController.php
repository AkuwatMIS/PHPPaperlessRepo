<?php

namespace frontend\controllers;
use common\components\Helpers\ArchiveReportsHelper;
use common\components\StructureHelper;
use common\models\ConnectionBanks;
use Yii;
use common\models\ArchiveReports;
use common\models\search\ArchiveReportsSearch;
use yii\base\ViewNotFoundException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\components\RbacHelper;
use yii\web\UnauthorizedHttpException;
use yii\filters\AccessControl;

/**
 * ArchiveReportsController implements the CRUD actions for ArchiveReports model.
 */
class ArchiveReportsController extends Controller
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
     * Lists all ArchiveReports models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ArchiveReportsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $sources = ArrayHelper::map(ArchiveReportsHelper::getBanksResources(),'bank_code','bank_name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sources' => $sources,
        ]);
    }


    /**
     * Displays a single ArchiveReports model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "ArchiveReports #".$id,
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
     * Creates a new ArchiveReports model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ArchiveReports();  
        $report_names = array('portfolio-report'=>'Portfolio Report', 'duelist-report'=>'Duelist Report');
        $regions = ArrayHelper::map(\common\components\Helpers\StructureHelper::getRegions(),'id','name');
        $projects = ArrayHelper::map(\common\components\Helpers\StructureHelper::getProjects(),'id','name');
        $products = ArrayHelper::map(\common\components\Helpers\StructureHelper::getProducts(),'id','name');
        $activities = ArrayHelper::map(\common\components\Helpers\StructureHelper::getActivities(),'id','name');
        //$gender = LoanHelper::getGender();
        $sources = ArrayHelper::map(ArchiveReportsHelper::getBanksResources(),'bank_code','bank_name');

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Archive Report",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post())){
                $model->set_values();
                if($model->save()){
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Create new ArchiveReports",
                        'content'=>'<span class="text-success">Create ArchiveReports success</span>',
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                    ];
                }else{
                    return [
                        'title'=> "Create new Archive Report",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'report_names' => $report_names,
                            'regions' => $regions,
                            'projects' => $projects,
                            'products' => $products,
                            'activities' => $activities,
                            //'gender' => $gender,
                            'sources' => $sources,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }

            }else{           
                return [
                    'title'=> "Create new Archive Report",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            /*echo '<pre>';
            $model->load($request->post());
            $model->save();
            print_r($model->getErrors());
            die();*/
            if ($model->load($request->post())) {
               // $model->set_values();
                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    return $this->render('create', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
                    ]);
                }


            } else {
                /*print_r($model->getErrors());
                die();*/
                return $this->render('create', [
                    'model' => $model,
                    'report_names' => $report_names,
                    'regions' => $regions,
                    'projects' => $projects,
                    'products' => $products,
                    'activities' => $activities,
                    //'gender' => $gender,
                    'sources' => $sources,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing ArchiveReports model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $report_names = array('portfolio-report'=>'Portfolio Report','recovery-report'=>'Recovery Report');
        $regions = ArrayHelper::map(\common\components\Helpers\StructureHelper::getRegions(),'id','name');
        $projects = ArrayHelper::map(\common\components\Helpers\StructureHelper::getProjects(),'id','name');
        $products = ArrayHelper::map(\common\components\Helpers\StructureHelper::getProducts(),'id','name');
        $activities = ArrayHelper::map(\common\components\Helpers\StructureHelper::getActivities(),'id','name');
        //$gender = LoanHelper::getGender();
        $sources = ArrayHelper::map(ArchiveReportsHelper::getBanksResources(),'bank_code','bank_name');

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update ArchiveReports #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "ArchiveReports #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update ArchiveReports #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'report_names' => $report_names,
                        'regions' => $regions,
                        'projects' => $projects,
                        'products' => $products,
                        'activities' => $activities,
                        //'gender' => $gender,
                        'sources' => $sources,
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
                    'report_names' => $report_names,
                    'regions' => $regions,
                    'projects' => $projects,
                    'products' => $products,
                    'activities' => $activities,
                    //'gender' => $gender,
                    'sources' => $sources,
                ]);
            }
        }
    }

    /**
     * Delete an existing ArchiveReports model.
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
     * Delete multiple existing ArchiveReports model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
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
     * Finds the ArchiveReports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ArchiveReports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArchiveReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $path=Yii::getAlias('@console').'/exports/'.$model->file_path;
        //echo $path;
        /*die();
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            throw new NotFoundHttpException("can't find {$model->file_path} file");
        }
    }*/

    public function actionExports($folder,$file_name){
        $file_path = Yii::$app->basePath.'\web\\'.$folder.'\\'.$file_name;
        if(file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        }
        else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    public function actionDownload(){
        $path = 'D:\wamp\www\cc\frontend\web\exports\portfolio_report20-01-2018-13-08-43.zip';
        if(file_exists($path)){
            \Yii::$app->response->sendFile($path)->send();
            //unlink($path);
        }
        else{
            return $this->redirect(['site/dashboard']);
        }
    }

}
