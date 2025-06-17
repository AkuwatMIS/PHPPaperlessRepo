<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 1/5/2018
 * Time: 6:49 PM
 */

namespace common\components\Helpers;


use common\models\Areas;
use common\models\BankAccounts;
use common\models\BranchAccount;
use common\models\Branches;
use common\models\Regions;
use yii\helpers\ArrayHelper;

class CashinhandHelper
{

public static function getBranchname($id){
    $branch= Branches::find()->select('name')->where (['id'=>$id])->asArray()->one();
    return $branch['name'];
}
    public static function getBranch($id){
        $branch= Branches::find()->where (['id'=>$id])->asArray()->one();
        return $branch;
    }
    public static function getBranchaccounts($branch){
        return BranchAccount::find(['account_id'])/*->select('project_id')*/->where (['branch_id'=>$branch])->asArray()->all();

    }
    public static function getAccountname($account_id,$type){
        return BankAccounts::find(/*['name']*/)->select('id,account_no as name')->where (['id'=>$account_id,'account_type_id'=>$type])->asArray()->all();

    }
    public static function getAccountnumber($account_id){
        return BankAccounts::find(/*['name']*/)->select('account_no')->where (['id'=>$account_id])->asArray()->one();

    }
    public static function getAreaofbranch($branch_id){
        $branch= Branches::find()->select(['area_id'])->where(['id' => $branch_id])->one();
        return $branch['area_id'];


    }
    public static function getRegionofbranch($branch_id){
        $branch=Branches::find()->select(['region_id'])->where(['id' => $branch_id])->one();
 return $branch['region_id'];
    }

    public static function getRegionId(){
        $reigon=Regions::find()->all();
        return $reigon;

    }

    public static function getAreaList($region_id){
        $area = Areas::find()->where(['region_id'=>$region_id])->all();
        return $area;

    }
    public static function getBranchList($area_id){
        $branch = Branches::find()->where(['area_id'=>$area_id])->all();
        return $branch;

    }

    public static function parse_json_cih($cih){
        $big_array  = [];

        if(empty($cih)){
            return json_encode($big_array);
        }
        $result = array();

        $i = 1;
        foreach($cih as $p){
            $p['id'] = $i;
            $result[$i]['pr']['project_id'] = $p['id'];
            $result[$i]['pd'] = $p;
            $i++;
        }

        $branches = ArrayHelper::map(LoanHelper::getBranches(), 'id', 'name');
        $areas = ArrayHelper::map(LoanHelper::getAreas(), 'id', 'name');
        $regions = ArrayHelper::map(LoanHelper::getRegions(), 'id', 'name');
        $temp           = [];
        $old_region_id  = 0;
        $old_area_id    = 0;
        $old_branch_id  = 0;
        end($result);
        $last_key = key($result);
        /*echo '<pre>';
        print_r($result);
        print_r($last_key);
        die();*/
        foreach ($result as $key => $one){
            $pd = $one['pd'];

            if($old_area_id==0){
                $old_region_id = $pd['region_id'];
                $old_area_id = $pd['area_id'];
                $old_branch_id = $pd['branch_id'];
            }

            if($pd['area_id']==$old_area_id){
                $temp[] = $pd;

                if($last_key == $key){
                    $big_array[$old_region_id][$old_area_id] = $temp;
                }
                continue;
            }else{
                $big_array[$old_region_id][$old_area_id] = $temp;
                $old_region_id  = $pd['region_id'];
                $old_area_id    = $pd['area_id'];
                $old_branch_id  = $pd['branch_id'];

                unset($temp);
                $temp[] = $pd;
            }
        }
        /*echo '<pre>';
        print_r($big_array);
        die();*/
        $temp_sum       = array('id'=>0,'deposited'=>0,'cih'=>0);
        $new_big_array  = array();
        $grand_sum      = $temp_sum;
        $grand_sum['id'] = 100;
        $grand_sum['name'] = 'Grand Total';

        $count_region  =   0;
        /*print_r($branches);
        die();*/
        foreach($big_array as $key => $region){
            $count_region++;
            $region_sum = $temp_sum;
            $region_sum['id'] = $key;
            //$region_sum['name'] = 'Region - '.$key;
            $region_sum['name'] = isset($regions[$key]) ? ($regions[$key]) : '';
            /*$region_code = Regions::find()->where(['id'=>$key])->asArray()->one();
            $region_sum['branch_code'] = isset($region_code['code']) ? ($region_code['code']) : '';*/

            $count_area = 0;
            $count  =   0;
            foreach ($region as $key_area => $area){
                $count_area++;
                $area_sum = $temp_sum;
                $area_sum['id']     = $key_area;
                $area_sum['name'] = isset($areas[$key_area]) ? ($areas[$key_area]) : '';
                /*$area_code = Areas::find()->where(['id'=>$key_area])->asArray()->one();
                $area_sum['branch_code'] = isset($area_code['code']) ? ($area_code['code']) : '';*/
                $count_branch =0;
                foreach($area as $b_key => $branch) {
                    $count_branch++;
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = $branches[$branch['branch_id']];
                    unset($branch['branch_id']);

                    $grand_sum['deposited']          += $branch['deposited'];
                    $grand_sum['cih']                += $branch['cih'];
                    //$grand_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $region_sum['deposited']          += $branch['deposited'];
                    $region_sum['cih']                += $branch['cih'];
                    //$region_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $area_sum['deposited']            += $branch['deposited'];
                    $area_sum['cih']                  += $branch['cih'];
                    $area_sum['children'][$b_key]     = $branch;
                }
                /*echo '<pre>';
                print_r($area_sum);
                die();*/
                $region_sum['children'][$count] = $area_sum;
                $count++;
            }

            //$new_big_array[] = $region_sum;
            //$new_big_array[] = $area_sum;
            $new_big_array[] = $region_sum;
        }


        $new_big_array[] = $grand_sum;
        $progress_report = json_encode($new_big_array);
        /*echo '<pre>';
        print_r($new_big_array);
        die();*/
        return $progress_report;
    }
}