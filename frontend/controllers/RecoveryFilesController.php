<?php

namespace frontend\controllers;

use common\components\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\RbacHelper;
use common\models\RecoveryErrors;
use Yii;
use common\models\RecoveryFiles;
use common\models\search\RecoveryFilesSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;
/**
 * RecoveryFilesController implements the CRUD actions for RecoveryFiles model.
 */
class RecoveryFilesController extends Controller
{
    /**
     * @inheritdoc
     */
    public $rbac_type = 'frontend';

    public function behaviors()
    {
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
    }

    /**
     * Lists all RecoveryFiles models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new RecoveryFilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $files = RecoveryFiles::find()->all();
        $files_list = ArrayHelper::map($files,'id','file_name');
        $source = [ 'branch' => 'branch', 'bi' => 'bi', 'hbl' => 'hbl', 'hble' => 'hble', 'mcb' => 'mcb', 'nbp' => 'nbp', 'ep' => 'ep', 'akb' => 'abk', 'trb' => 'trb','omni' => 'omni','abl'=>'abl','bop'=>'bop','WROFF'=>'Write-Off'];
        $status = ['0'=>'Approval Pending','1' => 'Approved','2' => 'Execute','3' => 'In-Process', '4' => 'Completed'];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'files_list' => $files_list,
            'source' => $source,
            'status' => $status,
        ]);
    }


    /**
     * Displays a single RecoveryFiles model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;


        if(isset($_GET['download'])){
            $model=$this->findModel($id);
            $file_name = $model->file_name;
            $file_source = $model->source;

            $file_path = ImageHelper::getAttachmentPath() .'/recoveries/' . $file_source . '/' . $file_name;

            if(file_exists($file_path)) {
                return Yii::$app->response->sendFile($file_path);
            }
            else {
                throw new NotFoundHttpException('File not exist');
            }
        }   

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote']).
                            Html::a('Download File',['view','id'=>$id, 'download'=>true],['class'=>'btn btn-info'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }



    }

    /**
     * Creates a new RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new RecoveryFiles();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new RecoveryFiles",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $random = Rand(1111, 9999);
                $model->status = '0';
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_by = Yii::$app->user->getId();
                if($model->validate()) {
                    $model->file->saveAs(ImageHelper::getAttachmentPath().'/recoveries/' . $model->source . '/' . $model->file->baseName . '.' . $model->file->extension);
                    $model->file_name = $model->file->name;
                } else {
                    return [
                        'title'=> "Create new RecoveryFiles",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }

                $file_name = $model->file_name;
                $file_source = $model->source;
                $file_path =ImageHelper::getAttachmentPath(). '/recoveries/' . $file_source . '/' . $file_name;
                if (($handle = fopen($file_path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['credit','cnic/sanction_no', 'recv_date', 'receipt_no', 'bank_branch_name', 'bank_branch_code'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($file_path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "Error",
                                'content' => '<span class="text-success">Recovery File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }
                    }
                }
                if ($model->save(true)) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new RecoveryFiles",
                        'content' => '<span class="text-success">Create RecoveryFiles success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                }
                else {
                    return [
                        'title'=> "Create new RecoveryFiles",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }
            }else{           
                return [
                    'title'=> "Create new RecoveryFiles",
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
            if ($model->load($request->post()) ) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $random = Rand(1111, 9999);
                $model->status = '0';
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_by = Yii::$app->user->getId();
                if($model->validate()) {
                    $model->file->saveAs(ImageHelper::getAttachmentPath().'/recoveries/' . $model->source . '/' . $model->file->baseName . '.' . $model->file->extension);
                    $model->file_name = $model->file->name;
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

                $file_name = $model->file_name;
                $file_source = $model->source;
                $file_path =ImageHelper::getAttachmentPath() . '/recoveries/' . $file_source . '/' . $file_name;
                if (($handle = fopen($file_path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    /*print_r($header);
                    die();*/
                    $columns = ['credit','sanction_no', 'recv_date', 'receipt_no', 'bank_branch_name', 'bank_branch_code'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($file_path);
                            return $this->render('create', [
                                'model' => $model,
                            ]);
                        }
                    }
                }
                if ($model->save(true)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing RecoveryFiles model.
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
                    'title'=> "Update RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->file = 'abc';
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update RecoveryFiles #".$id,
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
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }





    /**
     * Updates an existing RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionApprove($id)
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
                    'title'=> "Update RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->file = 'abc';
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update RecoveryFiles #".$id,
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
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }







    /**
     * Updates an existing RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionExecute($id)
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
                    'title'=> "Update RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->file = 'abc';
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update RecoveryFiles #".$id,
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
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }



    public function actionDownloadSample(){
        $file_path = Yii::$app->basePath.'/web/recoveries/sample/banks_recovery_sheet_sample.csv';
        //die($file_path);
        if(file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        }
        else {
            throw new NotFoundHttpException('File not exist');
        }
    }


    public function actionExports($id){
        $data = RecoveryErrors::find()->select(['bank_branch_name','bank_branch_code','source','sanction_no','cnic','from_unixtime(recv_date, \'%Y-%m-%d\') as recv_date','credit','receipt_no','balance','error_description','comments','status'])->where(['recovery_files_id' => $id])->asArray()->all();
        //$heading = ['bank_branch_name','bank_branch_code','source','sanction_no','recv_date','credit','receipt_no','balance','error_description','comments','status'];

        $heading = array_keys($data[0]);
        $file_model = $this->findModel($id);
        $array = explode('.csv',$file_model->file_name);
        $filename = $array[0]. '_error';
        //$filename = $file_model->source .'_'. date('Ymd',strtotime($file_model->file_date)). '_error';
        \common\components\Helpers\ExportHelper::ExportCSV($filename,$heading,$data);
    }

    /**
     * Delete an existing RecoveryFiles model.
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
     * Delete multiple existing RecoveryFiles model.
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
     * Finds the RecoveryFiles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RecoveryFiles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RecoveryFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
