<?php

namespace backend\controllers;

use backend\models\PasswordResetRequestForm;
use Codeception\Module\Memcache;
use common\components\Helpers\CodeHelper;
use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\StructureHelper;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Designations;
use common\models\mapping_models\UserWithObject;
use common\models\mapping_models\UserWithProjects;
use common\models\UserStructureMapping;
use tests\models\User;
use Yii;
use common\models\Users;
use common\models\search\UsersSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * UsersController implements the CRUD actions for UsersCopy model.
 */
class UsersController extends Controller
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
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        return Yii::$app->response->redirect(['site/main']);
                    }
                },
                'only' => ['index', 'view', 'create', 'update', '_form'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', '_form'],
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
     * Lists all UsersCopy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array' => ([
                'cities' => $cities
            ]),
        ]);
    }

    public function actionForgotPasswordSms($id)
    {
        $user = $this->findModel($id);
        $model = new PasswordResetRequestForm();
        $model->email = $user->email;
        $code = CodeHelper::getCode();
        $user->password = $code;
        $user->setPassword($user->password);
        $msg = SmsHelper::getCodeTextMIS($code);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($user->save()) {
            $sms = SmsHelper::Sendsms($user->mobile, $msg);
            if ($sms->corpsms[0]->type == 'Success') {
                SmsHelper::SmsLogs('register', $user);
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Forgot Password",
                    'content' => "<span class='text-success'>New password send to user's mobile. </span>",
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            } else {
                return $this->redirect(['view', 'id' => $id]);
            }
        }
    }

    public function actionForgotPassword($id)
    {
        $user = $this->findModel($id);
        $model = new PasswordResetRequestForm();
        $model->email = $user->email;
        $code = CodeHelper::getCode();
        $user->password = $code;
        $user->setPassword($user->password);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($user->save()) {
            if ($model->sendForgotPasswordEmail($code)) {

                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Forgot Password",
                    'content' => "<span class='text-success'>New password send to user's email. </span>",
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            }
        }
    }

    /**
     * Displays a single UsersCopy model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model_userwithproject = new UserWithProjects();
        $model_userwithproject->loadProjects($id);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $model_userwithbranches = new UserWithObject();
        $model_userwithbranches->loadBranches($id);
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');

        $model_userwithareas = new UserWithObject();
        $model_userwithareas->loadAreas($id);
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');

        $model_userwithregions = new UserWithObject();
        $model_userwithregions->loadRegions($id);
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');

        $configurations = ConfigurationsHelper::getConfig($id,"user");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("user");

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Users",
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                    'array' => ([
                        'projects' => $projects,
                        'model_userwithproject' => $model_userwithproject,
                        'branches' => $branches,
                        'model_userwithbranches' => $model_userwithbranches,
                        'areas' => $areas,
                        'model_userwithareas' => $model_userwithareas,
                        'regions' => $regions,
                        'model_userwithregions' => $model_userwithregions,
                        'configurations'=>$configurations,
                        'global_configurations'=>$global_configurations,
                    ]),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array' => ([
                    'projects' => $projects,
                    'model_userwithproject' => $model_userwithproject,
                    'branches' => $branches,
                    'model_userwithbranches' => $model_userwithbranches,
                    'areas' => $areas,
                    'model_userwithareas' => $model_userwithareas,
                    'regions' => $regions,
                    'model_userwithregions' => $model_userwithregions,
                    'configurations'=>$configurations,
                    'global_configurations'=>$global_configurations,

                ]),
            ]);
        }
    }

    /**
     * Creates a new UsersCopy model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Users();
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        $designations = ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');


        $model_userwithproject = new UserWithProjects();
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $model_userwithbranches = new UserWithObject();
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');

        $model_userwithareas = new UserWithObject();
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');

        $model_userwithregions = new UserWithObject();
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new Users",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,
                        ]),

                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) /*&& $model->save()*/) {
                $model->setPassword($model->password);
                $model->joining_date=strtotime($model->joining_date);
                $designation = Designations::findOne(['code' => $request->post()['designation']]);
                $model->designation_id = isset($designation) ? $designation->id : 0;
                if($model->save()) {
                    AuthAssignment::deleteAll(['user_id' => $model->id]);
                    $auth_assign = new AuthAssignment();
                    $auth_assign->item_name = ($request->post()['designation']);
                    $auth_assign->user_id = $model->id;
                    $auth_assign->save();
                    if (isset($request->post()['branch_id']) && $request->post()['branch_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['branch_id'];
                        $structuremapping->obj_type = 'branch';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['team_id']) && $request->post()['team_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['team_id'];
                        $structuremapping->obj_type = 'team';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['field_id']) && $request->post()['field_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['field_id'];
                        $structuremapping->obj_type = 'field';
                        $structuremapping->save();
                    }

                    $model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);

                    $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['area_ids'];
                    $model_userwithbranches->saveAreas($model->id);

                    if (isset($request->post()['UserWithObject']['branch_ids']) && $request->post()['UserWithObject']['branch_ids'] != null && (!isset($request->post()['branch_id']) || $request->post()['branch_id'] != null)) {
                        $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['branch_ids'];
                        $model_userwithbranches->saveBranches($model->id);
                    }

                    $model_userwithregions->obj_ids = Yii::$app->request->post()['UserWithObject']['region_ids'];
                    $model_userwithregions->saveRegions($model->id);

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Users",
                        'content' => '<span class="text-success">Create Users success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                }
                else {
                    return [
                        'title' => "Create new Users",
                        'content' => $this->renderAjax('create', [
                            'model' => $model,
                            'array' => ([
                                'designations' => $designations,
                                'cities' => $cities,
                                'projects' => $projects,
                                'model_userwithproject' => $model_userwithproject,
                                'branches' => $branches,
                                'model_userwithbranches' => $model_userwithbranches,
                                'areas' => $areas,
                                'model_userwithareas' => $model_userwithareas,
                                'regions' => $regions,
                                'model_userwithregions' => $model_userwithregions,
                            ]),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new Users",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,
                        ]),
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
                $model->setPassword($model->password);
                $model->joining_date=strtotime($model->joining_date);
                $designation = Designations::findOne(['code' => $request->post()['designation']]);
                $model->designation_id = isset($designation) ? $designation->id : 0;
                if($model->save()) {
                    AuthAssignment::deleteAll(['user_id' => $model->id]);
                    $auth_assign = new AuthAssignment();
                    $auth_assign->item_name = ($request->post()['designation']);
                    $auth_assign->user_id = $model->id;
                    $auth_assign->save();

                    if (isset($request->post()['branch_id']) && $request->post()['branch_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['branch_id'];
                        $structuremapping->obj_type = 'branch';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['team_id']) && $request->post()['team_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['team_id'];
                        $structuremapping->obj_type = 'team';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['field_id']) && $request->post()['field_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['field_id'];
                        $structuremapping->obj_type = 'field';
                        $structuremapping->save();
                    }

                    $model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);
                    if (isset($request->post()['UserWithObject']['branch_ids']) && $request->post()['UserWithObject']['branch_ids'] != null && (!isset($request->post()['branch_id']) || $request->post()['branch_id'] != null)) {
                        $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['branch_ids'];
                        $model_userwithbranches->saveBranches($model->id);
                    }
                    $model_userwithareas->obj_ids = Yii::$app->request->post()['UserWithObject']['area_ids'];
                    $model_userwithareas->saveAreas($model->id);
                    $model_userwithregions->obj_ids = Yii::$app->request->post()['UserWithObject']['region_ids'];
                    $model_userwithregions->saveRegions($model->id);

                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else {
                    return $this->render('create', [
                        'model' => $model,
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,

                        ]),
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'array' => ([
                        'designations' => $designations,
                        'cities' => $cities,
                        'projects' => $projects,
                        'model_userwithproject' => $model_userwithproject,
                        'branches' => $branches,
                        'model_userwithbranches' => $model_userwithbranches,
                        'areas' => $areas,
                        'model_userwithareas' => $model_userwithareas,
                        'regions' => $regions,
                        'model_userwithregions' => $model_userwithregions,

                    ]),
                ]);
            }
        }

    }

    /**
     * Updates an existing UsersCopy model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {


        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $designations = ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');


        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $model_userwithproject = new UserWithProjects();
        $model_userwithproject->loadProjects($id);

        $model_userwithbranches = new UserWithObject();
        $model_userwithbranches->loadBranches($id);
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');

        $model_userwithareas = new UserWithObject();
        $model_userwithareas->loadAreas($id);
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');

        $model_userwithregions = new UserWithObject();
        $model_userwithregions->loadRegions($id);
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        if ($request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update Users",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,


                        ]),
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) /*&& $model->save()*/) {
                $passwor_change=Users::find()->where(['id'=>$model->id])->one();
                if($passwor_change->password!=$model->password){
                    $model->setPassword($model->password);
                }
                $model->joining_date=strtotime($model->joining_date);
                if(isset($request->post()['designation']) && $request->post()['designation'] != null) {
                    $designation = Designations::findOne(['code' => $request->post()['designation']]);
                    $model->designation_id = isset($designation) ? $designation->id : 0;
                }
                if($model->save()) {
                    if(isset($request->post()['designation']) && $request->post()['designation'] != null) {
                        AuthAssignment::deleteAll(['user_id' => $id]);
                        $auth_assign = new AuthAssignment();
                        $auth_assign->item_name = ($request->post()['designation']);
                        $auth_assign->user_id = $id;
                        $auth_assign->save();
                    }
                    if (isset($request->post()['branch_id']) && $request->post()['branch_id'] != null) {

                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'branch']);
                        $structuremappingbranch = new UserStructureMapping();
                        $structuremappingbranch->user_id = $model->id;
                        $structuremappingbranch->obj_id = $request->post()['branch_id'];
                        $structuremappingbranch->obj_type = 'branch';
                        $structuremappingbranch->save();
                    }
                    if (isset($request->post()['team_id']) && $request->post()['team_id'] != null) {
                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'team']);
                        $structuremappingteam = new UserStructureMapping();
                        $structuremappingteam->user_id = $model->id;
                        $structuremappingteam->obj_id = $request->post()['team_id'];
                        $structuremappingteam->obj_type = 'team';
                        $structuremappingteam->save();
                    }
                    if (isset($request->post()['field_id']) && $request->post()['field_id'] != null) {
                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'field']);
                        $structuremappingfield = new UserStructureMapping();
                        $structuremappingfield->user_id = $model->id;
                        $structuremappingfield->obj_id = $request->post()['field_id'];
                        $structuremappingfield->obj_type = 'field';
                        $structuremappingfield->save();
                    }


                    $model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);
                    if (!isset($request->post()['branch_id']) || $request->post()['branch_id'] == null) {
                        $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['branch_ids'];
                        $model_userwithbranches->saveBranches($model->id);
                    }

                    $model_userwithareas->obj_ids = Yii::$app->request->post()['UserWithObject']['area_ids'];
                    $model_userwithareas->saveAreas($model->id);

                    $model_userwithregions->obj_ids = Yii::$app->request->post()['UserWithObject']['region_ids'];
                    $model_userwithregions->saveRegions($model->id);
                    $configurations = ConfigurationsHelper::getConfig($id, "user");
                    $global_configurations = ConfigurationsHelper::getConfigGlobal("user");
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Users",
                        'content' => '<span class="text-success">Updated User success</span>',

                    ];
                }
                else {
                    return [
                        'title' => "Update Users #" . $id,
                        'content' => $this->renderAjax('update', [
                            'model' => $model,
                            'model' => $this->findModel($id),
                            'array' => ([
                                'designations' => $designations,
                                'cities' => $cities,
                                'projects' => $projects,
                                'model_userwithproject' => $model_userwithproject,
                                'branches' => $branches,
                                'model_userwithbranches' => $model_userwithbranches,
                                'areas' => $areas,
                                'model_userwithareas' => $model_userwithareas,
                                'regions' => $regions,
                                'model_userwithregions' => $model_userwithregions,
                            ]),
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                    ];
                }
            } else {
                return [
                    'title' => "Update Users #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'model' => $this->findModel($id),
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,
                        ]),
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $passwor_change=Users::find()->where(['id'=>$model->id])->one();
                if($passwor_change->password!=$model->password){
                    $model->setPassword($model->password);
                }

                $model->joining_date=strtotime($model->joining_date);
                if(isset($request->post()['designation']) && $request->post()['designation'] != null) {
                    $designation = Designations::findOne(['code' => $request->post()['designation']]);
                    $model->designation_id = isset($designation) ? $designation->id : 0;
                }
                if($model->save()) {
                    AuthAssignment::deleteAll(['user_id' => $id]);
                    $auth_assign = new AuthAssignment();
                    $auth_assign->item_name = ($request->post()['designation']);
                    $auth_assign->user_id = $id;
                    $auth_assign->save();
                    if (isset($request->post()['branch_id']) && $request->post()['branch_id'] != null) {

                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'branch']);
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['branch_id'];
                        $structuremapping->obj_type = 'branch';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['team_id']) && $request->post()['team_id'] != null) {
                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'team']);
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['team_id'];
                        $structuremapping->obj_type = 'team';
                        $structuremapping->save();
                    }
                    if (isset($request->post()['field_id']) && $request->post()['field_id'] != null) {
                        UserStructureMapping::deleteAll(['user_id' => $id, 'obj_type' => 'field']);
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['field_id'];
                        $structuremapping->obj_type = 'field';
                        $structuremapping->save();
                    }


                    $model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);

                    if (isset($request->post()['UserWithObject']['branch_ids']) && $request->post()['UserWithObject']['branch_ids'] != null && (!isset($request->post()['branch_id']) || $request->post()['branch_id'] == null)) {
                        $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['branch_ids'];
                        $model_userwithbranches->saveBranches($model->id);
                    }

                    $model_userwithareas->obj_ids = Yii::$app->request->post()['UserWithObject']['area_ids'];
                    $model_userwithareas->saveAreas($model->id);

                    $model_userwithregions->obj_ids = Yii::$app->request->post()['UserWithObject']['region_ids'];
                    $model_userwithregions->saveRegions($model->id);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else {
                    return $this->render('update', [
                        'model' => $model,
                        'array' => ([
                            'designations' => $designations,
                            'cities' => $cities,
                            'projects' => $projects,
                            'model_userwithproject' => $model_userwithproject,
                            'branches' => $branches,
                            'model_userwithbranches' => $model_userwithbranches,
                            'areas' => $areas,
                            'model_userwithareas' => $model_userwithareas,
                            'regions' => $regions,
                            'model_userwithregions' => $model_userwithregions,

                        ]),
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'array' => ([
                        'designations' => $designations,
                        'cities' => $cities,
                        'projects' => $projects,
                        'model_userwithproject' => $model_userwithproject,
                        'branches' => $branches,
                        'model_userwithbranches' => $model_userwithbranches,
                        'areas' => $areas,
                        'model_userwithareas' => $model_userwithareas,
                        'regions' => $regions,
                        'model_userwithregions' => $model_userwithregions,

                    ]),
                ]);
            }
        }
    }

    /**
     * Delete an existing UsersCopy model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    /**
     * Delete multiple existing UsersCopy model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the UsersCopy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UsersCopy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionArea()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $region_id = $parents[0];
                //print_r($region_id);
                $out = StructureHelper::getAreasByRegion($region_id);

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
    public function actionBranch()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $area_id = $parents[0];
                //print_r($region_id);
                $out = StructureHelper::getBranchesByArea($area_id);

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
    public function actionTeam()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $branch_id = $parents[0];
                //print_r($region_id);
                $out = StructureHelper::getTeams($branch_id);

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionField()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $ids = $_POST['depdrop_parents'];

            //$region_id = empty($ids[0]) ? null : $ids[0];
            $team_id = empty($ids[0]) ? null : $ids[0];
            if ($team_id != null) {
                $out = StructureHelper::getFields($team_id);
                echo json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
    public function actionUserReport()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 300);
            $this->layout = 'csv';
            $headers=[];
            /*for ($i = 0; $i < 19; $i++) {
                array_push($headers, array_keys($_GET['UsersSearch'])[$i]);
            }*/
            $headers = array('Name', 'EMP Code'/*, 'Father Name'*//*'Role', 'Region', 'Area', 'Branch', 'Team', 'Field',*/,'Region', 'Area', 'Branch', 'No of Members', 'No of Applications', 'No of Social Appraisal',
                'No of Business Appraisals','No of Verifications', 'No of Groups', 'No of Loans', 'No of Fund Requests','No of Disbursements','No of Recoveries');

            // $headers = array_keys($_GET['UsersSearch']);
            $groups = array();
            $searchModel = new UsersSearch();
            $query = $searchModel->searchuserreport($_GET,true);
            $data = $query->all();
            $i=0;

            foreach ($data as $g){
                $groups[$i]['username'] = $g['fullname'];
               // $groups[$i]['cnic'] = $g['fullname'];
                $groups[$i]['emp_code'] = $g['emp_code'];
                /*$groups[$i]['role'] = $g['role']['item_name'];
                $groups[$i]['region_id'] = $g['regionname']['name'];
                $groups[$i]['area_id'] = $g['areaname']['name'];
                $groups[$i]['branch_id'] = $g['branchname']['name'];
                $groups[$i]['team_id'] = $g['teamname']['name'];
                $groups[$i]['field_id'] = $g['fieldname']['name'];*/
                $groups[$i]['region_id'] = $g['regionname']['name'];
                $groups[$i]['area_id'] = $g['areaname']['name'];
                $groups[$i]['branch_id'] = $g['branchname']['name'];

                $groups[$i]['no_of_members'] = $g['no_of_members'];
                $groups[$i]['no_of_applications'] = $g['no_of_applications'];
                $groups[$i]['no_of_social_appraisals'] = $g['no_of_social_appraisals'];
                $groups[$i]['no_of_business_appraisals'] = $g['no_of_business_appraisals'];
                $groups[$i]['no_of_verifications'] = $g['no_of_verifications'];
                $groups[$i]['no_of_groups'] = $g['no_of_groups'];
                $groups[$i]['no_of_loans'] = $g['no_of_loans'];
                $groups[$i]['no_of_fund_requests'] = $g['no_of_fund_requests'];
                $groups[$i]['no_of_disbursements'] = $g['no_of_disbursements'];
                $groups[$i]['no_of_recoveries'] = $g['no_of_recoveries'];

                $groups[$i]['city_id'] = isset($g->city->name)?$g->city->name:'';
                $i++;
            }
            $progress['data'] = array(array('region'=>!empty($searchModel->region_id)?\common\models\Regions::find()->where(['id'=>$searchModel->region_id])->one()->name:'All',
               'area'=> !empty($searchModel->area_id)? \common\models\Areas::find()->where(['id'=>$searchModel->area_id])->one()->name:'All',
               'branch'=> !empty($searchModel->branch_id)?\common\models\Branches::find()->where(['id'=>$searchModel->branch_id])->one()->name :'All',
                'team'=> !empty($searchModel->team_id)?\common\models\Teams::find()->where(['id'=>$searchModel->team_id])->one()->name :'All',
                'field'=> !empty($searchModel->field_id)?\common\models\Fields::find()->where(['id'=>$searchModel->field_id])->one()->name :'All',
                'role'=> !empty($searchModel->role)?$searchModel->role:'All',

            ));

            $progress['header'] = array('Region', 'Area', 'Branch', 'Team', 'Field', 'Role');
            ExportHelper::ExportCSV('User-Report.csv', $headers, $groups, $progress);
            die();
        }
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->searchuserreport(Yii::$app->request->queryParams);
        $roles=ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');

        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(),'id','name');
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');

        return $this->render('user_report/userreport', [
            'dataProvider' => $dataProvider,
            'searchModel'=>$searchModel,
            'regions'=>$regions,
            'areas'=>$areas,
            'branches'=>$branches,
            'roles'=>$roles
        ]);
    }
    public function actionUploadPic($id)
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
                    'title' => "Create new Users",
                    'content' => $this->renderAjax('upload_pic', [
                        'model' => $model,

                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) /*&& $model->save()*/) {


                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Users",
                        'content' => '<span class="text-success">Create Users success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
            } else {
                return [
                    'title' => "Create new Users",
                    'content' => $this->renderAjax('upload_pic', [
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
                if(!empty(UploadedFile::getInstance($model, 'image'))){
                    $img=Users::find()->where(['id'=>$id])->one()->image;
                    if (!empty($img)) {
                        /*if(file_exists(Yii::getAlias('@anyname') . '/frontend/web/' .'uploads/' . '/users/'  . '/' . $img)){
                            unlink(Yii::getAlias('@anyname') . '/frontend/web/' . 'uploads/' . '/users/' . '/' . $img);
                        }*/
                        if(file_exists(ImageHelper::getAttachmentPath() .'uploads/' . '/users/'  . '/' . $img)){
                        unlink(ImageHelper::getAttachmentPath() . 'uploads/' . '/users/' . '/' . $img);
                    }
                    }/*else {
                        FileHelper::createDirectory(Yii::getAlias('@anyname') . '/frontend/web/' .'uploads/' . 'profile_pic');
                    }*/
                    $rand=rand(111111, 999999);
                    $model->image=$rand;
                    $model->image = UploadedFile::getInstance($model, 'image');
                    //$model->image->saveAs(Yii::getAlias('@anyname') . '/frontend/web/' .'uploads/' . '/users/' .$model->username.'_' . $rand . '.' . 'jpeg');
                    $model->image->saveAs(ImageHelper::getAttachmentPath() .'uploads/' . '/users/' .$model->username.'_' . $rand . '.' . 'jpeg');
                    $model->image=$model->username.'_'.$rand.'.jpeg';
                }
                else{
                    $img=Users::find()->where(['id'=>$id])->one()->image;
                    $model->image=$img;
                }
                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);

                }
            } else {
                return $this->render('upload_pic', [
                    'model' => $model,
                ]);
            }
        }

    }
}
