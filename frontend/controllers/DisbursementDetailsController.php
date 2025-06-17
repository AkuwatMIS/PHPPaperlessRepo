<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\models\Funds;
use common\models\LoanTranches;
use common\models\ProjectFundDetail;
use Yii;
use common\models\DisbursementDetails;
use common\models\search\DisbursementDetailsSearch;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * DisbursementDetailsController implements the CRUD actions for DisbursementDetails model.
 */
class DisbursementDetailsController extends Controller
{
    /**
     * @inheritdoc
     *
     *
     */
    public $rbac_type = 'frontend';

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
     * Lists all DisbursementDetails models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','CNIC','Payment Method','Region','Area','Branch','Project','Tranch','Batch','Bank_name','Name','DOB','Account Title','Account No','Tranche Amount','Date Disbursed' ,'Date Published','Status','Response Description','activity','pmt status']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $disb_detail[$i]['sanction_no'] =isset($g['tranch']['loan']['sanction_no'])?$g['tranch']['loan']['sanction_no']:'';
                $disb_detail[$i]['cnic'] =isset($g['tranch']['loan']['application']['member']['cnic'])?$g['tranch']['loan']['application']['member']['cnic']:'';
                $disb_detail[$i]['payment_method'] =isset($g['payment']['name'])?$g['payment']['name']:'';
                $disb_detail[$i]['region_id'] = isset($g['tranch']['loan']['region']['name'])?$g['tranch']['loan']['region']['name']:'';
                $disb_detail[$i]['area_id'] = isset($g['tranch']['loan']['area']['name'])?$g['tranch']['loan']['area']['name']:'';
                $disb_detail[$i]['branch_id'] = isset($g['tranch']['loan']['branch']['name'])?$g['tranch']['loan']['branch']['name']:'';
                $disb_detail[$i]['project_id'] = isset($g['tranch']['loan']['project']['name'])?$g['tranch']['loan']['project']['name']:'';
                $disb_detail[$i]['tranch_id'] = isset($g['tranch']['tranch_no'])?$g['tranch']['tranch_no']:'';
                $disb_detail[$i]['batch_id'] = isset($g['tranch']['batch']['batch_no'])?$g['tranch']['batch']['batch_no']:'';
                $disb_detail[$i]['bank_name'] = isset($g['bank_name'])?$g['bank_name']:'';
                $disb_detail[$i]['name'] = isset($g['tranch']['loan']['application']['member']['full_name'])?$g['tranch']['loan']['application']['member']['full_name']:'';
                $disb_detail[$i]['dob'] = isset($g['tranch']['loan']['application']['member']['dob'])? date("d-M-y",$g['tranch']['loan']['application']['member']['dob']):'';
                $disb_detail[$i]['title'] = isset($g['tranch']['loan']['application']['member']['verifiedAccount1']['title'])?$g['tranch']['loan']['application']['member']['verifiedAccount1']['title']:'';
                $disb_detail[$i]['account_no'] = ($g['account_no'])?"'".$g['account_no']."'":'';
                $disb_detail[$i]['transferred_amount'] = isset($g['transferred_amount'])?number_format($g['transferred_amount']):'';
                $disb_detail[$i]['date_disbursed'] = ($g['tranch']['date_disbursed'] > 0)?  date("d-M-y",$g['tranch']['date_disbursed']):'';
                $disb_detail[$i]['created_at'] = ($g['tranch']['created_at'] > 0)?  date("d-M-y",$g['created_at']):'';
                $disb_detail[$i]['status'] = \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($g['status']);
                $disb_detail[$i]['response_description'] = ($g['response_description'])?$g['response_description']:'';
                $disb_detail[$i]['activity_id'] = isset($g['tranch']['loan']['activity_id'])?\common\components\Helpers\ListHelper::getActivityById($g['tranch']['loan']['activity_id'])->name:'';
                $disb_detail[$i]['pmt'] = isset($g['tranch']['loan']['application']['pmtStatus']['poverty_score'])?$g['tranch']['loan']['application']['pmtStatus']['poverty_score']:0;
                $i++;
            }

            ExportHelper::ExportCSV('Disbursement Detail',$headers,$disb_detail);
            die();
        }
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id'])) /*&& !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['status'])*/){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = [];
        }
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'regions' => $regions
        ]);
    }

    public function actionIndexPmyp()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','CNIC','Payment Method','Region','Area','Branch','Project','Tranch','Batch','Bank_name','Name','Account Title','Account No','Tranche Amount','Date Disbursed' ,'Date Published','Status','Response Description','activity','pmt status','gender','age','district_id','province_id']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $disb_detail[$i]['sanction_no'] =isset($g['tranch']['loan']['sanction_no'])?$g['tranch']['loan']['sanction_no']:'';
                $disb_detail[$i]['cnic'] =isset($g['tranch']['loan']['application']['member']['cnic'])?$g['tranch']['loan']['application']['member']['cnic']:'';
                $disb_detail[$i]['payment_method'] =isset($g['payment']['name'])?$g['payment']['name']:'';
                $disb_detail[$i]['region_id'] = isset($g['tranch']['loan']['region']['name'])?$g['tranch']['loan']['region']['name']:'';
                $disb_detail[$i]['area_id'] = isset($g['tranch']['loan']['area']['name'])?$g['tranch']['loan']['area']['name']:'';
                $disb_detail[$i]['branch_id'] = isset($g['tranch']['loan']['branch']['name'])?$g['tranch']['loan']['branch']['name']:'';
                $disb_detail[$i]['project_id'] = isset($g['tranch']['loan']['project']['name'])?$g['tranch']['loan']['project']['name']:'';
                $disb_detail[$i]['tranch_id'] = isset($g['tranch']['tranch_no'])?$g['tranch']['tranch_no']:'';
                $disb_detail[$i]['batch_id'] = isset($g['tranch']['batch']['batch_no'])?$g['tranch']['batch']['batch_no']:'';
                $disb_detail[$i]['bank_name'] = isset($g['bank_name'])?$g['bank_name']:'';
                $disb_detail[$i]['name'] = isset($g['tranch']['loan']['application']['member']['full_name'])?$g['tranch']['loan']['application']['member']['full_name']:'';
                $disb_detail[$i]['title'] = isset($g['tranch']['loan']['application']['member']['verifiedAccount1']['title'])?$g['tranch']['loan']['application']['member']['verifiedAccount1']['title']:'';
                $disb_detail[$i]['account_no'] = ($g['account_no'])?"'".$g['account_no']."'":'';
                $disb_detail[$i]['transferred_amount'] = isset($g['transferred_amount'])?number_format($g['transferred_amount']):'';
                $disb_detail[$i]['date_disbursed'] = ($g['tranch']['date_disbursed'] > 0)?  date("d-M-y",$g['tranch']['date_disbursed']):'';
                $disb_detail[$i]['created_at'] = ($g['tranch']['created_at'] > 0)?  date("d-M-y",$g['created_at']):'';
                $disb_detail[$i]['status'] = \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($g['status']);
                $disb_detail[$i]['response_description'] = ($g['response_description'])?$g['response_description']:'';
                $disb_detail[$i]['activity_id'] = isset($g['tranch']['loan']['activity_id'])?\common\components\Helpers\ListHelper::getActivityById($g['tranch']['loan']['activity_id'])->name:'';
                $disb_detail[$i]['pmt'] = isset($g['tranch']['loan']['application']['pmtStatus']['poverty_score'])?$g['tranch']['loan']['application']['pmtStatus']['poverty_score']:0;
                $disb_detail[$i]['gender'] = $g->tranch->loan->application->member->gender;
                $disb_detail[$i]['age'] = date('Y-m-d',$g->tranch->loan->application->member->dob);
                $disb_detail[$i]['district_id'] = $g->tranch->loan->application->branch->district->name;
                $disb_detail[$i]['province_id'] = $g->tranch->loan->application->branch->province->name;

                $i++;
            }

            ExportHelper::ExportCSV('Disbursement Detail',$headers,$disb_detail);
            die();
        }
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id'])) /*&& !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['status'])*/){
            $dataProvider = $searchModel->searchPmyp(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = [];
        }
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('index-pmyp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'regions' => $regions
        ]);
    }

    public function actionPublishedOld()
    {

        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','CNIC','Region','Area','Branch','Tranch','Bank_name','Account No','Tranche Amount','Date Disbursed','Status','Response Description']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->publish($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $disb_detail[$i]['sanction_no'] =isset($g['tranch']['loan']['sanction_no'])?$g['tranch']['loan']['sanction_no']:'';
                $disb_detail[$i]['cnic'] =isset($g['tranch']['loan']['application']['member']['cnic'])?$g['tranch']['loan']['application']['member']['cnic']:'';
                $disb_detail[$i]['region_id'] = isset($g['tranch']['loan']['region']['name'])?$g['tranch']['loan']['region']['name']:'';
                $disb_detail[$i]['area_id'] = isset($g['tranch']['loan']['area']['name'])?$g['tranch']['loan']['area']['name']:'';
                $disb_detail[$i]['branch_id'] = isset($g['tranch']['loan']['branch']['name'])?$g['tranch']['loan']['branch']['name']:'';
                //$disb_detail[$i]['tranch_id'] = isset($g['tranche_id'])?$g['tranche_id']:'';
                $disb_detail[$i]['tranch_id'] = isset($g['tranch']['tranch_no'])?$g['tranch']['tranch_no']:'';
                $disb_detail[$i]['bank_name'] = isset($g['bank_name'])?$g['bank_name']:'';
                $disb_detail[$i]['account_no'] = ($g['account_no'])?"'".$g['account_no']."'":'';
                $disb_detail[$i]['transferred_amount'] = isset($g['transferred_amount'])?number_format($g['transferred_amount']):'';
                $disb_detail[$i]['date_disbursed'] = ($g['tranch']['date_disbursed'] > 0)?  date("d-M-y",$g['tranch']['date_disbursed']):'';
                $disb_detail[$i]['status'] = \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($g['status']);
                $disb_detail[$i]['response_description'] = ($g['response_description'])?$g['response_description']:'';
                $i++;
            }
            ExportHelper::ExportCSV('Published Disbursement Detail',$headers,$disb_detail);
            die();
        }

        $request=Yii::$app->request;
        if ($request->post() && isset($request->post()['selection'])){
            foreach ($request->post()['selection'] as $id){
                $disb_Detail=DisbursementDetails::find()->where(['id'=>$id])->andWhere(['deleted'=>0])->one();
                if(!empty($disb_Detail)){
                    $disb_Detail->status      = 5;
                    $disb_Detail->updated_by  = Yii::$app->user->id;
                    $disb_Detail->updated_at  = strtotime(date('Y-m-d H:i:s'));
                    $disb_Detail->save(false);
                }
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        $params = Yii::$app->request->queryParams;
        $params['DisbursementDetailsSearch']['status'] = 0;
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id'])) /*&& !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['status'])*/){
            $dataProvider = $searchModel->publish(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = [];
        }
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('published/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'regions' => $regions
        ]);
    }

    public function actionPublished()
    {

        $batch_id = 0;

        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','CNIC','Region','Area','Branch','Tranch','Bank_name','Account No','Tranche Amount','Date Disbursed','Status','Response Description']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->publish($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $disb_detail[$i]['sanction_no'] =isset($g['tranch']['loan']['sanction_no'])?$g['tranch']['loan']['sanction_no']:'';
                $disb_detail[$i]['cnic'] =isset($g['tranch']['loan']['application']['member']['cnic'])?$g['tranch']['loan']['application']['member']['cnic']:'';
                $disb_detail[$i]['region_id'] = isset($g['tranch']['loan']['region']['name'])?$g['tranch']['loan']['region']['name']:'';
                $disb_detail[$i]['area_id'] = isset($g['tranch']['loan']['area']['name'])?$g['tranch']['loan']['area']['name']:'';
                $disb_detail[$i]['branch_id'] = isset($g['tranch']['loan']['branch']['name'])?$g['tranch']['loan']['branch']['name']:'';
                //$disb_detail[$i]['tranch_id'] = isset($g['tranche_id'])?$g['tranche_id']:'';
                $disb_detail[$i]['tranch_id'] = isset($g['tranch']['tranch_no'])?$g['tranch']['tranch_no']:'';
                $disb_detail[$i]['bank_name'] = isset($g['bank_name'])?$g['bank_name']:'';
                $disb_detail[$i]['account_no'] = ($g['account_no'])?"'".$g['account_no']."'":'';
                $disb_detail[$i]['transferred_amount'] = isset($g['transferred_amount'])?number_format($g['transferred_amount']):'';
                $disb_detail[$i]['date_disbursed'] = ($g['tranch']['date_disbursed'] > 0)?  date("d-M-y",$g['tranch']['date_disbursed']):'';
                $disb_detail[$i]['status'] = \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($g['status']);
                $disb_detail[$i]['response_description'] = ($g['response_description'])?$g['response_description']:'';
                $i++;
            }
            ExportHelper::ExportCSV('Published Disbursement Detail',$headers,$disb_detail);
            die();
        }

        $request=Yii::$app->request;
        $total_amount = 0;
        if ($request->post() && isset($request->post()['selection'])){

            foreach ($request->post()['selection'] as $id) {
                $disb_Detail=DisbursementDetails::find()->where(['id'=>$id])->andWhere(['deleted'=>0])->one();
                if(!empty($disb_Detail)) {

                        $disb_Detail->status = 5;
                        $disb_Detail->updated_by = Yii::$app->user->id;
                        $disb_Detail->updated_at = strtotime(date('Y-m-d H:i:s'));
                        $disb_Detail->save(false);

                    }
            }
            return $this->redirect(Yii::$app->request->referrer);


        }
        $params = Yii::$app->request->queryParams;
        $params['DisbursementDetailsSearch']['status'] = 0;
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id'])) /*&& !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['status'])*/){
            $dataProvider = $searchModel->publish(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = [];
        }
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(\common\models\Projects::find()->asArray()->all(), 'id', 'name');
        unset($projects[77]);
        unset($projects[78]);
        unset($projects[79]);
        return $this->render('published/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'regions' => $regions,
            'projects' => $projects,

        ]);
    }

    public function actionInProcess()
    {
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','CNIC','Region','Area','Branch','Tranch','Bank_name','Account No','Tranche Amount','Date Disbursed','Status','Response Description']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->searchInProcess($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $disb_detail[$i]['sanction_no'] =isset($g['tranch']['loan']['sanction_no'])?$g['tranch']['loan']['sanction_no']:'';
                $disb_detail[$i]['cnic'] =isset($g['tranch']['loan']['application']['member']['cnic'])?$g['tranch']['loan']['application']['member']['cnic']:'';
                $disb_detail[$i]['region_id'] = isset($g['tranch']['loan']['region']['name'])?$g['tranch']['loan']['region']['name']:'';
                $disb_detail[$i]['area_id'] = isset($g['tranch']['loan']['area']['name'])?$g['tranch']['loan']['area']['name']:'';
                $disb_detail[$i]['branch_id'] = isset($g['tranch']['loan']['branch']['name'])?$g['tranch']['loan']['branch']['name']:'';
                //$disb_detail[$i]['tranch_id'] = isset($g['tranche_id'])?$g['tranche_id']:'';
                $disb_detail[$i]['tranch_id'] = isset($g['tranch']['tranch_no'])?$g['tranch']['tranch_no']:'';
                $disb_detail[$i]['bank_name'] = isset($g['bank_name'])?$g['bank_name']:'';
                $disb_detail[$i]['account_no'] = ($g['account_no'])?"'".$g['account_no']."'":'';
                $disb_detail[$i]['transferred_amount'] = isset($g['transferred_amount'])?number_format($g['transferred_amount']):'';
                $disb_detail[$i]['date_disbursed'] = ($g['tranch']['date_disbursed'] > 0)?  date("d-M-y",$g['tranch']['date_disbursed']):'';
                $disb_detail[$i]['status'] = \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($g['status']);
                $disb_detail[$i]['response_description'] = ($g['response_description'])?$g['response_description']:'';
                $i++;
            }
            ExportHelper::ExportCSV('Published Disbursement Detail',$headers,$disb_detail);
            die();
        }
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id']))){
            $dataProvider = $searchModel->searchInProcess(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = [];
        }
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        return $this->render('inprocess/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'regions' => $regions
        ]);
    }

    public function actionDisbursementDetailResponse()
    {
        //print_r(strtotime(10-01-2020));die();
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Sanction No','Old Value','New Value','Stamp']);
            $disb_detail = array();
            $searchModel = new DisbursementDetailsSearch();
            $query = $searchModel->searchResponseLogs($_GET,true);
            //print_r($query);die();
            Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
            //$data = $query->all();
            $i=0;
            foreach ($query as $g){
                 $disb_detail[$i]['sanction_no'] =isset($g['sanction_no'])?$g['sanction_no']:'';
                 $disb_detail[$i]['old_value'] =isset($g['old_value'])?$g['old_value']:'';
                 $disb_detail[$i]['new_value'] =isset($g['new_value'])?$g['new_value']:'';
                 $disb_detail[$i]['stamp'] =isset($g['stamp'])?date( 'd-m-Y',$g['stamp']):'';
                 $i++;
            }
            ExportHelper::ExportCSV('Disbursement Response Detail',$headers,$disb_detail);
            die();
        }
        $searchModel = new DisbursementDetailsSearch();
        if (!empty(Yii::$app->request->queryParams) && (!empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['region_id']) || !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['project_id'])) /*&& !empty(Yii::$app->request->queryParams['DisbursementDetailsSearch']['status'])*/){
            $dataProvider = $searchModel->searchResponseLogs(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        }else{
            $dataProvider = $searchModel->searchResponseLogs(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

            //$dataProvider = [];
        }
        $bank_names= ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('response_report/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches_names' => $branches_name,
            'regions' => $regions
        ]);
    }


    /**
     * Displays a single DisbursementDetails model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "DisbursementDetails #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                           // Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionUpdate($id)
    {
       $model =$this->findModel($id);
       if($model){
           $model->status = 0;
           $model->save();
           return $this->redirect(Yii::$app->request->referrer);
       }
    }

    public function actionAllocateFunds()
    {
        $params = Yii::$app->request->queryParams;
        $params['DisbursementDetailsSearch']['status'] = 0;
        $request = $request=Yii::$app->request;
        $searchModel = new DisbursementDetailsSearch();
        if(isset($request->post()['DisbursementDetailsSearch']['project_id']) && isset($request->post()['DisbursementDetailsSearch']['bank_name'])){

            $params = $request->post()['DisbursementDetailsSearch'];
            $query = $searchModel->search_fund_allocation($params);
            $bank_name_filter = $params['bank_name'];
        } else {
            $query = $searchModel->search_fund_allocation($params);
            $bank_name_filter = $params['DisbursementDetailsSearch']['bank_name'];
        }

        Yii::$app->Permission->getSearchFilterQuery($query,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $branches_name = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        //$bank_names = ArrayHelper::map(\common\models\Lists::find()->where(['list_name'=>'bank_accounts'])->all(),'value','label');

        $batches = Funds::find()->where(['status' => 1])->asArray()->all();
        $funds = ArrayHelper::map($batches,'id', /*'fund_description'*/ function($batches) {
            return $batches['name'] .' - '. number_format($batches['total_fund']-($batches['fund_received']-$batches['recovery']));
        });
        $request=Yii::$app->request;

        if (isset($request->post()['DisbursementDetailsSearch']['fund_id'])){
            $fund_id = $request->post()['DisbursementDetailsSearch']['fund_id'];

            if(!empty($fund_id)) {

                $fund = Funds::find()->where(['id' => $fund_id])->one();
                if ($request->post()['sum'] <= ($fund->total_fund-($fund->fund_received-$fund->recovery))) {
                    $fund->fund_received = $fund->fund_received + $request->post()['sum'];
                    if (($fund->fund_received-$fund->recovery) >= $fund->total_fund) {
                        $fund->status = 2;
                    }
                    $fund->save();
                    $tranche_ids = $request->post()['batch'][0];
                    if (isset($fund_id)) {

                        $fund_batch = new  ProjectFundDetail();
                        $fund_batch->fund_id = $fund_id;
                        $fund_batch->project_id = $request->post()['project_id'];
                        $fund_batch->fund_batch_amount = $request->post()['sum'];
                        $fund_batch->no_of_loans = $request->post()['count'];
                        $fund_batch->allocation_date = strtotime(date('d-m-Y'));
                        $fund_batch->disbursement_source = $request->post()['bank_name_filter'];
                        if($fund_batch->save()){
                            $fund_batch_id = $fund_batch->id;
                            $fund_batch_name = $fund_batch->fund->name;
                            $batch = ProjectFundDetail::find()->where(['id' => $fund_batch_id])->one();
                            $batch->batch_no = 'AKT-' . $fund_batch_name . '-' . $fund_batch_id;
                            $batch->save();
                            Yii::$app->db->createCommand('UPDATE disbursement_details SET status = 6 WHERE tranche_id IN(' . $tranche_ids . ') AND status = 0')->execute();
                            Yii::$app->db->createCommand('UPDATE loan_tranches SET batch_id = ' .$fund_batch_id . ' WHERE id IN(' . $tranche_ids . ')')->execute();
                        }else{
                            Yii::$app->session->setFlash('error', "internal issue, Please contact with admin!");
                            return $this->redirect(Yii::$app->request->referrer);
                        }


                    }
                    Yii::$app->session->setFlash('success', "<br>Batch No : ". $batch->batch_no."
                                                        <br>"."Funding Line : ".$fund_batch->fund->name." 
                                                        <br>"."No of Loans : ".$request->post()['count']." 
                                                        <br>  Amount (Rs.) : " . number_format($request->post()['sum'])
                                                       );
                    return $this->redirect(Yii::$app->request->referrer);
                }
                Yii::$app->session->setFlash('error', "Source Balance Insufficient. Balance is " . number_format(($fund->total_fund-($fund->fund_received-$fund->recovery))) . " and you are trying to disburse " . number_format($request->post()['sum']));
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', "Please Select Funding Source!");
            return $this->redirect(Yii::$app->request->referrer);
        }


        return $this->render('fund-allocation/index', [
            'searchModel' => $searchModel,
            'query' => $query,
            //'bank_names' => $bank_names,
            'branches_names' => $branches_name,
            'areas' => $areas,
            'regions' => $regions,
            'funds' => $funds,
            'bank_name_filter' => $bank_name_filter,
        ]);
    }

    /**
     * Creates a new DisbursementDetails model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /**
     * Updates an existing DisbursementDetails model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    /**
     * Delete an existing DisbursementDetails model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
     /**
     * Delete multiple existing DisbursementDetails model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /**
     * Finds the DisbursementDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DisbursementDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DisbursementDetails::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
