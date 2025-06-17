<?php

namespace frontend\controllers;

use common\models\ApplicationActions;
use common\models\Applications;
use phpDocumentor\Reflection\Types\Null_;
use Ratchet\App;
use Yii;
use common\models\SocialAppraisal;
use common\models\search\SocialAppraisalSearch;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

/**
 * SocialAppraisalController implements the CRUD actions for SocialAppraisal model.
 */
class SocialAppraisalController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
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
     * Lists all SocialAppraisal models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new SocialAppraisalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions=Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions'=>$regions
        ]);
    }


    /**
     * Displays a single SocialAppraisal model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "SocialAppraisal #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new SocialAppraisal model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new SocialAppraisal();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new SocialAppraisal",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new SocialAppraisal",
                    'content'=>'<span class="text-success">Create SocialAppraisal success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new SocialAppraisal",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */

            if ($model->load($request->post()) /*&& $model->save()*/) {
                $model->total_expenses=$model->educational_expenses+$model->medical_expenses+$model->kitchen_expenses+$model->utility_bills+$model->other_expenses;
                $model->total_household_income=$model->job_income+$model->business_income+$model->house_rent_income+$model->other_income;
                $model->total_family_members=$model->ladies+$model->gents;
                $model->date_of_maturity=strtotime($model->date_of_maturity);
                $model->loan_amount = !empty($model->loan_amount) ? $model->loan_amount : 0;
                $model->house_rent_amount = !empty($model->house_rent_amount) ? $model->house_rent_amount : 0;

                if($model->save()){
                    $action_model = ApplicationActions::find()->where(['parent_id' => $model->application_id, 'action' => 'social_appraisal'])->one();
                    if (!empty($action_model)) {
                        $action_model->status = 1;
                        $action_model->save();
                    }
                    return $this->redirect(['/applications/view', 'id' => $model->application_id]);
                }
                else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing SocialAppraisal model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update SocialAppraisal #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "SocialAppraisal #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update SocialAppraisal #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) /*&& $model->save()*/) {
                $model->total_expenses=$model->educational_expenses+$model->medical_expenses+$model->kitchen_expenses+$model->utility_bills+$model->other_expenses;
                $model->total_household_income=$model->job_income+$model->business_income+$model->house_rent_income+$model->other_income;
                $model->total_family_members=$model->ladies+$model->gents;
                $model->date_of_maturity=strtotime($model->date_of_maturity);
                $model->loan_amount = !empty($model->loan_amount) ? $model->loan_amount : 0;
                $model->house_rent_amount = !empty($model->house_rent_amount) ? $model->house_rent_amount : 0;
                if($model->save()){
                    return $this->redirect(['/applications/view', 'id' => $model->application_id]);
                }
                else {
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing SocialAppraisal model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing SocialAppraisal model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }
    /**
     * Finds the SocialAppraisal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SocialAppraisal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SocialAppraisal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionSearchApplication($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $result = array();
            $query = new Query();
            $applications = Applications::find()
                ->join('inner join','members','members.id=applications.member_id')
                ->join('inner join','application_actions','application_actions.parent_id=applications.id')
                ->filterWhere(['like', 'applications.application_no', $q])
                ->orFilterWhere(['like', 'members.cnic', $q])
                ->orFilterWhere(['like', 'members.full_name', $q])
                ->andFilterWhere(['=','applications.deleted',0])
                ->andFilterWhere(['=','applications.is_lock',0])
                ->andFilterWhere(['=','application_actions.action','social_appraisal'])
                ->andFilterWhere(['=','application_actions.status','0'])
                //->andFilterWhere(['>=','application_actions.expiry_date',strtotime("-1 months")])
                ->andFilterWhere(['>=','applications.created_at',strtotime("-3 months")])
                ->orderBy(['applications.created_at' => SORT_DESC])
                ->all();
            /*$query->select('applications.id, applications.application_no,members.full_name,members.cnic')
                ->from('applications')
                ->join('inner join','members','members.id=applications.member_id')
                //->join('left join','social_appraisal','social_appraisal.application_id=applications.id')
                ->join('inner join','application_actions','application_actions.parent_id=applications.id')
                ->filterWhere(['like', 'applications.application_no', $q])
                ->orFilterWhere(['like', 'members.cnic', $q])
                ->orFilterWhere(['like', 'members.full_name', $q])
                ->andFilterWhere(['!=','applications.deleted','1'])
                //->andFilterWhere(['is','social_appraisal.application_id',new \yii\db\Expression('null')])
                ->andFilterWhere(['=','application_actions.action','social_appraisal'])
                ->andFilterWhere(['=','application_actions.status','0'])
                ->andFilterWhere(['>=','application_actions.expiry_date',time()])
                ->orderBy(['applications.created_at' => SORT_DESC]);
            $command = $query->createCommand();
            $data = $command->queryAll();*/

            foreach($applications as $k=>$application) {
                    $result[$k]['id'] = $application->id;
                    $result[$k]['text'] = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name . ' <strong>CNIC</strong>: ' . $application->member->cnic;
            }

            $out['results'] = $result;
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Applications::findOne($id)->form_no];
        }
        return $out;
    }
}
