<?php

namespace backend\controllers;

use common\components\Helpers\StructureHelper;
use common\models\ArcAccountReports;
use common\models\search\ArcAccountReportsSearch;
use Yii;
use common\models\ProgressReports;
use common\models\search\ProgressReportsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ProgressReportsController implements the CRUD actions for ProgressReports model.
 */
class AccountReportsController extends Controller
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
                'only' => ['index','view','create','update','_form'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','_form'],
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

    /**
     * Lists all ProgressReports models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArcAccountReportsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single ProgressReports model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "AccountReports #".$id,
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
     * Creates a new ProgressReports model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdateReports()
    {
        $request = Yii::$app->request;
        $model = new ArcAccountReports();
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $period = array('daily'=>'Daily','monthly'=>'Monthly','annually'=>'Annually','daily-project'=>'Daily-Project');
        $reports = ['takaf'=>'Takaful Summary','recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary'];

        if ($model->load($request->post()) /*&& $model->save()*/) {
            if($model->code=='don'){
                $name='Donation Summary';
            }elseif ($model->code=='recv'){
                $name='Recovery Summary';
            }else if($model->code=='app_disb'){
                $name='Application Disbursement Report';
            }else if($model->code=='disb'){
                $name='Disbursement Summary';
            }
            /*echo'<pre>';
            print_r($model);
            die();*/
            $date = explode(' - ', $model->report_date);
            $period = 'monthly';
            $dates=[];
            $to=$date[1];
            $d=$from=$date[0];

            for ($i=0;$d<$to;$i++){
                $dates[]=strtotime(date('Y-m-t-23:59',strtotime("+$i months",strtotime($from))));
                $d=date('Y-m-t-23:59',strtotime("+$i months",strtotime($from)));
            }
            foreach ($dates as $dt) {
                $account_report = ArcAccountReports::find()->where(['between','report_date',strtotime(date('Y-m-t',$dt)),$dt])->andWhere(['period' => $model->period,'project_id'=>$model->project_id,'code'=>$model->code])->one();
                if(empty($account_report))
                {
                    $account_report = new ArcAccountReports();
                    $account_report->add_account_report($name, $model->code,$model->period, $model->project_id, $dt);
                }else{
                    $account_report->do_update = 1;
                    $account_report->save();
                }

            }
            return $this->redirect(['index']);
        } else {
            return $this->render('_update_form', [
                'model' => $model,
                'projects' => $projects,
                'period' => $period,
                'reports'
            ]);
        }
    }

    public function actionUpdateMonthReports()
    {
        $request = Yii::$app->request;
        $model = new ArcAccountReports();
        $period = array('daily'=>'Daily','monthly'=>'Monthly','annually'=>'Annually','daily-project'=>'Daily-Project');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet) {
                return [
                    'title' => "Update AccountReports",
                    'content' => $this->renderAjax('update_report_form', [
                        'model' => $model,
                        'period' => $period
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        elseif ($model->load($request->post())) {

            $account_report = ArcAccountReports::find()
                ->where(['report_date' => strtotime(date("Y-m-d H:i:s", strtotime($model->report_date)))]);
            if ($model->code == 'all') {
                $reports = $account_report
                    ->andWhere(['period' => 'monthly'])
                    ->andWhere(['deleted' => 0])
                    ->andWhere(['do_delete' => 0])
                    ->all();
            } else{
                $reports = $account_report
                    ->andWhere(['period' => 'monthly'])
                    ->andWhere(['deleted' => 0])
                    ->andWhere(['do_delete' => 0])
                    ->andWhere(['code' => $model->code])
                    ->all();
            }
            foreach ($reports as $report){
                $report->do_update = $model->do_update;
                $report->is_awp = $model->is_awp;
                $report->save();
            }
            return $this->redirect(['index']);
        }

        } else {
            return $this->render('update_report_form', [
                'model' => $model,
                'period' => $period,
            ]);
        }
    }
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ArcAccountReports();
        $status = array('1'=>'Active','0'=>'In-Active');
        $flags = array('1'=>'Yes','0'=>'No');
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        $period = array('daily'=>'Daily','monthly'=>'Monthly','annually'=>'Annually','daily-project'=>'Daily-Project');
        $reports = ['takaf'=>'Takaful Summary','recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary'];
        $flag = true;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new AccountReports",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'status' => $status,
                        'flags' => $flags,
                        'projects' => $projects,
                        'period' => $period,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($request->post()){
                foreach ($reports as $k => $report) {
                    $model = new ArcAccountReports();
                    $model->load($request->post());
                    $model->report_date= strtotime($request->post()['ArcAccountReports']['report_date']);
                    $model->code= $k;
                    $model->report_name = $report;
                    $model->beforeCreate();
                    if(!$model->save(true)){
                      $flag = false;
                    }
                }
                /*print_r($model);
                die();*/
                if($flag){
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Create new AccountReports",
                        'content'=>'<span class="text-success">Create AccountReports success</span>',
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
                    ];
                } else {
                    return [
                        'title'=> "Create new AccountReports",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'status' => $status,
                            'flags' => $flags,
                            'projects' => $projects,
                            'period' => $period,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];

                }
            }else{
                return [
                    'title'=> "Create new AccountReports",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'status' => $status,
                        'flags' => $flags,
                        'projects' => $projects,
                        'period' => $period,
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
                foreach ($reports as $k => $report) {
                    $model = new ArcAccountReports();
                    $model->load($request->post());
                    $model->report_date= strtotime($request->post()['ArcAccountReports']['report_date']);
                    $model->code= $k;
                    $model->report_name = $report;
                    $model->beforeCreate();
                    if(!$model->save(true)){
                        $flag = false;
                    }
                }
                //$model->beforeCreate();

                if($flag){
                return $this->redirect(['view', 'id' => $model->id]);}
                else{
                    print_r($model->getErrors());
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'status' => $status,
                    'flags' => $flags,
                    'projects' => $projects,
                    'period' => $period,
                ]);
            }
        }


        /*$request = Yii::$app->request;
        $model = new ProgressReports();

        if ($request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new ProgressReports",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new ProgressReports",
                    'content' => '<span class="text-success">Create ProgressReports success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new ProgressReports",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {

            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }*/
       
    }

    /**
     * Updates an existing ProgressReports model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $status = array('1'=>'Active','0'=>'In-Active');
        $flags = array('1'=>'Yes','0'=>'No');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update AccountReports #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'status' => $status,
                        'flags' => $flags,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "AccountReports #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update AccountReports #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'status' => $status,
                        'flags' => $flags,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'status' => $status,
                    'flags' => $flags,
                ]);
            }
        }
    }

    /**
     * Delete an existing ProgressReports model.
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
     * Delete multiple existing ProgressReports model.
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
     * Finds the ProgressReports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgressReports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArcAccountReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
