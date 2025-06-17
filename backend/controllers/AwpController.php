<?php

namespace backend\controllers;

use common\models\ArcAccountReports;
use common\models\Awp;
use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\search\AwpSearch;
use Yii;
use common\models\Blacklist;
use common\models\search\BlacklistSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * BlacklistController implements the CRUD actions for Blacklist model.
 */
class AwpController extends Controller
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
     * Lists all Blacklist models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            //'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Awp();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Awp",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Awp",
                    'content'=>'<span class="text-success">Create Awp success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "Create new Awp",
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
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

    }
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
                    'title'=> "Update Awp #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Awp #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                return [
                    'title'=> "Update Awp #".$id,
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
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Awp #".$id,
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
    public function actionIndexSearch()
    {
        $searchModel = new AwpSearch();
        $dataProvider = $searchModel->search_index(Yii::$app->request->queryParams);

        return $this->render('index_', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionUnlockAwp()
    {
        $unlock=Yii::$app->db->createCommand('update awp set is_lock=0 where month="'.date('Y-m').'"')->execute();
        $lock=Yii::$app->db->createCommand('update awp set is_lock=1 where month="'.date("Y-m",strtotime("-1 month")).'"')->execute();

//        $first_day_of_this_month=strtotime(date('Y-m-01'));
//        $last_day_of_current_month=strtotime(date('Y-m-t'));
//
//        $arc_account_report = ArcAccountReports::find()
//            ->andWhere(['=','report_name','Recovery Summary'])
//            ->andWhere(['between','report_date',$first_day_of_this_month,$last_day_of_current_month])
//            ->one();
//        if($arc_account_report){
//            foreach ($arc_account_report as $report){
//                $model = ArcAccountReports::find()->where(['id'=>$report->id])->one();
//                $model->is_awp = 1;
//                $model->save(false);
//            }
//        }

        return $this->redirect('index');
    }
    public function actionLockAwp()
    {
        $unlock=Yii::$app->db->createCommand('update awp set is_lock=1 where month="'.date('Y-m').'"')->execute();
        //=Yii::$app->db->createCommand('update awp set is_lock=1 where month="'.date("Y-m",strtotime("-1 month")).'"')->execute();
        return $this->redirect('index');
    }

    public function actionCreateAwpBranch()
    {
        $month = isset(Yii::$app->request->post()['month'])?Yii::$app->request->post()['month']:date('Y-m');
        $branches = Branches::find()->where(['id' => Yii::$app->request->post()['branch_id']])->asArray()->all();
        $branch_projects_query = BranchProjectsMapping::find()->select('project_id')->where(['branch_id' =>Yii::$app->request->post()['branch_id']])->asArray()->all();
        $d_Awp=Awp::find()->where(['month' => $month, 'branch_id' => Yii::$app->request->post()['branch_id']])->andWhere(['not in','project_id',$branch_projects_query])->all();
        if(!empty($d_Awp)){
            foreach($d_Awp as $del){
                $del->delete();
            }
        }
        foreach ($branches as $branch) {
            $branch_projects = BranchProjectsMapping::find()->where(['branch_id' => $branch['id']])->all();
            foreach ($branch_projects as $proj) {
                $awp = Awp::find()->where(['project_id' => $proj['project_id'], 'month' => $month, 'branch_id' => $branch['id']])->one();
                if (empty($awp)) {
                    $awp = new Awp();
                    $awp->branch_id = $branch['id'];
                    $awp->region_id = $branch['region_id'];
                    $awp->area_id = $branch['area_id'];
                    $awp->project_id = $proj['project_id'];
                    $awp->month = $month;
                    if (!$awp->save()) {
                    };
                }

            }
        }
        return $this->redirect('index');
    }
    protected function findModel($id)
    {
        if (($model = Awp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
