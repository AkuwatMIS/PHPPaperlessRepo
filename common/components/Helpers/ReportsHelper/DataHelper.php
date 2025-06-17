<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 15/08/17
 * Time: 11:36 PM
 */

namespace common\components\Helpers\ReportsHelper;

use common\models\UserProjectsMapping;
use common\models\Branches;
use common\models\Projects;
use Yii;

class DataHelper {

    public static function getRegions($division_id){
        if(isset($division_id) && $division_id != 0){
            $regions_array = array();
            $regions = Branches::find()->select(['region_id'])->where(['cr_division_id'=>$division_id])->asArray()->all();
            foreach ($regions as $key => $value){
                //print_r($value);
                $regions_array[] = $value['id'];
            }
            return $regions_array;
        }
        return false;
    }

    public static function getAreas($region_id){
        if(isset($region_id) && $region_id != 0){
            $areas_array = array();
            $areas = Branches::find()->select(['area_id'])->distinct()->where(['region_id'=>$region_id])->asArray()->all();
            foreach ($areas as $key => $value){
                //print_r($value);
                $areas_array[] = $value['area_id'];
            }
            return $areas_array;
        }
        return false;
    }
    public static function getBranchesByArea($area_id){
        if(isset($area_id) && $area_id != 0){
            $branches_array = array();
            $branches = Branches::find()->select(['id'])->where(['area_id'=>$area_id])->asArray()->all();
            foreach ($branches as $key => $value){
                //print_r($value);
                $branches_array[] = $value['id'];
            }
            return $branches_array;
        }
        return false;
    }
    public static function getBranchesByRegion($region_id){
        if(isset($region_id) && $region_id != 0){
            $branches_array = array();
            $branches = Branches::find()->select(['id'])->where(['region_id'=>$region_id])->asArray()->all();
            foreach ($branches as $key => $value){
                //print_r($value);
                $branches_array[] = $value['id'];
            }
            return $branches_array;
        }
        return false;
    }

    public static function getProjects($user){
        //if(isset($project_id) && $project_id != 0){
            $projects_array = array();
            //$projects = UserProjectsMapping::find()->where(['user_id'=>$user->id])->asArray()->all();
            $projects = Projects::find()->asArray()->all();
            /*print_r($projects);
            die();*/
            foreach ($projects as $key => $value){
                //print_r($value);
                $projects_array[] = $value['id'];
            }
            return $projects_array;
        //}
    }

} 