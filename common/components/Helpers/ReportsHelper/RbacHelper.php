<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers\ReportsHelper;

use Yii;

class RbacHelper{

    static public function apiRbacNetwork($user){
        $data_array = array();
        $region_array = array();
        $area_array = array();
        $branch_array = array();
        $project_array = array();
        if($user){

            $desig = $user->designation->code;
            switch ($desig) {
                case 'ADMIN'://administrator
                    //echo "Your favorite color is red!";
                    break;
                case 'CEO'://CEO
                    break;
                case 'AMO'://AMO
                    break;
                case 'COO'://COO
                    break;
                case 'CCO'://CCO
                    break;
                case 'DIR'://DIR
                    break;
                case 'CFO'://CFO
                    break;
                case 'DM'://DM
                    break;
                case 'CS'://CS
                    break;
                case 'ACCM'://ACCM
                    break;
                case 'AACCM'://AACCM
                    break;
                case 'CAD'://CAD
                    break;
                case 'CMD'://CMD
                    break;
                case 'CIA'://CIA
                    break;
                case 'CTO'://CTO
                    break;
                case 'CTD'://CTD
                    break;
                case 'CHR'://CHR
                    break;
                case 'IAD'://IAD
                    break;
                case 'BOD'://BOD
                    break;
                case 'PM'://PM
                    $project_array = DataHelper::getProjects($user);
                    break;
                case 'RM'://RM
                    $region_array[] = $user->region->obj_id;
                    $area_array = DataHelper::getAreas($user->region->obj_id);
                    $branch_array = DataHelper::getBranchesByRegion($user->region->obj_id);
                    break;
                case 'RA'://RA
                    $region_array[] = $user->region->obj_id;
                    $area_array = DataHelper::getAreas($user->region->obj_id);
                    $branch_array = DataHelper::getBranchesByRegion($user->region->obj_id);
                    break;
                case 'RC'://RC
                    $region_array[] = $user->region->obj_id;
                    $area_array = DataHelper::getAreas($user->region->obj_id);
                    $branch_array = DataHelper::getBranchesByRegion($user->region->obj_id);
                    break;
                case 'AA'://AA
                    $region_array[] = $user->region->obj_id;
                    $area_array[] = $user->area->obj_id;
                    $branch_array = DataHelper::getBranchesByArea($user->area->obj_id);
                    break;
                case 'DEO'://DEO
                    $region_array[] = $user->region->obj_id;
                    $area_array[] = $user->area->obj_id;
                    $branch_array = DataHelper::getBranchesByArea($user->area->obj_id);
                    break;
                case 'AM'://AM
                    $region_array[] = $user->region->obj_id;
                    $area_array[] = $user->area->obj_id;
                    $branch_array = DataHelper::getBranchesByArea($user->area->obj_id);
                    break;
                case 'BM'://BM
                    $region_array[] = $user->region->obj_id;
                    $area_array[] = $user->area->obj_id;
                    $branch_array[] = $user->branch->obj_id;
                    break;
                default:
                    echo "You are not authorized";
            }
        }
        $data_array['region_array'] = $region_array;
        $data_array['area_array'] = $area_array;
        $data_array['branch_array'] = $branch_array;
        $data_array['project_array'] = $project_array;

        return $data_array;
    }

    public  static function searchRegionWiseFiltersOnBranch($query,$pattern)
    {
        $user_areas = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_projects = \common\models\UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_branches = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_regions = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match($pattern,$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->obj_id;
                    }
                    if (!$user_branches) {
                        $branch_ids = 0;
                    }
                    return $query->andFilterWhere(['in','branches.id',$branch_ids]);
                }
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $area_ids[] = $user_area->obj_id;
                    }
                    if(!$user_areas)
                    {
                        $area_ids = 0;
                    }
                    return $query->andFilterWhere(['in','branches.area_id',$area_ids]);
                }
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $region_ids[] = $user_region->obj_id;
                    }
                    if(!$user_regions)
                    {
                        $region_ids = 0;
                    }
                    return $query->andFilterWhere(['in','branches.region_id',$region_ids]);
                }
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $project_ids[] = $user_project->project_id;
                    }
                    if(!$user_projects)
                    {
                        $project_ids = 0;
                    }
                    return $query->andFilterWhere(['in','projects.id',$project_ids]);
                }
            }
        }
    }

}