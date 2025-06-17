<?php

namespace frontend\modules\user_management\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\models\AuthAssignment;
use common\models\Designations;
use common\models\Divisions;
use common\models\mapping_models\UserWithObject;
use common\models\mapping_models\UserWithProjects;
use common\models\search\UsersSearch;
use common\models\UserHierarchyChangeRequest;
use common\models\UserStructureMapping;
use common\models\UserTransferActions;
use common\models\UserTransferHierarchy;
use frontend\modules\user_management\UserManagement;
use Yii;
use common\models\UserTransfers;
use common\models\Users;
use common\models\search\UserTransfersSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TransfersController implements the CRUD actions for UserTransfers model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserTransfers models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->searchUsers(Yii::$app->request->queryParams);
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        //Yii::$app->Permission->getSearchFilter($dataProvider,'users',Yii::$app->controller->action->id,'frontend');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array' => ([
                'cities' => $cities
            ]),
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Users();
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $designations = UsersHelper::getSubRoles($user_role);
        //$designations = ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');


        $model_userwithproject = new UserWithProjects();
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $model_userwithbranches = new UserWithObject();
        //$branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');

        $model_userwithareas = new UserWithObject();
        //$areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');

        $model_userwithregions = new UserWithObject();
        //$regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,'frontend');
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id,'frontend');
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,'frontend');

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
                $model->mobile='92'.ltrim($model->mobile, '0');
                $designation = Designations::findOne(['code' => $request->post()['designation']]);
                $model->designation_id = isset($designation) ? $designation->id : 0;
                if($model->save()) {
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

                    /*$model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);*/

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
                $model->mobile='92'.ltrim($model->mobile, '0');
                if(isset($request->post()['designation']) && $request->post()['designation'] != null) {
                    $designation = Designations::findOne(['code' => $request->post()['designation']]);
                    $model->designation_id = isset($designation) ? $designation->id : 0;
                }
                if($model->save()) {
                    if(isset($request->post()['designation']) && $request->post()['designation'] != null) {
                        $auth_assign = new AuthAssignment();
                        $auth_assign->item_name = ($request->post()['designation']);
                        $auth_assign->user_id = (string)$model->id;
                        $auth_assign->save();
                    }

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

                    /*$model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);*/
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

    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
