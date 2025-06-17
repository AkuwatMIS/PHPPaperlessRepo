<?php

namespace frontend\controllers;

use common\components\Helpers\AcagHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\PDFHelper;
use common\components\Helpers\StructureHelper;
use common\components\RbacHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\Areas;
use common\models\Awp;
use common\models\Branches;
use common\models\Disbursements;
use common\models\EmergencyLoans;
use common\models\GroupActions;
use common\models\LoanActions;
use common\models\LoanTranches;
use common\models\Operations;
use common\models\PDF;
use common\models\Projects;
use common\models\Referrals;
use common\models\Regions;
use common\models\search\CreditSearch;
use common\models\search\DuelistSearch;
use common\models\search\GlobalsSearch;
use common\models\search\PortfolioSearch;
use common\components\Helpers\ProgressReportHelper;
use common\models\search\OverduelistSearch;
use common\models\search\TakafulDueSearch;
use common\models\Takafuldue;
use common\models\VegaLoan;
use common\models\VigaLoans;
use Yii;
use common\models\Loans;
use common\models\search\LoansSearch;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\components\Helpers\ExportHelper;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;
use yii\widgets\ActiveForm;

/**
 * LoansController implements the CRUD actions for Loans model.
 */
class LoansController extends Controller
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
                    //'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Loans models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['LoansSearch']);
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseLoansListCsvExportData($data);
            ExportHelper::ExportCSV('loans.csv', $headers, $groups);
            exit();
        }
        $searchModel = new LoansSearch();
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $params = Yii::$app->request->queryParams;
            $params['LoansSearch']['disb_date'] = strtotime(date("Y-m-d", strtotime("-8 Months")));

            $dataProvider = $searchModel->search($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }

        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
//        $projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $inst_type = LoanHelper::getInstType();
        $dsb_status = LoanHelper::getDsbStatus();
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'projects' => $projects, 'regions_by_id' => $regions_by_id, 'inst_type' => $inst_type, 'dsb_status' => $dsb_status,];
        return $this->render('index', [
            'data' => $data
        ]);
    }

    public function actionPledgeIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['LoansSearch']);
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->searchPledgeLoan($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseLoansListCsvExportData($data);
            ExportHelper::ExportCSV('loans.csv', $headers, $groups);
            exit();
        }

        $searchModel = new LoansSearch();
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $params = Yii::$app->request->queryParams;
            $params['LoansSearch']['disb_date'] = strtotime(date("Y-m-d", strtotime("-8 Months")));

            $dataProvider = $searchModel->searchPledgeLoan($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }


        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $inst_type = LoanHelper::getInstType();
        $dsb_status = LoanHelper::getDsbStatus();
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'projects' => $projects, 'regions_by_id' => $regions_by_id, 'inst_type' => $inst_type, 'dsb_status' => $dsb_status,];
        return $this->render('pledge_index', [
            'data' => $data
        ]);
    }


    public function actionUpdatePledgeStatus($id=0)
    {
        if($id>0){
            $model = Applications::find()->where(['id'=>$id])->one();
            $model->is_pledged = 1;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Pledge status updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update pledge status.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays a single Applications model.
     * @param integer $id
     * @return mixed
     */
    public function actionApplicationDetails($id)
    {
        $this->layout = 'main_simple_js';
        return $this->render('applicationDetails', [
            'model' => Applications::findOne($id),
        ]);
    }

    public function actionLoanDetails($id)
    {
        $this->layout = 'main_simple_js';
        return $this->render('loanDetails', [
            'model' => Loans::findOne($id),
        ]);
    }

    /**
     * Displays a single Loans model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Loans #" . $id,
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
     * Creates a new Loans model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {

        $request = Yii::$app->request;
        $model = new Loans();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $application = Applications::findOne($id);
                return [
                    'title' => "Create new Loans",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new Loans",
                    'content' => '<span class="text-success">Create Loans success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new Loans",
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
            if ($model->load($request->post())) {

                $applicationModel = Applications::find()
                    ->where(['id' => $request->post()['Loans']['application_id']])
                    ->select(['application_date','status'])->one();
                $toDay = date('Y-m-d');
                $approvedDate = $request->post()['Loans']['date_approved'];
                $applicationDate = date('Y-m-d', $applicationModel->application_date);
                $applicationStatus = $applicationModel->status;

                if ($approvedDate <= $toDay && $approvedDate >= $applicationDate && $applicationStatus == 'approved') {
                } else {
                    $application = Applications::findOne($id);
                    return $this->render('create', [
                        'model' => $model,
                        'application' => $application,
                    ]);
                }

                $model->status = 'pending';
                ///
                $application = Applications::find()->where(['id' => $model->application_id, 'deleted' => 0])->one();
                if (!empty($application->loan)) {
                    return $this->render('create', [
                        'model' => $model,
                        'application' => $application,
                    ]);
                }

                $model->validateLoanAmount($application);
                if (!empty($model->getErrors())) {
                    $application = Applications::findOne($id);
                    return $this->render('create', [
                        'model' => $model,
                        'application' => $application,
                    ]);
                }

                $transaction = Yii::$app->db->beginTransaction();
                $model = LoanHelper::setSanctionNo($model);
                $model = LoanHelper::CreateLoan($model);

                if ($model) {
                    $project = $model->project->name;
                    //$project='New Housing Scheme';
                    Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
                    $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/tranches/' . 'tranches.json';
                    $json = json_decode(file_get_contents($file_path), true)[$project];
//                    AIM & Khawaja Jalaluddin Roomi from
//                    Khawaja Jalaluddin Roomi to
                    $tranch_data = [];
                    if ($json) {
                        foreach ($json as $j) {
                            if ($model->loan_amount >= $j['min'] && $model->loan_amount <= $j['max']) {
                                $tranch_info = explode(',', $j['percent']);
                                $i = 1;
                                foreach ($tranch_info as $info) {
                                    $tranch_amount = ($info * $model->loan_amount) / 100;
                                    $tranch_data[] = array(
                                        "tranch_no" => $i,
                                        "tranch_amount" => $tranch_amount,
                                        "tranch_charges_amount" => $model->service_charges / $j['tranches'],
                                        "status" => ($i == 1) ? 3 : 0,
                                    );
                                    $i++;
                                }
                            }
                        }
                    }

                    $tranch_savae_flag = LoanHelper::saveTranches($model, $tranch_data);
                    if ($tranch_savae_flag) {
                        ActionsHelper::insertActions('loan', $model->project_id, $model->id, $model->created_by);
                        LoanHelper::updateLoanVerifyAcction($model);
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }

                    ///Update group action
                    $action_flag = true;
                    $application = Applications::find()->where(['group_id' => $model->group_id])->all();
                    foreach ($application as $app) {
                        if (!isset($app->loan)) {
                            $action_flag = false;
                        } else {
                            $app->is_lock = 1;
                            if ($app->save()) {
                                ////Application Actions
                            }
                        }
                    }
                    if ($action_flag == true) {
                        $action_model = GroupActions::find()->where(['parent_id' => $model->group_id, 'action' => 'lac'])->one();
                        if (!empty($action_model)) {
                            $action_model->status = 1;
                            $action_model->save();
                        } else {
                            $action_model = new GroupActions();
                            $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 1);
                            $action_model->created_by = $model->created_by;
                            $action_model->save();
                        }
                    } else {
                        $action_model = GroupActions::find()->where(['parent_id' => $model->group_id, 'action' => 'lac'])->one();
                        if (empty($action_model)) {
                            $action_model = new GroupActions();
                            $action_model->setValues($model->group_id, "lac", $model->created_by, $status = 0);
                            $action_model->created_by = $model->created_by;
                            $action_model->save();
                        }
                    }
//                    $takaf=new Takafuldue();
//                    $takaf->loan_id=$model->id;
//                    $takaf->branch_id=$model->branch_id;
//                    $takaf->region_id=$model->region_id;
//                    $takaf->area_id=$model->area_id;
//                    $takaf->olp=$model->loan_amount;
//                    $takaf->takaful_year=date("Y");
//                    $takaf->takaful_amnt=($model->loan_amount *0.5)/100;
//                    $takaf->save();
                    ////
                } else {
                    $transaction->rollBack();
                }
                return $this->redirect(['lac'/*, 'id' => $model->id*/]);
            } else {
                $application = Applications::findOne($id);
                return $this->render('create', [
                    'model' => $model,
                    'application' => $application,
                ]);
            }
        }

    }

    /**
     * Creates a new Loans model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionLedger($id)
    {
        $loan = $this->findModel($id);
        return $this->render('global-ledger', [
            'model' => $loan,
        ]);
    }

    public function actionGlobalLedger($id)
    {
        $secretKey = '123qwe';
        $id = Yii::$app->getSecurity()->decryptByPassword($id, $secretKey);

        $loan = Loans::find()->where(['id' => $id])->one();
        //die($id);
        return $this->render('global-ledger', [
            'model' => $loan,
        ]);
    }

    public function actionLac()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $application = new Applications();
        $applications = array();


        if (isset($request->post()['Applications'])) {

            $application->load($request->post());
            $applications = Applications::find()
                ->where(['groups.grp_no' => $application->grp_no])
                ->andWhere(['applications.deleted' => 0])
                ->andWhere(['or', ['>', 'applications.recommended_amount', 0], ['not in', 'applications.project_id', StructureHelper::trancheProjectsExclude()]]);
            $applications->joinWith('group');
            Yii::$app->Permission->getSearchFilterQuery($applications, 'applications', 'index', $this->rbac_type);
            $applications = $applications->all();
            $session['group'] = $applications;
            /*echo'<pre>';
            print_r($session['group']);
            die();*/
        } else {
            if (!isset($_SESSION['group'])) {
                $_SESSION['group'] = array();

            } else {
                /*print_r($session['group']);
                die('a');*/
                $flag = false;
                if (!empty($session['group'])) {

                    foreach ($session['group'] as $app) {
                        $loan = Loans::find()->where(['application_id' => $app->id, 'deleted' => 0])->one();

                        if (empty($loan)) {
                            $flag = true;

                        }
                    }
                    if ($flag == false) {
                        unset($session);
                        $_SESSION['group'] = array();
                    }
                }
            }
        }
        if (isset($session['group'])) {
            $applications = $session['group'];
        }

        return $this->render('lac', [
            'application' => $application,
            'applications' => $applications,
        ]);

    }

    public function actionChequePrint()
    {
        $request = Yii::$app->request->get();
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        //$projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        //$awp_trgets=0;
        $awp_target_amount = 0;

        if (isset($_GET['export']) && $_GET['export'] == 'export') {

            $this->layout = 'csv';
            $headers = array('Sanction No', 'Inst Type', 'Inst Month', 'Date', 'Description', 'Amount');
            $searchModel = new LoansSearch();
            $searchModel->load($request);
            $groups = array();
            $params = Yii::$app->request->queryParams;
            if (!empty($params['LoansSearch'])) {
                $loans = LoanTranches::find()->where(['loan_tranches.status' => 4])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('left join', 'fund_requests', 'fund_requests.id = loan_tranches.fund_request_id')
                    ->join('inner join', 'loan_tranches_actions', 'loan_tranches.id=loan_tranches_actions.parent_id')
                    ->andWhere(['=', 'loan_tranches_actions.action', 'cheque_printing'])
                    ->andWhere(['=', 'loan_tranches_actions.status', '0'])
                    ->andWhere(['loans.deleted' => 0])
                    ->andWhere(['and', ['!=', 'loan_tranches.fund_request_id', 0], ['=', 'fund_requests.status', 'processed']])
                    //->andWhere(['not in','loans.project_id',StructureHelper::trancheProjects()])
                    //->andWhere(['in','loan_tranches.platform' ,[0,1]])
                    ->andWhere(['like', 'loans.branch_id', Yii::$app->request->queryParams['LoansSearch']['branch_id']]);
                //->orWhere(['and',['=' , 'loans.platform',  2], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['loan_tranches.status'=>4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',Yii::$app->request->queryParams['LoansSearch']['branch_id']]])
                //->orWhere(['and',['in' , 'loans.project_id',  StructureHelper::trancheProjects()], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['loan_tranches.status'=>4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',Yii::$app->request->queryParams['LoansSearch']['branch_id']]]);
                /*if(!empty(Yii::$app->request->queryParams['LoansSearch']['branch_id'])) {
                    $loans = $loans->andWhere(['like', 'loans.branch_id', Yii::$app->request->queryParams['LoansSearch']['branch_id']]);
                }
                if(!empty(Yii::$app->request->queryParams['LoansSearch']['project_id'])) {
                    $loans = $loans->andWhere(['=', 'loans.project_id', Yii::$app->request->queryParams['LoansSearch']['project_id']]);
                }*/
                if (!empty($params['LoansSearch']['date_disbursed'])) {
                    $date = explode(' - ', $params['LoansSearch']['date_disbursed']);
                    $loans = $loans->andWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
                }
            } else {
                $loans = LoanTranches::find()->where(['loan_tranches.status' => 4])
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->join('left join', 'fund_requests', 'fund_requests.id = loan_tranches.fund_request_id')
                    ->join('inner join', 'loan_tranches_actions', 'loan_tranches.id=loan_tranches_actions.parent_id')
                    ->andWhere(['=', 'loan_tranches_actions.action', 'cheque_printing'])
                    ->andWhere(['=', 'loan_tranches_actions.status', '0'])
                    ->andWhere(['loans.deleted' => 0])
                    ->andWhere(['and', ['!=', 'loan_tranches.fund_request_id', 0], ['=', 'fund_requests.status', 'processed']])
                    //->andWhere(['not in','loans.project_id',StructureHelper::trancheProjects()])
                    //->andWhere(['in','loan_tranches.platform' ,[0,1]])
                    ->andWhere(['like', 'loans.branch_id', Yii::$app->request->queryParams['LoansSearch']['branch_id']]);
                //->orWhere(['and',['=' , 'loans.platform',  2], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['loan_tranches.status'=>4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',Yii::$app->request->queryParams['LoansSearch']['branch_id']]])
                //->orWhere(['and',['in' , 'loans.project_id',  StructureHelper::trancheProjects()], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['loan_tranches.status'=>4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',Yii::$app->request->queryParams['LoansSearch']['branch_id']]]);
            }
            Yii::$app->Permission->getSearchFilterQuery($loans, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $loans->orderBy(['sanction_no' => SORT_ASC]);
            $data = $loans->all();
            $groups = ExportHelper::parseChequePrintCsvExportData($data);
            ExportHelper::ExportCSV('Cheque-Prints-Report.csv', $headers, $groups);
            die();
        }
        //$request = Yii::$app->getRequest();
        $searchModel = new LoansSearch();
        $searchModel->load($request);
        if (!empty($request)) {
            // $loans = Loans::find()->where(['status'=>'pending'])->andWhere(['is not','sanction_no',NULL])->andWhere(['!=','sanction_no',''])->andWhere(['=', 'branch_id', $request['LoansSearch']['branch_id']]);
            $loans = LoanTranches::find()->where(['loan_tranches.status' => 4])
                ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                ->join('left join', 'fund_requests', 'fund_requests.id = loan_tranches.fund_request_id')
                ->join('inner join', 'loan_tranches_actions', 'loan_tranches.id=loan_tranches_actions.parent_id')
                ->andWhere(['=', 'loan_tranches_actions.action', 'cheque_printing'])
                ->andWhere(['=', 'loan_tranches_actions.status', '0'])
                ->andWhere(['loans.deleted' => 0])
                ->andWhere(['and', ['!=', 'loan_tranches.fund_request_id', 0], ['=', 'fund_requests.status', 'processed']])
                //->andWhere(['not in','loans.project_id',StructureHelper::trancheProjects()])
                //->andWhere(['in','loan_tranches.platform' ,[0,1]])
                ->andWhere(['=', 'loans.branch_id', $request['LoansSearch']['branch_id']]);
            //->orWhere(['and',['=' , 'loan_tranches.platform',  2], ['loan_tranches_actions.action'=>'cheque_printing'],['loan_tranches_actions.status'=>0],['=','loan_tranches.status',4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',$request['LoansSearch']['branch_id']],['=','loans.deleted' ,0]])
            //->orWhere(['and',['in' , 'loans.project_id',  StructureHelper::trancheProjects()], ['loan_tranches_actions.action'=>'cheque_printing'],['loan_tranches_actions.status'=>0],['=','loan_tranches.status',4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['like','loans.branch_id',$request['LoansSearch']['branch_id']],['=','loans.deleted' ,0]]);

            if (isset($request['LoansSearch']['project_id']) && !empty($request['LoansSearch']['project_id'])) {
                $loans = $loans->andWhere(['=', 'loans.project_id', $request['LoansSearch']['project_id']]);
            }
            if (!empty($request['LoansSearch']['date_disbursed'])) {

                $date = explode(' - ', $request['LoansSearch']['date_disbursed']);
                $loans = $loans->andWhere(['between', 'date_disbursed', strtotime($date[0]), strtotime($date[1])]);
            }
        } else {
            /*$loans = LoanTranches::find()->where(['loan_tranches.status' => 4])
                ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                ->join('left join','fund_requests','fund_requests.id = loan_tranches.fund_request_id')
                ->join('inner join', 'loan_tranches_actions', 'loan_tranches.id=loan_tranches_actions.parent_id')
                ->andWhere(['=', 'loan_tranches_actions.action', 'cheque_printing'])
                ->andWhere(['=', 'loan_tranches_actions.status', '0'])
                ->andWhere(['and',['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed']])
                //->andWhere(['in','loan_tranches.platform' ,[0,1]])
                ->andWhere(['loans.deleted' => 0]);
                //->andWhere(['not in','loans.project_id',StructureHelper::trancheProjects()])
                //->orWhere(['and',['=','loans.platform',2], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['=','loan_tranches.status',4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['=','loans.deleted' ,0]])
                //->orWhere(['and',['in' , 'loans.project_id',  StructureHelper::trancheProjects()], ['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>0],['=','loan_tranches.status',4],['!=','loan_tranches.fund_request_id', 0],['=','fund_requests.status' ,'processed'],['=','loans.deleted' ,0]]);*/
            $loans = [];
        }
        if (isset($request['LoansSearch']['project_id']) && isset($request['LoansSearch']['branch_id'])) {
            $cur_date = date('Y-m-d');
            $first_date = date('Y-m-1');
            $third_date = date('Y-m-3');

            if ($cur_date >= $first_date && $cur_date <= $third_date) {
                $month = date("Y-m", strtotime("last day of previous month"));
            } else {
                $month = date('Y-m');
            }
            $awp_target_amount = Awp::find()->where(['month' => $month, 'branch_id' => $request['LoansSearch']['branch_id'], 'project_id' => $request['LoansSearch']['project_id']])->sum('disbursement_amount');
        }
        if (empty($request)) {
            //Yii::$app->Permission->getSearchFilterQuery($loans, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
            $pages = new Pagination(['totalCount' => 0, 'pageSize' => 0]);
        } else {
            Yii::$app->Permission->getSearchFilterQuery($loans, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $pages = new Pagination(['totalCount' => $loans->count(), 'pageSize' => 50]);
            $loans = $loans->orderBy(['loans.sanction_no' => SORT_ASC])->offset($pages->offset)->limit($pages->pageSize)->all();

        }

        return $this->render('cheque_print', [
            'pages' => $pages,
            'loans' => $loans,
            'branches' => $branches,
            'searchModel' => $searchModel,
            'projects' => $projects,
            'awp_target_amount' => $awp_target_amount
        ]);
    }

    public function actionReadyForFundRequest()
    {
        $request = Yii::$app->request;
        if ($request->post() && isset($request->post()['selection']) && !isset($request->post()['export'])) {
            foreach ($request->post()['selection'] as $id) {
                $tranch = LoanTranches::findOne($id);
                $tranch->status = 4;
                $tranch->save();
                $loan = Loans::find()->where(['id' => $tranch->loan_id, 'deleted' => 0])->one();
                if ($loan->status == 'not collected') {
                    $loan->status = 'pending';
                    $loan->save();
                }
            }
        }

        if ($request->post() && isset($request->post()['selection']) && isset($request->post()['export'])) {
            $this->layout = 'csv';
            $headers = array('member name', 'member cnic', 'parentage', 'tranche amount', 'sanction no', 'group no', 'tranche no', 'bank name', 'account Title', 'account no');
            $member = array();
            $data = $request->post()['selection'];
            $i = 0;
            foreach ($data as $id) {
                $tranch = LoanTranches::findOne($id);
                $member[$i]['member name'] = $tranch->loan->application->member->full_name;
                $member[$i]['member cnic'] = $tranch->loan->application->member->cnic;
                $member[$i]['parentage'] = $tranch->loan->application->member->parentage;
                $member[$i]['tranche amount'] = $tranch->tranch_amount;
                $member[$i]['sanction no'] = $tranch->loan->sanction_no;
                $member[$i]['group no'] = $tranch->loan->group->grp_no;
                $member[$i]['tranche no'] = $tranch->tranch_no;
                $member[$i]['bank name'] = $tranch->loan->application->member->membersAccounts->bank_name;
                $member[$i]['account Title'] = $tranch->loan->application->member->membersAccounts->title;
                $member[$i]['account no'] = "'" . $tranch->loan->application->member->membersAccounts->account_no . "'";
                $i++;
            }
            ExportHelper::ExportCSV('ready_for_fund.csv', $headers, $member);
            die();
        }
        $searchModel = new LoansSearch();
        if (isset(Yii::$app->request->queryParams['LoansSearch']['branch_id']) && !empty(Yii::$app->request->queryParams['LoansSearch']['branch_id'])) {
            $dataProvider = $searchModel->search_ready_for_disbursement_list(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        } else {
            $dataProvider = [];
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('ready-for-fund-request/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }

    public function actionRemoveFundRequest()
    {
        $request = Yii::$app->request;
        if ($request->post() && isset($request->post()['selection'])) {
            foreach ($request->post()['selection'] as $id) {
                $tranch = LoanTranches::findOne($id);
                $tranch->status = 3;
                $tranch->save();
                $loan = Loans::find()->where(['id' => $tranch->loan_id, 'deleted' => 0])->one();
                if ($loan->status == 'not collected') {
                    $loan->status = 'pending';
                    $loan->save();
                }
            }
        }
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->searchRemoveFundRequest(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        return $this->render('remove-fund-request/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }

    /**
     * Updates an existing Loans model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->load($request->post());
        $application = Applications::find()->where(['id' => $model->application_id, 'deleted' => 0])->one();
        $model->validateLoanAmount($application);
        if (!empty($model->getErrors())) {
            $application = Applications::findOne($model->application_id);
            return $this->render('create', [
                'model' => $model,
                'application' => $application,
            ]);
        }
        if ($model->load($request->post())) {
            if (!in_array($model->project_id, StructureHelper::trancheProjects())) {
                $tranch_model = LoanTranches::find()->where(['loan_id' => $model->id])->one();

                if (!empty($tranch_model) && $tranch_model->fund_request_id == 0) {
                    if ($model->save()) {
                        $tranch_model->tranch_no = '1';
                        $tranch_model->loan_id = $model->id;
                        $tranch_model->tranch_amount = $model->loan_amount;
                        $tranch_model->status = 3;
                        $tranch_model->save();
                        return $this->redirect(['lac'/*, 'id' => $model->id*/]);
                    } else {
                        return $this->render('update', [
                            'model' => $model,
                            'application' => $model->application
                        ]);
                    }
                } else {
                    return $this->render('update', [
                        'model' => $model,
                        'application' => $model->application
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'application' => $model->application
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'application' => $model->application
            ]);
        }

    }

    public function actionUpdateChequeNo($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModelTranches($id);
        if (!empty($request->post()['LoanTranches']['cheque_no'] && $request->post()['LoanTranches']['cheque_no'] > 0)) {
            $model->load($request->post());
            $model->cheque_date = strtotime($request->post()['LoanTranches']['cheque_date']);
            $model->status = 4;
            if ($model->save()) {
                ActionsHelper::updateAction('tranche', $model->id, 'cheque_printing');

                $response['status_type'] = "success";
                $response['amount'] = $model->loan->loan_amount;
                $response['data']['message'] = "Saved";
            } else {
                $response['status_type'] = "error";
                $response['errors'] = $model->getErrors();
            }
        } else {
            $response['status_type'] = "error";
            $response['errors'] = array("cheque_no" => "Cheque No can not be blank or zero.");
        }
        return json_encode($response);
    }

    /**
     * Delete an existing Loans model.
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
     * Delete multiple existing Loans model.
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

    /**
     * Finds the Loans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loans::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelTranches($id)
    {
        if (($model = LoanTranches::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDisbursementSummary()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if (isset($params2['export']) && $params2['export'] == 'export') {

            $params = $params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $crop_types = array('rabi' => 'Rabi', 'kharif' => 'Kharif');
            $searchModel = new LoansSearch();
            $searchModel->load($params);
            $dataProvider = LoanHelper::DisbursmentSummary($params);
            $models = $dataProvider->getModels();
            $headers = [];
            if (isset($models[0]) && $models[0] != null) {
                foreach ($models[0] as $key => $headings) {
                    array_push($headers, $key);
                }
            }
            $data = [];
            foreach ($models as $model) {
                if (isset($model['region_name'])) {
                    $array = \common\components\Helpers\StructureHelper::getStructureList('regions', 'id', $model['region_name']);
                    $model['region_name'] = isset($array['0']['name']) ? $array['0']['name'] : 'Not Set';
                }
                if (isset($model['area_name'])) {
                    $array = \common\components\Helpers\StructureHelper::getStructureList('areas', 'id', $model['area_name']);
                    $model['area_name'] = isset($array['0']['name']) ? $array['0']['name'] : 'Not Set';
                }
                if (isset($model['branch_name'])) {
                    $array = \common\components\Helpers\StructureHelper::getStructureList('branches', 'id', $model['branch_name']);
                    $model['branch_name'] = isset($array['0']['code']) ? $array['0']['code'] : 'Not Set';
                }
                $data[] = $model;
            }
            ExportHelper::ExportCSV('Disbursement-Summary-Report.csv', $headers, $data);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $crop_types = array('rabi' => 'Rabi', 'kharif' => 'Kharif');
        $searchModel = new LoansSearch();

        if (empty($params['LoansSearch']['date_disbursed'])) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d');
            $params['LoansSearch']['date_disbursed'] = $from_date/*.' - '.$to_date*/
            ;
            //die("we are here");
        } else {
            if (strpos($params['LoansSearch']['date_disbursed'], ' - ') == false) {
                $params['LoansSearch']['date_disbursed'] = $params['LoansSearch']['date_disbursed']/*.' - '.$params['LoansSearch']['date_disbursed']*/
                ;
            }
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $searchModel->load($params);

        $dataProvider = LoanHelper::DisbursmentSummary($params);

        $total = array();
        $total_loan_amount = 0;
        $total_loans = 0;
        $models = $dataProvider->getModels();

        foreach ($models as $m) {
            $total_loan_amount += $m['loan_amount'];
            $total_loans += $m['no_of_loans'];
        }
        $total['loan_amount'] = $total_loan_amount;
        $total['no_of_loans'] = $total_loans;
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'regions' => $regions, 'areas' => $areas,
            'branches' => $branches, 'projects' => $projects, 'crop_types' => $crop_types, 'total' => $total];
        return $this->render('loan_summary/loan_summary', [
            'data' => $data
        ]);
    }

    public function actionChequewisereport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            if (!isset($params['LoansSearch']['date_disbursed'])) {
                $datedisburse = date('Y-m-d');
                $params['LoansSearch']['date_disbursed'] = date('Y-m-01', strtotime($datedisburse)) . ' - ' . date('Y-m-t', strtotime($datedisburse));
            }
            $headers = [];
            $this->layout = 'csv';
            for ($i = 0; $i < 14; $i++) {
                array_push($headers, array_keys($_GET['LoansSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->searchchequewise($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseChequewiseCsvExportData($data);
            ExportHelper::ExportCSV('Chequewise-Report(' . $params['LoansSearch']['date_disbursed'] . ').csv', $headers, $groups);
            die();
        }
        $searchModel = new LoansSearch();
        $dataProvider = array();
        if (!empty($params)) {
            if (!isset($params['LoansSearch']['date_disbursed'])) {
                $datedisburse = date('Y-m-d');
                $params['LoansSearch']['date_disbursed'] = date('Y-m-01', strtotime($datedisburse)) . ' - ' . date('Y-m-t', strtotime($datedisburse));
            }
            $dataProvider = $searchModel->searchchequewise($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $inst_type = LoanHelper::getInstType();
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'areas' => $areas, 'regions' => $regions,
            'branches' => $branches, 'projects' => $projects, 'inst_type' => $inst_type, 'inst_types' => $inst_type];
        return $this->render('chequewise/chequewise', [
            'data' => $data
        ]);
    }

    public function actionDuelist()
    {
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'pdf') {

            $searchModel = new DuelistSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $new_duelist2 = $query->all();
            $new_duelist = ExportHelper::parseDueListPdfExportData($new_duelist2, $params);

            $cond = [];
            if (empty($searchModel->branch_id)) {
                if (empty($searchModel->area_id)) {
                    if (!empty($searchModel->region_id)) {
                        $cond['column'] = 'd.region_id';
                        $cond['value'] = $searchModel->region_id;
                    }
                } else {
                    $cond['column'] = 'd.area_id';
                    $cond['value'] = $searchModel->area_id;
                }
            } else {
                //die('2');
                $cond['column'] = 'd.branch_id';
                $cond['value'] = $searchModel->branch_id;
            }
            $progress['data'] = ProgressReportHelper::getProgress($cond);
            $progress['header'] = array('Total Loans', 'Male Loans', 'Female Loans', 'Active Loans', 'Cum. Disb', 'Cum. Due', 'Cum. Recv', 'OD Borrowers', 'OD Amount', 'OD Percentage', 'PAR', 'PAR Percentage', 'Not Yet Due', 'OLP Recovery', 'Percentage');
            $file_name = '';

            $new_duelist1 = [];
            foreach ($new_duelist as $data) {

                $id = $data['grpno'];
                if (isset($new_duelist1[$id]) && ($data['grptype'] == 'GRP' || $data['grptype'] == 'IND')) {
                    $new_duelist1[$id][] = $data;
                } else {
                    $new_duelist1[$id] = array($data);
                }
            }
            if (isset($searchModel->branch_id) && !empty($searchModel->branch_id)) {
                $name = Branches::find()->select('name')->where(['id' => $searchModel->branch_id])->asArray()->one();
            } else if (isset($searchModel->area_id) && !empty($searchModel->area_id)) {
                $name = Areas::find()->select('name')->where(['id' => $searchModel->area_id])->asArray()->one();
            } else {
                $name = Regions::find()->select('name')->where(['id' => $searchModel->region_id])->asArray()->one();
            }
            $data = ["new_duelist1" => $new_duelist1, "progress_report_new" => $progress['data'][0]];
            $pdf_heading = 'Duelist of the month ' . date('F-Y', (strtotime($params['DuelistSearch']['report_date']))) . ' of Branch  (' . $name['name'] . ') ' . date('d/m/Y h:i:sa');
            PDFHelper::DueList($data, $pdf_heading, $name);

        }
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            //$headers = [];
            $this->layout = 'csv';
            $headers = array('Sanction No', 'Name', 'Parentage', 'Team Name', 'Disbursement Date', 'Due date', 'Loan Amount', 'Tranche Amount', 'Tranche No', 'Due Amount', 'Recv Amount', 'Balance', 'Group No', 'Address', 'Mobile No');
            $groups = array();
            $searchModel = new DuelistSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();

            $groups = ExportHelper::parseDueListCsvExportData($data, $params['DuelistSearch']['report_date']);
            $cond = [];
            if (empty($searchModel->branch_id)) {
                if (empty($searchModel->area_id)) {
                    if (!empty($searchModel->region_id)) {
                        $cond['column'] = 'd.region_id';
                        $cond['value'] = $searchModel->region_id;
                    }
                } else {
                    $cond['column'] = 'd.area_id';
                    $cond['value'] = $searchModel->area_id;
                }
            } else {
                $cond['column'] = 'd.branch_id';
                $cond['value'] = $searchModel->branch_id;
            }
            $progress['data'] = ProgressReportHelper::getProgress($cond);
            $progress['header'] = array('Total Loans', 'Male Loans', 'Female Loans', 'Active Loans', 'Cum. Disb', 'Cum. Due', 'Cum. Recv', 'OD Borrowers', 'OD Amount', 'OD Percentage', 'PAR', 'PAR Percentage', 'Not Yet Due', 'OLP Recovery', 'Percentage');
            $file_name = '';
            if (isset($searchModel->branch_id)) {
                $file_name = Branches::find()->select('name')->where(['id' => $searchModel->branch_id])->one()['name'];
            }
            ExportHelper::ExportCSV('DueList-Report-' . $file_name . '.csv', $headers, $groups, $progress);
            die();
        }
        $searchModel = new DuelistSearch();
        $dataProvider = array();
        if (!empty($params)) {
            if (!isset($params['DuelistSearch']['report_date']) || empty($params['DuelistSearch']['report_date'])) {
                $report1_date = date('Y-m');
                $params['DuelistSearch']['report_date'] = $report1_date;
            }
            $dataProvider = $searchModel->search(/*Yii::$app->request->queryParams)*/
                $params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        }
        $teams = LoanHelper::getTeams();
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $inst_type = LoanHelper::getInstType();
        $cond = [];
        if (empty($searchModel->branch_id)) {
            //die('1');
            //$cond=[];
            if (empty($searchModel->area_id)) {
                if (!empty($searchModel->region_id)) {
                    $cond['column'] = 'd.region_id';
                    $cond['value'] = $searchModel->region_id;
                }
            } else {
                $cond['column'] = 'd.area_id';
                $cond['value'] = $searchModel->area_id;
            }
        } else {
            $cond['column'] = 'd.branch_id';
            $cond['value'] = $searchModel->branch_id;
        }

        $progress = ProgressReportHelper::getProgress($cond);
        $dsb_status = LoanHelper::getDsbStatus();
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'branches' => $branches, 'regions' => $regions, 'areas' => $areas, 'projects' => $projects,
            'progress' => $progress, 'provinces' => $provinces, 'inst_type' => $inst_type, 'dsb_status' => $dsb_status, 'teams' => $teams];
        return $this->render('duelist/duelist', [
            'data' => $data
        ]);
    }

    public function actionOverdueList()
    {
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            for ($i = 0; $i < 7; $i++) {
                array_push($headers, array_keys($_GET['OverduelistSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new OverduelistSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseOverDueListCsvExportData($data);
            ExportHelper::ExportCSV('OverdueList-Report.csv', $headers, $groups);
            die();
        }
        $searchModel = new OverduelistSearch();
        $dataProvider = array();
        if (!empty($params)) {
            if (!isset($params['OverduelistSearch']['report_date']) || empty($params['OverduelistSearch']['report_date'])) {
                $report1_date = date('Y-m');
                $params['OverduelistSearch']['report_date'] = $report1_date;
            }
            $dataProvider = $searchModel->search($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces,
            'areas' => $areas, 'branches' => $branches, 'regions' => $regions, 'projects' => $projects];
        return $this->render('overduelist/overduelist', [
            'data' => $data,
        ]);
    }

    public function actionOverdueChargesList()
    {
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            for ($i = 0; $i < 7; $i++) {
                array_push($headers, array_keys($_GET['OverduelistSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new OverduelistSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseOverDueListCsvExportData($data);
            ExportHelper::ExportCSV('OverdueList-Report.csv', $headers, $groups);
            die();
        }
        $searchModel = new OverduelistSearch();
        $dataProvider = array();
        if (!empty($params)) {
            if (!isset($params['OverduelistSearch']['report_date']) || empty($params['OverduelistSearch']['report_date'])) {
                $report1_date = date('Y-m');
                $params['OverduelistSearch']['report_date'] = $report1_date;
            }
            $dataProvider = $searchModel->searchCharges($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getHousingProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces,
            'areas' => $areas, 'branches' => $branches, 'regions' => $regions, 'projects' => $projects];
        return $this->render('overduecharges/chargesoverduelist', [
            'data' => $data,
        ]);
    }

    public function actionDuevsrecovery()
    {
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            if (!isset($params['DuelistSearch']['report_date']) || empty($params['DuelistSearch']['report_date'])) {
                $report1_date = date('Y-m');
                $params['DuelistSearch']['report_date'] = $report1_date;
            }
            $headers = [];
            $this->layout = 'csv';
            for ($i = 0; $i < 12; $i++) {
                array_push($headers, array_keys($_GET['DuelistSearch'])[$i]);
            }
            $groups = array();
            $searchModel = new DuelistSearch();
            $query = $searchModel->search_due_vs_recovery($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseDueVsRecCsvExportData($data);
            ExportHelper::ExportCSV('DueVsRecovery-Report.csv', $headers, $groups);
            die();
        }
        $searchModel = new DuelistSearch();
        $dataProvider = array();
        if (!empty($params)) {
            if (!isset($params['DuelistSearch']['report_date']) || empty($params['DuelistSearch']['report_date'])) {
                $report1_date = date('Y-m');
                $params['DuelistSearch']['report_date'] = $report1_date;
            }
            $dataProvider = $searchModel->search_due_vs_recovery($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $teams = LoanHelper::getTeams();
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $teams = LoanHelper::getTeams();
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas, 'branches' => $branches,
            'regions' => $regions, 'projects' => $projects, 'teams' => $teams];
        return $this->render('due_vs_recovery/due_vs_recovery', [
            'data' => $data
        ]);
    }

    public function actionPortfolio()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 15000);
        $params = Yii::$app->request->queryParams;

        if (!isset($params['PortfolioSearch']['report_date']) || $params['PortfolioSearch']['report_date'] == null) {
            $datedisburse = date('Y-m-d');
            $params['PortfolioSearch']['report_date'] = date('Y-m-01', strtotime($datedisburse)) . ' - ' . date('Y-m-t', strtotime($datedisburse));
        }
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            $headers = array("0" => "Region", "1" => "Area", "2" => "Branch", "3" => "Project", "4" => "sanction_no", "5" => "name", "6" => "parentage", "7" => "cnic", "8" => "date_disbursed", "9" => "loan_amount", "10" => "tranch_amount", "11" => "tranch_no", "12" => "grpno", "13" => "gender", "14" => "Loan Expiry", "15" => "cheque_no", "16" => "mobile", "17" => "address", "18" => "Recovery", "19" => "Purpose", '20' => 'status', "21" => "Inst Amount", "22" => "Inst Months", "23" => "Cnic Issue Date", "24" => "Cnic Expiry Date");
            $groups = array();
            $searchModel = new PortfolioSearch();
            $data = $searchModel->search_portfolio($_GET, true);
            $groups = ExportHelper::parsePortfolioCsvExportData($data);
            ExportHelper::ExportCSV('Portfolio-Report.csv', $headers, $groups);
            die();
        }


        $searchModel = new PortfolioSearch();
        $dataProvider = '';
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('portfolio/portfolio', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
    }


    public function actionPortfolioWithFundSource()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;

        if (!isset($params['PortfolioSearch']['report_date']) || $params['PortfolioSearch']['report_date'] == null) {
            $datedisburse = date('Y-m-d');
            $params['PortfolioSearch']['report_date'] = date('Y-m-01', strtotime($datedisburse)) . ' - ' . date('Y-m-t', strtotime($datedisburse));
        }
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            $headers = array("0" => "Region", "1" => "Area", "2" => "Branch", "3" => "Project", "4" => "sanction_no", "5" => "name", "6" => "parentage", "7" => "cnic", "8" => "date_disbursed", "9" => "loan_amount", "10" => "tranch_amount", "11" => "tranch_no", "12" => "grpno", "13" => "gender", "14" => "Loan Expiry", "15" => "cheque_no", "16" => "mobile", "17" => "address", "18" => "Recovery", "19" => "Purpose", '20' => 'status', "21" => "Inst Amount", "22" => "Inst Months", "23" => "funding_source");
            $groups = array();
            $searchModel = new PortfolioSearch();
            $data = $searchModel->search_portfolio_fund_source($_GET, true);
            $groups = ExportHelper::parsePortfolioCsvExportDataKpp($data);
            ExportHelper::ExportCSV('Portfolio-Report.csv', $headers, $groups);
            die();
        }


        $searchModel = new PortfolioSearch();
        $dataProvider = '';
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('portfolio/portfolio_kpp', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
    }

    /**
     * Family Member report.
     */

    public function actionFamilyMemberReport()
    {
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = array("Region", "Area", "Branch", "Project", "Name", "Prentage", "CNIC", "Family Member CNIC", "Amount", "Sanction No", "Disbursement Date");
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->search_family_member_report($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseFamilyMemberCsvExportData($data);
            ExportHelper::ExportCSV('Family-Member-Report.csv', $headers, $groups);
            die();
        }
        $searchModel = new LoansSearch();
        $dataProvider = array();
        if (!empty($params)) {
            $dataProvider = $searchModel->search_family_member_report($params);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('family_member_report/family_member_report', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
    }

    public function actionValidateLoanAmount($id, $amount)
    {

        $model = Applications::find()->where(['id' => $id])->one();
        if (isset($model->appraisalsBusiness->new_required_assets_amount)) {
            if ($model->req_amount < $amount && $model->appraisalsBusiness->new_required_assets_amount < $amount) {
                $response['status_type'] = "failure";
                $response['date'] = "failure";
            } else {
                $response['status_type'] = "success";
                $response['data'] = "success";
            }
        } else {
            if ($model->req_amount < $amount) {

                $response['status_type'] = "failure";
                $response['data'] = "failure";
            } else {
                $response['status_type'] = "success";
                $response['data'] = "success";
            }
        }
        return json_encode($response);

    }

    public function actionPortfolio_lwc()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;
        if (!isset($params['PortfolioSearch']['report_date']) || $params['PortfolioSearch']['report_date'] == null) {

            $datedisburse = date('Y-m-d');
            $params['PortfolioSearch']['report_date'] = date('Y-m-01', strtotime($datedisburse)) . ' - ' . date('Y-m-t', strtotime($datedisburse));
        }

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            if (isset($params['PortfolioSearch']['report_date'])) {
                $date = explode(' - ', $params['PortfolioSearch']['report_date']);

                $date1 = strtotime($date[1]);
                $date2 = strtotime($date[0]);
                $date = time() - $date2;
                if ($date > (90 * 24 * 60 * 60)) {
                    $searchModel = new PortfolioSearch();
                    //$searchModel = RbacHelper::addConditionToSearchModel($searchModel);
                    $dataProvider = $searchModel->search_portfolio_lwc($params);
                    Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

                    $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
                    $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
                    $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
                    $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                    $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
                    $searchModel->addError('report_date', 'Not in Range');

                    return $this->render('portfolio/portfolio', [
                        'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces,
                        'areas' => $areas, 'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
                    ]);
                }
            }
            $headers = [];
            $this->layout = 'csv';
            $headers = array("0" => "Project", "1" => "CNIC", "2" => "Name", "3" => "Parentage", "4" => "Sanction No", "5" => "Gender", "6" => "Marital Status", "7" => "dob", "8" => "Branch", "9" => "Loan Amount", "10" => "Installment Period", "11" => "Date Disbursed");
            $groups = array();
            $searchModel = new PortfolioSearch();
            $query = $searchModel->search_portfolio_lwc($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parsePortfolioLwcCsvExportData($data);
            ExportHelper::ExportCSV('Portfolio-Report-LWC.csv', $headers, $groups);
            die();
        }
        $searchModel = new PortfolioSearch();
        //$searchModel = RbacHelper::addConditionToSearchModel($searchModel);
        $dataProvider = $searchModel->search_portfolio_lwc($params);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('portfolio_lwc/portfolio', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
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
                    'title' => "Log Loan #" . $id,
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

    public function actionGetTranchesDetail($project_id, $loan_amount)
    {
        $project = Projects::find()->select('name')->where(['id' => $project_id])->one();
        //$project->name='New Housing Scheme';
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/tranches/' . 'tranches.json';
        $json = json_decode(file_get_contents($file_path), true);

        $response = [];
        foreach ($json[$project->name] as $j) {
            if ($loan_amount >= $j['min'] && $loan_amount <= $j['max']) {
                $tranch_info = explode(',', $j['percent']);
                foreach ($tranch_info as $info) {
                    $div_width = floor(($info * 11) / 100);
                    $tranch_amount = ($info * $loan_amount) / 100;
                    $response[] = array(
                        "tranch_amount" => $tranch_amount,
                        "percent" => $info,
                        'div_width' => $div_width
                    );
                }
            }
        }
        return json_encode($response);
    }

    public function actionBmApprovalList()
    {
        $params = Yii::$app->request->post();
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $searchModel = new LoansSearch();

        $query = $searchModel->search_bm_list($params);
        Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 50]);
        $loans = $query->offset($pages->offset)->limit($pages->pageSize)->all();

        return $this->render('bm_approval', [
            'pages' => $pages,
            'loans' => $loans,
            'branches' => $branches,
            'searchModel' => $searchModel
        ]);
    }

    public function actionActiveTranch($id)
    {
        $loan_tranche = LoanTranches::find()->where(['loan_id' => $id, 'loan_tranches.deleted' => 0, 'loan_tranches.status' => 0])->one();
        $request = Yii::$app->request->post();
        $response = [];
        if (isset($loan_tranche)) {

            $loan_tranche->tranch_date = strtotime($request['Loans']['cheque_dt']);
            $loan_tranche->start_date = strtotime($request['Loans']['start_date']);
            $loan_tranche->total_expenses = $request['Loans']['total_expenses'];
            $loan_tranche->status = 1;
            $loan_tranche->save();
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        } else {
            $response['status_type'] = "error";
        }
        return json_encode($response);
    }

    public function actionUpdateTranch($id)
    {
        $loans = Loans::find()->where(['id' => $id, 'deleted' => 0])->one();
        return $this->render('am_approval', [
            'loans' => $loans
        ]);
    }

    public function actionAmApproveTranch($id)
    {
        $response = [];
        $tranch = LoanTranches::find()->where(['id' => $id, 'deleted' => 0])->one();
        if (isset($tranch)) {
            $tranch->status = 3;
            if ($tranch->save()) {
                $response['status_type'] = "success";
                $response['data']['message'] = "Updated";
            }
        } else {
            $response['status_type'] = "error";
        }
        return json_encode($response);
    }

    ////takaful save
    public function actionAddTakaf()
    {
        $request = Yii::$app->getRequest();
        $loans_search = new LoansSearch();
        if (Yii::$app->request->get()) {
            $loans = $loans_search->search_takaf_pending_list(Yii::$app->request->get());
            $pages = new Pagination(['totalCount' => $loans->getTotalCount(), 'pageSize' => $loans->getCount()]);
        } else {
            $loans = [];
            $pages = new Pagination(['totalCount' => 0, 'pageSize' => 0]);
        }
        // Yii::$app->Permission->getSearchFilter($loans,'loans',  Yii::$app->controller->action->id,$this->rbac_type);
        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('add-takaf', [
            'loans' => $loans,
            'pagination' => $pages,
            'regions_by_id' => $regions_by_id,
            'loans_search' => $loans_search,
            'branches' => $branches
        ]);
    }

    public function actionAnnualTakaful()
    {
        $request = Yii::$app->getRequest();
        $loansSearch = new TakafulDueSearch();
        if (Yii::$app->request->post()) {
            $response = array();
            $operation = new Operations();
            $operation->load($request->post());
            $operation->receive_date = strtotime($operation->receive_date);
            if ($operation->save()) {
                $takaful_due = Takafuldue::find()->where(['status' => 0])->andWhere(['loan_id' => $operation->loan_id])->one();
                if (!empty($takaful_due) && $takaful_due != null) {

                    $takaful_due->status = 1;
                    $takaful_due->overdue_amnt = 0;
                    $takaful_due->takaf_rec_date = $operation->receive_date;
                    $takaful_due->credit = $operation->credit;
                    $takaful_due->save();
                }
                $model = $loansSearch->search(Yii::$app->request->get());

            } else {
                var_dump($operation->getErrors());
                die();
            }

        } elseif (Yii::$app->request->get()) {
            $model = $loansSearch->search(Yii::$app->request->get());

        } else {
            $model = [];
        }
        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('annual-takaful', [
            'model' => $model,
            'regions_by_id' => $regions_by_id,
            'loans_search' => $loansSearch,
            'branches' => $branches
        ]);

    }

    public function actionSaveTakafLoans($id)
    {
        $request = Yii::$app->request;
        $model = LoanTranches::findOne($id);
        $response = array();
        $operation = new Operations();
        $operation->load($request->post());
        if (isset($request->post()['Operations']['credit']) && !empty($request->post()['Operations']['credit']) && isset($request->post()['Operations']['receipt_no']) && !empty($request->post()['Operations']['receipt_no'])) {
            $operation->receive_date = strtotime($operation->receive_date);
            if ($operation->save()) {
                $loan = Loans::findOne($operation->loan_id);
                ActionsHelper::updateAction('loan', $operation->loan_id, 'takaful');
                $response['status_type'] = "success";
                $response['data']['message'] = "Saved";
            } else {
                $response['status_type'] = "error";
                $response['errors'] = $operation->getErrors();
            }
        } else {
            ActionsHelper::updateAction('loan', $operation->loan_id, 'takaful');
            $response['status_type'] = "success";
            $response['data']['message'] = "Saved";
        }
        return json_encode($response);
    }

    public function actionMegaDisbursementReport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            $headers = ['Region', 'Area', 'Branch', 'Branch Code', 'Sanction No', 'Amount', 'Group No', 'Name', 'CNIC', 'Parentage', 'Gender', 'Religion', 'Dob', 'Education', 'Marital Status', 'Mobile', 'Address', 'Project', 'Product', 'Activity'];
            $groups = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->search_mega_disb_list($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $groups = ExportHelper::parseMegaDisbCsvExportData($data);
            ExportHelper::ExportCSV('Mega-Dis.-Report.csv', $headers, $groups);
            die();
        }

        $searchModel = new LoansSearch();
        //$searchModel = RbacHelper::addConditionToSearchModel($searchModel);
        $dataProvider = $searchModel->search_mega_disb_list($params);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('mega_disb/mega_disb', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
    }

    public function actionReferralReport()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Region', 'Area', 'Branch', 'Project', 'Application No', 'Loan Amount', 'Disbursed Date', 'Referred By']);
            $referral_report = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->searchReferralReport($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $referral_report[$i]['full_name'] = isset($g['application']['member']['full_name']) ? $g['application']['member']['full_name'] : '';
                $referral_report[$i]['parentage'] = isset($g['application']['member']['parentage']) ? $g['application']['member']['parentage'] : '';
                $referral_report[$i]['cnic'] = isset($g['application']['member']['cnic']) ? $g['application']['member']['cnic'] : '';
                $referral_report[$i]['region_name'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $referral_report[$i]['area_name'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $referral_report[$i]['branch_name'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $referral_report[$i]['project_name'] = isset($g['project']['name']) ? $g['project']['name'] : '';
                $referral_report[$i]['application_no'] = isset($g['application']['application_no']) ? $g['application']['application_no'] : '';
                $referral_report[$i]['loan_amount'] = isset($g['loan_amount']) ? $g['loan_amount'] : '';
                $referral_report[$i]['date_disbursed'] = date("d-M-y", isset($g['date_disbursed']) ? $g['date_disbursed'] : '');
                $referral_report[$i]['referral_id'] = isset($g['application']['referral']['name']) ? $g['application']['referral']['name'] : '';

                $i++;
            }
            ExportHelper::ExportCSV('Referral Report', $headers, $referral_report);
            die();
        }
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->searchReferralReport(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('referral_report/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }

    public function actionWriteoff()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = (['Name', 'Parentage', 'CNIC', 'Sanction No', 'Region', 'Area', 'Branch', 'Project', 'Purpose', 'Disbursed Date', 'Loan Amount', 'Mobile', 'Write Off Amount', 'Write Off Date', 'Write Off By']);
            $write_off = array();
            $searchModel = new LoansSearch();
            $query = $searchModel->searchwriteoff($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $write_off[$i]['member_name'] = ($g['member_name']) ? $g['member_name'] : '';
                $write_off[$i]['member_parentage'] = ($g['member_parentage']) ? $g['member_parentage'] : '';
                $write_off[$i]['member_cnic'] = ($g['member_cnic']) ? $g['member_cnic'] : '';
                $write_off[$i]['sanction_no'] = isset($g['sanction_no']) ? $g['sanction_no'] : '';
                $write_off[$i]['region'] = ($g['region']) ? $g['region'] : '';
                $write_off[$i]['area_name'] = ($g['area_name']) ? $g['area_name'] : '';
                $write_off[$i]['branch_name'] = ($g['branch_name']) ? $g['branch_name'] : '';
                $write_off[$i]['project_name'] = ($g['project_name']) ? $g['project_name'] : '';
                $write_off[$i]['activity_name'] = ($g['activity_name']) ? $g['activity_name'] : '';
                $write_off[$i]['date_disbursed'] = date("d-M-y", isset($g['date_disbursed']) ? $g['date_disbursed'] : '');
                $write_off[$i]['loan_amount'] = isset($g['loan_amount']) ? $g['loan_amount'] : '';
                $write_off[$i]['mobile'] = ($g['mobile']) ? $g['mobile'] : '';
                $write_off[$i]['write_off_amount'] = ($g['write_off_amount']) ? $g['write_off_amount'] : '';
                $write_off[$i]['write_off_by'] = ($g['write_off_by']) ? $g['write_off_by'] : '';
                $write_off[$i]['write_off_date'] = date("d-M-y", ($g['write_off_date']) ? $g['write_off_date'] : '');
                $i++;
            }
            ExportHelper::ExportCSV('write_off', $headers, $write_off);
            die();
        }
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->searchwriteoff(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $activities = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        return $this->render('write_off/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects' => $projects,
            'regions' => $regions,
            'activities' => $activities,
        ]);
    }

    public function actionHousingSearch()
    {
        $loans = [];
        $applications = [];
        $types = array('sanction_no' => ' Sanction No', 'borrower_cnic' => ' CNIC');
        if (!empty(Yii::$app->request->queryParams)) {
            $params = Yii::$app->request->queryParams;
            $searchModel = new GlobalsSearch();
            if (isset($params['GlobalsSearch']['borrower_cnic'])) {
                $searchModel->borrower_cnic = $params['GlobalsSearch']['borrower_cnic'];
                if (isset($params['GlobalsSearch']['borrower_cnic'])) {
                    $applications = Applications::find()->innerJoin('members', 'members.id=applications.member_id')->where(['members.cnic' => $params['GlobalsSearch']['borrower_cnic']])->all();
                } elseif (isset($params['GlobalsSearch']['sanction_no'])) {
                    $loans = Loans::find()->where(['sanction_no' => $params['GlobalsSearch']['sanction_no']])->one();
                }
                return $this->render('cnic_search/housing_search', [
                    'searchModel' => $searchModel,
                    'loans' => $loans,
                    'applications' => $applications,
                ]);
            } else {
                $searchModel = new GlobalsSearch();
                return $this->render('cnic_search/housing_search', [
                    'searchModel' => $searchModel,
                    'loans' => $loans,
                    'applications' => $applications,

                ]);
            }
        } else {
            $searchModel = new GlobalsSearch();
            return $this->render('cnic_search/housing_search', [
                'searchModel' => $searchModel,
                'loans' => $loans,
                'applications' => $applications,
            ]);
        }
    }

    public function actionViga()
    {
        $request = Yii::$app->request;
        if ($request->post() && isset($request->post()['selection'])) {
            foreach ($request->post()['selection'] as $id) {
                $model = new VigaLoans();
                $model->loan_id = $id;
                if ($model->save()) {
                } else {
                    $errors = $model->errors;
                }
            }
        }
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->searchVigaLoan(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(), 'id', 'name');
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        $purpose = ArrayHelper::map(StructureHelper::getProducts(), 'id', 'name');
        return $this->render('viga/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches' => $branches,
            'provinces' => $provinces,
            'cities' => $cities,
            'purpose' => $purpose,
        ]);
    }

    public function actionSearchEmergency()
    {
        $types = array('sanction_no' => ' Sanction No', 'borrower_cnic' => ' CNIC', 'grpno' => ' Group No');
        if (!empty(Yii::$app->request->queryParams)) {
            $params = Yii::$app->request->queryParams;
            if (isset($params['GlobalsSearch']['sanction_no']) || isset($params['GlobalsSearch']['borrower_cnic']) || isset($params['GlobalsSearch']['grpno'])) {
                $searchModel = new GlobalsSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                return $this->render('emergency/view', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'types' => $types
                ]);
            } else {
                $searchModel = new GlobalsSearch();
                return $this->render('emergency/view', [
                    'searchModel' => $searchModel,
                    'types' => $types
                ]);
            }
        } else {
            $searchModel = new GlobalsSearch();
            return $this->render('emergency/view', [
                'searchModel' => $searchModel,
                'types' => $types
            ]);
        }
        //return $this->render('welcome');

        //return $this->render('welcome');
//       return $this->redirect(['/members/index']);
    }

    public function actionAddEmergency($id)
    {
        $loan = Loans::find()->where(['id' => $id])->one();

        $exist = EmergencyLoans::find()->where(['member_id' => $loan->application->member_id])->one();
        if (empty($exist)) {
            $emer_model = new EmergencyLoans();
            $emer_model->loan_id = $id;
            $emer_model->member_id = $loan->application->member_id;
            $emer_model->city_id = $loan->branch->city_id;
            $emer_model->save();
        }
        $response['status_type'] = "success";
        $response['id'] = $id;
        $response['data']['message'] = "Saved";
        return json_encode($response);
    }

    public function actionCredit()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $params = Yii::$app->request->queryParams;

        /* if (!isset($params['CreditSearch']['application_date']) || $params['CreditSearch']['application_date'] == null) {
             $application_date = date('Y-m-d');
             $params['CreditSearch']['application_date_'] = date('Y-m-01', strtotime($application_date)) . ' - ' . date('Y-m-t', strtotime($application_date));
         }*/
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            $headers = array("0" => "Region", "1" => "Area", "2" => "Branch", "3" => "Application Date", "4" => "Application No.", "5" => "Name of Applicant", "6" => "CNIC No.", "7" => "Applicant Status", "8" => "Disbuesement Date if approved", "9" => "Sanction No. If approved", "10" => "Group No.", "11" => "Cheque No.", "12" => "Applicant New or Repeat", "13" => "Project");
            $groups = array();
            $searchModel = new CreditSearch();
            $data = $searchModel->search($_GET, true);
            // echo '<pre>';print_r($data);die();
            $groups = ExportHelper::parseCreditCsvExportData($data);
            ExportHelper::ExportCSV('Credit MIS Report', $headers, $groups);
            die();
        }


        $searchModel = new CreditSearch();
        $dataProvider = '';
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('credit_report/index', [
            'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'provinces' => $provinces, 'areas' => $areas,
            'branches' => $branches, 'regions' => $regions, 'projects' => $projects,
        ]);
    }
}
