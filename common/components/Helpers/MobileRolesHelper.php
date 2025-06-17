<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\MobilePermissions;
use Yii;

class MobileRolesHelper
{
    public static function getRoles($user_id)
    {
        /*$key = 'mobile_roles';
        $roles = CacheHelper::getUserIdentity($user_id,$key);

        if (empty($roles)) {*/
            $getRolesByUser = Yii::$app->authManager->getRolesByUser($user_id);
            $role = '';
            foreach ($getRolesByUser as $r) {
                if ($r->name != 'Collector') {
                    $role = $r->name;
                }
            }
            $mobile_roles = MobilePermissions::find()->where(['role' => $role])->all();
            $roles = array();
            foreach ($mobile_roles as $mobile_role) {
                $roles[] = isset($mobile_role->mobileScreen->name) ? $mobile_role->mobileScreen->name : '';
            }
           /* CacheHelper::setUserIdentity($user_id, $key, $roles);
        }*/
        return $roles;

    }
}