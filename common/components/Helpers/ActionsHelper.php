<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\models\ActionsConfigs;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Areas;
use common\models\Branches;
use common\models\ConfigRules;
use common\models\Fields;
use common\models\GroupActions;
use common\models\Lists;
use common\models\LoanActions;
use common\models\LoanTranchesActions;
use common\models\Teams;

class ActionsHelper
{
    public static function insertActions($type,$project_id,$parent_id,$user_id,$pre_check = 0,$product_id=0)
    {
        $action_configs = ActionsConfigs::findOne(['parent_type' => $type,'project_id' => $project_id]);
        if(!isset($action_configs))
        {
            $action_configs = ActionsConfigs::findOne(['parent_type' => $type,'project_id' => 0,'product_id'=>$product_id]);
            if(!isset($action_configs))
            {
                $action_configs = ActionsConfigs::findOne(['parent_type' => $type,'project_id' => 0,'product_id'=>0]);

            }
        }

        $model = ucwords(str_replace('_',' ',$action_configs->parent_table));
        $model = str_replace(' ','',$model);
        $model_class = 'common\models\\' . $model;
        if($pre_check == 0)
        {
            $actions = explode( ',',$action_configs->flow);
            foreach ($actions as $action)
            {
                $model = $model_class::find()->where(['parent_id' => $parent_id, 'action' => $action])->one();
                if(!isset($model))
                {
                    $model = new $model_class();
                    $model->parent_id = $parent_id;
                    $model->user_id = $user_id;
                    $model->action = $action;
                    $model->save();
                }
            }
        }
        else if($pre_check == 1)
        {
            $pre_action_configs = ActionsConfigs::findOne(['sort_order' => ($action_configs->sort_order - 1),'project_id' => $project_id]);
            if(!isset($pre_action_configs))
            {
                $pre_action_configs = ActionsConfigs::findOne(['sort_order' => ($action_configs->sort_order - 1),'project_id' => 0,'product_id'=>$product_id]);
                if(!isset($pre_action_configs))
                {
                    $pre_action_configs = ActionsConfigs::findOne(['sort_order' => ($action_configs->sort_order - 1),'project_id' => 0,'product_id'=>0]);

                }
            }

            $pre_actions_array = explode( ',',$pre_action_configs->flow);
            $pre_action = end($pre_actions_array);
            $pre_model = ucwords(str_replace('_',' ',$pre_action_configs->parent_table));
            $pre_model = str_replace(' ','',$pre_model);
            $pre_model_class = 'common\models\\' . $pre_model;
            $pre_action_model = $pre_model_class::find()->where(['parent_id' => $parent_id, 'action' => $pre_action,'status' => 1])->one();
            if(isset($pre_action_model)) {
                $actions = explode(',', $action_configs->flow);
                foreach ($actions as $action) {
                    $model = $model_class::find()->where(['parent_id' => $parent_id, 'action' => $action])->one();
                    if (!isset($model)) {
                        $model = new $model_class();
                        $model->parent_id = $parent_id;
                        $model->user_id = $user_id;
                        $model->action = $action;
                        $model->save();
                    }
                }
            }
        }

    }

    public static function updateAction($action_type,$parent_id,$action)
    {
        if($action_type == 'application') {
            $action_model = ApplicationActions::findOne(['parent_id' => $parent_id, 'action' => $action]);
        } else if($action_type == 'group') {
            $action_model = GroupActions::findOne(['parent_id' => $parent_id, 'action' => $action]);
        } else if($action_type == 'loan') {
            $action_model = LoanActions::findOne(['parent_id' => $parent_id, 'action' => $action]);
        } else if($action_type == 'tranche') {
            $action_model = LoanTranchesActions::findOne(['parent_id' => $parent_id, 'action' => $action]);
        }
        $action_model->status = 1;
        $action_model->expiry_date = strtotime('+3 months',strtotime( date('Y-m-d H:i:s')));
        if(!$action_model->save())
        {
            $errors = $action_model->getErrors();
            $errors['sync_status'] = false;
            return $errors;
        } else {
            return true;
        }
    }

    public static function getApplicationActionsList(){
        $data_list = Lists::find()->where(['list_name' => 'application_actions_list'])->orderBy('sort_order')->asArray()->all();
        foreach ($data_list as $data) {
            $action_list[$data['value']] = $data['label'];
        }
        return $action_list;
    }

    public static function getGroupActionsList(){
        $data_list = Lists::find()->where(['list_name' => 'group_actions_list'])->orderBy('sort_order')->asArray()->all();
        foreach ($data_list as $data) {
            foreach ($data_list as $data) {
                $action_list[$data['value']] = $data['label'];
            }
        }
        return $action_list;
    }

    public static function getLoanActionsList(){
        $data_list = Lists::find()->where(['list_name' => 'loan_actions_list'])->orderBy('sort_order')->asArray()->all();
        foreach ($data_list as $data) {
            foreach ($data_list as $data) {
                $action_list[$data['value']] = $data['label'];
            }
        }
        return $action_list;
    }

    public static function getHistory($id){
        $history = [];
        $pending_action = '';
        $outer_array = array();
        $unique_array = array();
        $flag = true;
        $family_info = '';
        $application = Applications::findOne(['id'=>$id, 'deleted' => 0]);
        if (isset($application->member)) {
            $data = [];
            $data['action'] = 'Member';
            $data['created_by'] = UsersHelper::getUserName($application->member->created_by)->fullname;
            $data['created_at'] = date('d-M-Y',$application->member->created_at);
            $data['status'] = 'Completed';
            $history[] = $data;
        }

        if (isset($application)) {
            $data = [];
            $data['action'] = 'Application';
            $data['created_by'] = UsersHelper::getUserName($application->created_by)->fullname;
            $data['created_at'] = date('d-M-Y',$application->created_at);
            $data['status'] = 'Completed';
            $history[] = $data;
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'family_member_info'])->one();
            if ($loan_action->status == 0) {
                $family_info = 'Family Member Information';
            } else {
                $data['action'] = 'Family Member Information';
                $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                $data['created_at'] =  date('d-M-Y',$loan_action->created_at);
                $data['status'] = 'Completed';
                $history[] = $data;
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'social_appraisal'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Social Appraisal';
                } else {
                    $data['action'] = 'Social Appraisal';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] = date('d-M-Y', $loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'business_appraisal'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Business Appraisal';
                } else {
                    $data['action'] = 'Business Appraisal';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] = date('d-M-Y', $loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'approved/rejected'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Verification';
                } else {
                    $data['action'] = 'Verification';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] = date('d-M-Y', $loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'group_formation'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Group Formation';
                } else {
                    $data['action'] = 'Group Formation';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] =  date('d-M-Y',$loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }

        }

        /* $application_actions = ApplicationActions::find()->groupBy('action')->where(['parent_id' => $id])->orderBy('created_at')->all();
         foreach ($application_actions as $application_action)
         {
             $data = [];
              if($application_action->status == 0)
              {
                  $pending_action = $application_action->action;
                  break;
              } else {
                  $data['action'] = $application_action->action;
                  $data['created_by'] = UsersHelper::getUserName($application_action->created_by)->fullname;
                  $data['created_at'] =  date('d-M-Y',$application_action->created_at);
                  $data['status'] = 'Completed';
                  $history[] = $data;
              }
         }*/

        if(empty($pending_action)) {
            $data = [];
            if (isset($application->loan)) {
                $data['action'] = 'Lac';
                $data['created_by'] = UsersHelper::getUserName($application->loan->created_by)->fullname;
                $data['created_at'] = date('d-M-Y',$application->loan->created_at);
                $data['status'] = 'Completed';
                $history[] = $data;
            } else {
                $pending_action = 'Lac';
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'cheque_printing'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Cheque Printing';
                } else {
                    $data['action'] = 'Cheque Printing';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] = date('d-M-Y', $loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action)) {
            $data = [];
            if ($application->loan->fund_request_id != 0) {
                $data['action'] = 'Fund Request';
                $data['created_by'] = UsersHelper::getUserName($application->loan->fundRequest->created_by)->fullname;
                $data['created_at'] = date('d-M-Y',$application->loan->fundRequest->created_at);
                $data['status'] = 'Completed';
                $history[] = $data;
            } else {
                $pending_action = 'Fund Request';
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'takaful'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Takaful';
                } else {
                    $data['action'] = 'Takaful';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] = date('d-M-Y', $loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action)) {
            $data = [];
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'disbursement'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Disbursement';
                } else {
                    $data['action'] = 'Disbursement';
                    $data['created_by'] = UsersHelper::getUserName($loan_action->created_by)->fullname;
                    $data['created_at'] =  date('d-M-Y',$loan_action->created_at);
                    $data['status'] = 'Completed';
                    $history[] = $data;
                }
            }
        }

        if(empty($pending_action))
        {
            $pending_action = 'Active Loan';
        }
        if(!empty($family_info))
        {
            $pending_action = $family_info. ' & '. $pending_action;
        }
        $history[] = ['action' => $pending_action,'status' => 'Active'];
        return array_reverse($history);

        /*foreach ($history as $key => $value) {
            //$data = $value;
            $data['action'] = $value['action'];
            $data['created_by'] = $value['created_by'];
            $inner_array = array();
            $field_name = $value['created_at'];

            if (!in_array($value['created_at'], $unique_array)) {
                array_push($unique_array, $field_name);
                unset($value['created_at']);
                array_push($inner_array, $data);
                $arr = [];
                $arr['key'] = $field_name;
                $arr['history'][] = $data;
                $outer_array[$field_name] = $arr;
            } else {
                unset($value['created_at']);
                array_push($outer_array[$field_name]['history'], $data);

            }
        }
        foreach ($outer_array as $k => $arr) {

            if ($k == date('d-F-Y')) {
                $flag = false;
                $arr['history'][] = ['action' => $pending_action];
            }
            $history_data[] = $arr;
        }

        if($flag) {
        $d['key'] = date('d-F-Y');
        $d['history'] = ['action' => $pending_action];
        $history_data[] = $d;
        }*/
        //return $history_data;
    }

    public static function getPendingAction($id){
        $pending_action = '';
        $family_info = '';
        $application = Applications::findOne(['id'=>$id, 'deleted' => 0]);

        if(empty($pending_action)) {
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'family_member_info'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $family_info = 'Family Member Information';
                }
            }
        }

        if(empty($pending_action)) {
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'social_appraisal'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Social Appraisal';
                }
            }
        }

        if(empty($pending_action)) {
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'business_appraisal'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Business Appraisal';
                }
            }
        }

        if(empty($pending_action)) {
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'approved/rejected'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Verification';
                }
            }
        }

        if(empty($pending_action)) {
            $loan_action = ApplicationActions::find()->where(['parent_id' => $id])->andWhere(['action' => 'group_formation'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Group Formation';
                }
            }
        }

        if(empty($pending_action)) {
            if (!isset($application->loan)) {
                $pending_action = 'Lac';
            }
        }

        if(empty($pending_action)) {
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'cheque_printing'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Cheque Printing';
                }
            }
        }

        if(empty($pending_action)) {
            if ($application->loan->fund_request_id == 0) {
                $pending_action = 'Fund Request';
            }
        }

        if(empty($pending_action)) {
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'takaful'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Takaful';
                }
            }
        }

        if(empty($pending_action)) {
            $loan_action = LoanActions::find()->where(['parent_id' => $application->loan->id])->andWhere(['action' => 'disbursement'])->one();
            if(isset($loan_action)) {
                if ($loan_action->status == 0) {
                    $pending_action = 'Disbursement';
                }
            }
        }

        if(empty($pending_action))
        {
            $pending_action = 'Active Loan';
        }
        if(!empty($family_info))
        {
            $pending_action = $family_info. ' & '. $pending_action;
        }

        return $pending_action;
    }
}