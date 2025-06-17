<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\StructureHelper;
use common\models\ConfigRules;
use common\models\mapping_models\ProjectWithProducts;
use common\models\Users;
use common\widgets\config\Config;
use Yii;
use common\models\Projects;
use common\models\search\ProjectsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * ProjectsController implements the CRUD actions for Projects model.
 */
class ProjectsController extends Controller
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
     * Lists all Projects models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Projects model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model_projectwithproduct = new ProjectWithProducts();
        $model_projectwithproduct->loadProducts($id);
        $products = ArrayHelper::map(StructureHelper::getProducts(),'id','name');
        $configurations = ConfigurationsHelper::getConfig($id,"project");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("project");


        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Projects #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'configurations'=>$configurations,
                            'global_configurations'=>$global_configurations
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array'=>([
                    'products'=>$products,
                    'model_projectwithproduct' => $model_projectwithproduct,
                    'configurations'=>$configurations,
                    'global_configurations'=>$global_configurations,

                ]),
            ]);
        }
    }

    /**
     * Creates a new Projects model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $request = Yii::$app->request;
        $model = new Projects();
        $products = ArrayHelper::map(StructureHelper::getProducts(),'id','name');
        $model_projectwithproduct = new ProjectWithProducts();

        $model_config=new ConfigRules();
       // echo'<pre>';
        /*$model_config['dropdown']=array(
            "project_id"=>ArrayHelper::map(ConfigurationsHelper::getConfigproject(),"id","name"),
            'group'=>ConfigurationsHelper::getConfiggroups(),
            'parent_type'=>ConfigurationsHelper::getConfigparenttype(),
            'priority'=>ConfigurationsHelper::getConfigpriority(),
        );*/

        if(isset($request->post()['ConfigRules'])) {
            $model_config->load($request->post());

            $model_config->save();

        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Projects",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'model_config'=>$model_config

                        ]),

                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) ){
                $model->created_by = Yii::$app->user->getId();
                $model->assigned_to = Yii::$app->user->getId();
                if($model->save()) {
                    $model_projectwithproduct->product_ids = Yii::$app->request->post()['ProjectWithProducts']['product_ids'];
                    $model_projectwithproduct->saveProducts($model->id);
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Projects",
                        'content' => '<span class="text-success">Create Projects success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    print_r($model->getErrors());
                    die();
                }
            }else{
                return [
                    'title'=> "Create new Projects",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'model_config'=>$model_config

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
                $model_projectwithproduct->product_ids = Yii::$app->request->post()['ProjectWithProducts']['product_ids'];
                $model_projectwithproduct->saveProducts($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'array'=>([
                        'products'=>$products,
                        'model_projectwithproduct' => $model_projectwithproduct,
                        'model_config'=>$model_config

                    ]),
                ]);
            }
        }

    }

    /**
     * Updates an existing Projects model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $products = ArrayHelper::map(StructureHelper::getProducts(),'id','name');
        $model_projectwithproduct = new ProjectWithProducts();
        $model_projectwithproduct->loadProducts($id);

        $model_config=new ConfigRules();
        /*$model_config['dropdown']=array(
            "project_id"=>ArrayHelper::map(ConfigurationsHelper::getConfigproject(),"id","name"),
            'group'=>ConfigurationsHelper::getConfiggroups(),
            'parent_type'=>ConfigurationsHelper::getConfigparenttype(),
            'priority'=>ConfigurationsHelper::getConfigpriority(),
        );*/
        if(isset($request->post()['ConfigRules'])) {
            $model_config->load($request->post());
            $model_config->save();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Projects #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,

                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'model_config'=>$model_config
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->save()){

                $model_projectwithproduct->product_ids = Yii::$app->request->post()['ProjectWithProducts']['product_ids'];
                $model_projectwithproduct->saveProducts($model->id);
                $configurations = ConfigurationsHelper::getConfig($id,"project");
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Projects #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'model_config'=>$model_config,
                            'configurations'=>$configurations,

                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "Update Projects #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'array'=>([
                            'products'=>$products,
                            'model_projectwithproduct' => $model_projectwithproduct,
                            'model_config'=>$model_config

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
                $model_projectwithproduct->product_ids = Yii::$app->request->post()['ProjectWithProducts']['product_ids'];
                $model_projectwithproduct->saveProducts($model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'array'=>([
                        'products'=>$products,
                        'model_projectwithproduct' => $model_projectwithproduct,
                        'model_config'=>$model_config

                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing Projects model.
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
     * Delete multiple existing Projects model.
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
     * Finds the Projects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Projects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Projects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUploadLogo($id)
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new Project",
                    'content' => $this->renderAjax('upload_pic', [
                        'model' => $model,

                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) /*&& $model->save()*/) {


                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new Project",
                    'content' => '<span class="text-success">Create Users success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new Project",
                    'content' => $this->renderAjax('upload_logo', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) /*&& $model->save()*/) {
                if(!empty(UploadedFile::getInstance($model, 'logo'))){
                    $img=Projects::find()->where(['id'=>$id])->one()->logo;
                    if (!empty($img)) {
                        if (file_exists(ImageHelper::getAttachmentPath() . 'uploads/' . '/projects/' . '/' . $img)) {
                            unlink(ImageHelper::getAttachmentPath() . 'uploads/' . '/projects/' . '/' . $img);
                        }
                    }

                    $file = UploadedFile::getInstance($model, 'logo');
                    $rand=rand(111111, 999999);
                   // $file_name =strtotime(date('Y-m-d H:i:s')).'_'. $file->baseName . '.' . $file->extension;
                    $file_name =strtotime(date('Y-m-d H:i:s')).'_'. $rand . '.' . $file->extension;
                    $file->saveAs(ImageHelper::getAttachmentPath().'/uploads/projects/' . $file_name);
                    $model->logo = $file_name;

                    //$rand=rand(111111, 999999);
                    //$model->logo=$rand;
                    //$model->logo = UploadedFile::getInstance($model, 'logo');
                    //$model->image->saveAs(Yii::getAlias('@anyname') . '/frontend/web/' .'uploads/' . '/users/' .$model->username.'_' . $rand . '.' . 'jpeg');
                    //$model->logo->saveAs(ImageHelper::getAttachmentPath() .'uploads/' . '/projects/' .$model->name.'_' . $rand . '.' . 'jpeg');
                    //$model->logo=$model->name.'_'.$rand.'.jpeg';
                }
                else{
                    $img=Projects::find()->where(['id'=>$id])->one()->logo;
                    $model->logo=$img;
                }
                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);

                }
            } else {
                return $this->render('upload_logo', [
                    'model' => $model,
                ]);
            }
        }

    }
}
