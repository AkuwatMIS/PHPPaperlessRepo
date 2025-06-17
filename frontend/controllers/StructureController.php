<?php

namespace frontend\controllers;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\AwpHelper;
use common\models\Activities;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Appraisals;
use common\models\AppraisalsAgriculture;
use common\models\AppraisalsBusiness;
use common\models\AppraisalsEmergency;
use common\models\AppraisalsHousing;
use common\models\ArcAccountReports;
use common\models\BranchProjectsMapping;
use common\models\Lists;
use common\models\Products;
use common\models\ProgressReports;
use common\models\Areas;
use common\models\Branches;
use common\models\Fields;
use common\models\ProjectAppraisalsMapping;
use common\models\Projects;
use common\models\Recoveries;
use common\models\Referrals;
use common\models\SocialAppraisal;
use common\models\Teams;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\helpers\ArrayHelper;
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
    public $rbac_type = 'frontend';
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
            $out = Yii::$app->Permission->getAreasByRegion(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,$this->rbac_type);
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
            $out = Yii::$app->Permission->getBranchesByArea(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,$this->rbac_type);
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchProjectByBranch()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];

            $projects = BranchProjectsMapping::find()->where(['branch_id' => $rgId])->select(['project_id'])->all();
            $pr_ids = [];

            foreach ($projects as $key=>$project){
                $pr_ids[$key] = $project->project_id;
            }

            $out = Projects::find()->where(['status' => 1])->where(['IN', 'id', $pr_ids])->all();
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
            $out = Yii::$app->Permission->getTeamsByBranch(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,$this->rbac_type);
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
            $out = Yii::$app->Permission->getFieldsByTeam(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId,$this->rbac_type);
            foreach($out as $f){
                if(isset($f->userStructureMapping->user->username)){
                    $f->name=  $f->name.' ('.$f->userStructureMapping->user->username.')';
                }
                else{
                    $f->name=$f->name.' (--)';
                }
            }
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
            $data = ProgressReports::find()->select(['id', 'report_date','gender'])->where(['project_id'=>$rgId,'status'=>1,'do_delete'=>0, 'deleted'=>0])->orderBy(['report_date'=>SORT_DESC])->asArray()->all();
            foreach($data as $key=>$d){
                $gender = '';
                if($d['gender'] == '0'){
                    $gender = 'All';
                }else if($d['gender'] == 'm'){
                    $gender = 'Male';
                }else if($d['gender'] == 'f'){
                    $gender = 'Female';
                }
                $out[$key]['id'] = $d['id'];
                $out[$key]['name'] = date('M j, Y',($d['report_date'])).'('.$gender.')';
                //   break;
            }
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchReportDateByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        print_r($parents);
        die();
        if (!empty($parents)) {
            $rgId = $parents[0];
            $code = $parents[1];

            //die($rgId);

            $data = ArcAccountReports::find()->select(['id', 'report_date'])->where(['project_id'=>$rgId,'status'=>1,'do_delete'=>0, 'deleted'=>0,'code'=>$code])->orderBy(['report_date'=>SORT_DESC])->asArray()->all();
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

    public function actionFetchReferralByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $rgId = $parents[0];
            if($rgId == 98){
                $referral = Referrals::find()->select(['id', 'name'])->asArray()->all();
            }else{
                $referral = Referrals::find()->select(['id', 'name'])->where(['<>','id', 49])->asArray()->all();
            }
            return Json::encode(['output'=>$referral, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionFetchAccountByBank()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (!empty($parents)) {
            $data = trim($parents[0]);
            $out = Lists::find()->where(['list_name' => 'write_off_account'])->andWhere(['label' => $data])->select(['lists.value as id', 'lists.value as name'])->asArray()->all();
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
            $out = Activities::find()->select(['activities.id', 'activities.name'])->where(['product_id'=>$rgId,'activities.status'=>1])->orderBy(['id' => SORT_DESC])->asArray()->all();
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
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
    public function actionBranchprojectsbyid()
    {
        $out = [];
        $out1 = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $branch_id = $parents[0];
               // $branch=StructureHelper::getBranchidfromcode($branch_code);
                $a = StructureHelper::getBranchprojects($branch_id);

                for ($i = 0; $i < count($a); $i++) {
                    $b = $a[$i]['project_id'];
                    $out1 = StructureHelper::getProject($b);
                    array_push($out, $out1[0]);

                }

                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }
    public function actionFetchAppraisalsByProject()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $app=Applications::find()->where(['id'=>$rgId])->one();
            $ids=[];
            if(!empty($app)){
                $sp = 0;
                $app_action = ApplicationActions::find()
                    ->where(['parent_id'=>$rgId])
                    ->all();
                foreach ($app_action as $act){
                    if ($act->action == 'social_appraisal' && $act->status == 1){
                        $sp = 1;
                    }
                }

                foreach ($app_action as $act){
                    $appraisalData = Appraisals::find()->where(['name'=>$act->action])->one();
                    if(!empty($appraisalData) && $appraisalData!=null){
                        if($appraisalData->id == 1 && $sp == 0){

                            array_push($ids,$appraisalData->id);

                        }elseif($appraisalData->id == 1 && $sp == 1){

                        }elseif($appraisalData->id == 2 && $sp == 0){

                        }elseif($appraisalData->id == 2 && $sp == 1){

                            array_push($ids,$appraisalData->id);

                        }elseif($appraisalData->id == 3 && $sp == 0){

                        }elseif ($appraisalData->id == 3 && $sp == 1){

                            array_push($ids,$appraisalData->id);

                        }elseif($appraisalData->id == 4 && $sp == 0){

                        }elseif ($appraisalData->id == 4 && $sp == 1){

                            array_push($ids,$appraisalData->id);

                        }elseif($appraisalData->id == 5 && $sp == 0){

                        }elseif ($appraisalData->id == 5 && $sp == 1){

                            array_push($ids,$appraisalData->id);

                        }elseif ($appraisalData->id == 6 && $sp == 1){
                            array_push($ids,$appraisalData->id);
                        }
                        else{
                            array_push($ids,$appraisalData->id);
                        }
                    }
                }

//                $app_map=ProjectAppraisalsMapping::find()->select(['appraisal_id'])->where(['project_id'=>$app->project_id])->asArray()->all();
//                foreach($app_map as $app_id){
//
//                    if($app_id['appraisal_id'] == 1 && $sp == 0){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }elseif($app_id['appraisal_id'] == 1 && $sp == 1){
//
//                    }elseif($app_id['appraisal_id'] == 2 && $sp == 0){
//
//                    }elseif($app_id['appraisal_id'] == 2 && $sp == 1){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }elseif($app_id['appraisal_id'] == 3 && $sp == 0){
//
//                    }elseif ($app_id['appraisal_id'] == 3 && $sp == 1){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }elseif($app_id['appraisal_id'] == 4 && $sp == 0){
//
//                    }elseif ($app_id['appraisal_id'] == 4 && $sp == 1){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }elseif($app_id['appraisal_id'] == 5 && $sp == 0){
//
//                    }elseif ($app_id['appraisal_id'] == 5 && $sp == 1){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }elseif ($app_id['appraisal_id'] == 6 && $sp == 1){
//                        array_push($ids,$app_id['appraisal_id']);
//                    }
//                    else{
//                        array_push($ids,$app_id['appraisal_id']);
//                    }
//                }
                $appraisals=Appraisals::find()->where(['in','id',$ids])->asArray()->all();

                foreach ($appraisals as $appraisal){
                    $appraisal['name'] =ucwords(str_replace('_',' ',$appraisal['name']));
                    array_push($out,$appraisal);
                }
            }
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFetchAppraisalsAll()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $app=Applications::find()->where(['id'=>$rgId])->one();
            $ids=[];
            if(!empty($app)){

                $app_action = ApplicationActions::find()
                    ->where(['parent_id'=>$rgId])
                    ->all();
                foreach ($app_action as $act){
                    if ($act->action == 'social_appraisal' && $act->status == 1){
                        $sp = 1;
                    }
                }

                foreach ($app_action as $act){
                    $appraisalData = Appraisals::find()->where(['name'=>$act->action])->one();
                    if(!empty($appraisalData) && $appraisalData!=null){
                        array_push($ids,$appraisalData->id);
                    }
                }

                $appraisals=Appraisals::find()->where(['in','id',$ids])->asArray()->all();

                foreach ($appraisals as $appraisal){
                    $appraisal['name'] =ucwords(str_replace('_',' ',$appraisal['name']));
                    array_push($out,$appraisal);
                }
            }
            return Json::encode(['output'=>$out, 'selected'=>'']);
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    public static function getProjectByName($branch)
    {
        return Projects::find(/*['name']*/)->select('id,name')->where(['id' => $branch])->asArray()->all();

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
    public function actionFetchProjectsByArea()
    {
        $out = [];
        $out1 = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $area = $parents[0];
                $a = AwpHelper::getAreaprojects($area);

                foreach ($a as $project) {
                    $b = $project['project_id'];
                    $out1 = self::getProjectByName($b);
                    array_push($out, $out1[0]);
                }
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);

    }
}
