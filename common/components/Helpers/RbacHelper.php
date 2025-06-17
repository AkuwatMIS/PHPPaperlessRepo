<?php
/**
 * Created by PhpStorm.
 * User: junaid.fayyaz
 * Date: 2/26/2018
 * Time: 12:27 PM
 */

namespace common\components\Helpers;


use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\AuthItemChild;
use common\models\AuthItemChildApi;
use common\models\AuthRule;
use yii\db\Query;
use yii\rbac\Permission;
use yii\rbac\Role;
use Yii;

class RbacHelper
{
    protected static function getChildrenList($rbac_type)
    {
        $query = (new Query())->from('auth_item_child')->where(['like','child',$rbac_type.'_'.'%', false]);
        $parents = [];
        foreach ($query->all()  as $row) {
            $parents[$row['parent']][] = $row['child'];
        }

        return $parents;
    }

    public static function getRule($controller,$method,$rbac_type,$user_id)
    {
        $controller = str_replace('-','',$controller);
        //$permission = self::getPermissionsByUser($user_id,$rbac_type);
        $key = 'permissions_with_rule';
        if($rbac_type == 'api')
        {
            $permission = CacheHelper::getUserIdentity($user_id,$key);
            if(empty($permission))
            {
                $permission = self::getPermissionsByUser($user_id,$rbac_type);
                CacheHelper::setUserIdentity($user_id,$key,$permission);
            }
        } else if($rbac_type == 'frontend') {
            if (empty(Yii::$app->session->get($key))) {
                Yii::$app->session->set('permissions_with_rule', self::getPermissionsByUser($user_id, $rbac_type));
            }
            $permission = Yii::$app->session->get('permissions_with_rule');
        }
        $rule = '';
        foreach ($permission as $key => $value) {
            if (preg_match('/' . $method . $controller . '/', $key)) {
                $rule = $value->ruleName;
            }
        }
        return $rule;
    }

    public static function getPermissionsByRole($roleName,$rbac_type)
    {
        $childrenList = self::getChildrenList($rbac_type);
       // $childrenList = AuthItemChildApi::find()->where(['parent' => $roleName])->andWhere(['like','child','api_'.'%', false])->asArray()->all();
       /* print_r($childrenList);
        die();*/
        $result = [];
        self::getChildrenRecursive($roleName, $childrenList, $result);
        if (empty($result)) {
            return [];
        }

        $query = (new Query())->from('auth_item')->where([
            'type' => 2,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all() as $row) {
            $permissions[$row['name']] = self::populateItem($row);
        }

        return $permissions;
    }

    protected static function populateItem($row)
    {
        $class = $row['type'] == 2 ? Permission::className() : Role::className();

        if (!isset($row['data']) || ($data = @unserialize(is_resource($row['data']) ? stream_get_contents($row['data']) : $row['data'])) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }

    protected static function getChildrenRecursive($name, $childrenList, &$result)
    {
        if (isset($childrenList[$name])) {

            foreach ($childrenList[$name] as $child) {
                $result[$child] = true;
                self::getChildrenRecursive($child, $childrenList, $result);
            }
        }
    }


    public static function getPermissionsByUser($userId,$rbac_type)
    {
        if (self::isEmptyUserId($userId)) {
            return [];
        }

        $directPermission = self::getDirectPermissionsByUser($userId,$rbac_type);
        $inheritedPermission = self::getInheritedPermissionsByUser($userId,$rbac_type);

        return array_merge($directPermission, $inheritedPermission);
        /*print_r(array_merge($directPermission, $inheritedPermission));
        die();*/
    }

    protected static function getDirectPermissionsByUser($userId,$rbac_type)
    {
        $query = (new Query())->select('b.*')
            ->from(['a' => 'auth_assignment', 'b' => 'auth_item'])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string) $userId])
            ->andWhere(['b.type' => 2]);

        $permissions = [];
        foreach ($query->all() as $row) {
            $permissions[$row['name']] = self::populateItem($row);
        }

        return $permissions;
    }

    protected static function getInheritedPermissionsByUser($userId,$rbac_type)
    {
        $query = (new Query())->select('item_name')
            ->from('auth_assignment')
            ->where(['user_id' => (string) $userId]);

        $childrenList = self::getChildrenList($rbac_type);
        $result = [];
        foreach ($query->column() as $roleName) {
            self::getChildrenRecursive($roleName, $childrenList, $result);
        }

        if (empty($result)) {
            return [];
        }

        $query = (new Query())->from('auth_item')->where([
            'type' => 2,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all() as $row) {
            $permissions[$row['name']] = self::populateItem($row);
        }

        return $permissions;
    }

    private static function isEmptyUserId($userId)
    {
        return !isset($userId) || $userId === '';
    }
}