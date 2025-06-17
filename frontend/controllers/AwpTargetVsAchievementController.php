<?php

namespace frontend\controllers;

use backend\Helpers\StructureHelper;
use common\components\Helpers\ExportHelper;
use common\components\RbacHelper;
use common\models\AwpBranchSustainability;
use common\models\AwpFinal;
use common\models\AwpLoansUm;
use common\models\AwpRecoveryPercentage;
use common\models\search\AwFinalSearch;
use common\models\search\AwpFinalSearch;
use common\models\AwpOverdue;
use common\models\search\AwpBranchSustainabilitySearch;
use common\models\search\AwpLoanManagementCostSearch;
use common\models\search\AwpLoansUmSearch;
use common\models\search\AwpOverdueSearch;
use common\models\search\AwpRecoveryPercentageSearch;
use common\models\search\AwpSearch;
use Yii;
use common\models\AwpTargetVsAchievement;
use common\models\search\AwpTargetVsAchievementSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AwpTargetVsAchievementController implements the CRUD actions for AwpTargetVsAchievement model.
 */
class AwpTargetVsAchievementController extends Controller
{

    /**
     * @inheritdoc
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
     * Lists all AwpTargetVsAchievement models.
     * @return mixed
     */
    public function actionIndex()
    {

        $params = Yii::$app->request->queryParams;
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $headers = [];
            $this->layout = 'csv';
            for ($i = 0; $i < 2; $i++) {
                array_push($headers, array_keys($_GET['AwpTargetVsAchievementSearch'])[$i]);
            }
            array_push($headers, 'Branch', 'Target Loans', 'Target Amount', 'Achieved Loans', 'Achieved Amount', 'Loans Diff', 'Amount Diff');
            $groups = array();
            $searchModel = new AwpTargetVsAchievementSearch();
            $data = $searchModel->search($_GET, true);
            foreach ($data as $g) {
                $groups[$i]['region_id'] = $g['region']['name'];
                $groups[$i]['area_id'] = $g['area']['name'];
                $groups[$i]['branch_id'] = $g['branch']['name'];
                $groups[$i]['target_loans'] = $g['target_loans'];
                $groups[$i]['target_amount'] = $g['target_amount'];
                $groups[$i]['achieved_loans'] = $g['achieved_loans'];
                $groups[$i]['achieved_amount'] = $g['achieved_amount'];
                $groups[$i]['loans_dif'] = $g['achieved_loans'] - $g['target_loans'];
                $groups[$i]['amount_dif'] = $g['achieved_amount'] - $g['target_amount'];
                $i++;
            }
            ExportHelper::ExportCSV('AWP-TargetVsAchievementReport.csv', $headers, $groups);
            die();
        }
        $searchModel = new AwpTargetVsAchievementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //RbacHelper::getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id);
        $overdue_params = array("AwpOverdueSearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
            "project_id" => isset($params['AwpTargetVsAchievementSearch']['project_id']) ? $params['AwpTargetVsAchievementSearch']['project_id'] : '',
            "month" => isset($params['AwpTargetVsAchievementSearch']['month']) ? $params['AwpTargetVsAchievementSearch']['month'] : '',
        ),
        );
        $sustain_params = array("AwpBranchSustainabilitySearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
            "project_id" => isset($params['AwpTargetVsAchievementSearch']['project_id']) ? $params['AwpTargetVsAchievementSearch']['project_id'] : '',
            "month" => isset($params['AwpTargetVsAchievementSearch']['month']) ? $params['AwpTargetVsAchievementSearch']['month'] : '',
            "surplus_deficit" => isset($params['branch_sustain']) ? $params['branch_sustain'] : '',
            "month_from" => isset($params['AwpTargetVsAchievementSearch']['month_from']) ? $params['AwpTargetVsAchievementSearch']['month_from'] : '',
        ),
        );
        $loan_management_params = array("AwpLoanManagementCostSearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
            //"project_id"=>isset($params['AwpTargetVsAchievementSearch']['project_id'])?$params['AwpTargetVsAchievementSearch']['project_id']:'',
            //"month"=>isset($params['AwpTargetVsAchievementSearch']['month'])?$params['AwpTargetVsAchievementSearch']['month']:'',
        ),
        );
        $awp_final_params = array("AwpSearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
            "project_id" => isset($params['AwpTargetVsAchievementSearch']['project_id']) ? $params['AwpTargetVsAchievementSearch']['project_id'] : '',
            "month" => isset($params['AwpTargetVsAchievementSearch']['month']) ? $params['AwpTargetVsAchievementSearch']['month'] : '',
            "month_from" => isset($params['AwpTargetVsAchievementSearch']['month_from']) ? $params['AwpTargetVsAchievementSearch']['month_from'] : '',
        ),
        );
        $loans_per_um_params = array("AwpLoansUmSearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
        ),
        );
        $recovery_percent = array("AwpRecoveryPercentageSearch" => array(
            "region_id" => isset($params['AwpTargetVsAchievementSearch']['region_id']) ? $params['AwpTargetVsAchievementSearch']['region_id'] : '',
            "area_id" => isset($params['AwpTargetVsAchievementSearch']['area_id']) ? $params['AwpTargetVsAchievementSearch']['area_id'] : '',
            "branch_id" => isset($params['AwpTargetVsAchievementSearch']['branch_id']) ? $params['AwpTargetVsAchievementSearch']['branch_id'] : '',
            "month" => isset($params['AwpTargetVsAchievementSearch']['month']) ? $params['AwpTargetVsAchievementSearch']['month'] : '',
            "month_from" => isset($params['AwpTargetVsAchievementSearch']['month_from']) ? $params['AwpTargetVsAchievementSearch']['month_from'] : '',
        ),
        );
        $searchModel_overdue = new AwpOverdueSearch();
        $dataProvider_overdue = $searchModel_overdue->search($overdue_params);

        $searchModel_overdue_ending = new AwpOverdueSearch();
        $dataProvider_overdue_pending = $searchModel_overdue_ending->search($overdue_params);

        $searchModel_branch_sus = new AwpBranchSustainabilitySearch();
        $dataProvider_branch_sus = $searchModel_branch_sus->search($sustain_params);
        //RbacHelper::getSearchFilter($dataProvider_branch_sus,'awp_branch_sustainability',Yii::$app->controller->action->id);

        $searchModel_branch_mang = new AwpLoanManagementCostSearch();
        $dataProvider_branch_mang = $searchModel_branch_mang->search($loan_management_params);

        $searchModel_loans_per_um = new AwpLoansUmSearch();
        $dataProvider_loans_per_um = $searchModel_loans_per_um->search($loans_per_um_params);

        $searchModel_recovery_percent = new AwpRecoveryPercentageSearch();
        $dataProvider_recovery_percent = $searchModel_recovery_percent->search($recovery_percent);

        $searchModel_awp_final = new AwpSearch();
        $dataProvider_awp_final = $searchModel_awp_final->search($awp_final_params);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(\common\components\Helpers\StructureHelper::getProjects(), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'searchModel_overdue' => $searchModel_overdue,
            'dataProvider_overdue' => $dataProvider_overdue,

            'searchModel_overdue_ending' => $searchModel_overdue_ending,
            'dataProvider_overdue_pending' => $dataProvider_overdue_pending,


            'searchModel_branch_sus' => $searchModel_branch_sus,
            'dataProvider_branch_sus' => $dataProvider_branch_sus,

            'searchModel_awp_final' => $searchModel_awp_final,
            'dataProvider_awp_final' => $dataProvider_awp_final,

            'searchModel_branch_mang' => $searchModel_branch_mang,
            'dataProvider_branch_mang' => $dataProvider_branch_mang,

            'searchModel_loans_per_um' => $searchModel_loans_per_um,
            'dataProvider_loans_per_um' => $dataProvider_loans_per_um,

            'searchModel_recovery_percent' => $searchModel_recovery_percent,
            'dataProvider_recovery_percent' => $dataProvider_recovery_percent,

            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects
        ]);
    }

    /**
     * Displays a single AwpTargetVsAchievement model.
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
     * Creates a new AwpTargetVsAchievement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AwpTargetVsAchievement();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AwpTargetVsAchievement model.
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
     * Deletes an existing AwpTargetVsAchievement model.
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
     * Finds the AwpTargetVsAchievement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AwpTargetVsAchievement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AwpTargetVsAchievement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
