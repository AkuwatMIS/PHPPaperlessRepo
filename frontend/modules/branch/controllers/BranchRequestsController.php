<?php

namespace frontend\modules\branch\controllers;

use common\components\Helpers\EmailHelper;
use common\models\BranchRequestActions;
use common\models\EmailsList;
use common\models\EmailsListDetails;
use common\models\Projects;
use Yii;
use common\models\BranchRequests;
use common\models\search\BranchRequestsSearch;
use common\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\Branches;

/**
 * BranchRequestsController implements the CRUD actions for BranchRequests model.
 */
class BranchRequestsController extends Controller
{
    public $dynAtt = []; //dynamicAttributes: on the fly attributes are created as value of this array

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        /*return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user->isGuest){
                        return Yii::$app->response->redirect(['site/login']);
                    }else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => RbacHelper::getRules(Yii::$app->controller->id)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];*/

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
     * Lists all BranchRequests models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BranchRequestsSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BranchRequests model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new BranchRequests model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BranchRequests();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $action_branch_request_pending = new BranchRequestActions();
            $user_id = Yii::$app->user->getId();
            $action_branch_request_pending->setValues($model->id, 'created', $user_id, 1);
            if (!$action_branch_request_pending->save()) {
                print_r($action_branch_request_pending->getErrors());
                die();
            }

            $action_branch_request_reviewed = new BranchRequestActions();
            $user_rm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = 'region' and a.item_name = 'RM' and m.obj_id = '".$model->region_id."'";
            $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();
            $user_id = $user_rm[0]['id'];
            $action_branch_request_reviewed->setValues($model->id, 'reviewed',$user_id);
            if (!$action_branch_request_reviewed->save()) {
                print_r($action_branch_request_reviewed->getErrors());
                die();
            }

            $action_branch_request_recommend = new BranchRequestActions();
            $user_dm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = 'cr_division' and a.item_name = 'DM' and m.obj_id = '".$model->cr_division_id."'";
            $user_dm = Yii::$app->db->createCommand($user_dm_select)->queryAll();
            $user_id = $user_dm[0]['id'];
            $action_branch_request_recommend->setValues($model->id, 'awaiting-recommended',$user_id);
            if (!$action_branch_request_recommend->save()) {
                print_r($action_branch_request_recommend->getErrors());
                die();
            }

            $action_branch_request_approved = new BranchRequestActions();
            $user_cco_select = "select u.id from users u 
                    inner join auth_assignment a on a.user_id = u.id
                    where a.item_name = 'CCO'";
            $user_cco = Yii::$app->db->createCommand($user_cco_select)->queryAll();
            $user_id = $user_cco[0]['id'];
            $action_branch_request_approved->setValues($model->id, 'approved',$user_id);
            if (!$action_branch_request_approved->save()) {
                print_r($action_branch_request_approved->getErrors());
                die();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionShuffle()
    {
        $model = new BranchRequests();

        if ($model->load(Yii::$app->request->post()) /*&& $model->save()*/) {
            $branch=Branches::find()->where(['id'=>$model->branch_id])->one();
            $model->name=$branch->name;
            $model->uc=$branch->uc;
            $model->village=$branch->village;
            $model->city_id=$branch->city_id;
            $model->tehsil_id=$branch->tehsil_id;
            $model->district_id=$branch->district_id;
            $model->division_id=$branch->division_id;
            $model->province_id=$branch->province_id;
            $model->country_id=$branch->country_id;
            $model->latitude=$branch->latitude;
            $model->longitude=$branch->longitude;
            $model->save();
            $action_branch_request_pending = new BranchRequestActions();
            $user_id = Yii::$app->user->getId();
            $action_branch_request_pending->setValues($model->id, 'created', $user_id, 1);
            if (!$action_branch_request_pending->save()) {
                print_r($action_branch_request_pending->getErrors());
                die();
            }

            /*$action_branch_request_reviewed = new BranchRequestActions();
            $user_rm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = 'region' and a.item_name = 'RM' and m.obj_id = '".$model->region_id."'";
            $user_rm = Yii::$app->db->createCommand($user_rm_select)->queryAll();
            $user_id = $user_rm[0]['id'];
            $action_branch_request_reviewed->setValues($model->id, 'reviewed',$user_id);
            if (!$action_branch_request_reviewed->save()) {
                print_r($action_branch_request_reviewed->getErrors());
                die();
            }*/

            $action_branch_request_recommend = new BranchRequestActions();
            $user_dm_select = "select u.id from users u inner join user_structure_mapping m on m.user_id = u.id 
                    inner join auth_assignment a on a.user_id = u.id
                    where m.obj_type = 'region' and a.item_name = 'DM' and m.obj_id = '".$model->region_id."'";
            $user_dm = Yii::$app->db->createCommand($user_dm_select)->queryAll();
            $user_id = isset($user_dm[0]['id'])?$user_dm[0]['id']:0;
            $action_branch_request_recommend->setValues($model->id, 'awaiting-recommended',$user_id);
            if (!$action_branch_request_recommend->save()) {
                print_r($action_branch_request_recommend->getErrors());
                die();
            }

            $action_branch_request_approved = new BranchRequestActions();
            $user_cco_select = "select u.id from users u 
                    inner join auth_assignment a on a.user_id = u.id
                    where a.item_name = 'CCO'";
            $user_cco = Yii::$app->db->createCommand($user_cco_select)->queryAll();
            $user_id = isset($user_cco[0]['id'])?$user_cco[0]['id']:0;
            $action_branch_request_approved->setValues($model->id, 'approved',$user_id);
            if (!$action_branch_request_approved->save()) {
                print_r($action_branch_request_approved->getErrors());
                die();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('shuffle', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing BranchRequests model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    public function actionUpdateShuffle($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('shuffle', [
                'model' => $model,
            ]);
        }
    }
    public function actionReviewed($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            return $this->renderAjax('reviewed', [
                'model' => $this->findModel($id),
            ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());
        if ($ifLoaded && $model->save()) {
            $model_branch_request_action = BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'reviewed'])->one();
            $model_branch_request_action->user_id = Yii::$app->user->getId();
            $model_branch_request_action->remarks = $model->reviewed_remarks;
            $model_branch_request_action->status = 1;
            if (!($model_branch_request_action->save())) {
                print_r($model_branch_request_action);
                die();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('reviewed', [
                'model' => $model,
            ]);
        }

    }
    public function actionRecommend($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $projects = ArrayHelper::map(Projects::find()->all(), 'id', 'name');

        if ($request->isAjax) {
            return $this->renderAjax('recommend', [
                    'model' => $this->findModel($id),
                    'projects'=>$projects
                ]);
        }
        $ifLoaded = $model->load(Yii::$app->request->post());

        if($ifLoaded){
            $projects_list = '';
            foreach ($model->projects as $project){
                $projects_list .= $project.',';
            }
            $model->projects = trim($projects_list, ',');
            if ($ifLoaded && $model->save()) {
                $model_branch_request_action = BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'awaiting-recommended'])->one();
                $model_branch_request_action->user_id = Yii::$app->user->getId();
                $model_branch_request_action->remarks = $model->recommended_remarks;
                $model_branch_request_action->status = 1;
                if (!($model_branch_request_action->save())) {
                    print_r($model_branch_request_action);
                    die();
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('recommend', [
                    'model' => $model,
                    'projects'=>$projects
                ]);
            }
        }else {
            return $this->render('recommend', [
                'model' => $model,
                'projects'=>$projects
            ]);
        }
        if ($ifLoaded && $model->save()) {
            $model_branch_request_action = BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'awaiting-recommended'])->one();
            $model_branch_request_action->user_id = Yii::$app->user->getId();
            $model_branch_request_action->remarks = $model->recommended_remarks;
            $model_branch_request_action->status = 1;
            if (!($model_branch_request_action->save())) {
                print_r($model_branch_request_action);
                die();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('recommend', [
                'model' => $model,
                'projects'=>$projects
            ]);
        }

    }

    public function actionApprove($id)
    {

        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model_list = EmailsList::find()->where(['name'=>'branch_request_approve'])->one();
        $model_list_details = ArrayHelper::map(EmailsListDetails::find()->where(['email_list_id'=>$model_list->id, 'status'=>1,'deleted'=>0])->all(),'id','receiver_email');
        $ifLoaded = $model->load(Yii::$app->request->post());

        if ($request->isAjax) {
            return $this->renderAjax('approve', [
                'model' => $this->findModel($id),
                'model_list_details' => $model_list_details
            ]);
        }
        if ($ifLoaded && $model->save()) {
            $model_branch_request_action = BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'approved'])->one();
            $model_branch_request_action->user_id = Yii::$app->user->getId();
            $model_branch_request_action->remarks = $model->approved_remarks;
            $model_branch_request_action->status = 1;
            if (!($model_branch_request_action->save())) {
                print_r($model_branch_request_action);
                die();
            }
            //Moving record to Branches model
            if($model->action=='branch_create') {
                $br = new Branches();
                $br->setAttributes($model->getAttributes(null, ['id']));
                $br->code = BranchRequests::generateCode($model->district_id);
                $br->status = 1;
                //$br->effective_date = $model->effective_date;
                if (!$br->save()) {
                    print_r($br->getErrors());
                    die();
                } else {
                    $query_update = "update branch_requests set branch_id = '" . $br->id . "' where id = '" . $model->id . "' ";
                    Yii::$app->db->createCommand($query_update)->execute();
                    $br->addProjects($model->projects);
                    if (!empty($model->emails)) {
                        foreach ($model->emails as $email) {
                            $email_list_detail = EmailsListDetails::find()->where(['email_list_id' => $model->email_list_id, 'id' => $email])->one();
                            $message = EmailHelper::getBranchRequestEmailMsg($br);
                            EmailHelper::SendEmail($model->sender_email, $email_list_detail->receiver_email, $message);
                        }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }else{
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('approve', [
                'model' => $model,
                'model_list' => $model_list,
                'model_list_details' => $model_list_details
            ]);
        }
    }

    /**
     * Deletes an existing BranchRequests model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionGetbranchcode($id)
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        if (isset($id)) {
            $branch_request = new BranchRequests();
            $out = $branch_request->generateCode($id);

            return Json::encode(['output' => $out, 'selected' => '']);
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Deletes an existing BranchRequests model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BranchRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BranchRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BranchRequests::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
