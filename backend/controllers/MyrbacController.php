<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 12/28/2017
 * Time: 11:55 AM
 */
namespace backend\controllers;

use common\components\Helpers\RbacHelper;
use common\components\Helpers\StringHelper;
use common\models\AuthItemApi;
use common\models\AuthItemChild;
use johnitvn\rbacplus\models\Role;
use johnitvn\rbacplus\models\RoleSearch;
use common\models\AuthItem;
use common\models\Actions;
use common\models\AuthAssignment;
use common\models\search\UsersSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MyrbacController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else{
                        return Yii::$app->response->redirect(['site/main']);
                    }
                },
                'only' => ['index','permission','create-role','user-list','deleteuser'],
                'rules' => [
                    [
                        'actions' => ['index','permission','create-role','user-list','deleteuser'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateRole()
    {
        $model = new AuthItem();
        //$model = new Role(null);

        if ($model->load(Yii::$app->request->post())) {
            $model->type = 1;
            if(!$model->save()){
                print_r($model->getErrors());
                die();
            }
            else {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create_role', [
            'model' => $model,
        ]);
    }

    public function actionPermission($name)
    {
        $rbac_type = 'frontend';
        $actions = Actions::find()->where(['module_type' => $rbac_type])->all();
        $user_list = AuthAssignment::find()->where(['item_name' => $name])->all();
        $auth = Yii::$app->authManager;
        $rule = $auth->getRules();

        $role_permission = array(0 => 'None', 1 => 'All');

        foreach ($rule as $key => $v)
        {
            array_push($role_permission,$key);
        }

        $action_model = [];
        $action_array = [];
        $action_heading = [];
        $arr = array();
        $per = [];

        foreach ($actions as $action)
        {
            $action_model[$action->module][$action->action] = 0;
        }

        foreach ($action_model as $key_name => $key_value) {
            foreach ($action_model[$key_name] as $key => $value) {
                if(!in_array($key,$action_heading))
                {
                    $action_heading[$key] = $key;
                }
            }
        }

        foreach ($action_model as $key => $value) {
            $keys = array_keys($value);

            foreach ($action_heading as $k => $v) {
                $per['label'] = $k;
                if (in_array($k, $keys)) {
                    $per['value'] = 0;
                    $per['display'] = true;
                    $action_array[$key][$k] = $per;
                }
                else {
                    $per['value'] = -1;
                    $per['display'] = false;
                    $action_array[$key][$k] = $per;
                }
            }
        }

        $role = $auth->getRole($name);
        $permission_array = RbacHelper::getPermissionsByRole($role->name,$rbac_type);

        foreach ($action_array as $key => $key_value) {
            foreach ($permission_array as $k => $val) {
                if (substr_count($k, $key) > 0) {
                    //$substring = self::lreplace($key, $k);
                    $substring = explode($rbac_type.'_', self::lreplace($key, $k));
                    $substring = $substring[1];
                    if (strpos($substring, $name)) {
                        $substring = str_replace($name, '', $substring);
                        $rule_name = AuthItem::find()->where(['name' => $k])->one();
                        $permission_value = array_keys($role_permission, $rule_name['rule_name']);

                        $action_array[$key][$substring]['value'] = $permission_value;
                    } else {
                        $action_array[$key][$substring]['value'] = 1;
                    }
                }

            }
        }

        $permission ='';
        $form_data = Yii::$app->request->post();
        if ($form_data)
        {
            $permission_a = RbacHelper::getPermissionsByRole($role->name,$rbac_type);
            foreach ($permission_a as $k=>$val) {
                $p = $permission_array[$k];
                $auth->removeChildren($p);
            }
            AuthItemChild::deleteAll(['and',['parent' => $name],['like','child',$rbac_type.'_'.'%', false]]);
            /*$auth->removeChildren($role);*/
            $access_array = $form_data['Permission'];

            foreach ($access_array as $key_name => $key_value) {
                foreach ($access_array[$key_name] as $key => $value) {
                    if ($value == 1) {
                        $permission =  $rbac_type.'_'.$key . $key_name;
                        $rbac = $auth->getPermission($permission);
                        $rbac_name = $auth->getRole($name);
                        $auth->addChild($rbac_name, $rbac);
                    }

                    if ($value >= 2) {
                        $rbac_name = $auth->getRole($name);
                        $permission =  $rbac_type.'_'.$key . $key_name;
                        $permission_child =  $rbac_type.'_'.$key . $key_name . $name;
                        if(!($auth->getPermission($permission_child)))
                        {
                            $auth_item = new AuthItem(null);
                            $auth_item->name = $permission_child;
                            $auth_item->type = 2;
                            $createPermission = $auth->createPermission($auth_item->name);
                            $createPermission->description = $permission_child;
                            $createPermission->ruleName = $role_permission[$value];
                            $auth->add($createPermission);
                        }
                        $rbac = $auth->getPermission($permission_child);
                        $rbac_child = $auth->getPermission($permission);
                        $model = AuthItem::findOne($permission_child);
                        $model->rule_name = $role_permission[$value];
                        $model->save();
                        $auth->addChild($rbac_name, $rbac);
                        $auth->addChild($rbac, $rbac_child);
                    }
                }
            }
            return $this->refresh();
        }

        return $this->render('permission', [
            //'action_array' => $action_model,
            'action_array' => $action_array,
            'user_list' => $user_list,
            'name' => $name,
            'role_permission' => $role_permission,
            'action_heading' => $action_heading,
        ]);
    }

    public function actionPermissionApi($name)
    {
        $rbac_type = 'api';
        $actions = Actions::find()->where(['module_type' => $rbac_type])->all();
        $user_list = AuthAssignment::find()->where(['item_name' => $name])->all();
        $auth = Yii::$app->authManager;
        $rule = $auth->getRules();

        $role_permission = array(0 => 'None', 1 => 'All');

        foreach ($rule as $key => $v)
        {
            array_push($role_permission,$key);
        }

        $action_model = [];
        $action_array = [];
        $action_heading = [];
        $arr = array();
        $per = [];

        foreach ($actions as $action)
        {
            $action_model[$action->module][$action->action] = 0;
        }

        foreach ($action_model as $key_name => $key_value) {
            foreach ($action_model[$key_name] as $key => $value) {
                if(!in_array($key,$action_heading))
                {
                    $action_heading[$key] = $key;
                }
            }
        }

        foreach ($action_model as $key => $value) {
            $keys = array_keys($value);

            foreach ($action_heading as $k => $v) {
                $per['label'] = $k;
                if (in_array($k, $keys)) {
                    $per['value'] = 0;
                    $per['display'] = true;
                    $action_array[$key][$k] = $per;
                }
                else {
                    $per['value'] = -1;
                    $per['display'] = false;
                    $action_array[$key][$k] = $per;
                }
            }
        }

        $role = $auth->getRole($name);
        $permission_array = RbacHelper::getPermissionsByRole($role->name,$rbac_type);
        //$permission_array = $auth->getPermissionsByRole($role->name);
        /*print_r($permission_array);
        die();*/
        if(isset($permission_array)) {
            foreach ($action_array as $key => $key_value) {
                foreach ($permission_array as $k => $val) {
                    if (substr_count($k, $key) > 0) {
                        $substring = explode($rbac_type.'_', self::lreplace($key, $k));
                        $substring = $substring[1];
                        if (strpos($substring, $name)) {

                            $substring = str_replace($name, '', $substring);
                            $rule_name = AuthItem::find()->where(['name' => $k])->one();
                            $permission_value = array_keys($role_permission, $rule_name['rule_name']);

                            $action_array[$key][$substring]['value'] = $permission_value;
                        } else {
                            $action_array[$key][$substring]['value'] = 1;
                        }
                    }

                }
            }
        }

        $permission ='';
        $form_data = Yii::$app->request->post();
        if ($form_data)
        {
           /* print_r($form_data['Permission']);
            die();*/
            $permission_a = RbacHelper::getPermissionsByRole($role->name,'api');
            /*print_r($permission_a);
            die();*/
            foreach ($permission_a as $k=>$val) {
                $p = $permission_array[$k];
                $auth->removeChildren($p);
            }
            AuthItemChild::deleteAll(['and',['parent' => $name],['like','child',$rbac_type.'_'.'%', false]] );
            //$auth->removeChildren($role);

            $access_array = $form_data['Permission'];

            foreach ($access_array as $key_name => $key_value) {
                foreach ($access_array[$key_name] as $key => $value) {
                    if ($value == 1) {
                        $permission = $rbac_type.'_'.$key . $key_name;
                        $rbac = $auth->getPermission($permission);
                        $rbac_name = $auth->getRole($name);
                        $auth->addChild($rbac_name, $rbac);
                    }

                    if ($value >= 2) {
                        $rbac_name = $auth->getRole($name);
                        $permission =  $rbac_type.'_'.$key . $key_name;
                        $permission_child =  $rbac_type.'_'.$key . $key_name . $name;
                        if(!($auth->getPermission($permission_child)))
                        {
                            $auth_item = new AuthItem(null);
                            $auth_item->name = $permission_child;
                            $auth_item->type = 2;
                            $createPermission = $auth->createPermission($auth_item->name);
                            $createPermission->description = $permission_child;
                            $createPermission->ruleName = $role_permission[$value];
                            $auth->add($createPermission);
                        }
                        $rbac = $auth->getPermission($permission_child);
                        $rbac_child = $auth->getPermission($permission);
                        $model = AuthItem::findOne($permission_child);
                        $model->rule_name = $role_permission[$value];
                        $model->save();
                        $auth->addChild($rbac_name, $rbac);
                        $auth->addChild($rbac, $rbac_child);
                    }
                }
            }
            return $this->refresh();
        }

        return $this->render('permission_api', [
            //'action_array' => $action_model,
            'action_array' => $action_array,
            'user_list' => $user_list,
            'name' => $name,
            'role_permission' => $role_permission,
            'action_heading' => $action_heading,
        ]);
    }

    public function actionPermissionApi_($name)
    {
        $actions = Actions::find()->where(['module_type' => 'api'])->all();
        $user_list = AuthAssignment::find()->where(['item_name' => $name])->all();
        $auth = Yii::$app->apiAuthManager;
        $rule = $auth->getRules();

        $role_permission = array(0 => 'None', 1 => 'All');

        foreach ($rule as $key => $v)
        {
            array_push($role_permission,$key);
        }

        $action_model = [];
        $action_array = [];
        $action_heading = [];
        $arr = array();
        $per = [];

        foreach ($actions as $action)
        {
            $action_model[$action->module][$action->action] = 0;
        }

        foreach ($action_model as $key_name => $key_value) {
            foreach ($action_model[$key_name] as $key => $value) {
                if(!in_array($key,$action_heading))
                {
                    $action_heading[$key] = $key;
                }
            }
        }

        foreach ($action_model as $key => $value) {
            $keys = array_keys($value);

            foreach ($action_heading as $k => $v) {
                $per['label'] = $k;
                if (in_array($k, $keys)) {
                    $per['value'] = 0;
                    $per['display'] = true;
                    $action_array[$key][$k] = $per;
                }
                else {
                    $per['value'] = -1;
                    $per['display'] = false;
                    $action_array[$key][$k] = $per;
                }
            }
        }

        $role = $auth->getRole($name);
        $permission_array = $auth->getPermissionsByRole($role->name);
        /*print_r($permission_array);
        die();*/
        foreach ($action_array as $key => $key_value) {
            foreach ($permission_array as $k => $val) {
                if (substr_count($k, $key) > 0) {

                    $substring = explode('api_',self::lreplace($key, $k));
                    $substring = $substring[1];
                    if (strpos($substring, $name)) {

                        $substring = str_replace($name, '', $substring);
                        $rule_name = AuthItem::find()->where(['name' => $k])->one();
                        $permission_value = array_keys($role_permission, $rule_name['rule_name']);

                        $action_array[$key][$substring]['value'] = $permission_value;
                    } else {
                        $action_array[$key][$substring]['value'] = 1;
                    }
                }

            }
        }

        $permission ='';
        $form_data = Yii::$app->request->post();
        if ($form_data)
        {
            /* print_r($form_data['Permission']);
             die();*/
            $permission_a = $auth->getPermissionsByRole($role->name);
            /*print_r($permission_a);
            die();*/
            foreach ($permission_a as $k=>$val) {
                if(strpos($k,'api_') !== false) {
                    $p = $permission_array[$k];
                    $auth->removeChildren($p);
                }
            }
            // AuthItemChild::deleteAll(['like','child', 'api_'],['parent' => $name]);
            $auth->removeChildren($role);

            $access_array = $form_data['Permission'];

            foreach ($access_array as $key_name => $key_value) {
                foreach ($access_array[$key_name] as $key => $value) {
                    if ($value == 1) {
                        $permission = 'api_'.$key . $key_name;
                        $rbac = $auth->getPermission($permission);
                        $rbac_name = $auth->getRole($name);
                        $auth->addChild($rbac_name, $rbac);
                    }

                    if ($value >= 2) {
                        $rbac_name = $auth->getRole($name);
                        $permission = 'api_'.$key . $key_name;
                        $permission_child = 'api_'.$key . $key_name . $name;
                        if(!($auth->getPermission($permission_child)))
                        {
                            $auth_item = new AuthItem(null);
                            $auth_item->name = $permission_child;
                            $auth_item->type = 2;
                            $createPermission = $auth->createPermission($auth_item->name);
                            $createPermission->description = $permission_child;
                            $createPermission->ruleName = $role_permission[$value];
                            $auth->add($createPermission);
                        }
                        $rbac = $auth->getPermission($permission_child);
                        $rbac_child = $auth->getPermission($permission);
                        $model = AuthItem::findOne($permission_child);
                        $model->rule_name = $role_permission[$value];
                        $model->save();
                        $auth->addChild($rbac_name, $rbac);
                        $auth->addChild($rbac, $rbac_child);
                    }
                }
            }
            return $this->refresh();
        }

        return $this->render('permission_api', [
            //'action_array' => $action_model,
            'action_array' => $action_array,
            'user_list' => $user_list,
            'name' => $name,
            'role_permission' => $role_permission,
            'action_heading' => $action_heading,
        ]);
    }

    public function actionUserlist($name)
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (isset($_POST['keylist'])) {

            $i=0;
            $keys = $_POST['keylist'];
            $auth = Yii::$app->authManager;
            $rbac_name = $auth->getRole($name);
            $role = $auth->getUserIdsByRole($name);
            foreach ($role as $r)
            {
                foreach ($keys as $k) {
                    if($r == $k)
                    {
                        return $this->redirect(['permission', 'name' => $name]);
                    }
                }
            }
            foreach ($keys as $k)
            {
                $auth->assign($rbac_name, $k);
            }

            return $this->redirect(['permission', 'name' => $name]);
        }
        else {
            return $this->render('userlist', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'name' => $name,
            ]);
        }
    }

    public function actionDeleteuser($id,$name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        $auth->revoke($role,$id);
        return $this->redirect(['permission', 'name' => $name]);
    }

    private function lreplace($search, $subject){
        $pos = strrpos($subject, $search);
        if($pos !== false){
            $subject = substr_replace($subject, '', $pos, strlen($search));
        }
        return $subject;
    }


}