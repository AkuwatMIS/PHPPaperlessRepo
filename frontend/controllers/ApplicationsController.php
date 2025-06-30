<?php

namespace frontend\controllers;


use common\components\DBSchemaHelper;
use common\components\Helpers\AcagHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\DBHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationActions;
use common\models\ApplicationDetails;
use common\models\ApplicationsCib;
use common\models\Appraisals;
use common\models\AuthItemChild;
use common\models\Branches;
use common\models\Cities;
use common\models\Designations;
use common\models\Documents;
use common\models\FilesApplication;
use common\models\Guarantors;
use common\models\Images;
use common\models\Lists;
use common\models\Loans;
use common\models\Members;
use common\models\MembersAccount;
use common\models\NadraVerisys;
use common\models\Operations;
use common\models\ProjectAppraisalsMapping;
use common\models\Projects;
use common\models\Provinces;
use common\models\Referrals;
use common\models\Regions;
use common\models\StructurePayments;
use common\models\User;
use common\models\Users;
use common\models\UserStructureMapping;
use common\models\Visits;
use Imagine\Image\Box;
use Imagine\Image\Point;
use kartik\mpdf\Pdf;
use phpDocumentor\Reflection\Types\Array_;
use Yii;
use common\models\Applications;
use common\models\search\ApplicationsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * ApplicationsController implements the CRUD actions for Applications model.
 */
class ApplicationsController extends Controller
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
     * Lists all Applications models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '20240M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['ApplicationsSearch']);
            $headers = ['full_name', 'parentage', 'parentage_type', 'dob', 'gender', 'cnic', 'application_no', 'region_id', 'area_id', 'branch_id', 'branch_code', 'team_id', 'field_id', 'city', 'district', 'address', 'Mobile No', 'requested_amount', 'status', 'application_date', 'application_created_date','applicant_property_id','is_pledged', 'project', 'CIB Verification', 'Nadra Verisys'];
            $groups = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();

            $i = 0;
            foreach ($data as $g) {
                $filter_date = date('Y-m-d', $g['created_at']);

                $groups[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $groups[$i]['father_name'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';;
                $groups[$i]['parentage_type'] = isset($g['member']['parentage_type']) ? $g['member']['parentage_type'] : '';;
                $groups[$i]['dob'] = isset($g['member']['dob']) ? date('Y-m-d', $g['member']['dob']) : '';;
                $groups[$i]['gender'] = isset($g['member']['gender']) ? $g['member']['gender'] : '';;
                $groups[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $groups[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $groups[$i]['region_id'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['branch_code'] = isset($g['branch']['code']) ? $g['branch']['code'] : '';;
                $groups[$i]['team_id'] = isset($g->team->name) ? $g->team->name : '';
                $groups[$i]['field_id'] = isset($g->field->name) ? $g->field->name : '';
                $groups[$i]['city'] = isset($g['branch']['city']['name']) ? $g['branch']['city']['name'] : '';
                $groups[$i]['district'] = isset($g['branch']['district']['name']) ? $g['branch']['district']['name'] : '';
                $groups[$i]['address'] = isset($g['member']['businessAddress']['address']) ? $g['member']['businessAddress']['address'] : '';
                $groups[$i]['mobile'] = isset($g['member']['membersMobile']['phone']) ? $g['member']['membersMobile']['phone'] : '';
                $groups[$i]['req_amount'] = isset($g['req_amount']) ? $g['req_amount'] : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';
                $groups[$i]['application_date'] = date('Y-m-d', $g['application_date']);
                $groups[$i]['created_date'] = $filter_date;
                $groups[$i]['applicant_property_id'] = isset($g['applicant_property_id']) ? $g['applicant_property_id'] : '';
                $groups[$i]['is_pledged'] = isset($g['is_pledged']) ? $g['is_pledged'] : '';
                $groups[$i]['project_id'] = isset($g['project']['name']) ? $g['project']['name'] : '';;
                $groups[$i]['cib_verification'] = !empty($g['cib']['status']) ? 'Yes' : 'No';
                $groups[$i]['nadra_verisys'] = (isset($g['nadra']['status']) && $g['nadra']['status'] == 1) ? 'Completed' : 'Pending';
                $i++;
            }
            ExportHelper::ExportCSV('Applications.csv', $headers, $groups);
            die();
        }
        $searchModel = new ApplicationsSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        //$regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        /*$teams = Yii::$app->Permission->getTeamList(Yii::$app->controller->id, Yii::$app->controller->action->id);
        $fields = Yii::$app->Permission->getFieldList(Yii::$app->controller->id, Yii::$app->controller->action->id);*/

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
        ]);
    }

    public function actionRejectedPendingApplications()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['ApplicationsSearch']);
            $groups = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->search_rejected_pending($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $groups[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $groups[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $groups[$i]['region_id'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['team_id'] = isset($g->team->name) ? $g->team->name : '';
                $groups[$i]['field_id'] = isset($g->field->name) ? $g->field->name : '';
                $groups[$i]['req_amount'] = isset($g['req_amount']) ? $g['req_amount'] : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';
                $groups[$i]['application_date'] = date('Y-m-d', $g['application_date']);
                $groups[$i]['project_id'] = isset($g['project']['name']) ? $g['project']['name'] : '';;
                $i++;
            }
            ExportHelper::ExportCSV('Applications.csv', $headers, $groups);
            die();
        }
        $searchModel = new ApplicationsSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = array();
        } else {
            $dataProvider = $searchModel->search_rejected_pending(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }

//        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
//        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        /*$teams = Yii::$app->Permission->getTeamList(Yii::$app->controller->id, Yii::$app->controller->action->id);
        $fields = Yii::$app->Permission->getFieldList(Yii::$app->controller->id, Yii::$app->controller->action->id);*/

        return $this->render('rejected_pending_application/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
        ]);
    }

    public function actionRejectedApplications()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Application No', 'Region ', 'Area ', 'Branch ', 'Status', 'Reject Reason', 'Project']);
            $groups = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->searchRejectedApplications($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $groups[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $groups[$i]['region_id'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';
                $groups[$i]['reject_reason'] = isset($g['reject_reason']) ? $g['reject_reason'] : '';
                $groups[$i]['project_id'] = isset($g['project']['name']) ? $g['project']['name'] : '';;
                $i++;
            }
            ExportHelper::ExportCSV('Rejected Applications', $headers, $groups);
            die();
        }
        $searchModel = new ApplicationsSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        $dataProvider = $searchModel->searchRejectedApplications(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        /*$teams = Yii::$app->Permission->getTeamList(Yii::$app->controller->id, Yii::$app->controller->action->id);
        $fields = Yii::$app->Permission->getFieldList(Yii::$app->controller->id, Yii::$app->controller->action->id);*/

        return $this->render('rejected_applications/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions_by_id,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
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
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Applications #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            $model = Applications::find()->where(['id' => $id])->one();
            $apraisals = ProjectAppraisalsMapping::find()->where(['project_id' => $model->project_id])->all();
            $data = [];
            foreach ($apraisals as $apraisal) {
                $appraisal_table = Appraisals::find()->where(['id' => $apraisal->appraisal_id])->select(['appraisal_table'])->one()->toArray();
                $data[] = DBSchemaHelper::getTableSchemaMysql($appraisal_table['appraisal_table']);
            }
            $modelNadraVarisys = NadraVerisys::find()->where(['application_id'=>$id])->select(['status'])->one();

            return $this->render('view', [
                'model' => $model,
                'data' => $data,
                'modelNadraVarisys' => $modelNadraVarisys,

            ]);
        }
    }

    /**
     * Displays a single Applications model.
     * @param integer $id
     * @return mixed
     */
    public function actionMemberDetails($id)
    {
        $this->layout = 'main_simple_js';
        return $this->render('memberDetails', [
            'model' => Members::findOne($id),
        ]);
    }

    /**
     * Creates a new Applications model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = 0)
    {
        $request = Yii::$app->request;

        $model = new Applications();
        $cib_model = new ApplicationsCib();

//        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type,$branch_ids);
//        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $projects = Projects::find()->where(['status' => 1])->where(['NOT IN', 'id', [18, 19, 29, 47, 33]])->all();
        $projects = ArrayHelper::map($projects, 'id', 'name');
        //unset($projects[18]); // to remove FATA from application list
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        if ($model->load($request->post())) {

            if($model->project_id == 132){
                $responseAcag = AcagHelper::actionGet($model->member->cnic);
                if($responseAcag == false){
                    $model->addError('project_id', 'Applicant data not found!.');
                    return $this->render('create', [
                        'model' => $model,
                        'projects' => $projects,
                        'branches' => $branches,
                        'cib_model' => $cib_model
                    ]);
                }
            }

            if ($id != 0) {
                $model->member_id = $id;
            }

            $post_auth_date = date('Y-m-d');
            $app_posted_date = (date('Y-m-d', strtotime($_POST['Applications']['application_date'])));
            $pre_auth_date = date('Y-m-01');

            if (strtotime($app_posted_date) <= strtotime($post_auth_date) && strtotime($app_posted_date) >= strtotime($pre_auth_date)) {

            } else {
                $cib_model->addError('application_date', 'Application posting is valid between current month only!.');
                return $this->render('create', [
                    'model' => $model,
                    'projects' => $projects,
                    'branches' => $branches,
                    'cib_model' => $cib_model
                ]);
            }

            $cib_model->load($request->post());
            if (!empty($model->sub_activity)) {
                $model->sub_activity = implode(',', $model->sub_activity);
            }

            $project = Projects::find()->where(['id' => $model->project_id])->select(['project_table'])->one()->toArray();
//            $project = Projects::findOne($model->project_id);

            if (isset($project['project_table'])) {
                if ($project['project_table'] == 'projects_sidb' && $_POST['Sidb'] == 0) {
                    $table_name = '';
                } else {
                    $table_name = $project['project_table'];
                }

            }


            $model->application_no = '200' . $model->application_no;
            $model->project_table = $project['project_table'];
            $model->no_of_times = 1;

            $branch = Branches::find()->where(['id' => $model->branch_id])->one();
            $province = Provinces::find()->where(['id' => $branch->province_id])->one();
            $appFeePercent = (!empty($province->app_tax_percent)) ? $province->app_tax_percent : 0;
            $appFee = (((int)$model->fee) / 100) * $appFeePercent;
            $cibFeePercent = (!empty($province->cib_tax_percent)) ? $province->cib_tax_percent : 0;
            $cibFee = (((int)$cib_model->fee) / 100) * $cibFeePercent;

            $model->region_id = $branch->region_id;
            $model->area_id = $branch->area_id;
            /*$model->field_id = $model->member->field_id;
            $model->team_id = $model->member->team_id;*/
            $model->activity_id = !empty($model->activity_id) ? $model->activity_id : 0;
            $model->status = 'pending';

            if (isset($model->fee) && !empty($model->fee)) {
                if (strtotime($model->application_date) > 1614538799) {
//                    if ($model->branch_id == 814) {
                    $payments = StructurePayments::find()
                        ->where(['project_id' => $model->project_id])
                        ->andWhere(['type' => 'fee'])
                        ->andWhere(['province_id' => $branch->province_id])
                        ->one();
                    $model->fee = (int)$payments->total_amount;
//                    }
                }

            }

            $flag = true;
            $model = ApplicationHelper::preConditionsApplication($model);
            if (!empty($model->getErrors())) {
                $flag = false;
            }
            $model = ApplicationHelper::preConditionsApplicationWithRejectedStatus($model);
            $transaction = Yii::$app->db->beginTransaction();
            if ($flag == true) {
                if ($model->save()) {
                    $nadra_verisys_model = new NadraVerisys();
                    $nadra_verisys_model->member_id = $model->member_id;
                    $nadra_verisys_model->application_id = $model->id;
                    $nadra_verisys_model->document_type = 'nadra_document';
                    $nadra_verisys_model->save();
                    //ActionsHelper::insertActions('application',$model->project_id,$model->id,Yii::$app->user->getId());
                    ////Application Actions
                    /*$action_model = new ApplicationActions();
                    $action_model->parent_id = $model->id;
                    $action_model->user_id = $model->created_by;
                    $action_model->action = "social_appraisal";
                    if($model->project_id==3){
                        $action_model->status=1;
                    }
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                    if($model->product_id==1) {
                        $action_model = new ApplicationActions();
                        $action_model->parent_id = $model->id;
                        $action_model->user_id = $model->created_by;
                        $action_model->action = "business_appraisal";
                        if($model->project_id==3){
                            $action_model->status=1;
                        }
                        $action_model->expiry_date = strtotime(date("Y-m-d H:i:s", time()) . " +1 month");
                        $action_model->save();
                    }*/
                    /////Operations

                    if ($model->application_date >= 1643673600) {
                        $operation_model_save = OperationHelper::saveOperations($model, 'nadra', 37, 0, time());
                        if ($operation_model_save != 1) {
                            $transaction->rollBack();
                            print_r($operation_model_save);
                            die();
                        }
                    }

                    if (isset($model->fee) && !empty($model->fee) && $model->fee != null) {

                        $operation_model_save = OperationHelper::saveOperations($model, 'fee', $model->fee, 0, time());
                        if ($operation_model_save != 1) {
                            $transaction->rollBack();
                            print_r($operation_model_save);
                            die();
                        }
                    }
                    if (isset($cib_model->fee) && !empty($cib_model->fee) && $cib_model->fee != null && isset($cib_model->receipt_no) && !empty($cib_model->receipt_no) && $cib_model->receipt_no != null) {
                        $operation_model_save = OperationHelper::saveOperations($model, 'cib', $cib_model->fee, $cib_model->receipt_no, time());
                        if ($operation_model_save != 1) {
                            $transaction->rollBack();
                            print_r($operation_model_save);
                            die();
                        } else {
                            if ($model->application_date > 1614538799) {
//                                if ($model->branch_id == 814) {
                                $payments = StructurePayments::find()
                                    ->where(['project_id' => $model->project_id])
                                    ->andWhere(['type' => 'cib'])
                                    ->andWhere(['province_id' => $branch->province_id])
                                    ->one();
                                $cib_model->fee = (int)$payments->total_amount;
//                                } else {
//                                    $cib_model->fee = (int)$cib_model->fee + $cibFee;
//                                }
                            }
                            $cib_model->application_id = $model->id;
                            if (!$cib_model->save()) {
                                $transaction->rollBack();
                                if (!empty($model->sub_activity)) {
                                    $model->sub_activity = explode(',', $model->sub_activity);
                                    $out = [];
                                    foreach ($model->sub_activity as $key => $value) {
                                        $out[$value] = $value;
                                    }
                                    $model->sub_activity = $out;
                                }
                                return $this->render('create', [
                                    'model' => $model,
                                    'projects' => $projects,
                                    'branches' => $branches,
                                    'cib_model' => $cib_model
                                ]);
                            }
                        }
                    } else {
                        if ($model->application_date > 1583020799 /*&&  in_array($model->project_id,[59,60])*/ && $model->req_amount > 10000) {
                            $transaction->rollBack();
                            $cib_model->addError('receipt_no', 'CIB Receipt No can not be blank.');
                            if (!empty($model->sub_activity)) {
                                $model->sub_activity = explode(',', $model->sub_activity);
                                $out = [];
                                foreach ($model->sub_activity as $key => $value) {
                                    $out[$value] = $value;
                                }
                                $model->sub_activity = $out;
                            }
                            return $this->render('create', [
                                'model' => $model,
                                'projects' => $projects,
                                'branches' => $branches,
                                'cib_model' => $cib_model
                            ]);
                        }
                    }
                    if (!empty($table_name)) {
                        $model_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
                        $path = '\common\models\\' . $model_name;
                        $project_model = new $path();

                        if ($project_model->load($request->post())) {
                            $project_model->application_id = $model->id;
                            if ($model->project_id == 3 || $model->project_id == 79) {
                                $_crops = '';
                                if ($model->project_id == 79) {
                                    foreach ($request->post()['kpp_crops'] as $key => $value) {
                                        if (!empty($_crops)) {
                                            $_crops .= ',';
                                        }
                                        $_crops .= $key;
                                    }
                                    $project_model->kpp_crops = $_crops;
                                } else {
                                    foreach ($request->post()['crops'] as $key => $value) {
                                        if (!empty($_crops)) {
                                            $_crops .= ',';
                                        }
                                        $_crops .= $key;
                                    }
                                    $project_model->crops = $_crops;
                                }


                            }

                            if ($project_model->save()) {

                                $transaction->commit();
                                //create application 132
                                if($model->project_id == 132){
                                    if($model->status == 'rejected'){
                                        $status = 'Loan Rejected';
                                        $statusReason = $model->reject_reason;
                                    }else{
                                        $status = 'Submitted';
                                        $statusReason = 'Submitted';
                                    }
                                    AcagHelper::actionPush($model,$status,$statusReason,$model->req_amount,date('Y-m-d'),0,null);
                                }

                            } else {
                                $transaction->rollBack();
//                                if($model->branch_id == 814){
//                                    print_r($project_model->getErrors());
//                                    die();
//                                }

                                foreach ($project_model->getErrors() as $error) {
                                    //array_push($model->getErrors(),$error);

                                    $model->addError('project_id', $error[0]);
                                }
                                if (!empty($model->sub_activity)) {
                                    $model->sub_activity = explode(',', $model->sub_activity);
                                    $out = [];
                                    foreach ($model->sub_activity as $key => $value) {
                                        $out[$value] = $value;
                                    }
                                    $model->sub_activity = $out;
                                }
                                return $this->render('create', [
                                    'model' => $model,
                                    'projects' => $projects,
                                    'branches' => $branches,
                                    'cib_model' => $cib_model
                                ]);
                                /*echo'<pre>';
                                print_r($project_model->getErrors());
                                die();*/
                            }
                        }
                    } else {
                        $transaction->commit();

                        //update application 132
                        if($model->project_id == 132){
                            if($model->status == 'rejected'){
                                $status = $model->status;
                                $statusReason = $model->reject_reason;
                            }else{
                                $status = 'Submitted';
                                $statusReason = 'Submitted';
                            }
                            AcagHelper::actionPush($model,$status,$statusReason,$model->req_amount,date('Y-m-d'),0,null);
                        }
                    }

                    if (in_array($model->project_id, [77, 78, 79, 105, 106, 132])) {
                        $app_detail_model = new ApplicationDetails();
                        $app_detail_model->application_id = $model->id;
                        $app_detail_model->parent_type = 'member';
                        $app_detail_model->parent_id = $model->member_id;
                        $app_detail_model->poverty_score = 0;
                        $app_detail_model->status = 0;
                        $app_detail_model->save();
                    }

                    //ActionsHelper::insertActions('application',0,$model->id,Yii::$app->user->getId());      //after application sync
                    /*$action_model=new ApplicationActions();
                    $action_model->parent_id=$model->id;
                    $action_model->action='social_appraisal';
                    $action_model->save();*/


                } else {
                    if (!empty($model->sub_activity)) {
                        $model->sub_activity = explode(',', $model->sub_activity);
                        $out = [];
                        foreach ($model->sub_activity as $key => $value) {
                            $out[$value] = $value;
                        }
                        $model->sub_activity = $out;
                    }
                    $model->application_no = substr($model->application_no, 3);
                    return $this->render('create', [
                        'model' => $model,
                        'projects' => $projects,
                        'branches' => $branches,
                        'cib_model' => $cib_model
                    ]);
                }
            } else {
                if (!empty($model->sub_activity)) {
                    $model->sub_activity = explode(',', $model->sub_activity);
                    $out = [];
                    foreach ($model->sub_activity as $key => $value) {
                        $out[$value] = $value;
                    }
                    $model->sub_activity = $out;
                }
                return $this->render('create', [
                    'model' => $model,
                    'projects' => $projects,
                    'branches' => $branches,
                    'cib_model' => $cib_model
                ]);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            return $this->render('create', [
                'model' => $model,
                'projects' => $projects,
                'branches' => $branches,
                'cib_model' => $cib_model
            ]);
        }
    }

    /**
     * Updates an existing Applications model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionGetApplicationFee($id, $branch_id = 0)
    {
//        if ($branch_id == 814) {
        $branch = Branches::find()->where(['id' => $branch_id])->one();
        $payments = StructurePayments::find()
            ->where(['project_id' => $id])
            ->andWhere(['in', 'type', ['fee', 'cib']])
            ->andWhere(['province_id' => $branch->province_id])
            ->all();

        if (!empty($payments) && $payments != null) {
            $response['fee'] = 0;
            $response['cib'] = 14;

            foreach ($payments as $payment) {
                if ($payment->type == 'fee') {
                    $response['fee'] = $payment->total_amount;
                } elseif ($payment->type == 'cib') {
                    $response['cib'] = $payment->total_amount;
                }
            }
            $response['status'] = "success";
        } else {
            $response['status'] = "error";
            $response['data'] = "";
        }
//        } else {
//            $project = Projects::find()->where(['id' => $id])->select(['application_fee'])->one()->toArray();
//            $user_id = Yii::$app->user->identity;
//            $user = Users::find()->where(['id' => $user_id])->select(['city_id'])->one();
//            $city = Cities::find()->where(['id' => $user['city_id']])->select(['province_id'])->one();
//            $province = Provinces::find()->where(['id' => $city['province_id']])->select(['cib_fee'])->one();
//            if (empty($project) && $project == null) {
//                $response['status'] = "error";
//                $response['data'] = "";
//            } else {
//                $response['status'] = "success";
//                $response['fee'] = $project['application_fee'];
//
//                if (!empty($province) && $province != null) {
//                    $response['cib'] = $province->cib_fee;
//                }
//            }
//        }

        return json_encode($response);
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $table_name = '';
        $preAppDate = '';
        $model = $this->findModelLock($id);

        // $preAppDate = $model->application_date;
        // $newAppDate = $request->application_date;

        // echo $preAppDate;
        // echo '-new-';
        // echo $newAppDate;
        // die();

        $cib_model = ApplicationsCib::find()->where(['application_id' => $model->id])->one();
        $cib_model = !empty($cib_model) ? $cib_model : new ApplicationsCib();
        if ($cib_model->isNewRecord) {
            $cib_model->application_id = $id;
        }
//        $projects = Yii::$app->Permission->g(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        if (!empty($model->sub_activity)) {
            $model->sub_activity = explode(',', $model->sub_activity);
            $out = [];
            foreach ($model->sub_activity as $key => $value) {
                $out[$value] = $value;
            }
            $model->sub_activity = $out;
        }
        if ($model->load($request->post())) {
            $model = ApplicationHelper::preConditionsApplicationUpdateRejectedStatus($model);
            
            $currect_date = strtotime("now");
            $app_date = strtotime($_POST['Applications']['application_date']);
            if ($app_date > $currect_date) {
                return $this->render('update', [
                    'model' => $model,
                    'projects' => $projects,
                    'branches' => $branches,
                    'cib_model' => $cib_model
                ]);
            }

            if($model->project_id == 132){
                $responseAcag = AcagHelper::actionGet($model->member->cnic);
                if($responseAcag == false){
                    $model->addError('project_id', 'Applicant data not found!.');
                    return $this->render('update', [
                        'model' => $model,
                        'projects' => $projects,
                        'branches' => $branches,
                        'cib_model' => $cib_model
                    ]);
                }
            }

            $cib_model->load($request->post());
            if (!empty($model->sub_activity)) {
                $model->sub_activity = implode(',', $model->sub_activity);
            }
            $project = Projects::findOne($model->project_id);
            if (isset($project->project_table)) {
                $table_name = $project->project_table;
            }
            $model->project_table = $project->project_table;
            $branch = Branches::find()->where(['id' => $model->branch_id])->one();
//            $province = Provinces::find()->where(['id' => $branch->province_id])->one();
//            $appFeePercent = (!empty($province->app_tax_percent)) ? $province->app_tax_percent : 0;
//            $appFee = (((int)$model->fee) / 100) * $appFeePercent;
//            $cibFeePercent = (!empty($province->cib_tax_percent)) ? $province->cib_tax_percent : 0;
//            $cibFee = (((int)$cib_model->fee) / 100) * $cibFeePercent;

            if (isset($model->fee) && !empty($model->fee)) {
                if (strtotime($model->application_date) > 1614538799) {
                    $payments = StructurePayments::find()
                        ->where(['project_id' => $model->project_id])
                        ->andWhere(['type' => 'fee'])
                        ->andWhere(['province_id' => $branch->province_id])
                        ->one();
                    $model->fee = (int)$payments->total_amount;
                }

            }

            $model->region_id = $branch->region_id;
            $model->area_id = $branch->area_id;

            if (!$model->save()) {
                if (!empty($model->sub_activity)) {
                    $model->sub_activity = explode(',', $model->sub_activity);
                    $out = [];
                    foreach ($model->sub_activity as $key => $value) {
                        $out[$value] = $value;
                    }
                    $model->sub_activity = $out;
                }
                return $this->render('update', [
                    'model' => $model,
                    'projects' => $projects,
                    'branches' => $branches,
                    'cib_model' => $cib_model
                ]);
            }

            if (in_array($model->project_id, [77, 78, 79, 105, 106, 132])) {
                $existApplicationAction = ApplicationDetails::find()->where(['application_id' => $model->id])->one();
                if (empty($existApplicationAction) && $existApplicationAction == null) {
                    $app_detail_model = new ApplicationDetails();
                    $app_detail_model->application_id = $model->id;
                    $app_detail_model->parent_type = 'member';
                    $app_detail_model->parent_id = $model->member_id;
                    $app_detail_model->poverty_score = 0;
                    $app_detail_model->status = 0;
                    $app_detail_model->save();
                }
            }

            if (!isset($model->fee) || empty($model->fee) || $model->fee == null) {
                $model->fee = 0;
            }
            $operation_model_save = OperationHelper::saveOperations($model, 'fee', $model->fee, 0, time());
            if ($operation_model_save != 1) {
                print_r($operation_model_save);
                die();
            }
            if (isset($cib_model->fee) && !empty($cib_model->fee) && $cib_model->fee != null && isset($cib_model->receipt_no) && !empty($cib_model->receipt_no) && $cib_model->receipt_no != null) {
                if (!empty($model->sub_activity)) {
                    $model->sub_activity = explode(',', $model->sub_activity);
                    $out = [];
                    foreach ($model->sub_activity as $key => $value) {
                        $out[$value] = $value;
                    }
                    $model->sub_activity = $out;
                }
            }
            if (!empty($table_name)) {
                $model_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
                $path = '\common\models\\' . $model_name;
                $project_model = $path::find()->where(['application_id' => $model->id])->one();
                if (!empty($project_model)) {
                    if ($project_model->load($request->post())) {
                        if ($model->project_id == 3) {
                            $_crops = '';
                            if (isset($request->post()['crops'])) {
                                foreach ($request->post()['crops'] as $key => $value) {
                                    if (!empty($_crops)) {
                                        $_crops .= ',';
                                    }
                                    $_crops .= $key;
                                }
                            }
                            $project_model->crops = $_crops;
                        }
                        if ($project_model->save()) {
                            return $this->redirect(['view', 'id' => $model->id]);

                        } else {
                            foreach ($project_model->getErrors() as $error) {
                                $model->addError('project_id', $error[0]);
                            }
                            if (!empty($model->sub_activity)) {
                                $model->sub_activity = explode(',', $model->sub_activity);
                                $out = [];
                                foreach ($model->sub_activity as $key => $value) {
                                    $out[$value] = $value;
                                }
                                $model->sub_activity = $out;
                            }
                            return $this->render('update', [
                                'model' => $model,
                                'projects' => $projects,
                                'branches' => $branches,
                                'cib_model' => $cib_model
                            ]);
                        }
                    } else {
                        if (!empty($model->sub_activity)) {
                            $model->sub_activity = explode(',', $model->sub_activity);
                            $out = [];
                            foreach ($model->sub_activity as $key => $value) {
                                $out[$value] = $value;
                            }
                            $model->sub_activity = $out;
                        }
                        return $this->render('update', [
                            'model' => $model,
                            'projects' => $projects,
                            'branches' => $branches,
                            'cib_model' => $cib_model
                        ]);
                    }
                } else {
                    $project_model = new $path();
                    if ($project_model->load($request->post())) {
                        $project_model->application_id = $model->id;
                        if ($model->project_id == 3) {
                            $_crops = '';
                            if (isset($request->post()['crops'])) {
                                foreach ($request->post()['crops'] as $key => $value) {
                                    if (!empty($_crops)) {
                                        $_crops .= ',';
                                    }
                                    $_crops .= $key;
                                }
                            }
                            $project_model->crops = $_crops;
                        }

                        if ($project_model->save()) {

                            //update application 132
                            if($model->project_id == 132){
                                if($model->status == 'rejected'){
                                    $status = "Loan Rejected";
                                    $statusReason = $model->reject_reason;
                                }else{
                                    $status = 'Submitted';
                                    $statusReason = 'Submitted';
                                }
                                AcagHelper::actionPush($model,$status,$statusReason,$model->req_amount,date('Y-m-d'),0,null);
                            }
                            return $this->redirect(['view', 'id' => $model->id]);

                        } else {
                            foreach ($project_model->getErrors() as $error) {
                                $model->addError('project_id', $error[0]);
                            }
                            if (!empty($model->sub_activity)) {
                                $model->sub_activity = explode(',', $model->sub_activity);
                                $out = [];
                                foreach ($model->sub_activity as $key => $value) {
                                    $out[$value] = $value;
                                }
                                $model->sub_activity = $out;
                            }
                            return $this->render('update', [
                                'model' => $model,
                                'projects' => $projects,
                                'branches' => $branches,
                                'cib_model' => $cib_model
                            ]);
                        }
                    }
                }
            } else {
                //update application 132
                if($model->project_id == 132){
                    if($model->status == 'rejected'){
                        $status = 'Loan Rejected';
                        $statusReason = $model->reject_reason;
                    }else{
                        $status = 'Submitted';
                        $statusReason = 'Submitted';
                    }
                    AcagHelper::actionPush($model,$status,$statusReason,$model->req_amount,date('Y-m-d'),0,null);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'projects' => $projects,
                'branches' => $branches,
                'cib_model' => $cib_model
            ]);
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
        $model->save();
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
            $model->deleted = 1;
            $model->deleted_by = Yii::$app->user->getId();
            $model->deleted_at = strtotime(date('Y-m-d'));
            $model->save();
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

    public function actionSearchMember($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $result = array();
            //$query = new Query();
            $query = Members::find()->select('id, full_name , cnic')
                // ->from('members')
                //->join('inner join','members','members.id=files.member_id')
                // ->JOIN	my_other_table	t2
                //     ON	t1.primary_id_field = t2.foreign_key_id_fiel
                //->filterWhere(['like', 'full_name', $q])
                ->orFilterWhere(['=', 'cnic', $q])
                ->andFilterWhere(['!=', 'deleted', '1']);
            //Yii::$app->Permission->getSearchFilterQuery($query,'members',Yii::$app->controller->action->id,$this->rbac_type);
            //$command = $query->createCommand();
            $data = $query->asArray()->all();
            foreach ($data as $k => $v) {
                $result[$k]['id'] = $v['id'];
                $result[$k]['text'] = '<strong>Name</strong>: ' . $v['full_name'] . ' <strong>CNIC</strong>: ' . $v['cnic'];
            }
            $out['results'] = $result;
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Applications::findOne($id)->form_no];
        }
        //echo json_encode($out);
        return $out;
    }

    public function actionSearchApplication($q = null, $id = null)
    {
        $cur_date = strtotime(date('Y-m-d'));
        $six_month_back_date = strtotime(date("Y-m-d", strtotime("-8 Months")));
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $result = array();
            if (strpos($q, '-') !== false) {
                $query = Applications::find()->select('applications.id, members.full_name , members.cnic, applications.application_no')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->orFilterWhere(['like', 'members.cnic', $q])
                    ->andFilterWhere(['!=', 'applications.deleted', '1'])
                    ->andFilterWhere(['applications.status' => 'pending'])
                    ->andFilterWhere(['between', 'applications.application_date', $six_month_back_date, $cur_date])
                    ->orderBy(['applications.created_at' => SORT_DESC]);
            } else {
                $query = Applications::find()->select('applications.id,members.cnic,members.full_name,applications.application_no')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->orFilterWhere(['like', 'applications.application_no', $q])
                    ->andFilterWhere(['!=', 'applications.deleted', '1'])
                    ->andFilterWhere(['applications.status' => 'pending'])
                    ->andFilterWhere(['between', 'applications.application_date', $six_month_back_date, $cur_date])
                    ->orderBy(['applications.created_at' => SORT_DESC]);
            }
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $query = $query->asArray()->all();
            foreach ($query as $k => $v) {
                $result[$k]['id'] = $v['id'];
                $result[$k]['text'] = '<strong>Application No</strong>: ' . $v['application_no'] . ' <strong>Name</strong>: ' . $v['full_name'] . ' <strong>CNIC</strong>: ' . $v['cnic'];
            }
            $out['results'] = $result;
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Applications::findOne($id)->form_no];
        }
        //echo json_encode($out);
        return $out;
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

    protected function findModelLock($id)
    {
        if (($model = Applications::find()->where(['id' => $id, 'is_lock' => 0])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLogs($id = null, $field = null)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return $this->renderAjax('logs', [
                    'id' => $id,
                    'field' => $field,
                ]);
                /*return [
                    'title' => "Log Application #" . $id,
                    'header'=> [
                        'close' =>['display'=> 'none'],
                    ],
                    'content' => $this->renderAjax('logs', [
                        'id' => $id,
                        'field' => $field,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]).
                        Html::a('Back',['view','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];*/
            }
        }

        return $this->render('logs', [
            'id' => $id,
            'field' => $field,
        ]);
    }

    public function actionValidateReqAmount($id)
    {
        $limit = ApplicationHelper::validateReqAmount($id);
        if (!empty($limit)) {
            $response['status_type'] = "success";
            $response['limit'] = $limit->loan_amount_limit;
        } else {
            $response['status_type'] = "failure";

        }
        return json_encode($response);
    }


    public function actionValidateCibAmount($id)
    {
        $branch = Branches::find()->where(['id' => $id])->one();
        $province = Provinces::find()->where(['id' => $branch->province_id])->select(['cib_fee'])->one();
        if (!empty($province)) {
            $response['status_type'] = "success";
            $response['cib_fee'] = $province->cib_fee;
        } else {
            $response['status_type'] = "failure";

        }
        return json_encode($response);
    }


    public function actionApproveApp()
    {
        $request = Yii::$app->request;
        if ($request->post() && isset($request->post()['selection'])) {
            foreach ($request->post()['selection'] as $id) {
                $application = Applications::findOne($id);
                $application->status = $request->post()['status'];
                if ($application->status == 'rejected') {
                    $application->reject_reason = $request->post()['reject_reason'];
                }
                $application->save(false);

                ActionsHelper::updateAction('application', $application->id, 'approved/rejected');
                ActionsHelper::insertActions('verification', $application->project_id, $application->id, $application->created_by, 1);

                //ActionsHelper::insertActions('application',0,$application->id,$application->created_by,1);

                /*$app_action=ApplicationActions::find()->where(['parent_id'=>$application->id,'action'=>'approved/rejected'])->one();
                if(!empty($app_action)){
                    $app_action->status=1;
                    $app_action->save();
                }*/
            }
        }
        $searchModel = new ApplicationsSearch();
        if (!empty(Yii::$app->request->queryParams)) {
            $dataProvider = $searchModel->search_pending_applications(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        } else {
            $dataProvider = [];
        }
        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        //$regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('approve-applications/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions_by_id,
            //'regions' => $regions,
            //'areas' => $areas,
            //'branches' => $branches,
            //'projects' => $projects,
        ]);
    }

    public function actionVisitDetails($id)
    {
        $model = $this->findModel($id);
        /* $active_first_visit = true;
        $visit = Visits::find()->where(['parent_type' => 'application','parent_id' => $id, 'deleted' => 0,'is_tranche'=>1])->one();
        if(isset($visit)&& !empty($visit)){
            $active_first_visit = false;
        }*/
        $details = ApplicationHelper::getVisitsByRole($id, false);

        return $this->render('visits_view', [
            'details' => $details,
            'model' => $model,
            // 'active_first_visit' => $active_first_visit
        ]);
    }

    public function actionUpdateVisit($application_id)
    {
        $request = Yii::$app->request->post();
        if (!empty($request)) {
            $visit = Visits::find()->where(['id' => $request['Visits']['id']])->one();
            $visit->load($request);
            $visit->save();
        }
        return $this->redirect(['visit-details', 'id' => $application_id]);
    }

    public function actionDeleteVisitImage($id, $image_name)
    {
        $visit = Visits::find()->where(['id' => $id])->one();
        if ($visit->imagesCount - 1 >= 3) {
            Images::deleteAll(['parent_id' => $id, 'image_name' => $image_name, 'parent_type' => 'visits']);
        }

        return $this->redirect(['visit-details', 'id' => $visit->parent_id]);
    }

    public function actionPublishImage($id, $app_id, $page)
    {
        $image = Images::find()->where(['id' => $id])->one();
        if ($image->is_published == 1) {
            $image->is_published = 0;
        } else {
            $image->is_published = 1;
        }
        $image->save();

        return $this->redirect(['visit-images', 'page' => $page]);
    }

    public function actionVisitImages()
    {
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->searchVisitsImages(Yii::$app->request->queryParams);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $visitCount = [1 => "One", 2 => "Two", 3 => "Three", ">3" => "Greater Than 3"];
        $disb_status = ['disbursed' => "Disbursed", 'partial' => "Partially Disbursed", 'null' => "Not Disbursed"];
        $images_status = ['0' => "Not Published", '1' => "Published"];
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('visit-images/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'visitCount' => $visitCount,
            'disb_status' => $disb_status,
            'images_status' => $images_status,
        ]);
    }

    public function actionFloodVisitImages()
    {
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->searchVisitsFloodImages(Yii::$app->request->queryParams);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $visitCount = [1 => "One", 2 => "Two", 3 => "Three", ">3" => "Greater Than 3"];
        $disb_status = ['disbursed' => "Disbursed", 'partial' => "Partially Disbursed", 'null' => "Not Disbursed"];
        $images_status = ['0' => "Not Published", '1' => "Published"];
        $referrals_nr = ['not-referred' => "Not Referred"];
        $referrals = ArrayHelper::map(Referrals::find()->all(), 'name', 'name');
        $referrals = array_merge($referrals_nr, $referrals);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('visit-images/flood-visit-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'visitCount' => $visitCount,
            'disb_status' => $disb_status,
            'images_status' => $images_status,
            'referrals' => $referrals
        ]);
    }

    public function actionDeleteVisit($visit_id, $id)
    {
        $request = Yii::$app->request;
        $visit = Visits::find()->where(['id' => $visit_id])->one();
        $visit->deleted = 1;
        $visit->save();
        if (!empty($visit->estimated_figures) && isset($visit->estimated_figures) && ($visit->is_tranche == 0)) {
            $next_visit = Visits::find()->where(['parent_id' => $visit->parent_id, 'deleted' => 0, 'is_tranche' => 0])->orderBy('id')->one();
            if (!empty($next_visit)) {
                $next_visit->estimated_figures = $visit->estimated_figures;
                $next_visit->save();
            }
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['visit-details', 'id' => $id]);
        }

    }

    public function actionAddDocument($id)
    {
        $request = Yii::$app->request;
        $application = $this->findModel($id);
        $documents = ArrayHelper::map(Documents::find()->where(['module_type' => 'applications'])
            ->orWhere(['and', ['module_type' => 'projects'], ['module_id' => $application->project_id]])
            ->asArray()->all(), 'name', 'name');
        $post = Yii::$app->request->post();

        $image_model = new Images();
        $image_type = $post['Images']['image_type'];
        $parent_id = $post['Images']['parent_id'];
        $parent_type = $post['Images']['parent_type'];
        //$rand = rand(111111, 999999);

        if (Yii::$app->request->isPost) {
            $data = file_get_contents($_FILES['Images']['tmp_name']['image_data']);
            $data = base64_encode($data);
            $image_name1 = $image_type . '_' . rand(111111, 999999) . '.png';
            $flag = ImageHelper::imageUpload($parent_id, $parent_type, $image_type, $image_name1, $data);
            if ($flag) {
                return $this->redirect(['view', 'id' => $application->id]);
            } else {
                return $this->render('add-document', [
                    'model' => $image_model,
                    'application' => $application,
                    'documents' => $documents,
                ]);
            }
        } else {
            return $this->render('add-document', [
                'model' => $image_model,
                'application' => $application,
                'documents' => $documents,

            ]);
        }

    }

    public function actionVisitsReports()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Region', 'Area', 'Branch', 'Site Enginner', 'Percent', 'Visited_at', 'No of Visits', 'Application No', 'Application Date', 'Recommended Amount', 'Shifted Status']);
            $visits_detail = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->searchVisitsReport($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $visits_detail[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $visits_detail[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $visits_detail[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $visits_detail[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $visits_detail[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $visits_detail[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $visits_detail[$i]['Site_Engineer_Name'] = isset($g['lastVisit']['user']['username']) ? $g['lastVisit']['user']['username'] : '';
                $visits_detail[$i]['percent'] = isset($g['lastVisit']['percent']) ? "'" . $g['lastVisit']['percent'] . "'" : '';
                //$visits_detail[$i]['visited_at'] = date("d-M-y", isset($g['lastVisit']['created_at']) ? $g['lastVisit']['created_at'] : '');
                $visits_detail[$i]['visited_at'] = date(
                    "d-M-y",
                    isset($g['lastVisit']['created_at'])
                        ? strtotime($g['lastVisit']['created_at'])
                        : null
                );
                $visits_detail[$i]['No_of_visits'] = isset($g['visitsCount']) ? $g['visitsCount'] : '';
                $visits_detail[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $visits_detail[$i]['application_date'] = date("d-M-y", isset($g['application_date']) ? $g['application_date'] : '');
                $visits_detail[$i]['recommended_amount'] = isset($g['recommended_amount']) ? $g['recommended_amount'] : '';
                $visits_detail[$i]['shifted_status'] = (!empty($g->applicationDetails) && $g->applicationDetails != null && $g->applicationDetails->is_shifted == 1) ? 'Shifted' : 'Not Shifted';
                $i++;
            }
            ExportHelper::ExportCSV('visits_detail', $headers, $visits_detail);
            die();
        }
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->searchVisitsReport(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('visits-report/visitsreport', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
        ]);
    }

    public function actionVisitsShiftedApproval()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Region', 'Area', 'Branch', 'Site Enginner', 'Percent', 'Visited_at', 'No of Visits', 'Application No', 'Application Date', 'Recommended Amount', 'Shifted Status']);
            $visits_detail = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->visitsShiftedApprovalList($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $visits_detail[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $visits_detail[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $visits_detail[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $visits_detail[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $visits_detail[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $visits_detail[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $visits_detail[$i]['Site_Engineer_Name'] = isset($g['lastVisit']['user']['username']) ? $g['lastVisit']['user']['username'] : '';
                $visits_detail[$i]['percent'] = isset($g['lastVisit']['percent']) ? "'" . $g['lastVisit']['percent'] . "'" : '';
                $visits_detail[$i]['visited_at'] = date("d-M-y", isset($g['lastVisit']['created_at']) ? $g['lastVisit']['created_at'] : '');
                $visits_detail[$i]['No_of_visits'] = isset($g['visitsCount']) ? $g['visitsCount'] : '';
                $visits_detail[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $visits_detail[$i]['application_date'] = date("d-M-y", isset($g['application_date']) ? $g['application_date'] : '');
                $visits_detail[$i]['recommended_amount'] = isset($g['recommended_amount']) ? $g['recommended_amount'] : '';
                $visits_detail[$i]['shifted_status'] = (!empty($g->applicationDetails) && $g->applicationDetails != null && $g->applicationDetails->is_shifted == 1) ? 'Shifted' : 'Not Shifted';
                $i++;
            }
            ExportHelper::ExportCSV('visits_detail', $headers, $visits_detail);
            die();
        }
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->visitsShiftedApprovalList(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('visits-report/visitsApprovalList', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
        ]);
    }

    public function actionConstructionCompletedApproval()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Region', 'Area', 'Branch', 'Site Enginner', 'Percent', 'Visited_at', 'No of Visits', 'Application No', 'Application Date', 'Recommended Amount', 'Shifted Status']);
            $visits_detail = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->visitsConstructionCompletedApprovalList($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $visits_detail[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $visits_detail[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $visits_detail[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $visits_detail[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $visits_detail[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $visits_detail[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $visits_detail[$i]['Site_Engineer_Name'] = isset($g['lastVisit']['user']['username']) ? $g['lastVisit']['user']['username'] : '';
                $visits_detail[$i]['percent'] = isset($g['lastVisit']['percent']) ? "'" . $g['lastVisit']['percent'] . "'" : '';
                $visits_detail[$i]['visited_at'] = date("d-M-y", isset($g['lastVisit']['created_at']) ? $g['lastVisit']['created_at'] : '');
                $visits_detail[$i]['No_of_visits'] = isset($g['visitsCount']) ? $g['visitsCount'] : '';
                $visits_detail[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $visits_detail[$i]['application_date'] = date("d-M-y", isset($g['application_date']) ? $g['application_date'] : '');
                $visits_detail[$i]['recommended_amount'] = isset($g['recommended_amount']) ? $g['recommended_amount'] : '';
                $visits_detail[$i]['shifted_status'] = (!empty($g->applicationDetails) && $g->applicationDetails != null && $g->applicationDetails->is_shifted == 1) ? 'Shifted' : 'Not Shifted';
                $i++;
            }
            ExportHelper::ExportCSV('visits_detail', $headers, $visits_detail);
            die();
        }
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->visitsConstructionCompletedApprovalList(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('visits-report/constructionApprovalList', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
        ]);
    }

    public function actionShiftedVisitDetails($appId,$visitId)
    {
        $model = $this->findModel($appId);
        $details = ApplicationHelper::getShiftedVisitsId($visitId, false);

        return $this->render('visits-report/shifted_visits_view', [
            'details' => $details,
            'model' => $model,
        ]);
    }

    public function actionConstructionVisitDetails($appId,$visitId)
    {
        $model = $this->findModel($appId);
        $details = ApplicationHelper::getShiftedVisitsId($visitId, false);

        return $this->render('visits-report/construction_visits_view', [
            'details' => $details,
            'model' => $model,
        ]);
    }

    public function actionConstructionPercentagePush($id){
        $visit = Visits::find()->where(['id' => $id])
            ->andWhere(['deleted'=>0])
            ->one();
        $visit->construction_verified_by = 1;
        if($visit->save()){
            $application = Applications::find()->where(['id' => $visit->parent_id])->one();
            if ($application && $application->project_id == 132) {
                $loan = Loans::find()->where(['application_id'=>$application->id])->one();
                if(!empty($loan) && $loan!=null){
                    AcagHelper::actionPush($application,'Construction','Construction',0,date('Y-m-d'),$visit->id,$loan);
                    return $this->redirect(['construction-completed-approval']);
                }
            }
        }else{
            return $this->redirect(['visits-report/construction-completed-approval']);
        }
    }


    public function actionApproveShiftedVisit($id){
        $request = Yii::$app->request;
        $visit = Visits::find()->where(['id' => $id])
            ->andWhere(['deleted'=>0])
            ->one();
        $visit->shifted_verified_by = 1;
        if($visit->save()){
            $isShifted = ApplicationDetails::find()->where(['parent_type'=>'application'])
                ->andWhere(['parent_id'=>$visit->parent_id])
                ->one();

            if(!empty($isShifted) && $isShifted!=null){
                $isShifted->is_shifted = 1;
                $isShifted->shifted_verified_by = Yii::$app->user->id;
                if($isShifted->save()){
                    $application = Applications::find()->where(['id' => $isShifted->parent_id])->one();
                    if ($application && $application->project_id == 132) {
                        $loan = Loans::find()->where(['application_id'=>$application->id])->one();
                        if(!empty($loan) && $loan!=null){
                            AcagHelper::actionPush($application,'Visit','Visit',0,date('Y-m-d'),$visit->id,$loan);
                            AcagHelper::actionPush($application,'Construction','Construction',0,date('Y-m-d'),$visit->id,$loan);
                            return $this->redirect(['visits-shifted-approval']);
                        }
                    }
                }
            }else{
                $model_isShifted = new ApplicationDetails();
                $model_isShifted->application_id = $visit->parent_id;
                $model_isShifted->parent_type = $visit->parent_type;
                $model_isShifted->parent_id = $visit->parent_id;
                $model_isShifted->is_shifted =  1;
                $model_isShifted->shifted_verified_by =  Yii::$app->user->id;
                if($model_isShifted->save()){
                    $application = Applications::find()->where(['id' => $visit->parent_id])->one();
                    if ($application && $application->project_id == 132) {
                        $loan = Loans::find()->where(['application_id'=>$application->id])->one();
                        if(!empty($loan) && $loan!=null){
                            AcagHelper::actionPush($application,'Visit','Visit',0,date('Y-m-d'),$visit->id,$loan);
                            AcagHelper::actionPush($application,'Construction','Construction',0,date('Y-m-d'),$visit->id,$loan);
                            return $this->redirect(['visits-shifted-approval']);
                        }
                    }
                }
            }
            return $this->redirect(['visits-report/visits-shifted-approval']);
        }else{
            return $this->redirect(['visits-report/visits-shifted-approval']);
        }
    }

    public function actionOwnHousingReport()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['CNIC', 'Parentage', 'Parentage_type', 'Mother_name',
            'Gender', 'Project', 'Region', 'Area', 'Branch', 'code', 'Title','Account_no','Marital_status',
             'DOB', 'Birthplace', 'Cnic_issue_date', 'Cnic_expiry_date',
            'Address', 'Mobile','source_of_income', 'Salary','status']);
            $apni_chatt = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->searchOwnHouseModel($_GET, true);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {

                $apni_chatt[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $apni_chatt[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $apni_chatt[$i]['parentage_type'] = isset($g['member']['parentage_type']) ? $g['member']['parentage_type'] : '';
                $apni_chatt[$i]['mother_name'] = isset($g['member']['memberInfo']['mother_name']) ? $g['member']['memberInfo']['mother_name'] : '';
                $apni_chatt[$i]['gender'] = isset($g['member']['gender']) ? $g['member']['gender'] : '';
                $apni_chatt[$i]['project_name'] = isset($g['project']['name']) ? $g['project']['name'] : '';
                $apni_chatt[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $apni_chatt[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $apni_chatt[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $apni_chatt[$i]['branch_code'] = isset($g['branch']['code']) ? $g['branch']['code'] : '';
                $apni_chatt[$i]['title'] = isset($g['member']['memberAccount']['title']) ? $g['member']['memberAccount']['title'] : '';
                $apni_chatt[$i]['account_no'] = isset($g['member']['memberAccount']['account_no']) ? "'" . $g['member']['memberAccount']['account_no']. "'": '';
                $apni_chatt[$i]['marital_status'] = isset($g['member']['marital_status']) ? $g['member']['marital_status'] : '';
                $apni_chatt[$i]['dob'] = isset($g['member']['dob']) ? date('Y-m-d',$g['member']['dob']) : '';
                $apni_chatt[$i]['dbirthplaceob'] = isset($g['member']['birthplace']) ? $g['member']['birthplace'] : '';
                $apni_chatt[$i]['cnic_issue_date'] = isset($g['member']['memberInfo']['cnic_issue_date']) ? $g['member']['memberInfo']['cnic_issue_date'] : '';
                $apni_chatt[$i]['cnic_expiry_date'] = isset($g['member']['memberInfo']['cnic_expiry_date']) ? $g['member']['memberInfo']['cnic_expiry_date'] : '';
                $apni_chatt[$i]['address'] = isset($g['member']['membersAddress']['address']) ? $g['member']['membersAddress']['address'] : '';
                $apni_chatt[$i]['phone'] = isset($g['member']['membersPhone']['phone']) ? $g['member']['membersPhone']['phone'] : '';
                $apni_chatt[$i]['source_of_income'] = isset($g['socialAppraisal']['source_of_income']) ? $g['socialAppraisal']['source_of_income'] : '';
                $apni_chatt[$i]['salary'] = isset($g['socialAppraisal']['total_household_income']) ? $g['socialAppraisal']['total_household_income'] : '';
                $apni_chatt[$i]['status'] = isset($g['status'])?$g['status']:'';
                $i++;
            }
            ExportHelper::ExportCSV('ApnichatApnagher', $headers, $apni_chatt);
            die();
        }
        $searchModel = new ApplicationsSearch();

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $dataProvider = $searchModel->searchOwnHouseModel(Yii::$app->request->queryParams);
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects_name = ArrayHelper::map(Projects::find()->where(['in', 'id', [            
        132
        ]])->all(), 'id', 'name');
        return $this->render('bank-accounts/apnichatt', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'bank_names' => $bank_names,
            'projects_name' => $projects_name,
            'regions' => $regions
        ]);
    }

    public function actionBankaccount()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Application No', 'Bank Name', 'Project', 'Region', 'Area', 'Branch', 'Title', 'Account No', 'Status', 'Account File ID', 'Account verified At', 'Account Created At']);
            $bank_accounts = array();
            $searchModel = new ApplicationsSearch();
            $query = $searchModel->searchBankReport($_GET, true);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $bank_accounts[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $bank_accounts[$i]['parentage'] = isset($g['member']['parentage']) ? $g['member']['parentage'] : '';
                $bank_accounts[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $bank_accounts[$i]['application_no'] = isset($g['application_no']) ? $g['application_no'] : '';
                $bank_accounts[$i]['bank_name'] = isset($g['member']['memberAccount']['bank_name']) ? $g['member']['memberAccount']['bank_name'] : '';
                $bank_accounts[$i]['project_name'] = isset($g['project']['name']) ? $g['project']['name'] : '';
                $bank_accounts[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $bank_accounts[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $bank_accounts[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $bank_accounts[$i]['title'] = isset($g['member']['memberAccount']['title']) ? $g['member']['memberAccount']['title'] : '';
                $bank_accounts[$i]['account_no'] = isset($g['member']['memberAccount']['account_no']) ? "'" . $g['member']['memberAccount']['account_no'] . "'" : '';
                $bank_accounts[$i]['status'] = \common\components\Helpers\StructureHelper::getMemberaccountstatus($g['member']['memberAccount']['status']);
                $bank_accounts[$i]['account_file_id'] = isset($g['member']['memberAccount']['acc_file_id']) ?  $g['member']['memberAccount']['acc_file_id'] : '0';
                $bank_accounts[$i]['account_file_very_at'] = isset($g['member']['memberAccount']['verified_at']) ? date('Y-m-d', $g['member']['memberAccount']['verified_at']) : '';
                $bank_accounts[$i]['created_at'] = isset($g['member']['memberAccount']['created_at']) ?  date('Y-m-d', $g['member']['memberAccount']['created_at']) : '';

                $i++;
            }
            ExportHelper::ExportCSV('bankreport', $headers, $bank_accounts);
            die();
        }
        $searchModel = new ApplicationsSearch();

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $dataProvider = $searchModel->searchBankReport(Yii::$app->request->queryParams);
        }

        //$bank_names = ArrayHelper::map(\common\models\Lists::find()->where(['list_name' => 'bank_accounts'])->all(), 'value', 'label');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
//        $projects_name = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $projects_name = ArrayHelper::map(Projects::find()->where(['in', 'id', [
            52, 61, 62, 67, 64, 76, 77, 78, 79, 83, 90, 97, 103, 109, 105, 106, 36,
            74, 85, 86, 88, 94, 96, 99, 100, 110, 113, 11, 56, 118, 114, 119, 124, 126, 127,132,87,134,136,128,35,96

        ]])->all(), 'id', 'name');
        return $this->render('bank-accounts/bankaccount', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'bank_names' => $bank_names,
            'projects_name' => $projects_name,
            'regions' => $regions
        ]);
    }

    public function actionVerifyAccount($id)
    {
        $request = Yii::$app->request;
        $model = MembersAccount::find()->where(['id' => $id])->one();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update BankAccounts #" . $id,
                    'content' => $this->renderAjax('bank-accounts/update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Update', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {
                if ($model->save(false)) {
                    if($model->status == 1){
                        MemberHelper::getVerficationAction($model->member_id,$model->created_by);
                    }
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Update Bank Account",
                        'content' => '<span class="text-success"> Account Updated Successfully. </span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    return [
                        'title' => "Update Bank Accounts #" . $id,
                        'content' => $this->renderAjax('bank-accounts/update', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Update', ['class' => 'btn btn-primary', 'type' => "submit"])
                    ];
                }
            } else {
                return [
                    'title' => "Update BankAccounts #" . $id,
                    'content' => $this->renderAjax('bank-accounts/update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Update', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                if ($model->save(false)) {
                    MemberHelper::getVerficationAction($model->member_id,$model->created_by);
                    return $this->redirect(['bankaccount']);
                } else {
                    return $this->render('bank-accounts/update', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('bank-accounts/update', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionImageToCrop()
    {

        return $this->render('test_crop', [

        ]);

    }

    public function actionDownloadFile($id)
    {
        $file = FilesApplication::find()->where(['application_id' => $id])->one();

        if (!empty($file)) {
            $file_path = ImageHelper::getAttachmentPath() . '/cib_files/' . $file->file_path;
            if (file_exists($file_path)) {
                return Yii::$app->response->sendFile($file_path);
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    public function actionCib($application_id)
    {
        $cib = ApplicationsCib::find()->where(['application_id' => $application_id])->one();
        if ($cib->type == 1) {
            if (!empty($cib)) {
                $file_path = ImageHelper::getAttachmentPath() . '/cib_files/' . $cib->file_path;
                if (file_exists($file_path)) {
                    return Yii::$app->response->sendFile($file_path);
                } else {
                    throw new NotFoundHttpException('File not exist');
                }
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        } else if ($cib->transfered == 1) {

            if (!empty($cib)) {
                $file_path = ImageHelper::getAttachmentPath() . $cib->file_path;
                if (file_exists($file_path)) {
                    return Yii::$app->response->sendFile($file_path);
                } else {
                    throw new NotFoundHttpException('File not exist');
                }
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        } else {
            $response = json_decode($cib->response);
            if ($cib->cib_type_id == 2) {
                $response = json_decode(json_encode($response), true);
                $content = \common\widgets\CibDataCheck\CibDataCheck::widget(['model' => $response]);
            } else {
                $content = \common\widgets\Cib\Cib::widget(['model' => $response]);
            }

            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_LEGAL,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssFile' => '/css/cib.css',
                'options' => ['title' => '<h1>Consumer Credit Report</h1>'],
                'methods' => [
                    //'SetHeader'=>['<h3 style="text-align: center">Consumer Credit Information Report</h3>'],
                    'SetHeader' => ['Akhuwat'],
                    'SetFooter' => ['{PAGENO}'],
                ]
            ]);
            $pdf->filename = time() . '_' . $cib->application_id . '_cib.pdf';
            $pdf->destination = '/';
            return $pdf->render();
        }
    }

    public function actionPdf($id, $type)
    {
        if ($type == 'app') {
            $application = Applications::findOne($id);
            $image = NadraVerisys::findOne(['application_id' => $id, 'member_id' => $application->member_id, 'document_type' => 'nadra_document']);
            $attachment_path = ImageHelper::getAttachmentPath() . '/uploads/members/' . $id . '/' . $image->document_name;

            // This will need to be the path relative to the root of your app.
            // Might need to change '@app' for another alias
            $completePath = $attachment_path;
            if (file_exists($completePath)) {
                return Yii::$app->response->sendFile($completePath, $image->document_name);
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        } else {
            $model = Members::findOne($id);
            $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'nadra_document']);
            $attachment_path = ImageHelper::getAttachmentPath() . '/uploads/members/' . $model->id . '/' . $image->image_name;

            // This will need to be the path relative to the root of your app.
            // Might need to change '@app' for another alias
            $completePath = $attachment_path;
            if (file_exists($completePath)) {
                return Yii::$app->response->sendFile($completePath, $image->image_name);
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        }
    }

    public function actionAddNadraDocument($id)
    {
        $request = Yii::$app->request;
        $application = Applications::find()->where(['id' => $id])->one();
        $post = Yii::$app->request->post();
        $image_model = NadraVerisys::find()->where(['application_id'=>$id])->one();


        $parent_id = $id;
        $image_type = 'nadra_document';
        $parent_type = 'members';

        $rand = rand(111111, 999999);

        if (Yii::$app->request->isPost) {

            $data = file_get_contents($_FILES['Images']['tmp_name']['image_data']);
            $data = base64_encode($data);
            if ($image_type == 'nadra_document') {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.jpg';
            } else {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.png';
            }
            $flag = ImageHelper::imageUploadApp($parent_id, $parent_type, $image_name1, $data);
            if ($flag) {
                $image_model->upload_by = Yii::$app->user->getId();
                $image_model->upload_at = strtotime(date('Y-m-d H:i:s'));
                if($image_model->save()){
                    return $this->redirect(['view', 'id' => $application->id]);
                }else{
                    return $this->redirect(['view', 'id' => $application->id]);
                }
            } else {
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
    }

    public function actionUpdateNadraStatus($id)
    {
        $model = NadraVerisys::find()->where(['application_id'=>$id])->one();
        $model->status = 1;
        $model->approved_by = Yii::$app->user->getId();
        $model->approved_at = strtotime(date('Y-m-d H:i:s'));

        if($model->save()){
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            print_r($model->getErrors());
            die();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}
