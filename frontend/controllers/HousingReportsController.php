<?php


namespace frontend\controllers;


use common\models\search\HousingReportsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class HousingReportsController extends Controller
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

    public function actionDashboard()
    {
        $searchModel = new HousingReportsSearch();
        $result = $searchModel->search(Yii::$app->request->queryParams);
        $result_regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
       // $result_projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $result_projects = ['52'=>'Low Cost Housing Scheme','132'=>'Apni Chhat Apna Ghar'];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'result' => $result,
            'result_regions' => $result_regions,
            'result_projects' => $result_projects
        ]);

    }
}