<?php

namespace frontend\controllers;
use common\components\Helpers\AccountsReportHelper;
use common\components\Helpers\ExportHelper;
use common\components\RbacHelper;
use common\components\Helpers\ProgressReportHelper;
use common\components\Helpers\StructureHelper;
use common\models\ArcAccountReports;
use common\models\Districts;
use common\models\DynamicReports;
use common\models\ReportDefinations;
use common\models\search\ArcAccountReportDetailsSearch;
use common\models\search\DynamicReportsSearch;
use common\models\search\ProgressReportDetailsSearch;
use Yii;
use common\models\ProgressReports;
use common\models\search\ProgressReportsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * ProgressReportsController implements the CRUD actions for ProgressReports model.
 */
class AccountReportsController extends Controller
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
     * Lists all ProgressReports models.
     * @return mixed
     */
    public function actionDisbursementreportoverall()
    {
        $params = Yii::$app->request->post();
        /*print_r($params);
        die();*/
        
        $project_id = 0;
        $account_report_dates =array();
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        $code = 'disb';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);
        /*$account_report_dates_array = array();
        $account_report_dates_array = ArrayHelper::map($account_reports, 'id','report_date');
        foreach ($account_reports as $a){
            $date = date('Y-m-d', strtotime($a->report_date));
            $account_report_dates[$a->id] = date('M j, Y', strtotime($a->report_date));

        }*/
        $searchAccountReport = new ArcAccountReportDetailsSearch();
       /* if(!isset($params['ArcAccountReportDetailsSearch']['arc_account_report_id']) || empty($params['ArcAccountReportDetailsSearch']['arc_account_report_id'])){
            $params['ArcAccountReportDetailsSearch']['arc_account_report_id'] = key($account_report_dates);
        }*/
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'disb';
        $account_report_data = $searchAccountReport->search($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;
        $account_report = AccountsReportHelper::parse_json_account_reports($new_account_report_data);

        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        /*$account_report_date = isset($account_report_dates_array[$searchAccountReport->arc_account_report_id])?$account_report_dates_array[$searchAccountReport->arc_account_report_id]:0;
        $account_report_project = ($project_id != 0) ? $projects[$project_id] : 'overall';*/

        return $this->render('disbursement_summary_all', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            //'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Disbursement Summary Report',
        ]);
    }

    public function actionFundRequestReport()
    {
        $params = Yii::$app->request->post();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $fund_request_report_data = $searchAccountReport->searchFundrequestReport($params);
        //Yii::$app->Permission->searchFundrequestReport($params['controller'],$params['method'],$params['rbac_type']);

        $new_fund_request_report_data = array();
        $new_fund_request_report_data = $fund_request_report_data;
        $fundRequest_report = AccountsReportHelper::parse_json_fundRequest_reports($new_fund_request_report_data);
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        return $this->render('fund_request_report/index', [
            'searchModel' => $searchAccountReport,
            'fundRequest_report' => $fundRequest_report,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Fund Request Report',
        ]);
    }

    public function actionDonationReport()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }

        $code = 'don';
        $progress_reports = ProgressReportHelper::getProgressReports($project_id);
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);
        $account_report_dates_array = array();
        $account_report_dates_array = ArrayHelper::map($account_reports, 'id','report_date');
        foreach ($account_reports as $a){
            $date = date('Y-m-d', strtotime($a->report_date));
            $account_report_dates[$a->id] = date('M j, Y', strtotime($a->report_date));

        }
        foreach ($progress_reports as $p){
            $date = date('Y-m-d', strtotime($p->report_date));
            $progress_report_dates[$p->id] = date('M j, Y', strtotime($p->report_date));

        }
        $searchAccountReport = new ArcAccountReportDetailsSearch();
        if(!isset($params['ArcAccountReportDetailsSearch']['arc_account_report_id']) || empty($params['ArcAccountReportDetailsSearch']['arc_account_report_id'])){
            $params['ArcAccountReportDetailsSearch']['arc_account_report_id'] = key($account_report_dates);
        }
        $params['ArcAccountReportDetailsSearch']['code'] = 'disb';
        $account_report_data = $searchAccountReport->search($params);

        $searchProgress = new ProgressReportDetailsSearch();
        if(!isset($params['ProgressReportDetailsSearch']['progress_report_id']) || empty($params['ProgressReportDetailsSearch']['progress_report_id'])){
            $params['ProgressReportDetailsSearch']['progress_report_id'] = key($progress_report_dates);
        }
        $progress_data = $searchProgress->search($params);

        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports($new_account_report_data);

        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        $account_report_date = isset($account_report_dates_array[$searchAccountReport->arc_account_report_id])?$account_report_dates_array[$searchAccountReport->arc_account_report_id]:0;
        $account_report_project = ($project_id != 0) ? $projects[$project_id] : 'overall';

        return $this->render('donation_report', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Donation Report as on '.date('d-M-Y',$account_report_date).'('.$account_report_project.')',
        ]);
    }

    public function actionRecoveryreportoverall()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        $code = 'recv';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);

        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'recv';
        $account_report_data = $searchAccountReport->search($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports($new_account_report_data);
        //$projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('recovery_summary_all', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Recovery Summary Report',
        ]);
    }

    public function actionDonationreportoverall()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        $code = 'don';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);

        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'don';
        $account_report_data = $searchAccountReport->search_mdp_per_borrower($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports_donation($new_account_report_data);
        //$projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('donation_summary_all', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Donation Report/Borrower',
        ]);
    }
    public function actionDonationreportoverallcommulative()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        $code = 'don';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);

        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'don';
        $account_report_data = $searchAccountReport->search($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports_donation($new_account_report_data);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('donation_summary_all_commulative', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Donation Report Commulative',
        ]);
    }
    public function actionDynamicReports()
    {
        $searchModel = new DynamicReportsSearch();
        $searchModel->report_defination_id = [9,10];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('dynamic_monthly_progress/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionMonthlyProgressDynamic()
    {
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $reports_list = AccountsReportHelper::getReportList(Yii::$app->user->identity->role->item_name);
        $searchModel = new ArcAccountReportDetailsSearch();
        $data=[];
        $request = Yii::$app->request;
        $model = new DynamicReports();
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new DynamicReports",
                    'content' => $this->renderAjax('dynamic_monthly_progress/_create_form', [
                        'model' => $searchModel,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($request->isPost) {

                $filters['ArcAccountReportDetailsSearch'] = $request->post()['ArcAccountReportDetailsSearch'];
                $model = new DynamicReports();
                $model->report_defination_id = (int)$params['ArcAccountReportDetailsSearch']['report_defination_id'];
                $model->sql_filters = serialize($filters);
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $model->region_id = isset($params['ArcAccountReportDetailsSearch']['region_id'])?$params['ArcAccountReportDetailsSearch']['region_id']:0;
                $model->area_id = isset($params['ArcAccountReportDetailsSearch']['area_id'])?$params['ArcAccountReportDetailsSearch']['area_id']:0;
                $model->branch_id = isset($params['ArcAccountReportDetailsSearch']['branch_id'])?$params['ArcAccountReportDetailsSearch']['branch_id']:0;
                $model->report_date=$params['ArcAccountReportDetailsSearch']['from_date'].' - '.$params['ArcAccountReportDetailsSearch']['to_date'];
                $model->save();
                if ($model->save()) {
                    return $this->redirect(['dynamic-reports']);
                } else {
                    return [
                        'title' => "Create new DynamicReports",
                        'content' => $this->renderAjax('dynamic_monthly_progress/_create_form', [
                            'model' => $searchModel,
                            'reports_list' => $reports_list,
                            'regions' => $regions,
                            'projects' => $projects,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            }
        }else {
            if ($request->isPost) {
                /*echo'<pre>';
                print_r($request->post());
                die();*/
                $filters['ArcAccountReportDetailsSearch'] = $request->post()['ArcAccountReportDetailsSearch'];


                $model = new DynamicReports();
                $model->report_defination_id = (int)$params['ArcAccountReportDetailsSearch']['report_defination_id'];
                $model->sql_filters = serialize($filters);
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $model->region_id = isset($params['ArcAccountReportDetailsSearch']['region_id'])?$params['ArcAccountReportDetailsSearch']['region_id']:0;
                $model->area_id = isset($params['ArcAccountReportDetailsSearch']['area_id'])?$params['ArcAccountReportDetailsSearch']['area_id']:0;
                $model->branch_id = isset($params['ArcAccountReportDetailsSearch']['branch_id'])?$params['ArcAccountReportDetailsSearch']['branch_id']:0;
                $model->report_date=$params['ArcAccountReportDetailsSearch']['from_date'].' - '.$params['ArcAccountReportDetailsSearch']['to_date'];
                $model->save();
                /*echo'<pre>';
                print_r($model->getErrors());
                die();*/
                if ($model->save()) {
                    return $this->redirect(['dynamic-reports']);
                } else {
                    return $this->render('dynamic_monthly_progress/_create_form', [
                        'model' => $searchModel,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]);
                }
            } else {
                return $this->render('dynamic_monthly_progress/_create_form', [
                    'model' => $searchModel,
                    'reports_list' => $reports_list,
                    'regions' => $regions,
                    'projects' => $projects,
                ]);
            }
        }
    }
    public function actionMonthlyProgress()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if(isset($params['export'])&&$params['export']=='export'){

            //$params=$params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'disb';

            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $data = AccountsReportHelper::ProgressSummary($params,true);

            $headers=[ 'Region','Area','Branch Code', 'Branch Name', 'Recovery Amount','Disbursement Amount','OLP'];
            /*if(isset($data) && $data !=null){
                foreach ($data as $key=>$headings){
                    array_push($headers,$key);
                }
            }*/
            ExportHelper::ExportCSV('Monthly-Progress-Report.csv',$headers,$data);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        $searchModel = new ArcAccountReportDetailsSearch();
        $data=[];
        /*if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
         }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $searchModel->load($params);*/


        $data = AccountsReportHelper::ProgressSummary($params);
        return $this->render('monthly_progress/monthly_progress', [
            'searchModel' => $searchModel,
            'data' => $data,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' =>$projects,
        ]);
    }

    public function actionMonthlyProgressDetails()
    {
        $arr = [];


        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if(isset($params['export'])&&$params['export']=='export'){

            //$params=$params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'disb';

            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $data = AccountsReportHelper::ProgressSummaryDetails($params,true);
            foreach ($data as $d) {
                foreach ($d as $k => $val) {
                    if($k == 'disb')
                    {
                        $array = explode('+', $val);
                        $a['disb_amount'] = isset($array[0]) ? ($array[0]) : 0;
                        $a['disb_loans'] = isset($array[1]) ? ($array[1]) : 0;
                    } else if ($k == 'opening') {
                        $array = explode('+', $val);
                        $a['opening_olp']  = isset($array[0]) ? ($array[0]) : 0;
                        $a['opening_active_loans']  = isset($array[1]) ? ($array[1]) : 0;
                    } else if ($k == 'closing') {
                        $array = explode('+', $val);
                        $a['closing_olp']  = isset($array[0]) ? ($array[0]) : 0;
                        $a['closing_active_loans']  = isset($array[1]) ? ($array[1]) : 0;
                    } else {
                        $a[$k] = $val;
                    }

                }
                $arr[] = $a;
            }
            $progress = ExportHelper::parseProgressReportExportData($arr);
            $headers=['Region','Area','Branch Code', 'Branch Name', 'Opening Active Loans','Opening OLP','No. of loans disbursed','Disbursement Amount','Recovery Amount','Closing Active Loans','Closing OLP'];
            /*if(isset($data) && $data !=null){
                foreach ($data as $key=>$headings){
                    array_push($headers,$key);
                }
            }*/
            ExportHelper::ExportCSV('Monthly-Detail-Progress-Report.csv',$headers,$progress);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $searchModel = new ArcAccountReportDetailsSearch();

        /*if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $searchModel->load($params);


        $data = AccountsReportHelper::ProgressSummaryDetails($params);*/
        $data=[];

        return $this->render('monthly_progress_details/monthly_progress', [
            'searchModel' => $searchModel,
            'data' => $data,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' =>$projects,
        ]);
    }

    public function actionDisbursementSummary()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if(isset($params2['export'])&&$params2['export']=='export'){

            $params=$params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'disb';

            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $dataProvider = AccountsReportHelper::Summary($params);
            $models = $dataProvider->getModels();
            $headers=[];
            if(isset($models[0]) && $models[0]!=null){
                foreach ($models[0] as $key=>$headings){
                    array_push($headers,$key);
                }
            }
            ExportHelper::ExportCSV('Disbursement-Summary-Report.csv',$headers,$models);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $searchModel = new ArcAccountReportDetailsSearch();

        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['code'] = 'disb';
        $searchModel->load($params);
        /*print_r($params);
        die();*/

        $dataProvider = AccountsReportHelper::Summary($params);

        $total =array();
        $total_loan_amount =  0;
        $total_loans =  0;
        $models = $dataProvider->getModels();

        foreach ($models as $m){
            $total_loan_amount += $m['amount'];
            $total_loans += $m['no_of_loans'];
        }
        $total['amount'] = $total_loan_amount;
        $total['no_of_loans'] = $total_loans;
        return $this->render('loan_summary/loan_summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' =>$projects,
            //'projects' => $projects,
            'total' => $total,
        ]);
    }

    public function actionRecoverySummary()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if (isset($params2['export']) && $params2['export'] == 'export') {
            $params=$params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'recv';
            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $dataProvider = AccountsReportHelper::Summary($params);
            $models = $dataProvider->getModels();
            $headers = [];
            if (isset($models[0]) && $models[0] != null) {
                foreach ($models[0] as $key => $headings) {
                    array_push($headers, $key);
                }
            }
            ExportHelper::ExportCSV('Recovery-Summary-Report.csv', $headers, $models);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $searchModel = new ArcAccountReportDetailsSearch();

        if (empty($params['ArcAccountReportDetailsSearch']['report_date'])) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01', strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date . ' - ' . $to_date;
            //die("we are here");
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['code'] = 'recv';
        $searchModel->load($params);
        /*print_r($params);
        die();*/

        $dataProvider = AccountsReportHelper::Summary($params);

        $total = array();
        $total_loan_amount = 0;
        $total_loans = 0;
        $models = $dataProvider->getModels();

        foreach ($models as $m) {
            $total_loan_amount += $m['amount'];
            $total_loans += $m['no_of_loans'];
        }
        $total['amount'] = $total_loan_amount;
        $total['no_of_loans'] = $total_loans;
        return $this->render('recovery_summary/recovery_summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
            //'projects' => $projects,
            'total' => $total,
        ]);
    }

    public function actionDonationSummary()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if (isset($params2['export']) && $params2['export'] == 'export') {

            $params = $params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'don';
            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $dataProvider = AccountsReportHelper::Summary($params);
            $models = $dataProvider->getModels();
            $headers = [];
            if (isset($models[0]) && $models[0] != null) {
                foreach ($models[0] as $key => $headings) {
                    array_push($headers, $key);
                }
            }
            ExportHelper::ExportCSV('Donation-Summary-Report.csv', $headers, $models);
            die();

        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        $searchModel = new ArcAccountReportDetailsSearch();

        if (empty($params['ArcAccountReportDetailsSearch']['report_date'])) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01', strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date . ' - ' . $to_date;
            //die("we are here");
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['code'] = 'don';
        $searchModel->load($params);
        /*print_r($params);
        die();*/

        $dataProvider = AccountsReportHelper::Summary($params);

        $total = array();
        $total_loan_amount = 0;
        $total_loans = 0;
        $models = $dataProvider->getModels();

        foreach ($models as $m) {
            $total_loan_amount += $m['amount'];
            $total_loans += $m['no_of_loans'];
        }
        $total['amount'] = $total_loan_amount;
        $total['no_of_loans'] = $total_loans;
        return $this->render('donation_summary/donation_summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
            //'projects' => $projects,
            'total' => $total,
        ]);
    }

    public function actionApplicationReport()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        $code = 'app_disb';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);

        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'app_disb';
        $account_report_data = $searchAccountReport->search($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports($new_account_report_data);

        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('application_report', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Application Report',
        ]);
    }

    public function actionApplicationDetailsReport()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $account_report_dates =array();
        if(isset($params['ArcAccountReportDetailsSearch']['project_id'])){
            $project_id = $params['ArcAccountReportDetailsSearch']['project_id'];
        }
        if(empty($params['ArcAccountReportDetailsSearch']['report_date'])){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-01',strtotime($to_date));
            $params['ArcAccountReportDetailsSearch']['report_date'] = $from_date.' - '.$to_date;
        }
        $code = 'app_disb';
        $account_reports = AccountsReportHelper::getAccountReports($project_id,$code);

        $searchAccountReport = new ArcAccountReportDetailsSearch();
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['ArcAccountReportDetailsSearch']['code'] = 'app_disb';
        $account_report_data = $searchAccountReport->search($params);


        $new_account_report_data = array();
        foreach ($account_report_data as $a){

            $district = Districts::find()->where(['id'=>$a['district_id']])->asArray()->one();
            $a['district_id'] = $district['name'];
            $new_account_report_data[] = $a;
        }
        $new_account_report_data=$account_report_data;

        $account_report = AccountsReportHelper::parse_json_account_reports($new_account_report_data);

        $projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);


        return $this->render('application_disbursement_report', [
            'searchModel' => $searchAccountReport,
            'account_report' => $account_report,
            'account_report_dates' => $account_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Application Disbursement Report',
        ]);
    }
    /**
     * Finds the ProgressReports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgressReports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArcAccountReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
