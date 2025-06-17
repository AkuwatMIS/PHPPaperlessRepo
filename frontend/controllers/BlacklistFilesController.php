<?php

namespace frontend\controllers;

use Yii;
use common\models\BlacklistFiles;
use common\models\search\BlacklistFilesSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * BlacklistFilesController implements the CRUD actions for BlacklistFiles model.
 */
class BlacklistFilesController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * @inheritdoc
     */
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
     * Lists all BlacklistFiles models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new BlacklistFilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDownloadSample(){
        $file_path = Yii::$app->basePath.'/web/blacklist/sample/blacklist_sheet_sample.csv';
        //die($file_path);
        if(file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        }
        else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    /**
     * Displays a single BlacklistFiles model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "BlacklistFiles #".$id,
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

    public function actionCreate()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        $request = Yii::$app->request;
        $model = new BlacklistFiles();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Add new Blacklist File",
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
                $model->created_by = Yii::$app->user->getId();
                $model->updated_by = Yii::$app->user->getId();
                if($model->validate()) {
                    $model->file->saveAs('blacklist/' . $random.'_'.$model->file->baseName . '.' . $model->file->extension);
                    $model->file_name = $random.'_'.$model->file->name;
                } else {
                    return [
                        'title'=> "Add new Blacklist file",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }

                $file_name = $model->file_name;
                $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/blacklist/' . $file_name;
                $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                if (($handle = fopen($file_path, "r")) !== FALSE && strtolower($ext)!="json") {
                    $header = fgetcsv($handle);
                    $columns = ['name','parentage','cnic', 'cnic_invalid','institute_name', 'province','reject_reason', 'description', 'location','type'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($file_path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "Error",
                                'content' => '<span class="text-success">Blacklist File have not required fields.</span>',
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
                        'title' => "Add new Blacklist File",
                        'content' => '<span class="text-success">Blacklist File added successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                }
                else {
                    print_r($model->getErrors());
                    die();
                    return [
                        'title'=> "Add new Blacklist File",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }
            }else{
                return [
                    'title'=> "Add new Blacklist File",
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
                $model->created_by = Yii::$app->user->getId();
                $model->updated_by = Yii::$app->user->getId();
                if($model->validate()) {
                    $model->file->saveAs('blacklist/'. $random.'_'.$model->file->baseName . '.' . $model->file->extension);
                    $model->file_name = $random.'_'.$model->file->name;
                } else {
                    print_r($model->getErrors());
                    die();
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

                $file_name = $model->file_name;
                $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/blacklist/' . $file_name;

                if (($handle = fopen($file_path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    /*print_r($header);
                    die();*/

                    $columns = ['name','parentage','cnic', 'cnic_invalid','institute_name', 'province','reject_reason', 'description', 'location','type'];
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

    public function actionExports($folder,$file_name){
        $file_path = Yii::$app->basePath.'/web/'.$folder.'/'.$file_name;
        if(file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        }
        else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    /**
     * Updates an existing BlacklistFiles model.
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
                    'title'=> "Update BlacklistFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "BlacklistFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update BlacklistFiles #".$id,
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
     * Delete an existing BlacklistFiles model.
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
     * Delete multiple existing BlacklistFiles model.
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
     * Finds the BlacklistFiles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlacklistFiles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlacklistFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
