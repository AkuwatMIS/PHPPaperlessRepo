<?php

namespace frontend\modules\user_management\controllers;
use common\models\Activities;
use common\models\Cities;
use common\models\Districts;
use common\models\Divisions;
use common\models\Lists;
use common\models\Products;
use common\models\ProgressReports;
use common\models\Areas;
use common\models\Branches;
use common\models\Fields;
use common\models\Provinces;
use common\models\Regions;
use common\models\Teams;
use common\components\Helpers\StructureHelper;
use common\models\Tehsils;
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionAbc()
    {
        //die('here');
    }
    public function actionGetBranchRegionArea($id)
    {
        $out = array();
        $branch = Branches::findOne($id);
        $out['branch_id'] = $branch->id;
        $out['area_id'] =  $branch->area_id;
        $out['region_id'] =  $branch->region_id;

        return Json::encode($out);
    }
    public function actionFetchAreaByRegion()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            //$out = Areas::find()->select(['id', 'name'])->where(['region_id'=>$rgId])->asArray()->all();
            $out = Yii::$app->Permission->getAreasByRegion(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,'frontend');
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionFetchBranchByArea()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            //$out = Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->asArray()->all();
            $out = Yii::$app->Permission->getBranchesByArea(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,'frontend');
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchTeamByBranch()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            //$out = Teams::find()->select(['id', 'name'])->where(['branch_id'=>$rgId])->asArray()->all();
            $out = Yii::$app->Permission->getTeamsByBranch(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,'frontend');
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchFieldByTeam()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            //$out = Fields::find()->select(['id', 'name'])->where(['team_id'=>$rgId])->asArray()->all();
            $out = Yii::$app->Permission->getFieldsByTeam(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,'frontend');
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionFetchDateByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $data = ProgressReports::find()->select(['id', 'report_date'])->where(['project_id'=>$rgId,'status'=>1,'do_delete'=>0, 'deleted'=>0])/*->orderBy(['report_date'=>SORT_DESC])*/->asArray()->all();
            foreach($data as $key=>$d){
                $out[$key]['id'] = $d['id'];
                $out[$key]['name'] = date('M j, Y',($d['report_date']));
                //   break;
            }
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionFetchProductByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $out = Products::find()->select(['products.id', 'products.name'])->join('inner join','project_product_mapping','project_product_mapping.product_id=products.id')
                ->where(['project_product_mapping.project_id'=>$rgId,'products.status'=>1])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchAccountByBank()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            $out = Lists::find()->select(['lists.id', 'lists.value'])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchActivityByProduct()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            //die($rgId);
            $out = Activities::find()->select(['activities.id', 'activities.name'])->join('inner join','product_activity_mapping','product_activity_mapping.activity_id=activities.id')
                ->where(['product_activity_mapping.product_id'=>$rgId,'activities.status'=>1])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionBranchprojects()
    {
        $out = [];
        $out1 = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $branch_code = $parents[0];
                $branch=StructureHelper::getBranchidfromcode($branch_code);
                $a = StructureHelper::getBranchprojects($branch['id']);

                for ($i = 0; $i < count($a); $i++) {
                    $b = $a[$i]['project_id'];
                    $out1 = StructureHelper::getProjectname($b);
                    array_push($out, $out1[0]);

                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionFetchRegionsByCrDivision()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $cdId = $parents[0];
            $out = Regions::find()->select(['id', 'name'])->where(['cr_division_id'=>$cdId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchProvincesByCountry()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $countryId = $parents[0];
            $out = Provinces::find()->select(['id', 'name'])->where(['country_id'=>$countryId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchCitiesByProvince()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $provinceId = $parents[0];
            $out = Cities::find()->select(['id', 'name'])->where(['province_id'=>$provinceId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchDivisionsByProvince()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $provinceId = $parents[0];
            $out = Divisions::find()->select(['id', 'name'])->where(['province_id'=>$provinceId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchByDivision()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $divId = $parents[0];
            $out = Districts::find()->select(['id', 'name'])->where(['division_id'=>$divId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchTehsilsByDistrict()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $disId = $parents[0];
            $out = Tehsils::find()->select(['id', 'name'])->where(['district_id'=>$disId])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }
}
