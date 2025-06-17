<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use common\models\EmergencyLoans;
use common\models\search\EmergencyLoansSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

/**
 * EmergencyLoansController implements the CRUD actions for EmergencyLoans model.
 */
class EmergencyLoansController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * {@inheritdoc}
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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
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
     * Lists all EmergencyLoans models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Region','Area','Branch','Name','Parentage','CNIC','Sanction No', 'Date Disbursed','Project']);
            $emergency_loans = array();
            $searchModel = new EmergencyLoansSearch();
            $query = $searchModel->search($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,'loans','index',$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $emergency_loans[$i]['region'] = isset($g['loan']['region']['name'])?$g['loan']['region']['name']:'';
                $emergency_loans[$i]['area'] = isset($g['loan']['area']['name'])?$g['loan']['area']['name']:'';
                $emergency_loans[$i]['branch'] = isset($g['loan']['branch']['name'])?$g['loan']['branch']['name']:'';
                $emergency_loans[$i]['full_name'] = isset($g['loan']['application']['member']['full_name'])?$g['loan']['application']['member']['full_name']:'';
                $emergency_loans[$i]['parentage'] = isset($g['loan']['application']['member']['parentage'])?$g['loan']['application']['member']['parentage']:'';
                $emergency_loans[$i]['cnic'] = isset($g['loan']['application']['member']['cnic'])?$g['loan']['application']['member']['cnic']:'';
                $emergency_loans[$i]['Sanction No'] = isset($g['loan']['sanction_no'])?$g['loan']['sanction_no']:'';
                $emergency_loans[$i]['Date Disbursed'] = isset($g['loan']['date_disbursed'])?date('d M Y', $g['loan']['date_disbursed']):'';
                $emergency_loans[$i]['Project'] = isset($g['loan']['project']['name'])?$g['loan']['project']['name']:'';
                $i++;
            }
            ExportHelper::ExportCSV('Emergency Loans',$headers,$emergency_loans);
            die();
        }
        $searchModel = new EmergencyLoansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider,'loans','index',$this->rbac_type);
        $projects_name = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects_name' =>$projects_name,
            'regions' =>$regions,
        ]);
    }
    public function actionEmergencyLoansCityWise()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Province','District','City Name','City ID','Name','Parentage','CNIC','Sanction No', 'Date Disbursed','Donated Date','Status','Project']);
            $emergency_loans = array();
            $searchModel = new EmergencyLoansSearch();
            $query = $searchModel->searchCityWise($_GET,true);
            Yii::$app->Permission->getSearchFilterQuery($query,'loans','index',$this->rbac_type);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                //print_r($g);die();
                $emergency_loans[$i]['province'] = isset($g['loan']['branch']['province']['name'])?$g['loan']['branch']['province']['name']:'';
                $emergency_loans[$i]['district'] = isset($g['loan']['branch']['district']['name'])?$g['loan']['branch']['district']['name']:'';
                $emergency_loans[$i]['city'] = isset($g['loan']['branch']['city']['name'])?$g['loan']['branch']['city']['name']:'';
                $emergency_loans[$i]['city_id'] = isset($g['loan']['branch']['city']['id'])?$g['loan']['branch']['city']['id']:'';
                $emergency_loans[$i]['full_name'] = isset($g['loan']['application']['member']['full_name'])?$g['loan']['application']['member']['full_name']:'';
                $emergency_loans[$i]['parentage'] = isset($g['loan']['application']['member']['parentage'])?$g['loan']['application']['member']['parentage']:'';
                $emergency_loans[$i]['cnic'] = isset($g['loan']['application']['member']['cnic'])?$g['loan']['application']['member']['cnic']:'';
                $emergency_loans[$i]['Sanction No'] = isset($g['loan']['sanction_no'])?$g['loan']['sanction_no']:'';
                $emergency_loans[$i]['Date Disbursed'] = isset($g['loan']['date_disbursed'])?date('d M Y', $g['loan']['date_disbursed']):'';
                $emergency_loans[$i]['Donated Date'] = isset($g['donated_date'])?date('d M Y', strtotime($g['donated_date'])):'';
                $emergency_loans[$i]['Status'] = \common\components\Helpers\StructureHelper::getEmergencyLoanstatus($g['status']);
                $emergency_loans[$i]['Project'] = isset($g['loan']['project']['name'])?$g['loan']['project']['name']:'';
                $i++;
            }
            ExportHelper::ExportCSV('Emergency Loans City Wise ',$headers,$emergency_loans);
            die();
        }
        $searchModel = new EmergencyLoansSearch();
        $dataProvider = $searchModel->searchCityWise(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, 'loans','index',$this->rbac_type);
        $projects_name = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $districts = ArrayHelper::map(StructureHelper::getDistricts(), 'id', 'name');
        $provinces = ArrayHelper::map(StructureHelper::getProvinces(), 'id', 'name');
        $cities = ArrayHelper::map(StructureHelper::getCities(), 'id', 'name');
        return $this->render('emergency_report_city_wise/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects_name' =>$projects_name,
            'provinces' =>$provinces,
            'districts' =>$districts,
            'cities' =>$cities,
        ]);
    }

    /**
     * Displays a single EmergencyLoans model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EmergencyLoans model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmergencyLoans();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmergencyLoans model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EmergencyLoans model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmergencyLoans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmergencyLoans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmergencyLoans::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
