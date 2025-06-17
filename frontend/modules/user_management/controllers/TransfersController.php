<?php

namespace frontend\modules\user_management\controllers;

use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\models\AuthAssignment;
use common\models\Designations;
use common\models\Divisions;
use common\models\mapping_models\UserWithObject;
use common\models\mapping_models\UserWithProjects;
use common\models\search\UsersSearch;
use common\models\UserHierarchyChangeRequest;
use common\models\UserStructureMapping;
use common\models\UserTransferActions;
use common\models\UserTransferHierarchy;
use frontend\modules\user_management\UserManagement;
use Yii;
use common\models\UserTransfers;
use common\models\Users;
use common\models\search\UserTransfersSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TransfersController implements the CRUD actions for UserTransfers model.
 */
class TransfersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserTransfers models.
     * @return mixed
     */

    public function actionIndex()
    {
       // $newstr = substr_replace('92', '-', 4, 0);
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $sub_user_roles = UserTransferHierarchy::find()->select('value,type')->where(['role' => $user_role])->andWhere(['!=','type','promotion_level'])->asArray()->all();

        $roles = [];
        foreach ($sub_user_roles as $role)
        {

            $role_name = explode( ',',$role['value']);
            $roles = array_unique(array_merge($roles,$role_name));
        }
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->searchManagement(Yii::$app->request->queryParams);
       // Yii::$app->Permission->getSearchFilter($dataProvider,'users',Yii::$app->controller->action->id,'frontend');

        return $this->render('user_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'roles'=>$roles
        ]);
    }

    public function actionTransfer($id)
    {
        $request = Yii::$app->request;
        $model = Users::findOne($id);
        //$designations = ArrayHelper::map(StructureHelper::getDesignations(), 'id', 'name');

        //$types = ['transfer' => 'Transfer', 'promot/demot' => 'Promotion/Demotion', 'leave' => 'Leave'];
        $user_role = UsersHelper::getRole(Yii::$app->user->getId());
        $designations = UsersHelper::getPromotionRoles($user_role);
        $actions = UserTransferHierarchy::find()->select('value,type')->where(['role' => $user_role])->andWhere(['like','value', UsersHelper::getRole($id)])->andWhere(['!=','type','promotion_level'])->asArray()->all();
        $types = [];
        foreach ($actions as $action)
        {
            $types[$action['type']] = $action['type'];
        }
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $id,'obj_type'=>'region'])/*->asArray()*/->one();
        $structure=['region'=>$region,'area'=>$area,'branch'=>$branch];
        $regions=Yii::$app->Permission->getRegionList('users', Yii::$app->controller->action->id,'frontend');
        $transfer_model=new UserTransfers();
        ///RM id and user  Role
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($model->id);
        $users = $auth->getUserIdsByRole('RM');
        $user_region_id = \common\models\UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->one();
        $rm_id = 0;
        foreach ($users as $user) {
            if (!empty($user_region_id)) {
                $obj_id = \common\models\UserStructureMapping::find()->select(['user_id'])->where(['user_id' => $user, 'obj_type' => 'region', 'obj_id' => $user_region_id->obj_id])->one();
                if (!empty($obj_id)) {
                    $rm_id = $obj_id;
                }
            }
        }
        if ($transfer_model->load(Yii::$app->request->post())) {
            if($transfer_model->type=='leave'){
                $transfer_model->region_id=0;
                $transfer_model->branch_id=0;

            }
            if(!$transfer_model->save())
            {
                /*print_r($transfer_model->getErrors());
                die();*/
            }
            if($user_role == 'AM' && $region->obj_id != $transfer_model->region_id && $transfer_model->type != 'leave')
            {
                $hierarchy = UserTransferHierarchy::find()->where(['type' => $transfer_model->type, 'role' => $user_role])->andWhere(['like', 'value', UsersHelper::getRole($id)])->andWhere(['is not','action_column',null])->one();

            }else if(in_array($user_role,['RM','RC']) && $region->obj_id != $transfer_model->region_id && $transfer_model->type != 'leave'){
                $hierarchy = UserTransferHierarchy::find()->where(['type' => $transfer_model->type, 'role' => $user_role])->andWhere(['like', 'value', UsersHelper::getRole($id)])->andWhere(['is not','action_column',null])->one();
            }
            else {
                $hierarchy = UserTransferHierarchy::find()->where(['type' => $transfer_model->type, 'role' => $user_role])->andWhere(['like', 'value', UsersHelper::getRole($id)])->one();
            }
            if($transfer_model->type == 'leave'){
                $user = Users::findOne(['id' => $transfer_model->user_id]);
                $user->is_block = 1;
                $user->save(false);
            }
            $transfer_action_model = new UserTransferActions();
            $transfer_action_model->setValues($transfer_model->id, 'created',$transfer_model->created_by,1);
            $transfer_action_model->save();

            if($user_role == 'BM')
            {
                $parent_type = 'area';
            }
            if($user_role == 'AM' || $user_role == 'RM' || $user_role == 'RC' || $user_role == 'RA')
            {
                $parent_type = 'region';
            }
            /*if($user_role == 'DM'){
                $parent_type = 'cr_division';
            }*/
            $parent_value =$parent_type.'_id';

            if(isset($hierarchy->recommeded_by) && !empty($hierarchy->recommeded_by))
            {
                $transfer_action_model = new UserTransferActions();
                $user_rm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = '".$parent_type."' and a.item_name = '".$hierarchy->recommeded_by."' and m.obj_id = '".$structure[$parent_type]->obj_id."'";
                $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();           
                $user_id = isset($user_rm[0]['id'])?$user_rm[0]['id']:0;
                $transfer_action_model->setValues($transfer_model->id, 'reviewed',$user_id);
                $transfer_action_model->save();
            }
            if(isset($hierarchy->accepted_by) && !empty($hierarchy->accepted_by) && ($user_role == 'AM' || $user_role == 'RM'))
            {
                $transfer_action_model = new UserTransferActions();
                $user_rm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = '".$parent_type."' and a.item_name = '".$hierarchy->accepted_by."' and m.obj_id = '".$transfer_model->$parent_value."'";
                $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();           
                $user_id = isset($user_rm[0]['id'])?$user_rm[0]['id']:0;
                $transfer_action_model->setValues($transfer_model->id, 'accepted',$user_id);
                $transfer_action_model->save();
            }
            if(isset($hierarchy->approved_by))
            {
                if($hierarchy->approved_by=='CCO' || $hierarchy->approved_by=='ACCM'){
                    $transfer_action_model = new UserTransferActions();
                    $user_rm_select = "select u.id from users u 
                      inner join auth_assignment a on a.user_id = u.id
                      where  a.item_name = '".$hierarchy->approved_by."'";
                }else{
                    $transfer_action_model = new UserTransferActions();
                    $user_rm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                     inner join auth_assignment a on a.user_id = u.id
                     where m.obj_type = '".$parent_type."' and a.item_name = '".$hierarchy->approved_by."' and m.obj_id = '".$structure[$parent_type]->obj_id."'";
                }
                
                $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();
                $user_id = isset($user_rm[0]['id'])?$user_rm[0]['id']:0;
                $transfer_action_model->setValues($transfer_model->id, 'approved',$user_id);
                $transfer_action_model->save();
            }

            if(isset($hierarchy->finalized_by))
            {
                $transfer_action_model = new UserTransferActions();
                $user_rm_select = "select u.id from users u
                    inner join auth_assignment a on a.user_id = u.id
                    where a.item_name = '".$hierarchy->finalized_by."'";
                $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();
                $user_id = isset($user_rm[0]['id'])?$user_rm[0]['id']:0;
                $transfer_action_model->setValues($transfer_model->id, 'hr_acceptance',$user_id);
                $transfer_action_model->save();
            }
            return $this->redirect(['view', 'id' => $transfer_model->id]);
        }
        ////
        /*
        *   Process for non-ajax request
        */
        return $this->render('transfer', [
            'model' => $model,
            'array'=>[
                "region"=>$region,
                "area"=>$area,
                "branch"=>$branch,
                'team'=>$team,
                "field"=>$field
            ],
            'auth'=>[
                'roles'=>$roles,
                'users'=>$users,
                'rm_id'=>$rm_id
            ],
            "regions"=>$regions,
            'change_model'=>$transfer_model,
            'types'=>$types,
            'designations'=>$designations
        ]);
    }

    public function actionList()
    {
        $searchModel = new UserTransfersSearch();
        $searchModel->status=0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTransferredList()
    {
        $searchModel = new UserTransfersSearch();
        $searchModel->status=1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserTransfers model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $transfer_model = $this->findModel($id);
        $model = Users::findOne($transfer_model->user_id);
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' =>  $model->id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' =>  $model->id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' =>  $model->id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' =>  $model->id,'obj_type'=>'region'])/*->asArray()*/->one();
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($model->id);
        return $this->render('transfer_old', [
            'transfer_model' => $transfer_model,
            'model' => $model,
            'array'=>[
                "region"=>$region,
                "area"=>$area,
                "branch"=>$branch,
                'team'=>$team,
                "field"=>$field
            ],
            'auth'=>[
                'roles'=>$roles,
            ],
        ]);
    }

    public function actionRecommend($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            return $this->renderAjax('recommend', [
                'model' => $this->findModel($id)
            ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());
        if ($ifLoaded && $model->save()) {
            $transfer_action_model = UserTransferActions::find()->where(['parent_id' => $model->id, 'action' => 'reviewed'])->one();
            $transfer_action_model->user_id = Yii::$app->user->getId();
            $transfer_action_model->remarks = $model->recommend_remarks;
            $transfer_action_model->status = 1;
            if (!($transfer_action_model->save())) {
                print_r($transfer_action_model);
                die();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('recommend', [
                'model' => $model,
            ]);
        }

    }
    public function actionAccepted($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            return $this->renderAjax('accept', [
                'model' => $this->findModel($id)
            ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());
        if ($ifLoaded && $model->save()) {
            $transfer_action_model = UserTransferActions::find()->where(['parent_id' => $model->id, 'action' => 'accepted'])->one();
            $transfer_action_model->user_id = Yii::$app->user->getId();
            $transfer_action_model->remarks = $model->recommend_remarks;
            $transfer_action_model->status = 1;
            if (!($transfer_action_model->save())) {
                print_r($transfer_action_model);
                die();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('accept', [
                'model' => $model,
            ]);
        }

    }

    public function actionApproved($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            return $this->renderAjax('approve', [
                'model' => $this->findModel($id)
            ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());
        if ($ifLoaded && $model->save()) {
            $transfer_action_model = UserTransferActions::find()->where(['parent_id' => $model->id, 'action' => 'approved'])->one();
            $transfer_action_model->user_id = Yii::$app->user->getId();
            $transfer_action_model->remarks = $model->approved_remarks;
            $transfer_action_model->status = 1;
            if (!($transfer_action_model->save())) {
                print_r($transfer_action_model);
                die();
            }
            $transfer_action = UserTransferActions::find()->where(['parent_id' => $model->id, 'action' => 'hr_acceptance'])->one();
            if(!isset($transfer_action))
            {
                UsersHelper::userTransfer($model);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('approve', [
                'model' => $model,
            ]);
        }

    }

    public function actionHracceptance($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            return $this->renderAjax('hr_acceptance', [
                'model' => $this->findModel($id)
            ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());
        if ($ifLoaded && $model->save()) {
            $transfer_action_model = UserTransferActions::find()->where(['parent_id' => $model->id, 'action' => 'hr_acceptance'])->one();
            $transfer_action_model->user_id = Yii::$app->user->getId();
            $transfer_action_model->remarks = $model->hr_acceptance_remarks;
            $transfer_action_model->status = 1;
            if (!($transfer_action_model->save())) {
                print_r($transfer_action_model);
                die();
            }
            UsersHelper::userTransfer($model);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('hr_acceptance', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Creates a new UserTransfers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    /**
     * Deletes an existing UserTransfers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Finds the UserTransfers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserTransfers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserTransfers::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
