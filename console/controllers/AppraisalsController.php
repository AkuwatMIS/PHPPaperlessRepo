<?php


namespace console\controllers;


use common\models\ActionsConfigs;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsAgriculture;
use common\models\AppraisalsBusiness;
use common\models\AppraisalsEmergency;
use common\models\AppraisalsHousing;
use common\models\SocialAppraisal;
use Yii;
use yii\console\Controller;

class AppraisalsController extends Controller
{

    public function actionAddAppraisal()
    {
        $applications = Applications::find()
            ->where(['group_id'=>0])
            ->andWhere(['deleted'=>0])
            ->andWhere(['status'=> ['approved','pending']])
            ->all();

        foreach ($applications as $app) {
            $action_configs = ActionsConfigs::findOne(['parent_type' => 'application','project_id' => $app->project_id]);
            if(!isset($action_configs))
            {
                $action_configs = ActionsConfigs::findOne(['parent_type' => 'application','project_id' => 0,'product_id'=>$app->product_id]);
                if(!isset($action_configs))
                {
                    $action_configs = ActionsConfigs::findOne(['parent_type' => 'application','project_id' => 0,'product_id'=>0]);

                }
            }
            $actions = explode( ',',$action_configs->flow);
            if($actions){
                foreach ($actions as $action) {
                    if ($action == 'family_member_info') {
                        $familyActions = ApplicationActions::find()
                            ->where(['parent_id' => $app->id])
                            ->andWhere(['action' => 'family_member_info'])
                            ->one();
                        if(!$familyActions){
                            $family_member_info = new ApplicationActions();
                            $family_member_info->parent_id = $app->id;
                            $family_member_info->user_id = $app->created_by;
                            $family_member_info->action = $action;
                            $family_member_info->created_by = $app->created_by;
                            $family_member_info->save();
                        }else{
                            $familyActions->status = 0;
                            $familyActions->save(false);
                        }
                    } elseif ($action == 'social_appraisal') {
                        $socialAppraisals = SocialAppraisal::find()->where(['application_id' => $app->id])->one();
                        if ($socialAppraisals) {
                            $applicationActions = ApplicationActions::find()
                                ->where(['parent_id' => $socialAppraisals->application_id])
                                ->andWhere(['action' => $action])
                                ->one();
                            if ($applicationActions) {

                            } else {
                                $socialAppAction = new ApplicationActions();
                                $socialAppAction->parent_id = $app->id;
                                $socialAppAction->user_id = $app->created_by;
                                $socialAppAction->status = 1;
                                $socialAppAction->action = $action;
                                $socialAppAction->created_by = $app->created_by;
                                $socialAppAction->save();
                            }
                        } else {
                            $socialAppAction = new ApplicationActions();
                            $socialAppAction->parent_id = $app->id;
                            $socialAppAction->user_id = $app->created_by;
                            $socialAppAction->action = $action;
                            $socialAppAction->created_by = $app->created_by;
                            $socialAppAction->save();
                        }
                    } elseif ($action == 'business_appraisal') {
                        $businessAppraisals = AppraisalsBusiness::find()->where(['application_id' => $app->id])->one();
                        if ($businessAppraisals) {
                            $applicationActions = ApplicationActions::find()
                                ->where(['parent_id' => $businessAppraisals->application_id])
                                ->andWhere(['action' => $action])
                                ->one();
                            if ($applicationActions) {

                            } else {
                                $business_appraisal = new ApplicationActions();
                                $business_appraisal->parent_id = $app->id;
                                $business_appraisal->user_id = $app->created_by;
                                $business_appraisal->status = 1;
                                $business_appraisal->action = $action;
                                $business_appraisal->created_by = $app->created_by;
                                $business_appraisal->save();
                            }
                        } else {
                            $business_appraisal_new = new ApplicationActions();
                            $business_appraisal_new->parent_id = $app->id;
                            $business_appraisal_new->user_id = $app->created_by;
                            $business_appraisal_new->action = $action;
                            $business_appraisal_new->created_by = $app->created_by;
                            $business_appraisal_new->save();
                        }
                    } elseif ($action == 'agriculture_appraisal') {
                        $agriAppraisals = AppraisalsAgriculture::find()->where(['application_id' => $app->id])->one();
                        if ($agriAppraisals) {
                            $applicationActions = ApplicationActions::find()
                                ->where(['parent_id' => $agriAppraisals->application_id])
                                ->andWhere(['action' => $action])
                                ->one();
                            if ($applicationActions) {

                            } else {
                                $agriculture_appraisal = new ApplicationActions();
                                $agriculture_appraisal->parent_id = $app->id;
                                $agriculture_appraisal->user_id = $app->created_by;
                                $agriculture_appraisal->status = 1;
                                $agriculture_appraisal->action = $action;
                                $agriculture_appraisal->created_by = $app->created_by;
                                $agriculture_appraisal->save();
                            }
                        } else {
                            $agriculture_appraisal_new = new ApplicationActions();
                            $agriculture_appraisal_new->parent_id = $app->id;
                            $agriculture_appraisal_new->user_id = $app->created_by;
                            $agriculture_appraisal_new->action = $action;
                            $agriculture_appraisal_new->created_by = $app->created_by;
                            $agriculture_appraisal_new->save();
                        }
                    } elseif ($action == 'housing_appraisal') {
                        $housingAppraisals = AppraisalsHousing::find()->where(['application_id' => $app->id])->one();
                        if ($housingAppraisals) {
                            $applicationActions = ApplicationActions::find()
                                ->where(['parent_id' => $housingAppraisals->application_id])
                                ->andWhere(['action' => $action])
                                ->one();
                            if ($applicationActions) {

                            } else {
                                $housing_appraisal = new ApplicationActions();
                                $housing_appraisal->parent_id = $app->id;
                                $housing_appraisal->user_id = $app->created_by;
                                $housing_appraisal->status = 1;
                                $housing_appraisal->action = $action;
                                $housing_appraisal->created_by = $app->created_by;
                                $housing_appraisal->save();
                            }
                        } else {
                            $housing_appraisal_new = new ApplicationActions();
                            $housing_appraisal_new->parent_id = $app->id;
                            $housing_appraisal_new->user_id = $app->created_by;
                            $housing_appraisal_new->action = $action;
                            $housing_appraisal_new->created_by = $app->created_by;
                            $housing_appraisal_new->save();
                        }
                    }
                }
                $check_approved_reject_action = ApplicationActions::find()->where(['parent_id' => $app->id])->andWhere(['action' => 'approved/rejected'])->one();
                if (!$check_approved_reject_action) {
                    $approved_rejected_appraisal = new ApplicationActions();
                    $approved_rejected_appraisal->parent_id = $app->id;
                    $approved_rejected_appraisal->user_id = $app->created_by;
                    $approved_rejected_appraisal->action = 'approved/rejected';
                    $approved_rejected_appraisal->created_by = $app->created_by;
                    $approved_rejected_appraisal->save();
                }
                $group_formation_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'group_formation'])->one();
                if(!$group_formation_action){
                    $group_formation_appraisal = New ApplicationActions();
                    $group_formation_appraisal->parent_id = $app->id;
                    $group_formation_appraisal->user_id = $app->created_by;
                    $group_formation_appraisal->expiry_date = 1609372800;
                    $group_formation_appraisal->action = 'group_formation';
                    $group_formation_appraisal->created_by = $app->created_by;
                    $group_formation_appraisal->save();
                }
            }
            echo $app->id;
            echo '----';
        }
    }


    public function actionAddAppraisalHousing()
    {
        $projects = [52,61,62];
        $applications = Applications::find()
            ->where(['in','project_id',$projects])
            ->andWhere(['<>','group_id', 0])
            ->andWhere(['deleted'=>0])
            ->andWhere(['status'=>'approved'])
            ->all();
        foreach ($applications as $app) {
            $check_family_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'family_member_info'])->one();
            if(!$check_family_action){
                $family_member_info = New ApplicationActions();
                $family_member_info->parent_id = $app->id;
                $family_member_info->user_id = $app->created_by;
                $family_member_info->action = 'family_member_info';
                $family_member_info->created_by = $app->created_by;
                $family_member_info->save();
            }
            $group_formation_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'group_formation'])->one();
            if(!$group_formation_action){
                $group_formation_appraisal = New ApplicationActions();
                $group_formation_appraisal->parent_id = $app->id;
                $group_formation_appraisal->user_id = $app->created_by;
                $group_formation_appraisal->status = 1;
                $group_formation_appraisal->expiry_date = 1609372800;
                $group_formation_appraisal->action = 'group_formation';
                $group_formation_appraisal->created_by = $app->created_by;
                $group_formation_appraisal->save();
            }
            $check_approved_reject_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'approved/rejected'])->one();
            if(!$check_approved_reject_action) {
                $approved_rejected_appraisal = New ApplicationActions();
                $approved_rejected_appraisal->parent_id = $app->id;
                $approved_rejected_appraisal->user_id = $app->created_by;
                $approved_rejected_appraisal->status = 1;
                $approved_rejected_appraisal->action = 'approved/rejected';
                $approved_rejected_appraisal->created_by = $app->created_by;
                $approved_rejected_appraisal->save();
            }


            $socialAppraisals = SocialAppraisal::find()->where(['application_id'=>$app->id])->one();
            if($socialAppraisals){
                $applicationActions = ApplicationActions::find()
                    ->where(['parent_id'=>$socialAppraisals->application_id])
                    ->andWhere(['action'=>'social_appraisal'])
                    ->one();
                if($applicationActions){

                }else{
                    $socialAppAction = New ApplicationActions();
                    $socialAppAction->parent_id = $app->id;
                    $socialAppAction->user_id = $app->created_by;
                    $socialAppAction->status = 1;
                    $socialAppAction->action = 'social_appraisal';
                    $socialAppAction->created_by = $app->created_by;
                    $socialAppAction->save();
                }
            }

            $housingAppraisals = AppraisalsHousing::find()->where(['application_id'=>$app->id])->one();
            if($housingAppraisals){
                $applicationActions = ApplicationActions::find()
                    ->where(['parent_id'=>$housingAppraisals->application_id])
                    ->andWhere(['action'=>'housing_appraisal'])
                    ->one();
                if($applicationActions){

                }else{
                    $housing_appraisal = New ApplicationActions();
                    $housing_appraisal->parent_id = $app->id;
                    $housing_appraisal->user_id = $app->created_by;
                    $housing_appraisal->status = 1;
                    $housing_appraisal->action = 'housing_appraisal';
                    $housing_appraisal->created_by = $app->created_by;
                    $housing_appraisal->save();
                }
            }

            echo $app->id;
            echo '----';
        }
    }


    public function actionAddAppraisalEmergency()
    {
        $applications = Applications::find()
            ->where(['project_id'=>60])
            ->andWhere(['deleted'=>0])
            ->andWhere(['status'=> ['approved','pending']])
            ->all();

        foreach ($applications as $app) {
            $family_member_info = New ApplicationActions();
            $family_member_info->parent_id = $app->id;
            $family_member_info->user_id = $app->created_by;
            $family_member_info->action = 'family_member_info';
            $family_member_info->created_by = $app->created_by;
            $family_member_info->save();

            if($app->group_id != 0){
                $check_approved_reject_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'approved/rejected'])->one();
                if(!$check_approved_reject_action) {
                    $approved_rejected_appraisal = New ApplicationActions();
                    $approved_rejected_appraisal->parent_id = $app->id;
                    $approved_rejected_appraisal->user_id = $app->created_by;
                    $approved_rejected_appraisal->status = 1;
                    $approved_rejected_appraisal->action = 'approved/rejected';
                    $approved_rejected_appraisal->created_by = $app->created_by;
                    $approved_rejected_appraisal->save();
                }

                $group_formation_action = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'group_formation'])->one();
                if(!$group_formation_action){
                    $group_formation_appraisal = New ApplicationActions();
                    $group_formation_appraisal->parent_id = $app->id;
                    $group_formation_appraisal->user_id = $app->created_by;
                    $group_formation_appraisal->status = 1;
                    $group_formation_appraisal->expiry_date = 1609372800;
                    $group_formation_appraisal->action = 'group_formation';
                    $group_formation_appraisal->created_by = $app->created_by;
                    $group_formation_appraisal->save();
                }
            }

            $emergencyAppraisals = AppraisalsEmergency::find()->where(['application_id'=>$app->id])->one();
            if($emergencyAppraisals && $app->group_id != 0){
                $applicationActions = ApplicationActions::find()
                    ->where(['parent_id'=>$emergencyAppraisals->application_id])
                    ->andWhere(['action'=>''])
                    ->one();
                if($applicationActions){

                }else{
                    $emergency_appraisal = New ApplicationActions();
                    $emergency_appraisal->parent_id = $app->id;
                    $emergency_appraisal->user_id = $app->created_by;
                    $emergency_appraisal->status = 1;
                    $emergency_appraisal->action = 'emergency_appraisal';
                    $emergency_appraisal->created_by = $app->created_by;
                    $emergency_appraisal->save();
                }
            }else{
                $emergency_appraisal_new = New ApplicationActions();
                $emergency_appraisal_new->parent_id = $app->id;
                $emergency_appraisal_new->user_id = $app->created_by;
                $emergency_appraisal_new->action = 'emergency_appraisal';
                $emergency_appraisal_new->created_by = $app->created_by;
                $emergency_appraisal_new->save();
            }

            echo $app->id;
            echo '----';
        }
    }


    public function actionApprovedReject(){

        $array=[
            '4725423'
        ];

        $applications = Applications::find()
            ->where(['id'=>$app_id_list])
            ->andWhere(['deleted'=>0])
            ->all();
            foreach ($applications as $app) {
                if(count($applications) > 0){
                    $checkApproveReject   = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'approved/rejected'])->all();
                    if(count($checkApproveReject)>1){
                        \Yii::$app
                            ->db
                            ->createCommand()
                            ->delete('application_actions', ['id' => $checkApproveReject[0]->id])
                            ->execute();
                    }
//                    $family_member_info_appraisal = new ApplicationActions();
//                    $family_member_info_appraisal->parent_id =$app->id;
//                    $family_member_info_appraisal->user_id =$app->created_by;
//                    $family_member_info_appraisal->action ='family_member_info';
//                    $family_member_info_appraisal->status =1;
//                    $family_member_info_appraisal->created_by =$app->created_by;
//                    $family_member_info_appraisal->created_at =$app->created_at;
//                    if($family_member_info_appraisal->save()){
//                        $social_appraisal_appraisal = new ApplicationActions();
//                        $social_appraisal_appraisal->parent_id =$app->id;
//                        $social_appraisal_appraisal->user_id =$app->created_by;
//                        $social_appraisal_appraisal->action ='social_appraisal';
//                        $social_appraisal_appraisal->status =1;
//                        $social_appraisal_appraisal->created_by =$app->created_by;
//                        $social_appraisal_appraisal->created_at =$app->created_at;
//                        if($social_appraisal_appraisal->save()){
//                            $business_appraisal_appraisal = new ApplicationActions();
//                            $business_appraisal_appraisal->parent_id =$app->id;
//                            $business_appraisal_appraisal->user_id =$app->created_by;
//                            $business_appraisal_appraisal->action ='business_appraisal';
//                            $business_appraisal_appraisal->status =1;
//                            $business_appraisal_appraisal->created_by =$app->created_by;
//                            $business_appraisal_appraisal->created_at =$app->created_at;
//                            if($business_appraisal_appraisal->save()){
//                                $approved_rejected_appraisal = new ApplicationActions();
//                                $approved_rejected_appraisal->parent_id =$app->id;
//                                $approved_rejected_appraisal->user_id =$app->created_by;
//                                $approved_rejected_appraisal->action ='approved/rejected';
//                                $approved_rejected_appraisal->status =0;
//                                $approved_rejected_appraisal->created_by =$app->created_by;
//                                $approved_rejected_appraisal->created_at =$app->created_at;
//                                $approved_rejected_appraisal->save();
//                            }
//                        }
//
//                    }

                echo $app->id;
                echo '----';
            }
        }
    }


    public function actionDeleteAppraisalExtra()
    {
        $array=[29,25,44];
        $applications = Applications::find()
            ->where(['region_id'=>$array])
            ->andWhere(['between','application_date',1593561600 , 1605186838])
            ->andWhere(['deleted'=>0])
            ->all();
        if(count($applications) > 0){
            foreach ($applications as $app) {

                $checkFamily          = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'family_member_info'])->all();
                $checkSocial          = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'social_appraisal'])->all();
                $checkBusiness        = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'business_appraisal'])->all();
                $checkApproveReject   = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'approved/rejected'])->all();
                $checkGroupFormation  = ApplicationActions::find()->where(['parent_id'=>$app->id])->andWhere(['action'=>'group_formation'])->all();

                if(count($checkFamily)>0){
                    if(count($checkFamily)>1){
                        foreach ($checkFamily as $checkF){
                            \Yii::$app
                                ->db
                                ->createCommand()
                                ->delete('application_actions', ['id' => $checkF->id])
                                ->execute();
                        }
                        $family_member_info_appraisal = new ApplicationActions();
                        $family_member_info_appraisal->parent_id =$app->id;
                        $family_member_info_appraisal->user_id =$app->created_by;
                        $family_member_info_appraisal->action ='family_member_info';
                        $family_member_info_appraisal->status =1;
                        $family_member_info_appraisal->created_by =$app->created_by;
                        $family_member_info_appraisal->created_at =$app->created_at;
                        $family_member_info_appraisal->save();

                    }
                }

                if(count($checkSocial)>0){
                    if(count($checkSocial)>1){
                        foreach ($checkSocial as $checkS){
                            \Yii::$app
                                ->db
                                ->createCommand()
                                ->delete('application_actions', ['id' => $checkS->id])
                                ->execute();
                        }
                        $social_appraisal_appraisal = new ApplicationActions();
                        $social_appraisal_appraisal->parent_id =$app->id;
                        $social_appraisal_appraisal->user_id =$app->created_by;
                        $social_appraisal_appraisal->action ='social_appraisal';
                        $social_appraisal_appraisal->status =1;
                        $social_appraisal_appraisal->created_by =$app->created_by;
                        $social_appraisal_appraisal->created_at =$app->created_at;
                        $social_appraisal_appraisal->save();
                    }
                }

                if(count($checkBusiness)>0){
                    if(count($checkBusiness)>1){
                        foreach ($checkBusiness as $checkB){
                            \Yii::$app
                                ->db
                                ->createCommand()
                                ->delete('application_actions', ['id' => $checkB->id])
                                ->execute();
                        }
                        $business_appraisal_appraisal = new ApplicationActions();
                        $business_appraisal_appraisal->parent_id =$app->id;
                        $business_appraisal_appraisal->user_id =$app->created_by;
                        $business_appraisal_appraisal->action ='business_appraisal';
                        $business_appraisal_appraisal->status =1;
                        $business_appraisal_appraisal->created_by =$app->created_by;
                        $business_appraisal_appraisal->created_at =$app->created_at;
                        $business_appraisal_appraisal->save();
                    }
                }

                if(count($checkApproveReject)>0){
                    if(count($checkApproveReject)>1){
                        if(count($checkApproveReject)==2){
                            $checkOne = ApplicationActions::find()
                                ->where(['parent_id'=>$app->id])
                                ->andWhere(['status'=>0])
                                ->andWhere(['action'=>'approved/rejected'])->one();
                            if($checkOne){
                                \Yii::$app
                                    ->db
                                    ->createCommand()
                                    ->delete('application_actions', ['id' => $checkOne->id])
                                    ->execute();
                            }else{
                                $checkTwo = ApplicationActions::find()
                                    ->where(['parent_id'=>$app->id])
                                    ->andWhere(['status'=>1])
                                    ->andWhere(['action'=>'approved/rejected'])->one();
                                if($checkTwo){
                                    \Yii::$app
                                        ->db
                                        ->createCommand()
                                        ->delete('application_actions', ['id' => $checkTwo->id])
                                        ->execute();
                                }

                            }

                        }else{
                            foreach ($checkApproveReject as $checkAROne){
                                \Yii::$app
                                    ->db
                                    ->createCommand()
                                    ->delete('application_actions', ['id' => $checkAROne->id])
                                    ->execute();
                            }
                            $approved_rejected_appraisal = new ApplicationActions();
                            $approved_rejected_appraisal->parent_id =$app->id;
                            $approved_rejected_appraisal->user_id =$app->created_by;
                            $approved_rejected_appraisal->action ='approved/rejected';
                            $approved_rejected_appraisal->status =0;
                            $approved_rejected_appraisal->created_by =$app->created_by;
                            $approved_rejected_appraisal->created_at =$app->created_at;
                            $approved_rejected_appraisal->save();
                        }
                    }
                }else{
                    if(count($checkFamily)>0 && count($checkSocial)>0 && count($checkBusiness)>0){
                        $approved_rejected_appraisal = new ApplicationActions();
                        $approved_rejected_appraisal->parent_id =$app->id;
                        $approved_rejected_appraisal->user_id =$app->created_by;
                        $approved_rejected_appraisal->action ='approved/rejected';
                        $approved_rejected_appraisal->status =0;
                        $approved_rejected_appraisal->created_by =$app->created_by;
                        $approved_rejected_appraisal->created_at =$app->created_at;
                        $approved_rejected_appraisal->save();
                    }

                }

                if(count($checkGroupFormation)>0){
                    if(count($checkGroupFormation)>1){
                        if(count($checkGroupFormation)==2){
                            $checkGOne = ApplicationActions::find()
                                ->where(['parent_id'=>$app->id])
                                ->andWhere(['status'=>0])
                                ->andWhere(['action'=>'group_formation'])->one();
                            if($checkGOne){
                                \Yii::$app
                                    ->db
                                    ->createCommand()
                                    ->delete('application_actions', ['id' => $checkGOne->id])
                                    ->execute();
                            }else{
                                $checkGTwo = ApplicationActions::find()
                                    ->where(['parent_id'=>$app->id])
                                    ->andWhere(['status'=>1])
                                    ->andWhere(['action'=>'group_formation'])->one();
                                if($checkGTwo){
                                    \Yii::$app
                                        ->db
                                        ->createCommand()
                                        ->delete('application_actions', ['id' => $checkGTwo->id])
                                        ->execute();
                                }

                            }

                        }
                    }
                }

                echo $app->id;
                echo '----';
            }
        }
    }

}
