<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationDetails;
use common\models\Areas;
use common\models\Branches;
use common\models\MemberInfo;
use common\models\NadraVerisys;
use common\models\Regions;
use common\models\Projects;
use common\models\RejectedNadraVerisys;
use Yii;
use common\models\Applications;
use common\models\ProjectsTevta;
use common\models\search\ApplicationsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ApplicationsController implements the CRUD actions for Applications model.
 */
class ApplicationsController extends Controller
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
         * Lists all Applications models.
         * @return mixed
         */
        public function actionIndex()
        {
                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', 300);
                $searchModel = new ApplicationsSearch();
                $params = Yii::$app->request->queryParams;
                $params['ApplicationsSearch']['app_date'] = strtotime(date("Y-m-d", strtotime("-1 Years")));
                $dataProvider = $searchModel->search($params);
                $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
                $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
                $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
                $is_lock = array("0" => "unlocked", "1" => "locked");
                $referrals = ArrayHelper::map(\common\components\Helpers\ListHelper::getReferralsList(), 'id', 'name');

                return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'array' => ([
                                'projects' => $projects,
                                'regions' => $regions,
                                'areas' => $areas,
                                'branches' => $branches,
                                'is_lock' => $is_lock,
                                'referrals' => $referrals,
                        ]),
                ]);
        }

        public function actionIndexSearch()
        {
                /*$date1 = date('Y-m-10', '1526108334');
                print_r($date1);
                die();*/
                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', 300);
                $searchModel = new ApplicationsSearch();
                $params = Yii::$app->request->queryParams;

                $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
                $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
                $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
                $referrals = ArrayHelper::map(\common\components\Helpers\ListHelper::getReferralsList(), 'id', 'name');
                $is_lock = array("0" => "unlocked", "1" => "locked");
                if (empty(Yii::$app->request->queryParams)) {
                        $dataProvider = array();
                } else {
                        $params['ApplicationsSearch']['app_date'] = strtotime(date("Y-m-d", strtotime("-1 Years")));
                        $dataProvider = $searchModel->search($params);
                }
                return $this->render('index_search', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'regions' => $regions,
                        'array' => ([
                                'projects' => $projects,
                                'regions' => $regions,
                                'areas' => $areas,
                                'branches' => $branches,
                                'is_lock' => $is_lock,
                                'referrals' => $referrals,
                        ]),
                ]);
        }

        /**
         * Displays a single Applications model.
         * @param integer $id
         * @return mixed
         */
        public function actionView($id)
        {
                $request = Yii::$app->request;
                $model = $this->findModel($id);
                $configurations = ConfigurationsHelper::getConfig($id, "application");
                $global_configurations = ConfigurationsHelper::getConfigGlobal("application");
                if ($request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                                'title' => "Applications #" . $id,
                                'content' => $this->renderAjax('view', [
                                        'model' => $this->findModel($id),
                                        'array' => ([
                                                'configurations' => $configurations,
                                                'global_configurations' => $global_configurations,
                                        ]),

                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                        ];
                } else {
                        return $this->render('view', [
                                'model' => $this->findModel($id),
                                'array' => ([
                                        'configurations' => $configurations,
                                        'global_configurations' => $global_configurations,
                                ]),

                        ]);
                }
        }

        /**
         * Creates a new Applications model.
         * For ajax request will return json object
         * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
         * @return mixed
         */
        public function actionCreate()
        {
                $request = Yii::$app->request;
                $model = new Applications();
                $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
                $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
                $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
                $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                $products = ArrayHelper::map(StructureHelper::getProducts(), 'id', 'name');
                $activities = ArrayHelper::map(StructureHelper::getActivities(), 'id', 'name');
                $is_lock = array("0" => "unlocked", "1" => "locked");

                if ($request->isAjax) {
                        /*
                        *   Process for ajax request
                        */
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        if ($request->isGet) {
                                return [
                                        'title' => "Create new Applications",
                                        'content' => $this->renderAjax('create', [
                                                'model' => $model,
                                                'array' => ([
                                                        'regions' => $regions,
                                                        'areas' => $areas,
                                                        'branches' => $branches,
                                                        'projects' => $projects,
                                                        'products' => $products,
                                                        'activities' => $activities,
                                                        'is_lock' => $is_lock,

                                                ]),
                                        ]),
                                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                                ];
                        } else if ($model->load($request->post()) && $model->save()) {
                                return [
                                        'forceReload' => '#crud-datatable-pjax',
                                        'title' => "Create new Applications",
                                        'content' => '<span class="text-success">Create Applications success</span>',
                                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                                ];
                        } else {
                                return [
                                        'title' => "Create new Applications",
                                        'content' => $this->renderAjax('create', [
                                                'model' => $model,
                                                'array' => ([
                                                        'regions' => $regions,
                                                        'areas' => $areas,
                                                        'branches' => $branches,
                                                        'projects' => $projects,
                                                        'products' => $products,
                                                        'activities' => $activities,
                                                        'is_lock' => $is_lock,

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
                        if ($model->load($request->post()) && $model->save()) {
                                return $this->redirect(['view', 'id' => $model->id]);
                        } else {
                                return $this->render('create', [
                                        'model' => $model,
                                        'array' => ([
                                                'regions' => $regions,
                                                'areas' => $areas,
                                                'branches' => $branches,
                                                'projects' => $projects,
                                                'products' => $products,
                                                'activities' => $activities,
                                                'is_lock' => $is_lock,

                                        ]),
                                ]);
                        }
                }

        }

        /**
         * Updates an existing Applications model.
         * For ajax request will return json object
         * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id
         * @return mixed
         */
        public function actionUpdate($id)
        {
                $request = Yii::$app->request;
                $model = $this->findModel($id);
                $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
                $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
                $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
                $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                $products = ArrayHelper::map(StructureHelper::getProducts(), 'id', 'name');
                $activities = ArrayHelper::map(StructureHelper::getActivities(), 'id', 'name');
                $is_lock = array("0" => "unlocked", "1" => "locked");
                $configurations = ConfigurationsHelper::getConfig($id, "application");
                $global_configurations = ConfigurationsHelper::getConfigGlobal("application");
                if (!empty($model->sub_activity)) {
                        $model->sub_activity = explode(',', $model->sub_activity);
                        $out = [];
                        foreach ($model->sub_activity as $key => $value) {
                                $out[$value] = $value;
                        }
                        $model->sub_activity = $out;
                }
                if ($request->isAjax) {
                        /*
                        *   Process for ajax request
                        */
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        if ($request->isGet) {
                                return [
                                        'title' => "Update Applications #" . $id,
                                        'content' => $this->renderAjax('update', [
                                                'model' => $model,
                                                'array' => ([
                                                        'regions' => $regions,
                                                        'areas' => $areas,
                                                        'branches' => $branches,
                                                        'projects' => $projects,
                                                        'products' => $products,
                                                        'activities' => $activities,
                                                        'is_lock' => $is_lock,

                                                ]),
                                        ]),
                                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                                ];
                        } else if ($model->load($request->post())/* && $model->save()*/) {
                                if (!empty($model->sub_activity)) {
                                        $model->sub_activity = implode(',', $model->sub_activity);
                                }
                                if ($model->save()) {
                                        $rejected_nadra_verysis = RejectedNadraVerisys::find()->where(['application_id' => $id])->one();
                                        $member_info = MemberInfo::find()->where(['member_id' => $model->member_id])->one();
                                        if (!empty($rejected_nadra_verysis) && $rejected_nadra_verysis != null) {
                                                $rejected_nadra_verysis->member_info_id = $member_info->id;
                                                $rejected_nadra_verysis->save();

                                        }
                                        $modelNadraVerisys = NadraVerisys::find()->where(['application_id'=>$model->id])->one();
                                        if(!empty($modelNadraVerisys) && $modelNadraVerisys!=null){
                                            $modelNadraVerisys->member_id = $model->member_id;
                                            $modelNadraVerisys->save(false);
                                        }
                                        return [
                                                'forceReload' => '#crud-datatable-pjax',
                                                'title' => "Applications #" . $id,
                                                'content' => $this->renderAjax('view', [
                                                        'model' => $this->findModel($id),
                                                        'array' => ([
                                                                'configurations' => $configurations,
                                                                'global_configurations' => $global_configurations,
                                                        ]),
                                                ]),
                                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                                        ];
                                } else {
                                        return [
                                                'title' => "Update Applications #" . $id,
                                                'content' => $this->renderAjax('update', [
                                                        'model' => $model,
                                                        'array' => ([
                                                                'regions' => $regions,
                                                                'areas' => $areas,
                                                                'branches' => $branches,
                                                                'projects' => $projects,
                                                                'products' => $products,
                                                                'activities' => $activities,
                                                                'is_lock' => $is_lock,

                                                        ]),
                                                ]),
                                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                                        ];
                                }

                        } else {
                                return [
                                        'title' => "Update Applications #" . $id,
                                        'content' => $this->renderAjax('update', [
                                                'model' => $model,
                                                'array' => ([
                                                        'regions' => $regions,
                                                        'areas' => $areas,
                                                        'branches' => $branches,
                                                        'projects' => $projects,
                                                        'products' => $products,
                                                        'activities' => $activities,
                                                        'is_lock' => $is_lock,

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
                        if ($model->load($request->post())/* && $model->save()*/) {
                                if (!empty($model->sub_activity)) {
                                        $model->sub_activity = implode(',', $model->sub_activity);
                                }
                                if ($model->save()) {
                                        return $this->redirect(['view', 'id' => $model->id]);
                                } else {
                                        return $this->render('update', [
                                                'model' => $model,
                                                'array' => ([
                                                        'regions' => $regions,
                                                        'areas' => $areas,
                                                        'branches' => $branches,
                                                        'projects' => $projects,
                                                        'products' => $products,
                                                        'activities' => $activities,
                                                        'is_lock' => $is_lock,

                                                ]),
                                        ]);
                                }
                        } else {
                                return $this->render('update', [
                                        'model' => $model,
                                        'array' => ([
                                                'regions' => $regions,
                                                'areas' => $areas,
                                                'branches' => $branches,
                                                'projects' => $projects,
                                                'products' => $products,
                                                'activities' => $activities,
                                                'is_lock' => $is_lock,

                                        ]),
                                ]);
                        }
                }
        }

        /**
         * Delete an existing Applications model.
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
                $model->deleted_by = Yii::$app->user->getId();
                $model->deleted_at = strtotime(date('Y-m-d'));
                if ($model->save()) {
                        $applicationDetails = ApplicationDetails::find()->where(['application_id' => $id])->andWhere(['parent_type' => 'member'])->one();
                        if ($applicationDetails) {
                                $applicationDetails->deleted = 1;
                                $applicationDetails->save(false);
                        }
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
         * Delete multiple existing Applications model.
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
         * Finds the Applications model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param integer $id
         * @return Applications the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
                if (($model = Applications::findOne($id)) !== null) {
                        return $model;
                } else {
                        throw new NotFoundHttpException('The requested page does not exist.');
                }
        }

        public function actionLogs($id = null, $field = null)
        {
                $request = Yii::$app->request;
                $model = $this->findModel($id);
                /*print_r($model);
                die();*/
                if ($request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        if ($request->isGet) {
                                return [
                                        'title' => "Log Applications #" . $id,
                                        'header' => [
                                                'close' => ['display' => 'none'],
                                        ],
                                        'content' => $this->renderAjax('logs', [
                                                'id' => $id,
                                                'field' => $field,
                                        ]),
                                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                                Html::a('Back', ['view', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                                ];
                        }
                }

                return $this->render('logs', [
                        'id' => $id,
                        'field' => $field,
                ]);
        }

        public function actionApplicationsLogs()
        {
                $request = Yii::$app->request;
                if ($request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        if ($request->isGet) {
                                return [
                                        'title' => "Log Application",
                                        'header' => [
                                                'close' => ['display' => 'none'],
                                        ],
                                        'content' => $this->renderAjax('logs', [
                                                'id' => '',
                                                'field' => '',
                                        ]),
                                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                                ];
                        }
                }
                return $this->render('logs', [
                        'id' => '',
                        'field' => '',
                ]);
        }
}
