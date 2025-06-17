<?php

namespace frontend\controllers;

use common\models\UserStructureMapping;
use Yii;
use common\models\UserHierarchyChangeRequest;
use common\models\search\UserHierarchyChangeRequestSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;
/**
 * UserHierarchyChangeRequestController implements the CRUD actions for UserHierarchyChangeRequest model.
 */
class UserHierarchyChangeRequestController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserHierarchyChangeRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserHierarchyChangeRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions=Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>[
                'regions'=>$regions
            ]
        ]);
    }

    /**
     * Displays a single UserHierarchyChangeRequest model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserHierarchyChangeRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserHierarchyChangeRequest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserHierarchyChangeRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserHierarchyChangeRequest model.
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
     * Finds the UserHierarchyChangeRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserHierarchyChangeRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserHierarchyChangeRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionCreateRequest()
    {
        /*print_r(Yii::$app->request->post());
        die();*/
        $model = new UserHierarchyChangeRequest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionRecommendByRm($id)
    {
        $model = $this->findModel($id);
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'region'])/*->asArray()*/->one();
        if (Yii::$app->request->isPost) {
            $model->status = 'recommended';
            $model->recommended_by=Yii::$app->user->getId();
            $model->save();
        }
        return $this->render('recommend', [
            'model' => $model,
            'array'=>[
                "region"=>$region,
                "area"=>$area,
                "branch"=>$branch,
                'team'=>$team,
                "field"=>$field
            ],
        ]);
    }
    public function actionApproveByDa($id)
    {
        $model = $this->findModel($id);
        $field = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'field'])/*->asArray()*/->one();
        $team = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'team'])/*->asArray()*/->one();
        $branch = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'branch'])/*->asArray()*/->one();
        $area = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'area'])/*->asArray()*/->one();
        $region = UserStructureMapping::find()/*->select(['obj_id'])*/->where(['user_id' => $model->user_id,'obj_type'=>'region'])/*->asArray()*/->one();
        if (Yii::$app->request->isPost) {
            if ($model->region_id != 0) {
                UserStructureMapping::deleteAll(['user_id' => $model->user_id, 'obj_type' => 'region']);
                UserStructureMapping::deleteAll(['user_id' => $model->user_id, 'obj_type' => 'area']);
                UserStructureMapping::deleteAll(['user_id' => $model->user_id, 'obj_type' => 'branch']);
                UserStructureMapping::deleteAll(['user_id' => $model->user_id, 'obj_type' => 'team']);
                UserStructureMapping::deleteAll(['user_id' => $model->user_id, 'obj_type' => 'field']);

                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $model->user_id;
                $user_obj->obj_id = $model->region_id;
                $user_obj->obj_type = 'region';
                $user_obj->save();
            }
            if ($model->area_id != 0) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $model->user_id;
                $user_obj->obj_id = $model->area_id;
                $user_obj->obj_type = 'area';
                $user_obj->save();
            }
            if ($model->branch_id != 0) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $model->user_id;
                $user_obj->obj_id = $model->branch_id;
                $user_obj->obj_type = 'branch';
                $user_obj->save();
            }
            if ($model->team_id != 0) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $model->user_id;
                $user_obj->obj_id = $model->team_id;
                $user_obj->obj_type = 'team';
                $user_obj->save();
            }
            if ($model->field_id != 0) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $model->user_id;
                $user_obj->obj_id = $model->field_id;
                $user_obj->obj_type = 'field';
                $user_obj->save();
            }
            $model->status = 'approved';
            $model->save();
        }
        return $this->render('approve', [
            'model' => $model,
            'array'=>[
                "region"=>$region,
                "area"=>$area,
                "branch"=>$branch,
                'team'=>$team,
                "field"=>$field
            ],
        ]);
    }

}
