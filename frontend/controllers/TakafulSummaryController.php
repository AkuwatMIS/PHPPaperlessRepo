<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\models\search\ArcAccountReportDetailsSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use common\components\Helpers\StructureHelper;
use Yii;
use common\components\Helpers\AccountsReportHelper;

class TakafulSummaryController extends Controller
{
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

    public function actionIndex()
    {
        $params = Yii::$app->request->post();
        $params2 = Yii::$app->request->queryParams;

        if (isset($params2['export']) && $params2['export'] == 'export') {
            $params = $params2;
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['code'] = 'takaf';
            $searchModel = new ArcAccountReportDetailsSearch();
            $searchModel->load($params);
            $dataProvider = AccountsReportHelper::TakafulSummary($params);
            $models = $dataProvider->getModels();
            $headers = [];
            if (isset($models[0]) && $models[0] != null) {
                foreach ($models[0] as $key => $headings) {
                    array_push($headers, $key);
                }
            }
            ExportHelper::ExportCSV('Takaful-Summary-Report.csv', $headers, $models);
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

        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['code'] = 'takaf';
        $searchModel->load($params);
        $dataProvider = AccountsReportHelper::TakafulSummary($params);

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
        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
            'total' => $total,
        ]);

    }
}