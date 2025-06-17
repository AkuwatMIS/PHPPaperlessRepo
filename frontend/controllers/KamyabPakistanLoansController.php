<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use common\models\Applications;
use common\models\Members;
use common\models\NadraVerisysRejectReasons;
use common\models\Projects;
use common\models\RejectedNadraVerisys;
use common\models\search\KamyabPakistanSearch;
use common\models\search\nadra\RejectedNadraVerisysSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * VigaLoansController implements the CRUD actions for VigaLoansSearch model.
 */
class KamyabPakistanLoansController extends Controller
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

    /**
     * Lists all VigaLoansSearch models.
     * @return mixed
     */
    public function actionIndex()
    {

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['KamyabPakistanSearch']);
            $headers = ['Region', 'Area', 'Branch', 'Project', 'Name', 'Parentage', 'CNIC', 'CNIC Issue Date', 'CNIC Expiry Date', 'Nadra Verification', 'Application Date', 'Nadra Upload Date', 'Status'];
            $groups = array();
            $searchModel = new KamyabPakistanSearch();
            $query = $searchModel->searchNadra($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['region'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['project'] = isset($g['project']['name']) ? $g['project']['name'] : '';
                $groups[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $groups[$i]['cnic_issue_date'] = isset($g['member']['info']['cnic_issue_date']) ? $g['member']['info']['cnic_issue_date'] : '';
                $groups[$i]['cnic_expiry_date'] = isset($g['member']['info']['cnic_expiry_date']) ? $g['member']['info']['cnic_expiry_date'] : '';
                $groups[$i]['nadra'] = (isset($g['nadra']['status']) && $g['nadra']['status'] == 1) ? 'YES' :'NO';
                $groups[$i]['application_date'] = isset($g['application_date']) ? \common\components\Helpers\StringHelper::dateFormatter($g['application_date']) : '';
                $groups[$i]['upload_at'] = isset($g['nadra']['upload_at']) ? \common\components\Helpers\StringHelper::dateFormatter($g['nadra']['upload_at']) : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';

                $i++;
            }
            ExportHelper::ExportCSV('nadra_verisys_status', $headers, $groups);
            die();
        }

        $params = Yii::$app->request->queryParams;
        $searchModel = new KamyabPakistanSearch();

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {

            $dataProvider = $searchModel->searchNadra(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);
        }

        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'projects' => $projects
        ]);
    }

    public function actionRejectedNicList()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
         //   $headers = array_keys($_GET[' RejectedNadraVerisysSearch']);
            $headers = ['Region', 'Area', 'Branch', 'Name', 'Parentage', 'CNIC', 'CNIC Issue Date', 'CNIC Expiry Date', 'Reject Reason', 'Remarks', 'Rejected Date', 'Status'];
            $groups = array();
            $searchModel = new RejectedNadraVerisysSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
            //   echo '<pre>';
            //   print_r($g['info']['member']['full_name']);
            //    die();

                $groups[$i]['region'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['full_name'] = isset($g['info']['member']['full_name']) ? $g['info']['member']['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['info']['member']['parentage']) ? $g['info']['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['info']['member']['cnic']) ? $g['info']['member']['cnic'] : '';
                $groups[$i]['cnic_issue_date'] = isset($g['info']['cnic_issue_date']) ? $g['info']['cnic_issue_date'] : '';
                $groups[$i]['cnic_expiry_date'] = isset($g['info']['cnic_expiry_date']) ? $g['info']['cnic_expiry_date'] : '';
                $groups[$i]['reject_reason'] = isset($g['reject_reason']) ? $g['reject_reason']: '';
                $groups[$i]['remarks'] = isset($g['remarks']) ? $g['remarks']: '';
                $groups[$i]['rejected_date'] = isset($g['rejected_date']) ? \common\components\Helpers\StringHelper::dateFormatter($g['rejected_date']) : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';

                $i++;
            }
            ExportHelper::ExportCSV('nadra_verisys_status', $headers, $groups);
            die();
        }
        $params = Yii::$app->request->queryParams;
        $searchModel = new RejectedNadraVerisysSearch();
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        if (empty($params)) {
            $dataProvider = [];
        } else {

            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);
        }

        return $this->render('rejected_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects' => $projects,
            'regions' => $regions
        ]);
    }

    public function actionRejectedSubmitNicList()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['RejectedNadraVerisysSearch']);
            $headers = ['Region', 'Area', 'Branch', 'Project', 'Name', 'Parentage', 'CNIC', 'CNIC Issue Date', 'CNIC Expiry Date', 'Reject Reason', 'Remarks', 'Re_Submitted Date'];
            $groups = array();
            $searchModel = new RejectedNadraVerisysSearch();
            $query = $searchModel->searchRejectedReSubmit($_GET, true);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['region'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';

                $groups[$i]['project'] = isset($g['applications'][0]['project']['name']) ? $g['applications'][0]['project']['name'] : '';
                $groups[$i]['full_name'] = isset($g['applications'][0]['member']['full_name']) ? $g['applications'][0]['member']['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['applications'][0]['member']['parentage']) ? $g['applications'][0]['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['applications'][0]['member']['cnic']) ? $g['applications'][0]['member']['cnic'] : '';

                $groups[$i]['cnic_issue_date'] = isset($g->info['cnic_issue_date']) ? $g->info['cnic_issue_date'] : '';
                $groups[$i]['cnic_expiry_date'] = isset($g->info['cnic_expiry_date']) ? $g->info['cnic_expiry_date'] : '';
                $groups[$i]['reject_reason'] = isset($g->reject_reason) ? $g->reject_reason : '';
                $groups[$i]['remarks'] = isset($g->remarks) ? $g->remarks : '';
                $groups[$i]['rejected_date'] = isset($g['rejected_date']) ? \common\components\Helpers\StringHelper::dateFormatter($g['rejected_date']) : '';
                $i++;
            }
            ExportHelper::ExportCSV('nadra_verisys_resubmit', $headers, $groups);
            die();
        }

        $params = Yii::$app->request->queryParams;
        $searchModel = new RejectedNadraVerisysSearch();
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $dataProvider = $searchModel->searchRejectedReSubmit(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);


        return $this->render('rejected_submit_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects' => $projects,
            'regions' => $regions
        ]);
    }

    public function actionAddRemarksNadraVerisys()
    {
        $params = Yii::$app->request->queryParams;
        $request = Yii::$app->request;
        if (empty($params)) {
            $id = $request->post()['RejectedNadraVerisys']['id'];
        } else {
            $id = $params['id'];
        }

        $model = RejectedNadraVerisys::find()->where(['id' => $id])->one();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Submit Nadra Verisys",
                    'content' => $this->renderAjax('update_rejected_nadra_verisys', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            } else if ($model->load($request->post())) {
                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new VigaLoansSearch",
                        'content' => '<span class="text-success">Rejected Nadra Verisys Reason Created successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create_rejected_nadra_verisys'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    var_dump($model->getErrors());
                    die();
                }
            } else {
                return [
                    'title' => "Submit Nadra Verisys",
                    'content' => $this->renderAjax('update_rejected_nadra_verisys', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            }
        } else {
            if (!empty($model) && $model != null) {
                $model->status = $request->post()['RejectedNadraVerisys']['status'];
                $model->reject_reason = $request->post()['RejectedNadraVerisys']['reject_reason'];
                $model->remarks = $request->post()['RejectedNadraVerisys']['remarks'];
                $model->rejected_date = strtotime(date('Y-m-d H:i:s'));;
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Remarks Updated Successfully');



                } else {
                    var_dump($model->getErrors());
                    die();
                }
            }

            $modelExistingReason = NadraVerisysRejectReasons::find()
                ->where(['nadra_verisys_rejected_id' => $id])
                ->one();
            if (!empty($modelExistingReason) && $modelExistingReason != null) {

                $modelReason = new NadraVerisysRejectReasons();
                $modelReason->nadra_verisys_rejected_id = $model->id;
                $modelReason->parent_id = $modelExistingReason->id;
                $modelReason->reject_reason = $model->reject_reason;
                $modelReason->remarks = $model->remarks;
                $modelReason->rejected_date = $model->rejected_date;
                if ($modelReason->save()) {
                    return $this->redirect(Yii::$app->request->referrer);
                } else {
                    var_dump($modelReason->getErrors());
                    die();
                }
            }


        }
    }

    public function actionDeleteSubmitNadraVerisys()
    {
        //die('hi');
        $params = Yii::$app->request->queryParams;
        $request = Yii::$app->request;
     
        $model = RejectedNadraVerisys::find()->where(['id' => $params['id']])->one();
        if (!empty($model) && $model != null) {
            $model->status = 2;
            if ($model->save()) {
                return $this->redirect(['rejected-submit-nic-list']);
            } else {
                var_dump($model->getErrors());
                die();
            }

        }
    }

    public function actionSubmitRejectedNadraVerisys()
    {
        //die('hi');
        $params = Yii::$app->request->queryParams;
        $request = Yii::$app->request;
        if (empty($params)) {
            $id = $request->post()['RejectedNadraVerisys']['id'];
        } else {
            $id = $params['id'];
        }
        $model = RejectedNadraVerisys::find()->where(['id' => $id])->one();
        if (!empty($model) && $model != null) {
            $model->status = 2;
            if ($model->save()) {
                return $this->redirect(['rejected-submit-nic-list']);
            } else {
                var_dump($model->getErrors());
                die();
            }

        }
    }

    public function actionRejectNadraVerisys()
    {
        $params = Yii::$app->request->queryParams;
        $model = new RejectedNadraVerisys();
        $request = Yii::$app->request;

        $app_id = isset($params['application_id'])?$params['application_id']:0;
        $mem_info_id = isset($params['member_info_id'])?$params['member_info_id']:0;


        if ($request->isAjax) {
            $model->application_id = $app_id;
            $model->member_info_id = $mem_info_id;
            $modelApp = Applications::find()->where(['id' => $app_id])->one();
            if (!empty($modelApp) && $modelApp != null) {
                $model->region_id = $modelApp->region_id;
                $model->branch_id = $modelApp->branch_id;
                $model->area_id = $modelApp->area_id;
            }

            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Reject Nadra Verisys",
                    'content' => $this->renderAjax('create_rejected_nadra_verisys', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            } else if ($model->load($request->post())) {
                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new VigaLoansSearch",
                        'content' => '<span class="text-success">Rejected Nadra Verisys Reason Created successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create_rejected_nadra_verisys'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    var_dump($model->getErrors());
                    die();
                }
            } else {
                return [
                    'title' => "Reject Nadra Verisys",
                    'content' => $this->renderAjax('create_rejected_nadra_verisys', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $modelExisting = RejectedNadraVerisys::find()
                    ->where(['application_id' => $_POST['RejectedNadraVerisys']['application_id']])
                    ->andWhere(['member_info_id' => $_POST['RejectedNadraVerisys']['member_info_id']])->one();

                if (empty($modelExisting) && $modelExisting == null) {
                    $model->rejected_date = strtotime(date('Y-m-d H:i:s'));
                    if ($model->save()) {
                        $modelReason = new NadraVerisysRejectReasons();
                        $modelReason->nadra_verisys_rejected_id = $model->id;
                        $modelReason->reject_reason = $model->reject_reason;
                        $modelReason->remarks = $model->remarks;
                        $modelReason->rejected_date = $model->rejected_date;
                        if ($modelReason->save()) {
                            Yii::$app->session->setFlash('success', 'Nadra Verisys Rejected');
                            return $this->redirect(['index']);
                        } else {
                            var_dump($modelReason->getErrors());
                            die();
                        }
                    } else {
                        var_dump($model->getErrors());
                        die();
                    }
                } else {
                    $modelExisting->status = 0;
                    $modelExisting->reject_reason = $_POST['RejectedNadraVerisys']['reject_reason'];
                    $modelExisting->remarks = $_POST['RejectedNadraVerisys']['remarks'];
                    $modelExisting->rejected_date = strtotime(date('Y-m-d H:i:s'));
                    if ($modelExisting->save(false)) {
                        $modelReasonNew = new NadraVerisysRejectReasons();
                        $modelReasonNew->nadra_verisys_rejected_id = $modelExisting->id;
                        $modelReasonNew->reject_reason = $modelExisting->reject_reason;
                        $modelReasonNew->remarks = $modelExisting->remarks;
                        $modelReasonNew->rejected_date = $modelExisting->rejected_date;
                        if ($modelReasonNew->save()) {
                            Yii::$app->session->setFlash('success', 'Nadra Verisys Rejected');
                            return $this->redirect(['index']);
                        } else {
                            var_dump($modelReasonNew->getErrors());
                            die();
                        }
                    } else {
                        var_dump($modelExisting->getErrors());
                        die();
                    }
                }

            } else {
                return $this->render('create_rejected_nadra_verisys', [
                    'model' => $model
                ]);
            }
        }
    }

    public function actionRejectSubmitedNadraVerisys()
    {
        $params = Yii::$app->request->queryParams;
        $request = Yii::$app->request;

        $model = new NadraVerisysRejectReasons();
        if ($request->isAjax) {
            $model->nadra_verisys_rejected_id = $params['id'];

            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Reject Nadra Verisys",
                    'content' => $this->renderAjax('create_rejected_nadra_verisys_reason', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            } else if ($model->load($request->post())) {
                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new VigaLoansSearch",
                        'content' => '<span class="text-success">Rejected Nadra Verisys Created successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create_rejected_nadra_verisys'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    var_dump($model->getErrors());
                    die();
                }
            } else {
                return [
                    'title' => "Reject Nadra Verisys",
                    'content' => $this->renderAjax('create_rejected_nadra_verisys_reason', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $model->rejected_date = strtotime(date('Y-m-d H:i:s'));
                if ($model->save()) {
                    $modelNrv = RejectedNadraVerisys::find()->where(['id' => $model->nadra_verisys_rejected_id])->one();
                    $modelNrv->reject_reason = $model->reject_reason;
                    $modelNrv->remarks = $model->remarks;
                    $modelNrv->rejected_date = strtotime(date('Y-m-d H:i:s'));
                    $modelNrv->status=0;
                    if ($modelNrv->save(false)) {
                        Yii::$app->session->setFlash('success', 'Reason Added Successfully');
                        return $this->redirect(['rejected-submit-nic-list']);
                    } else {
                        var_dump($modelNrv->getErrors());
                        die();
                    }
                } else {
                    var_dump($model->getErrors());
                    die();
                }

            } else {
                return $this->render('create_rejected_nadra_verisys_reason', [
                    'model' => $model
                ]);
            }
        }
    }


    /**
     * Displays a single VigaLoansSearch model.
     * @param integer $id
     * @return mixed
     */

    public function actionSummary()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        $params = Yii::$app->request->queryParams;
        $searchModel = new KamyabPakistanSearch();

        if (empty($params['KamyabPakistanSearch']['created_at'])) {
            $result = [];
        } else {

            $result = $searchModel->searchNadraSummary(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, 'applications'/* Yii::$app->controller->id*/, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        return $this->render('summary_index', [
            'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
            'result' => $result,
            'regions' => $regions,
            'projects' => $projects
        ]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "VigaLoansSearch #" . $id,
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
     * Creates a new VigaLoansSearch model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new VigaLoans();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new VigaLoansSearch",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new VigaLoansSearch",
                    'content' => '<span class="text-success">Create VigaLoansSearch success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new VigaLoansSearch",
                    'content' => $this->renderAjax('create', [
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
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Updates an existing VigaLoansSearch model.
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
                    'title' => "Update VigaLoansSearch #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "VigaLoansSearch #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update VigaLoansSearch #" . $id,
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
     * Delete an existing VigaLoansSearch model.
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
     * Delete multiple existing VigaLoansSearch model.
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
     * Finds the VigaLoansSearch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Members the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Members::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
