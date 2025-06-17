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
use common\models\Teams;

class ActionsHelper
{
    public static function insertActions($type,$project_id,$parent_id,$user_id,$pre_check = 0)
    {
        $action_configs = ActionsConfigs::findOne(['parent_type' => $type,'project_id' => $project_id]);
        if(!isset($action_configs))
        {
            $action_configs = ActionsConfigs::findOne(['parent_type' => $type,'project_id' => 0]);
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
                $pre_action_configs = ActionsConfigs::findOne(['sort_order' => ($action_configs->sort_order - 1),'project_id' => 0]);
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
}