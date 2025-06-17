<?php

namespace backend\controllers;

use common\components\ProgressReportHelper;
use common\models\Areas;
use common\models\Branches;
use common\models\ProgressReports;
use common\models\Projects;
use common\models\Regions;
use Yii;
use common\models\ProgressReportUpdate;
use common\models\search\ProgressReportUpdateSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ProgressReportUpdateController implements the CRUD actions for ProgressReportUpdate model.
 */
class ProgressReportUpdateController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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
     * Lists all ProgressReportUpdate models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ProgressReportUpdateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions=ArrayHelper::map(Regions::find()->select('id,name')->all(), 'id', 'name');
        $areas=ArrayHelper::map(Areas::find()->select('id,name')->all(), 'id', 'name');
        $branches=ArrayHelper::map(Branches::find()->select('id,name')->all(), 'id', 'name');
        $progress_report_data = ProgressReports::find()->select(['id', 'project_id','report_date as name'])/*->orderBy(['report_date'=>SORT_DESC])*/->asArray()->all();
        $progress_report_data_ = [];
        $i = 0;
        foreach($progress_report_data as $d) {
            if ($d['project_id'] == 0) {
                $progress_report_data_[$i]['id'] = $d['id'];
                $progress_report_data_[$i]['name'] = date('M j, Y', ($d['name'])) . '(Overall)';
                //   break;
            } else {
                $project_name = Projects::find()->select('name')->where(['id' => $d['project_id']])->one()['name'];
                $progress_report_data_[$i]['name'] = date('M j, Y', $d['name']) . '(' . $project_name . ')';
                $progress_report_data_[$i]['id'] = $d['id'];
            }
            $i++;
        }
        $progress_report_data=ArrayHelper::map($progress_report_data_, 'id', 'name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions'=>$regions,
            'areas'=>$areas,
            'branches'=>$branches,
            'progress_report_data'=>$progress_report_data

        ]);
    }


    /**
     * Displays a single ProgressReportUpdate model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "ProgressReportUpdate #".$id,
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
     * Creates a new ProgressReportUpdate model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ProgressReportUpdate();
        $progress_report_ = [];
        $i = 0;
        $progress_reports = ProgressReports::find(/*['name']*/)->select('id,period,project_id,report_date as name')->asArray()->all();
        foreach ($progress_reports as $progress_report) {
           if ($progress_report['project_id'] == 0) {
               $progress_report_[$i]['name'] = date('M j, Y', $progress_report['name']) . '(Overall)';
                $progress_report_[$i]['id'] = $progress_report['id'];
            } else {
                $project_name=Projects::find()->select('name')->where(['id'=>$progress_report['project_id']])->one()['name'];
                $progress_report_[$i]['name'] = date('M j, Y', $progress_report['name']) . '('.$project_name. ')';
                $progress_report_[$i]['id'] = $progress_report['id'];
            }
            $i++;
        }
        $progress_reports = ArrayHelper::map($progress_report_, 'id', 'name');
        $regions = ArrayHelper::map(Regions::find()->select('id,name')->all(), 'id', 'name');
       // array_splice($regions, 0, 0, array('Select Region'));
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new ProgressReportUpdate",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'progress_reports' => $progress_reports,
                        'regions' => $regions,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save(false)) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new ProgressReportUpdate",
                    'content' => '<span class="text-success">Create ProgressReportUpdate success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new ProgressReportUpdate",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'progress_reports' => $progress_reports,
                        'regions' => $regions,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'progress_reports' => $progress_reports,
                    'regions' => $regions,
                ]);
            }
        }
    }

    /**
     * Updates an existing ProgressReportUpdate model.
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
                    'title'=> "Update ProgressReportUpdate #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "ProgressReportUpdate #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update ProgressReportUpdate #".$id,
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

    /**
     * Delete an existing ProgressReportUpdate model.
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
     * Delete multiple existing ProgressReportUpdate model.
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
     * Finds the ProgressReportUpdate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgressReportUpdate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgressReportUpdate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionFetchAreaByRegion()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Areas::find()->select(['id', 'name'])->where(['region_id'=>$rgId])->asArray()->all();
            //$out = Yii::$app->Permission->getAreasByRegion(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId);
            echo Json::encode(['output'=>$out, 'selected'=>'']);
            return;
        }

        echo Json::encode(['output'=>'', 'selected'=>'']);
        return;
    }

    public function actionFetchBranchByArea()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            $rgId = $parents[0];
            $out = Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->asArray()->all();
            //$out = Yii::$app->Permission->getBranchesByArea(Yii::$app->controller->id, Yii::$app->controller->action->id,$rgId);
            echo Json::encode(['output'=>$out, 'selected'=>'']);
            return;
        }

        echo Json::encode(['output'=>'', 'selected'=>'']);
        return;
    }
}
