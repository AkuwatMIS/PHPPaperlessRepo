<?php

namespace backend\controllers;

use common\models\Activities;
use common\models\Lists;
use common\models\Products;
use common\models\ProgressReports;
use common\models\Areas;
use common\models\Branches;
use common\models\Fields;
use common\models\Teams;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\UnauthorizedHttpException;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class StructureController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        return Yii::$app->response->redirect(['site/main']);
                    }
                },
                'only' => ['index', 'view', 'create', 'update', '_form'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', '_form'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionGetBranchRegionArea($id)
    {
        $out = array();
        $branch = Branches::findOne($id);
        $out['branch_id'] = $branch->id;
        $out['area_id'] = $branch->area_id;
        $out['region_id'] = $branch->region_id;

        echo Json::encode($out);
    }

    public function actionFetchAreaByRegion()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Areas::find()->select(['id', 'name'])->where(['region_id'=>$rgId,'deleted' => 0])->asArray()->all();
            //$out = Yii::$app->Permission->getAreasByRegion(Yii::$app->controller->id, Yii::$app->controller->action->id, $rgId);
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }

        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchBranchByArea()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId,'deleted' => 0])->asArray()->all();
            //$out = Yii::$app->Permission->getBranchesByArea(Yii::$app->controller->id, Yii::$app->controller->action->id, $rgId);
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }

        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchTeamByBranch()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Teams::find()->select(['id', 'name'])->where(['branch_id'=>$rgId])->asArray()->all();
            //$out = Yii::$app->Permission->getTeamsByBranch(Yii::$app->controller->id, Yii::$app->controller->action->id, $rgId);
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }

        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchFieldByTeam()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Fields::find()->select(['id', 'name'])->where(['team_id'=>$rgId])->asArray()->all();
            //$out = Yii::$app->Permission->getFieldsByTeam(Yii::$app->controller->id, Yii::$app->controller->action->id, $rgId);
            /*foreach ($out as $f) {
                if (isset($f->userStructureMapping->user->username)) {
                    $f->name = $f->name . ' (' . $f->userStructureMapping->user->username . ')';
                } else {
                    $f->name = $f->name . ' (--)';
                }
            }*/
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }

        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchDateByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $data = ProgressReports::find()->select(['id', 'report_date'])->where(['project_id' => $rgId, 'status' => 1, 'do_delete' => 0, 'deleted' => 0])/*->orderBy(['report_date'=>SORT_DESC])*/
            ->asArray()->all();
            foreach ($data as $key => $d) {
                $out[$key]['id'] = $d['id'];
                $out[$key]['name'] = date('M j, Y', ($d['report_date']));
                //   break;
            }
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }
        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchProductByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $out = Products::find()->select(['products.id', 'products.name'])->join('inner join', 'project_product_mapping', 'project_product_mapping.product_id=products.id')
                ->where(['project_product_mapping.project_id' => $rgId, 'products.status' => 1])->asArray()->all();
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }
        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }

    public function actionFetchActivityByProduct()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $out = Activities::find()->select(['activities.id', 'activities.name'])->where(['product_id' => $rgId, 'activities.status' => 1])->asArray()->all();
            echo Json::encode(['output' => $out, 'selected' => '']);
            return;
        }
        echo Json::encode(['output' => '', 'selected' => '']);
        return;
    }
    public function actionFetchSubActivityByActivity()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            $activity=Activities::find()->where(['id'=>$rgId])->one();
            //die($rgId);
            $out = Lists::find()->select(['lists.value as id', 'lists.label as name'])->where(['list_name'=>$activity->name.'_sub-activity'])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionStructure()
    {
        $out = [];
        $out1 = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $structure = ucfirst($parents[0]);
                $structure = 'common\models\\' . $structure;
                $out = $structure::find()->select(['id', 'name'])->all();

                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionBranchprojects()
    {
        $out = [];
        $out1 = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $branch_code = $parents[0];
                $branch = StructureHelper::getBranchidfromcode($branch_code);
                $a = StructureHelper::getBranchprojects($branch['id']);

                for ($i = 0; $i < count($a); $i++) {
                    $b = $a[$i]['project_id'];
                    $out1 = StructureHelper::getProjectname($b);
                    array_push($out, $out1[0]);

                }

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionFetchAreasByRegion()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Areas::find()->select(['id', 'name'])->where(['region_id'=>$rgId,'deleted' => 0])->asArray()->all();
            //$out = \backend\Helpers\StructureHelper::getAreasByRegion($rgId);
            return Json::encode(['output' => $out, 'selected' => '']);
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionFetchBranchesByArea()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId, 'deleted' => 0])->asArray()->all();
            //$out = \backend\Helpers\StructureHelper::getBranchesByArea($rgId);
            return Json::encode(['output' => $out, 'selected' => '']);
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionFetchTeamsByBranch()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Teams::find()->select(['id', 'name'])->where(['branch_id'=>$rgId])->asArray()->all();
            //$out = \backend\Helpers\StructureHelper::getTeamsByBranch($rgId);
            return Json::encode(['output' => $out, 'selected' => '']);
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }
    public function actionFetchFieldsByTeam()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Fields::find()->select(['id', 'name'])->where(['team_id'=>$rgId])->asArray()->all();
            //$out = \backend\Helpers\StructureHelper::getFieldsByTeam($rgId);
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionFetchBankByType()
    {
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $type = $parents[0];
                $out = Lists::find()->where(['list_name' => $type])->select(['lists.value as id', 'lists.label as name'])->asArray()->all();
                return Json::encode(['output'=>$out, 'selected'=>'']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);

    }
}
