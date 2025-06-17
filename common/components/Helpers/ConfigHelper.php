<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\models\Areas;
use common\models\Branches;
use common\models\ConfigRules;
use common\models\Fields;
use common\models\Teams;

class ConfigHelper
{
    public static function globalConfigs($parent_type = 'global', $parent_id = 0, $project_id = 0){

        //$conf = ConfigRules::find()->select(['parent_type'])->distinct('priority')->orderBy('priority')->asArray()->all();
        $configs = [];
        $global_configs = ConfigRules::find()->where(['parent_type'=>'global'])->all();
        foreach ($global_configs as $global_config)
        {
            $configs[$global_config->group.'_'.$global_config->key] = $global_config->value;
        }

        if($parent_type == 'project') {
            $project_configs = self::ProjectConfigs($parent_id);
            $configs = array_merge($configs, $project_configs);
        }

        /*if($parent_type == 'region_project') {
            $region_configs = self::RegionProjectConfigs($parent_id,$project_id);
            $configs = array_merge($configs, $region_configs);
        }*/

        if($parent_type == 'region') {
            $region_configs = self::RegionConfigs($parent_id,$project_id);
            $configs = array_merge($configs, $region_configs);
        }

        if($parent_type == 'area') {
            $area_configs = self::AreaConfigs($parent_id);
            /*print_r($area_configs);
            die();*/
            $configs = array_merge($configs, $area_configs);
        }

        if($parent_type == 'branch') {
            $branch_configs = self::BranchConfigs($parent_id);
            $configs = array_merge($configs, $branch_configs);
        }

        if($parent_type == 'team') {
            $team_configs = self::TeamConfigs($parent_id);
            $configs = array_merge($configs, $team_configs);
        }

        if($parent_type == 'field') {
            $field_configs = self::FieldConfigs($parent_id);
            $configs = array_merge($configs, $field_configs);
        }

        if($parent_type == 'user') {
            $user_configs = self::UserConfigs($parent_id);
            $configs = array_merge($configs, $user_configs);
        }

       /* print_r($configs);
        die();*/

        return $configs;
    }

    private static function ProjectConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'project', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }
        return $response;
    }

    /*private static function RegionProjectConfigs($parent_id, $project_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'project', 'parent_id'=> $parent_id, 'project_id' => $project_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }
        return $response;
    }*/

    private static function RegionConfigs($parent_id, $project_id = 0)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'region', 'parent_id'=> $parent_id, 'project_id' => $project_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }
        return $response;
    }

    private static function AreaConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'area', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }
        $area = Areas::find()->select(['region_id'])->where(['id' => $parent_id])->one();

        if(isset($area) || !empty($area)) {
            $region_configs = self::RegionConfigs($area->region_id);
            $response = array_merge($response, $region_configs);
        }
        return $response;
    }

    private static function BranchConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'branch', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }

        $branch = Branches::find()->select(['area_id'])->where(['id' => $parent_id])->one();
        if(isset($branch) || !empty($branch)) {
            $area_configs = self::AreaConfigs($branch->area_id);
            $response = array_merge($response, $area_configs);
        }
        return $response;
    }

    private static function TeamConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'team', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }

        $team = Teams::find()->select(['branch_id'])->where(['id' => $parent_id])->one();
        if(isset($team) || !empty($team)) {
            $branch_configs = self::BranchConfigs($team->branch_id);
            $response = array_merge($response, $branch_configs);
        }

        return $response;
    }

    private static function FieldConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'field', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }

        $field = Fields::find()->select(['team_id'])->where(['id' => $parent_id])->one();
        if(isset($field) || !empty($field)) {
            $team_configs = self::TeamConfigs($field->team_id);
            $response = array_merge($response, $team_configs);
        }
        return $response;
    }

    private static function UserConfigs($parent_id)
    {
        $response = [];
        $configs = ConfigRules::find()->where(['parent_type'=>'user', 'parent_id'=> $parent_id])->all();

        foreach ($configs as $config)
        {
            $response[$config->group.'_'.$config->key] = $config->value;
        }
        return $response;
    }

}