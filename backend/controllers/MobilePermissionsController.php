<?php

namespace backend\controllers;

use johnitvn\rbacplus\models\Role;
use johnitvn\rbacplus\models\RoleSearch;
use common\models\MobileScreens;
use Yii;
use common\models\MobilePermissions;
use common\models\search\MobilePermissionsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MobilePermissionsController implements the CRUD actions for MobilePermissions model.
 */
class MobilePermissionsController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all MobilePermissions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MobilePermissions model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($name)
    {
        $screens = MobileScreens::find()->all();
        $mobile_permissions = MobilePermissions::find()->where(['role' => $name])->all();
        $permissions = [];
        foreach ($mobile_permissions as $permission)
        {
            $permissions[] = $permission->mobileScreen->name;
        }
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Permissions of ".$name,
                'content'=>$this->renderAjax('view', [
                    'permissions' => $permissions,
                    'screens' => $screens,
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
            ];
        }else {
            return $this->render('view', [
                'permissions' => $permissions,
                'screens' => $screens,
            ]);
        }
    }

    /**
     * Creates a new MobilePermissions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate($name)
    {
        $request = Yii::$app->request;
        $model = new MobilePermissions();
        $mobile_screens = MobileScreens::find()->all();
        $mobile_permissions = MobilePermissions::find()->where(['role' => $name])->all();
        $mobile_screens = ArrayHelper::map($mobile_screens,'id','name');
        $mobile_permissions_array = array();
        foreach ($mobile_permissions as $key=>$mobile_permission){
            $mobile_permissions_array[] = $mobile_permission->mobile_screen_id;
        }

        $form_data = $request->post();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Set Mobile Permissions",
                    'content'=>$this->renderAjax('create', [
                        'mobile_screens' => $mobile_screens,
                        'mobile_permissions' => $mobile_permissions_array,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($form_data){

                foreach ($form_data['Permission'] as $p)
                {
                    if(!in_array($p,$mobile_permissions_array)) {
                        $model = new MobilePermissions();
                        $model->role = $name;
                        $model->mobile_screen_id = $p;
                        $model->save();
                    }

                }
                foreach ($mobile_permissions_array as $item)
                {
                    if(!in_array($item, $form_data['Permission']))
                    {
                        $model = MobilePermissions::find()->where(['role' => $name, 'mobile_screen_id'=> $item])->one();
                        $model->delete();
                    }
                }
                $screens = MobileScreens::find()->all();
                $mobile_permissions = MobilePermissions::find()->where(['role' => $name])->all();
                $permissions = [];
                foreach ($mobile_permissions as $permission)
                {
                    $permissions[] = $permission->mobileScreen->name;
                }
                return [
                    'title'=> "Mobile Permissions",
                    'content'=>$this->renderAjax('view', [
                        'permissions' => $permissions,
                        'screens' => $screens,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['create','name'=>$name],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
              //  return $this->redirect(['index']);

            }else{
                return [
                    'title'=> "Set Mobile Permissions",
                    'content'=>$this->renderAjax('create', [
                        'mobile_screens' => $mobile_screens,
                        'mobile_permissions' => $mobile_permissions_array,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($form_data) {
                foreach ($form_data['Permission'] as $p)
                {
                    if(!in_array($p,$mobile_permissions_array)) {
                        $model = new MobilePermissions();
                        $model->role = $name;
                        $model->mobile_screen_id = $p;
                        $model->save();
                    }

                }
                foreach ($mobile_permissions_array as $item)
                {
                    if(!in_array($item, $form_data['Permission']))
                    {
                        $model = MobilePermissions::find()->where(['role' => $name, 'mobile_screen_id'=> $item])->one();
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);

            } else {
                return $this->render('create', [
                    'mobile_screens' => $mobile_screens,
                    'mobile_permissions' => $mobile_permissions_array,
                ]);
            }
        }

    }
    /**
     * Updates an existing MobilePermissions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MobilePermissions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MobilePermissions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MobilePermissions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MobilePermissions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
