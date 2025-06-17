<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\Applications;
use common\models\Blacklist;
use common\models\Branches;
use common\models\Provinces;
use common\models\Users;
use common\models\BranchProjectsMapping;
use frontend\modules\branch\Branch;
use Yii;

class BlacklistHelper
{
    static public function checkBlacklist($cnic)
    {
        $blacklist_member = array();
        if(isset($cnic) && !empty($cnic)){
            $blacklist_member = Blacklist::find()->where(['cnic'=>$cnic])->andWhere(['deleted'=>0])->one();
            return $blacklist_member;
        }else{
            return $blacklist_member;
        }
    }

    static public function checksJournalBlacklist($model,$module,$branch_id)
    {
        $blacklist_member = array();

        if($module == 'member'){
            $branch   = Branches::find()->where(['id'=>$branch_id])->one();
            $province = Provinces::find()->where(['id'=>$branch->province_id])->one();

            $name = trim($model->full_name);
            $parentage = trim($model->parentage);

            $blacklist_member = Blacklist::find()
                ->where(['name'=>$name])
                ->andWhere(['parentage'=>$parentage])
                ->andWhere(['province'=>$province->name])
                ->andWhere(['deleted'=>0])->one();
        }elseif($module == 'other'){
            $branch   = Branches::find()->where(['id'=>$branch_id])->one();
            $province = Provinces::find()->where(['id'=>$branch->province_id])->one();
            $name = trim($model->name_of_other);

            $blacklist_member = Blacklist::find()
                ->where(['name'=>$name])
                ->andWhere(['province'=>$province->name])
                ->andWhere(['deleted'=>0])->one();
        }elseif($module == 'family'){
            $branch   = Branches::find()->where(['id'=>$branch_id])->one();
            $province = Provinces::find()->where(['id'=>$branch->province_id])->one();
            $name = trim($model->family_member_name);
            $parentage = trim($model->parentage);

            $blacklist_member = Blacklist::find()
                ->where(['name'=>$name])
//                ->andWhere(['parentage'=>$parentage])
                ->andWhere(['province'=>$province->name])
                ->andWhere(['deleted'=>0])->one();
        }elseif ($module == 'parentage'){
            $branch   = Branches::find()->where(['id'=>$branch_id])->one();
            $province = Provinces::find()->where(['id'=>$branch->province_id])->one();
            $parentage = trim($model->parentage);

            $blacklist_member = Blacklist::find()
                ->where(['name'=>$parentage])
                ->andWhere(['province'=>$province->name])
                ->andWhere(['deleted'=>0])
                ->one();
        }

        return $blacklist_member;
    }

    static public function VerifyBlacklist($name,$parentage,$module,$province)
    {
        $blacklist_member = array();

        if($module == 'member'){
            $blacklist_member = Applications::find()
                ->join('inner join','members','members.id=applications.member_id')
                ->join('inner join','branches','branches.id=applications.branch_id')
                ->where(['members.full_name'=>$name])
                ->andWhere(['members.parentage'=>$parentage])
                ->andWhere(['branches.province_id'=>$province])
                ->select(['applications.id','applications.member_id','members.cnic'])
                ->one();
//                ->createCommand()->getRawSql();
        }elseif($module == 'other'){
            $blacklist_member = Applications::find()
                ->join('inner join','members','members.id=applications.member_id')
                ->join('inner join','branches','branches.id=applications.branch_id')
                ->where(['applications.name_of_other'=>$name])
                ->andWhere(['branches.province_id'=>$province])
                ->select(['applications.id','applications.member_id'])
                ->one();
        }elseif($module == 'family'){
            $blacklist_member = Applications::find()
                ->join('inner join','members','members.id=applications.member_id')
                ->join('inner join','branches','branches.id=applications.branch_id')
                ->where(['members.family_member_name'=>$name])
                ->andWhere(['branches.province_id'=>$province])
                ->select(['applications.id','applications.member_id'])
                ->one();
        }

        return $blacklist_member;
    }
}