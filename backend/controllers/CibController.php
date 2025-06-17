<?php

namespace backend\controllers;

use common\components\Helpers\ExportHelper;
use common\models\Projects;
use common\models\Regions;
use Yii;
use common\models\ApplicationsCib;
use common\models\search\ApplicationsCibSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CibController implements the CRUD actions for ApplicationsCib model.
 */
class CibController extends Controller
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
     * Lists all ApplicationsCib models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ApplicationsCibSearch();
        $dataProvider = $searchModel->searchAdvance(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionIndexSearch()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        $regions  = Regions::find()->all();
        $projects = Projects::find()->all();
        $searchModel = new ApplicationsCibSearch();
        $params = Yii::$app->request->queryParams;
        if(isset($_GET['export']) && $_GET['export']  == 'export'){
            $this->layout = 'csv';
            $headers = (['Cib Id','Application Id','CNIC','Member Name','Parentage','Gender','DateOfBirth','ApplicationNO','RequestedAmount','Project','Cib Receipt No','Region','Area','Branch','CibStatus','City','ApplicationDate','CibDate','ApplicationStatus','Address']);
            $groups = array();
            $searchModel = new ApplicationsCibSearch();
            $query = $searchModel->searchAdvance($_GET,true);
            $data = $query->all();
            $i=0;
            foreach ($data as $g){
                $groups[$i]['cib_id'] = isset($g['id'])?$g['id']:'';
                $groups[$i]['app_id'] = isset($g['application']['id'])?$g['application']['id']:'';
                $groups[$i]['cnic'] = isset($g['application']['member'])?$g['application']['member']['cnic']:'';
                $groups[$i]['member_name'] =  isset($g['application']['member'])?$g['application']['member']['full_name']:'';
                $groups[$i]['parentage'] =  isset($g['application']['member'])?$g['application']['member']['parentage']:'';
                $groups[$i]['gender'] =  isset($g['application']['member'])?$g['application']['member']['gender']:'';
                $groups[$i]['dob'] =  isset($g['application']['member'])?$g['application']['member']['dob']:'';
                $groups[$i]['app_no'] =  isset($g['application']['application_no'])?$g['application']['application_no']:'';
                $groups[$i]['loan_amount'] =  isset($g['application']['req_amount'])?$g['application']['req_amount']:'';
                $groups[$i]['project_id'] =  isset($g['application']['project_id'])?$g['application']['project']['name']:'';
                $groups[$i]['receipt_no'] =  isset($g['receipt_no'])?$g['receipt_no']:'';;
                $groups[$i]['region_id'] = isset($g['application']['region']['name'])?$g['application']['region']['name']:'';
                $groups[$i]['area_id'] = isset($g['application']['area']['name'])?$g['application']['area']['name']:'';
                $groups[$i]['branch_id'] = isset($g['application']['branch']['name'])?$g['application']['branch']['name']:'';
                $groups[$i]['cib_status'] =  isset($g['status'])?$g['status']:'';
                $groups[$i]['city_id'] =  isset($g['application']['branch']['city'])?$g['application']['branch']['city']['name']:'';
                $groups[$i]['app_date'] =  isset($g['application']['application_date'])?$g['application']['application_date']:'';
                $groups[$i]['cib_date'] =  isset($g['updated_at'])?$g['updated_at']:'';;
                $groups[$i]['app_status'] =  isset($g['application']['status'])?$g['application']['status']:'';
                $groups[$i]['address'] =  isset($g['application']['member']['membersAddresses'])?$g['application']['member']['membersAddresses'][0]['address']:'';
                $i++;
            }
            ExportHelper::ExportCSV('cib-data.csv',$headers,$groups);
            die();
        }

        if(empty(Yii::$app->request->queryParams)){
            $dataProvider=array();
        }else{
            $dataProvider = $searchModel->searchAdvance($params);
        }
        return $this->render('index_search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'projects' => $projects,
        ]);
    }

    /**
     * Displays a single ApplicationsCib model.
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
     * Creates a new ApplicationsCib model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ApplicationsCib();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ApplicationsCib model.
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
     * Deletes an existing ApplicationsCib model.
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
     * Finds the ApplicationsCib model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApplicationsCib the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApplicationsCib::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
