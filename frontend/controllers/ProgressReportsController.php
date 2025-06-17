<?php

namespace frontend\controllers;
use common\components\RbacHelper;
use common\components\Helpers\ProgressReportHelper;
use common\components\Helpers\StructureHelper;
use common\models\Districts;
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
class ProgressReportsController extends Controller
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
    public function actionIndex()
    {
        $params = Yii::$app->request->post();
        $project_id = 0;
        $progress_report_dates =array();
        if(isset($params['ProgressReportDetailsSearch']['project_id'])){
            $project_id = $params['ProgressReportDetailsSearch']['project_id'];
        }

        $progress_reports = ProgressReportHelper::getProgressReports($project_id);
        $progress_report_dates_array = array();
        $progress_report_dates_array = ArrayHelper::map($progress_reports, 'id','report_date');
        foreach ($progress_reports as $p){
            $date = date('Y-m-d', strtotime($p->report_date));
            $progress_report_dates[$p->id] = date('M j, Y', strtotime($p->report_date));

        }
        $searchProgress = new ProgressReportDetailsSearch();
        if(!isset($params['ProgressReportDetailsSearch']['progress_report_id']) || empty($params['ProgressReportDetailsSearch']['progress_report_id'])){
            $params['ProgressReportDetailsSearch']['progress_report_id'] = key($progress_report_dates);
        }
        $progress_data = $searchProgress->search($params);


        $new_progress_data = array();
        foreach ($progress_data as $p){

            $district = Districts::find()->where(['id'=>$p['district_id']])->asArray()->one();
            $p['district_id'] = $district['name'];
            $new_progress_data[] = $p;
        }
        $new_progress_data=$progress_data;

        $progress_report = ProgressReportHelper::parse_json_progress($new_progress_data);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        //$regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        $progress_report_date = isset($progress_report_dates_array[$searchProgress->progress_report_id])?$progress_report_dates_array[$searchProgress->progress_report_id]:0;
        $progress_report_project = ($project_id != 0) ? $projects[$project_id] : 'overall';

        return $this->render('index', [
            'searchModel' => $searchProgress,
            'progress_report' => $progress_report,
            'progress_report_dates' => $progress_report_dates,
            //'branches' => $branches,
            //'areas' => $areas,
            'regions' => $regions,
            'projects' =>  $projects,
            'heading' => 'Progress Report as on '.date('d-M-Y',$progress_report_date).'('.$progress_report_project.')',
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
        if (($model = ProgressReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
