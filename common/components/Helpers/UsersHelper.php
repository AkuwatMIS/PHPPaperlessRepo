<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;
use common\models\Accounts;
use common\models\Activities;
use common\models\Areas;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Cities;
use common\models\Countries;
use common\models\CreditDivisions;
use common\models\Designations;
use common\models\Districts;
use common\models\Divisions;
use common\models\Fields;
use common\models\Products;
use common\models\Projects;
use common\models\Provinces;
use common\models\Regions;
use common\models\Teams;
use common\models\User;
use common\models\Users;
use common\models\BranchProjectsMapping;
use common\models\UserStructureMapping;
use common\models\UserTransferHierarchy;
use Yii;
use yii\helpers\ArrayHelper;

class UsersHelper
{

    static public function getUserName($id){
        return Users::find()->where(['id'=>$id])->one();
    }

    static public function getDesignation($id){
        $key = 'designation';
        $role = CacheHelper::getUserIdentity($id,$key);
        if (empty($role)) {
            $getRolesByUser = Yii::$app->authManager->getRolesByUser($id);
            $role = '';
            if (isset($getRolesByUser)) {
                foreach ($getRolesByUser as $r) {
                    if ($r->name != 'Collector') {
                        if (isset($r->description) && !empty($r->description)) {
                            $role = $r->description;
                        } else {
                            $role = $r->name;
                        }
                    }
                }
                CacheHelper::setUserIdentity($id,$key,$role);
            }
        }
        return $role;
    }

    static public function getRoles($id){
        $key = 'roles';
        $roles = CacheHelper::getUserIdentity($id,$key);
        if (empty($roles)) {
            $roles = Yii::$app->authManager->getRolesByUser($id);
            CacheHelper::setUserIdentity($id,$key,$roles);
        }
        return $roles;
    }

    static public function getRole($id){
        $getRolesByUser = Yii::$app->authManager->getRolesByUser($id);
        $role = '';
        if (isset($getRolesByUser)) {
            foreach ($getRolesByUser as $r) {
                if ($r->name != 'Collector') {
                    $role = $r->name;
                }
            }
        }

        return $role;
    }

    static public function getUserIdByAccessToken($access_token){
        $user = Users::findIdentityByAccessToken($access_token);
        return $user->id;
    }

    static public function getUserBranches($user)
    {
        $branch_ids =[];
        $branches = $user->branches;
        if(isset($branches)) {
            foreach ($branches as $branch) {
                $branch_ids[] = $branch->obj_id;
            }
        }
        return $branch_ids;
    }

    public static function getPromotionRoles($role)
    {
        $promotion_role = UserTransferHierarchy::find()->where(['role' =>$role, 'type' => 'promotion_level'])->one();
        $roles = [];
        $role_name = explode( ',',$promotion_role['value']);
        foreach ($role_name as $role)
        {
           $roles[$role] = $role;
        }
        return $roles;

    }

    public static function getSubRoles($role)
    {
        $roles['AM'] = ['LO','BM'];
        $roles['RC'] = ['AM','AA','AAA'];
        $query = AuthItem::find()->select('name as id,description as name')->where(['type'=>1]);
        if(isset($roles[$role]))
        {
            $query->andWhere(['in','name',$roles[$role]]);
        }
        $designations = $query->all();
        $designations = ArrayHelper::map($designations, 'id', 'name');
        return $designations;

    }

    public static function setUserStructure($user_id,$obj_type,$obj_id)
    {
        $user_mapping = new UserStructureMapping();
        $user_mapping->user_id = $user_id;
        $user_mapping->obj_type = $obj_type;
        $user_mapping->obj_id = $obj_id;
        $user_mapping->save();
    }

    public static function userTransfer($model)
    {
        if($model->type != 'leave') {
            UserStructureMapping::deleteAll(['user_id' => $model->user_id]);
            if ($model->region_id > 0) {
                UsersHelper::setUserStructure($model->user_id, 'region', $model->region_id);
            }
            if ($model->area_id > 0) {
                UsersHelper::setUserStructure($model->user_id, 'area', $model->area_id);
            }
            if ($model->branch_id > 0) {
                UsersHelper::setUserStructure($model->user_id, 'branch', $model->branch_id);
            }
            if ($model->team_id > 0) {
                UsersHelper::setUserStructure($model->user_id, 'team', $model->team_id);
            }
            if ($model->field_id > 0) {
                UsersHelper::setUserStructure($model->user_id, 'field', $model->field_id);
            }
        }
        if($model->type == 'promotion')
        {
            $designation = Designations::findOne(['code' => $model->role]);
            $user = Users::findOne(['id' => $model->user_id]);
            $user->designation_id = isset($designation) ? $designation->id : 0;
            $user->save();
            AuthAssignment::deleteAll(['user_id' => $model->user_id]);
            $auth_assign = new AuthAssignment();
            $auth_assign->item_name = $model->role;
            $auth_assign->user_id =(string)$model->user_id;
            $auth_assign->save();
        }
        if($model->type == 'leave')
        {
            $user = Users::findOne(['id' => $model->user_id]);
            $user->is_block = 1;
            $user->save();
        }
        $model->status = '1';
        $model->save();
    }
}