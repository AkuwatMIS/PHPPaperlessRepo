<?php

namespace frontend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\mapping_models\UserWithObject;
use common\models\mapping_models\UserWithProjects;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\search\MembersSearch;
use common\models\search\UsersSearch;
use common\models\UserHierarchyChangeRequest;
use common\models\Users;
use common\models\UserStructureMapping;
use common\models\UserTransferHierarchy;
use common\models\UserTransfers;
use Yii;
use common\models\Members;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\models\Model;
use yii\web\UnauthorizedHttpException;
/**
 * MembersController implements the CRUD actions for Members model.
 */
class UsersController extends Controller
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
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else {
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
     * Lists all UsersCopy models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = array_keys($_GET['UsersSearch']);
            $groups = array();
            $searchModel = new UsersSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilter($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['username'] = $g['username'];
                $groups[$i]['fullname'] = $g['fullname'];
                $groups[$i]['father_name'] = $g['father_name'];
                $groups[$i]['email'] = $g['email'];
                $groups[$i]['emp_code'] = $g['emp_code'];
                $groups[$i]['cnic'] = $g['cnic'];
                $groups[$i]['city_id'] = isset($g->city->name)?$g->city->name:'';
                $i++;
            }
            ExportHelper::ExportCSV('users.csv',$headers,$groups);
            die();
        }
        $searchModel = new UsersSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        $regions = ArrayHelper::map(StructureHelper::getRegions(),'id','name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array' => ([
                'cities' => $cities,
                'regions'=>$regions
            ]),
        ]);
    }

    public function actionManagement($id)
    {
        $request = Yii::$app->request;
        $model = Users::findOne($id);
        $designations = ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');
        //$types = ['transfer' => 'Transfer', 'promot/demot' => 'Promotion/Demotion', 'leave' => 'Leave'];
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $actions = UserTransferHierarchy::find()->select('value,type')->where(['role' => $user_role])->andWhere(['like','value', '%'.UsersHelper::getRole($id).'%'])->asArray()->all();
        $types = [];
        foreach ($actions as $action)
        {
            $types[] = $action->type;
        }
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'region'])/*->asArray()*/->one();
        $regions=Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,'frontend');
        $transfer_model=new UserTransfers();
        ///RM id and user  Role
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($model->id);
        $users = $auth->getUserIdsByRole('RM');
        $user_region_id = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->one();
        $rm_id = 0;
        foreach ($users as $user) {
            if (!empty($user_region_id)) {
                $obj_id = \common\models\UserStructureMapping::find()->select(['user_id'])->where(['user_id' => $user, 'obj_type' => 'region', 'obj_id' => $user_region_id->obj_id])->one();
                if (!empty($obj_id)) {
                    $rm_id = $obj_id;
                }
            }
        }
        if ($transfer_model->load(Yii::$app->request->post())) {
            if(!$transfer_model->save())
            {
                /*print_r($transfer_model->getErrors());
                die();*/
            }
            return $this->redirect(['view', 'id' => $transfer_model->id]);
        }
        ////
        /*
        *   Process for non-ajax request
        */
        return $this->render('transfer', [
            'model' => $model,
            'array'=>[
                "region"=>$region,
                "area"=>$area,
                "branch"=>$branch,
                'team'=>$team,
                "field"=>$field
            ],
            'auth'=>[
                'roles'=>$roles,
                'users'=>$users,
                'rm_id'=>$rm_id
            ],
            "regions"=>$regions,
            'change_model'=>$transfer_model,
            'types'=>$types,
            'designations'=>$designations
        ]);
    }

    public function actionProfile()
    {
        return $this->render('profile', [
            'model' => $this->findModel(Yii::$app->user->getId())
        ]);
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
        $designations = ArrayHelper::map(StructureHelper::getDesignations(), 'name', 'name');


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
            } else if ($model->load($request->post())) {
                $model->setPassword($model->password);
                if($model->save()){
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
            /*echo '<pre>';
            print_r($request->post());
            $model->load($request->post());
            print_r($model);
            die();*/
            /*
            *   Process for non-ajax request
            */
            if ($request->post()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->load($request->post());
                    $model->save();
                    //auth assignment model, set values and save
                    AuthAssignment::deleteAll(['user_id' => $model->id]);
                    $auth_assign = new AuthAssignment();
                    $auth_assign->item_name = ($request->post()['designation']);
                    $auth_assign->user_id = (string)$model->id;
                    $auth_assign->save();

                    //save selected projects
                    $model_userwithproject->project_ids = Yii::$app->request->post()['UserWithProjects']['project_ids'];
                    $model_userwithproject->saveProjects($model->id);
                    //save selected branches
                    $model_userwithbranches->obj_ids = Yii::$app->request->post()['UserWithObject']['branch_ids'];
                    $model_userwithbranches->saveBranches($model->id);
                    //save selected areas
                    $model_userwithareas->obj_ids = Yii::$app->request->post()['UserWithObject']['area_ids'];
                    $model_userwithareas->saveAreas($model->id);
                    //save selected regions
                    $model_userwithregions->obj_ids = Yii::$app->request->post()['UserWithObject']['region_ids'];
                    $model_userwithregions->saveRegions($model->id);

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }else {
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

            /*if ($model->load($request->post())) {
                $model->setPassword($model->password);
                if($model->save()){
                    AuthAssignment::deleteAll(['user_id' => $model->id]);
                    $auth_assign = new AuthAssignment();
                    $auth_assign->item_name = ($request->post()['designation']);
                    $auth_assign->user_id = $model->id;
                    if(!$auth_assign->save()){
                        return $this->redirect(['create']);
                    }

                    if (isset($request->post()['branch_id']) && $request->post()['branch_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['branch_id'];
                        $structuremapping->obj_type = 'branch';
                        if(!$structuremapping->save()){
                            return $this->redirect(['create']);
                        }
                    }
                    if (isset($request->post()['team_id']) && $request->post()['team_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['team_id'];
                        $structuremapping->obj_type = 'team';
                        if(!$structuremapping->save()){
                            return $this->redirect(['create']);
                        }
                    }
                    if (isset($request->post()['field_id']) && $request->post()['field_id'] != null) {
                        $structuremapping = new UserStructureMapping();
                        $structuremapping->user_id = $model->id;
                        $structuremapping->obj_id = $request->post()['field_id'];
                        $structuremapping->obj_type = 'field';
                        if(!$structuremapping->save()){
                            return $this->redirect(['create']);
                        }
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
                }else{
                    return $this->redirect(['create']);
                }
            } */
        }

    }
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        /*
        *   Process for non-ajax request
        */
        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['profile']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

    }
    public function actionTransfer($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'region'])/*->asArray()*/->one();
        $regions=Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $change_model=new UserHierarchyChangeRequest();
    ///RM id and user  Role
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($model->id);
        $users = $auth->getUserIdsByRole('RM');
        $user_region_id = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->one();
        $rm_id = 0;
        foreach ($users as $user) {
            if (!empty($user_region_id)) {
                $obj_id = \common\models\UserStructureMapping::find()->select(['user_id'])->where(['user_id' => $user, 'obj_type' => 'region', 'obj_id' => $user_region_id->obj_id])->one();
                if (!empty($obj_id)) {
                    $rm_id = $obj_id;
                }
            }
        }
    ////
        /*
        *   Process for non-ajax request
        */
            return $this->render('transfer', [
                'model' => $model,
                'array'=>[
                    "region"=>$region,
                    "area"=>$area,
                    "branch"=>$branch,
                    'team'=>$team,
                    "field"=>$field
                ],
                'auth'=>[
                    'roles'=>$roles,
                    'users'=>$users,
                    'rm_id'=>$rm_id
                ],
                "regions"=>$regions,
                'change_model'=>$change_model
                ]);
    }

    public function actionStaff()
    {
        $searchModel = new UsersSearch();

        $dataProvider = $searchModel->searchStaff(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=30;
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $designations = ArrayHelper::map(AuthItem::find()->select('name as id,description as name')->where(['type'=>1])->andWhere(['in','name',['RM','AM']])->all(), 'id', 'name');
        return $this->render('staff/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' =>$regions,
            'designations' => $designations
        ]);

    }

    /**
     * Finds the Members model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Members the loaded model
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
}
