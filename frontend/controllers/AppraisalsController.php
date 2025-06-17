<?php

namespace frontend\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Lists;
use common\models\SectionFieldsConfigs;
use common\models\ViewSectionFields;
use common\models\ViewSections;
use Yii;
use common\models\Appraisals;
use common\models\search\AppraisalsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * AppraisalsController implements the CRUD actions for Appraisals model.
 */
class AppraisalsController extends Controller
{
    /**
     * @inheritdoc
     */
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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
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
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    /**
     * Lists all Appraisals models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppraisalsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProject($id){
        $appData = Applications::find()->where(['id'=>$id])->select(['project_id'])->one();
        $response['project_id'] = $appData->project_id;
        return json_encode($response);
    }


    /**
     * Displays a single Appraisals model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Appraisals #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Appraisals model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        // $model = new Appraisals();
        $model = new Applications();
        /*echo '<pre>';
        print_r($request->post());
        die();*/
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');


        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new Appraisals",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new Appraisals",
                    'content' => '<span class="text-success">Create Appraisals success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new Appraisals",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            if (($request->post()) /*&& $model->save()*/) {
                if(empty($request->post()['Applications']['id']) || empty($request->post()['Applications']['appraisal_id'])){
                    $model->load($request->post());
                    $model->addError('appraisal_id', 'Application or Appraisal Type Not Set!');
                    return $this->render('create', [
                        'model' => $model,
                        'projects' => $projects,
                    ]);
                }
                $appraisal = Appraisals::find()->where(['id' => $request->post()['Applications']['appraisal_id']])->one();
                $app_check = ApplicationActions::find()->where(['parent_id' => $request->post()['Applications']['id']])->andWhere(['action' => $appraisal->name])->one();
                if (!empty($app_check) && $app_check->status == 0) {
                    $table_name = '';
                    $data = explode('_', $appraisal->appraisal_table);
                    foreach ($data as $d) {
                        $table_name .= ucfirst($d);
                    }
                    $class = 'common\models\\' . ucfirst($table_name);

                    if (file_exists(Yii::getAlias('@anyname') . '/common/models/' . ucfirst($table_name) . '.php')) {
                        $_appraisal_data = new $class();
                        $_appraisal_data->set_values($request->post());
                        if (!empty($_appraisal_data->getErrors())) {
                            $model->id = ($request->post()['Applications']['id']);
                            $model->appraisal_id = ($request->post()['Applications']['appraisal_id']);
                            foreach ($_appraisal_data->getErrors() as $key => $value) {
                                $model->addError($key, $value[0]);
                            }
                            return $this->render('create', [
                                'model' => $model,
                                'projects' => $projects,

                            ]);
                        }

                        if (!$_appraisal_data->save()) {
                            $model->id = ($request->post()['Applications']['id']);
                            $model->appraisal_id = ($request->post()['Applications']['appraisal_id']);
                            foreach ($_appraisal_data->getErrors() as $key => $value) {
                                $model->addError($key, $value[0]);
                            }
                            return $this->render('create', [
                                'model' => $model,
                                'projects' => $projects,

                            ]);
                        } else {
                            ActionsHelper::updateAction('application', $_appraisal_data->application_id, $appraisal->name);      //after application sync
                            //$update_actions = ApplicationActions::find()->where(['parent_id' => $request->post()['Applications']['id']])->andWhere(['action' => $appraisal->name])->one();
                            //$update_actions->status = 1;
                            //$update_actions->save();
                            $appp = Applications::find()->select(['id','project_id','product_id','activity_id','created_by'])->where(['id' => $request->post()['Applications']['id']])->one();
                            $appAction = ApplicationActions::find()->where(['parent_id'=>$appp->id])->andWhere(['action'=>"approved/rejected"])->one();

                            if(empty($appAction) && $appAction==null){
                                if($request->post()['Applications']['appraisal_id'] == 3 && $appp->activity_id == 1){
                                    $model = new ApplicationActions();
                                    $model->parent_id = $appp->id;
                                    $model->user_id = $appp->created_by;
                                    $model->action = 'approved/rejected';
                                    if(!$model->save()){
                                        var_dump($model->getErrors());
                                        die();
                                    }
                                }elseif ($request->post()['Applications']['appraisal_id'] == 6 && $appp->activity_id == 23){
                                    $model = new ApplicationActions();
                                    $model->parent_id = $appp->id;
                                    $model->user_id = $appp->created_by;
                                    $model->action = 'approved/rejected';
                                    if(!$model->save()){
                                        var_dump($model->getErrors());
                                        die();
                                    }
                                }else{
                                    ActionsHelper::insertActions('appraisal', $appp->project_id, $request->post()['Applications']['id'], $_appraisal_data->created_by, 1,$appp->product_id);
                                }
                            }


                        }
                        $data = explode('_', $appraisal->appraisal_table);
                        $app_name = ucfirst($data[1]) . ' ' . ucfirst($data[0]);
                        $session->addFlash('success', $app_name . ' Done');
                        $model = new Applications();
                        $model->id = $request->post()['Applications']['id'];
                        return $this->render('create', [
                            'model' => $model,
                            'projects' => $projects,

                        ]);
                        return $this->redirect('create');
                        return $this->redirect('/applications/view?id=' . $request->post()['Applications']['id']);

                    }
                    return $this->render('create', [
                        'model' => $model,
                        'projects' => $projects,

                    ]);
                } else {
                    $model->id = $request->post()['Applications']['id'];
                    $model->addError('appraisal_id', $appraisal->name . ' already Done');
                    return $this->render('create', [
                        'model' => $model,
                        'projects' => $projects,

                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'projects' => $projects,

                ]);
            }


        }

    }

    /**
     * Updates an existing Appraisals model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update Appraisals #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Appraisals #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update Appraisals #" . $id,
                    'content' => $this->renderAjax('update', [
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
     * Delete an existing Appraisals model.
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
     * Delete multiple existing Appraisals model.
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

    public function actionForm($id,$application_id=0)
    {
        $appraisal = Appraisals::findOne($id);
        $array = array();
        $app_check = ApplicationActions::find()->where(['parent_id' => $application_id])->andWhere(['action' => $appraisal->name])->one();
               if (!empty($app_check) && $app_check->status == '1') {
                    $array=['success_type'=>'false','message'=>'Appraisal already done'];
                   return  \Yii::$app->response->data = json_encode($array);
        }
        $array = array();

        //$key = $appraisal->appraisal_table.'_'.$application->activity_id;
        //$form = CacheHelper::getFormCache($key);
            //if (empty($form)) {

            if (!empty($appraisal)) {
                $application = Applications::find()->where(['id' => $application_id])->one();
                $project_details = ViewSectionFields::find()->where(['table_name' => $appraisal->appraisal_table,'parent_id'=>0])->orderBy('sort_order asc')->all();
                //Buisness Appraisal additional fields
                if ($appraisal->appraisal_table == 'appraisals_business') {
                    //$project_details = ViewSectionFields::find()->where(['table_name' => 'appraisals_business'])->all();
                    $asset_types = array('fixed_business_assets', 'running_capital', 'business_expenses', 'new_required_assets');
//                    $application = Applications::find()->where(['id' => $application_id])->one();
                    if (!in_array($application->product_id,[1,13,16])) {
                        $array = ['success_type' => 'false', 'message' => 'Business Appraisal Not available for this application'];
                        return \Yii::$app->response->data = json_encode($array);
                    }
                    foreach ($project_details as $project_detail) {
                        if ($project_detail['field'] == 'fixed_business_assets' || $project_detail['field'] == 'running_capital' || $project_detail['field'] == 'business_expenses' || $project_detail['field'] == 'new_required_assets') {
                            if(isset($project_detail['field']) && $project_detail['field'] == 'fixed_business_assets'){
                                $ansValueData = self::getAnswersValues("fixed_business_assets");
                                $type_reset = 'reverse';
                            }else{
                                $ansValueData = self::getAnswersValues(($project_detail['field']) ? $application->activity->name . '-' . $project_detail['field'] : "");
                                $type_reset = '';
                            }
                            $array[] = [
                                "id" => isset($project_detail->id) ? $project_detail->id : '',
                                "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                                "column" => isset($project_detail->field) ? $project_detail->field : '',
                                "question_id" => isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                                "is_mandatory" => isset($project_detail->sectionFieldConfigRequired->value) ? $project_detail->sectionFieldConfigRequired->value : '0',
                                "is_hidden" => false,
                                "type" => isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                                "format" => isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : '',
                                "label" => isset($project_detail->sectionFieldConfigLabel->value) ? $project_detail->sectionFieldConfigLabel->value : '',
                                "place_holder" => isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                                "default_value" => "",
                                "mask" => "",
                                "answers" => isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                                "answers_values" => $ansValueData,
                                //"answers_values"=> self::getAnswersValues(($project_detail['field']) ? 'Agriculture inputs-new_required_assets' : ""),
                                "default_visibility" => isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                                "style" => array(
                                    "width" => isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                                    "height" => isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                                ),
                                "constraints" => [],
                                "dependent_question" => [
                                    /*"question_id" => "et_institute_name",
                                    "actions" => array(
                                        "action" => "update_content",
                                        "visibility" => "visible",
                                        "visible_on_answers" => "TEVTA"
                                    )*/
                                ],
                                'type_reset'=>$type_reset
                            ];
                        } else {
                            $array[] = [
                                "id" => isset($project_detail->id) ? $project_detail->id : '',
                                "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                                "column" => isset($project_detail->field) ? $project_detail->field : '',
                                "question_id" => isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                                "is_mandatory" => isset($project_detail->sectionFieldConfigRequired->value) ? $project_detail->sectionFieldConfigRequired->value : '0',
                                "is_hidden" => false,
                                "type" => isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                                "format" => isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : '',
                                "label" => isset($project_detail->sectionFieldConfigLabel->value) ? $project_detail->sectionFieldConfigLabel->value : '',
                                "place_holder" => isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                                "default_value" => "",
                                "mask" => "",
                                "answers" => isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                                "answers_values" => self::getAnswersValues(isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : ""),
                                "default_visibility" => isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                                "style" => array(
                                    "width" => isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                                    "height" => isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                                ),
                                "constraints" => [],
                                "dependent_question" => [
                                    /*"question_id" => "et_institute_name",
                                    "actions" => array(
                                        "action" => "update_content",
                                        "visibility" => "visible",
                                        "visible_on_answers" => "TEVTA"
                                    )*/
                                ],
                                'type_reset'=>''
                            ];
                        }
                    }
                } else {
                    foreach ($project_details as $project_detail) {
                        if ($application->activity_id == 302){
                            if ($project_detail->field=='current_price'){
                            }else{
                                $array[] = [
                                    "id" => isset($project_detail->id) ? $project_detail->id : '',
                                    "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                                    "column" => isset($project_detail->field) ? $project_detail->field : '',
                                    "question_id" => isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                                    "is_mandatory" => isset($project_detail->sectionFieldConfigRequired->value) ? $project_detail->sectionFieldConfigRequired->value : '0',
                                    "is_hidden" => '0',
                                    "type" => isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                                    "format" => isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : 'text',
                                    "label" => isset($project_detail->sectionFieldConfigLabel->value) ? $project_detail->sectionFieldConfigLabel->value : '',
                                    "place_holder" => isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                                    "default_value" => "",
                                    "mask" => "",
                                    "answers" => isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                                    "answers_values" => self::getAnswersValues(isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : ""),
                                    "default_visibility" => isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                                    "style" => array(
                                        "width" => isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                                        "height" => isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                                    ),
                                    "constraints" => [],
                                    "dependent_question" => [
                                        /*"question_id" => "et_institute_name",
                                        "actions" => array(
                                            "action" => "update_content",
                                            "visibility" => "visible",
                                            "visible_on_answers" => "TEVTA"
                                        )*/
                                    ],
                                    'type_reset'=>''
                                ];
                            }
                        }else{
                            $array[] = [
                                "id" => isset($project_detail->id) ? $project_detail->id : '',
                                "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                                "column" => isset($project_detail->field) ? $project_detail->field : '',
                                "question_id" => isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                                "is_mandatory" => isset($project_detail->sectionFieldConfigRequired->value) ? $project_detail->sectionFieldConfigRequired->value : '0',
                                "is_hidden" => '0',
                                "type" => isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                                "format" => isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : 'text',
                                "label" => isset($project_detail->sectionFieldConfigLabel->value) ? $project_detail->sectionFieldConfigLabel->value : '',
                                "place_holder" => isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                                "default_value" => "",
                                "mask" => "",
                                "answers" => isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                                "answers_values" => self::getAnswersValues(isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : ""),
                                "default_visibility" => isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                                "style" => array(
                                    "width" => isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                                    "height" => isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                                ),
                                "constraints" => [],
                                "dependent_question" => [
                                    /*"question_id" => "et_institute_name",
                                    "actions" => array(
                                        "action" => "update_content",
                                        "visibility" => "visible",
                                        "visible_on_answers" => "TEVTA"
                                    )*/
                                ],
                                'type_reset'=>''
                            ];
                        }

                    }
                }
                /*if ($appraisal->appraisal_table == 'appraisals_business') {
                    $project_details = ViewSectionFields::find()->where(['table_name' => 'appraisals_business_details'])->all();
                    $asset_types = array('fixed_business_assets', 'running_capital', 'business_expenses', 'new_required_assets');
                    $i=0;
                    foreach ($asset_types as $asst_type) {
                        $application=Applications::findOne($application_id);
                        foreach ($project_details as $project_detail) {
                            $array[] = [
                                "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                                "column" => isset($project_detail->field) ? $asst_type . '-' . $project_detail->field : '',
                                "question_id" => isset($project_detail->sectionFieldsConfigs[0]->value) ? $project_detail->sectionFieldsConfigs[0]->value : '',
                                "is_mandatory" => true,
                                "is_hidden" => false,
                                "type" => isset($project_detail->sectionFieldsConfigs[1]->value) ? $project_detail->sectionFieldsConfigs[1]->value : '',
                                "format" => isset($project_detail->sectionFieldsConfigs[2]->value) ? $project_detail->sectionFieldsConfigs[2]->value : '',
                                "label" => "",
                                "place_holder" => isset($project_detail->sectionFieldsConfigs[3]->value) ? $project_detail->sectionFieldsConfigs[3]->value : "Enter",
                                "default_value" => $project_detail->field=="total_amount"?"":$project_detail->field,
                                "mask" => "",
                                "answers" => ($project_detail->field=="assets_list") ? $asst_type : "",
                                "answers_values" =>self::getAnswersValues(($project_detail->field) ? $application->activity->name.'-'.$asst_type : ""),
                                "default_visibility" => isset($project_detail->sectionFieldsConfigs[4]->value) ? $project_detail->sectionFieldsConfigs[4]->value : "",
                                "style" => array(
                                    "width" => isset($project_detail->sectionFieldsConfigs[6]->value) ? $project_detail->sectionFieldsConfigs[6]->value : "",
                                    "height" => isset($project_detail->sectionFieldsConfigs[7]->value) ? $project_detail->sectionFieldsConfigs[7]->value : ""
                                ),
                                "constraints" => [],
                                "dependent_question" => [
                                ],
                            ];
                        }
                        $i++;
                    }
                }*/
                ///Business Appraisal additional fields
            }

            //CacheHelper::setFormCache($key,json_encode($array));

       // }
        \Yii::$app->response->data =  json_encode($array);
    }

    protected static function getAnswersValues($answers_values)
    {
        $array = array();
        if ($answers_values != '') {
            $list = Lists::find()->where(['list_name' => $answers_values])->orderBy(['sort_order' => SORT_DESC])->all();
            foreach ($list as $l) {
                $array[] = [
                    'value' => $l->value,
                    'label' => $l->label
                ];
            }
        }
        return $array;
    }

    public function actionRecyclerForm($field, $application_id = 0)
    {
        //$appraisal = Appraisals::findOne($id);
        $array = array();
            $project_details = ViewSectionFields::find()->where(['parent_id' => $field])->all();
                foreach ($project_details as $project_detail) {
                    $array[] = [
                        "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                        "column" => isset($project_detail->field) ? $project_detail->field : '',
                        "question_id" => isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                        "is_mandatory" => isset($project_detail->sectionFieldConfigRequired->value) ? $project_detail->sectionFieldConfigRequired->value : '0',
                        "is_hidden" => false,
                        "type" => isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                        "format" => isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : '',
                        "label" => "",
                        "place_holder" => isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                        "default_value" => "",
                        "mask" => "",
                        "answers" => isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                        "answers_values" => self::getAnswersValues(isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : ""),
                        "default_visibility" => isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                        "style" => array(
                            "width" => isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                            "height" => isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                        ),
                        "constraints" => [],
                        "dependent_question" => [

                        ],
                    ];
            }
        \Yii::$app->response->data = json_encode($array);
    }
    /**
     * Finds the Appraisals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appraisals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appraisals::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetProjectId($application_id)
    {
        $app_id=Applications::find()->select('project_id')->where(['id'=>$application_id])->one();
        $response['project_id'] = $app_id->project_id;

        return json_encode($response);

    }
}
