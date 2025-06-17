<?php

namespace frontend\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\ReportHelper;
use common\models\DisbursementDetails;
use common\models\Referrals;
use common\models\ReportDefinations;
use common\components\Helpers\StructureHelper;
use Yii;
use common\models\DynamicReports;
use common\models\search\DynamicReportsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * DynamicReportsController implements the CRUD actions for DynamicReports model.
 */
class DynamicReportsController extends Controller
{
    /**
     * @inheritdoc
     */
    public $rbac_type = 'frontend';

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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                   // 'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all DynamicReports models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DynamicReportsSearch();
        $searchModel->report_defination_id = [1,2,3,4,5,6,7,8,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAcc()
    {
        $searchModel = new DynamicReportsSearch();
        $searchModel->report_defination_id = 16;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index-area-acc', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionApproveReports()
    {
        $request=Yii::$app->request;
        if($request->post() && isset($request->post()['selection']) ) {
            foreach ($request->post()['selection'] as $id) {
                $report = DynamicReports::findOne($id);
                $report->is_approved = $request->post()['status'];
                $report->save();
            }
        }
        $searchModel = new DynamicReportsSearch();
        $searchModel->status=0;
        $searchModel->is_approved=0;

        $dataProvider = $searchModel->search_approval_list(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        return $this->render('approve-reports/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DynamicReports model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "DynamicReports #".$id,
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
     * Creates a new DynamicReports model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new DynamicReports();
        $modelReferrals = ArrayHelper::map(StructureHelper::getReferrals(), 'id', 'name');

        /*if(Yii::$app->user->identity->role->item_name=='ADMIN'){
            $reports_list = ArrayHelper::map(ReportDefinations::find()->where(['in','id',[18]])->all(),'id','name');
        }else{
            $reports_list = ArrayHelper::map(ReportDefinations::find()->where(['is','role',null])->orWhere(['role' => Yii::$app->user->identity->role->item_name])->andWhere(['!=','type','progress'])->all(),'id','name');
        }*/
        $reports_list = ArrayHelper::map(ReportDefinations::find()->where(['is','role',null])->orWhere(['role' => Yii::$app->user->identity->role->item_name])->andWhere(['!=','type','progress'])->all(),'id','name');

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){

                return [
                    'title'=> "Create new DynamicReports",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post())){
                $reports_columns = ReportHelper::getColumnName($model->report->name);
                $query_filters = '';

                $arrayExtract = [9,10,11,15,16,17,18,19,22,23,32,33,40,42,43];
                if(!in_array($model->report_defination_id,$arrayExtract)) {
                    if($model->report_defination_id == 6 || $model->report_defination_id == 7 || $model->report_defination_id == 4){
                        $query_filters .= '  AND l.status != \'grant\'';
                    }else{
                        if($model->report_defination_id == 37){
                            $query_filters .= '  AND a.status != \'grant\'';
                        }else{
                            $query_filters .= '  AND loans.status != \'grant\'';
                        }
                    }
                }


                if($model->report_defination_id==20 && isset($model->referral_id) && !empty($model->referral_id) && ($model->referral_id!=0))
                {
                    $query_filters .= ' AND applications.'.$reports_columns['referral_id'] .' = '. $model->referral_id;
                }
                if(isset($model->region_id) && !empty($model->region_id))
                {
                    $query_filters .= ' AND '.$reports_columns['region_id'] .' = '. $model->region_id;
                }
                if(isset($model->area_id) && !empty($model->area_id))
                {
                    $query_filters .= ' AND '.$reports_columns['area_id'] .' = '. $model->area_id;
                }
                if(isset($model->branch_id) && !empty($model->branch_id))
                {
                    $query_filters .= ' AND '.$reports_columns['branch_id'] .' = '. $model->branch_id;
                }
                if(isset($model->project_id) && !empty($model->project_id))
                {
                    $query_filters .= ' AND '.$reports_columns['project_id'] .' = '. $model->project_id;
                }
                $reportArrayDateDisbursed = [24,34,35,36,37];
                if (in_array($model->report_defination_id, $reportArrayDateDisbursed)) {
                    $date = explode(' - ', $model->report_date);
                    $query_filters .= ' and ' . $reports_columns['date_disbursed'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['date_disbursed'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                }

                if($model->report_defination_id==22){
                    $date = explode(' - ', $model->report_date);
                    $query_filters .= ' and ' . $reports_columns['application_date'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['application_date'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                }

                if($model->report_defination_id==32){
                    $date = explode(' - ', $model->report_date);
                    $query_filters .= ' and ' . $reports_columns['updated_at'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['updated_at'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    $query_filters .= ' GROUP BY app.application_no';
                }

                if($model->report_defination_id==14) {
                    if (isset($model->notification) && !empty($model->notification)) {
                        $date = explode(' - ', $model->notification);
                        $query_filters .= ' and ' . $reports_columns['date_disbursed'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['date_disbursed'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    }
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' and ' . $reports_columns['application_date'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['application_date'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    }
                }
                if($model->report_defination_id==12) {
                    $query_filters .= ' GROUP BY member_id';
                }
                if($model->report_defination_id==14) {
                    $query_filters .= ' GROUP BY loans.branch_id order by loans.region_id asc';
                }
                if($model->report_defination_id==21) {
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' and ' . $reports_columns['date_disbursed'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['date_disbursed'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    }
                    $query_filters .= ' GROUP BY t.id';
                }

                $arrayIds = [22,14,24,32,34,35,36,37,43,21];
                if(!in_array($model->report_defination_id,$arrayIds)) {
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' HAVING ' . $reports_columns['date'] . ' >= "' . $date[0] . '" AND ' . $reports_columns['date'] . ' <= "' . $date[1] . ' 23:59:59"';
                    }
                }

                $model->sql_filters = $query_filters;
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $date1=strtotime(date('Y-m-d'));
                $date2=strtotime(date('Y-m-d 23-59-59'));
                //check if report already exists
                if (isset($model->report_date) && !empty($model->report_date)) {
                    $dynamic_report = DynamicReports::find()->where([
                        'sql_filters' => $model->sql_filters,
                        'report_defination_id' => $model->report_defination_id,
                        'status' => '1',
                        'deleted' => 0
                    ])->andWhere(['between', 'created_at',$date1,$date2])->one();
                    if (!empty($dynamic_report)) {
                        $model->file_path = $dynamic_report->file_path;
                        $model->status = '1';
                    }
                }
                /*if(in_array(Yii::$app->user->identity->role->item_name,array('ADMIN','PM'))){
                    $model->is_approved=1;
                }*/

                if(in_array($model->report_defination_id,[11,15,16,17,18,19,40,44])){
                    if($model->report_defination_id==11){
                        $folder='ledger';
                    }else if($model->report_defination_id==15){
                        $folder='schedule';
                    }else if($model->report_defination_id==17){
                        $folder='member_info';
                    }else if($model->report_defination_id==19){
                        $folder='response_description';
                    }else if($model->report_defination_id==40){
                        $folder='duelist';
                    }else if($model->report_defination_id==44){
                        $folder='duelist';
                    }else{
                        $folder='account';
                    }
                    $model->file = UploadedFile::getInstance($model, 'file');
                    $random = Rand(1111, 9999);
                    if($model->validate()) {
                        $model->file->saveAs(ImageHelper::getAttachmentPath().'/dynamic_reports/' . $folder . '/' . $random.'_'.$model->file->baseName . '.' . $model->file->extension);
                        $model->uploaded_file = $random.'_'.$model->file->name;
                    }
                }

                if($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new DynamicReports",
                        'content' => '<span class="text-success">Create DynamicReports success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    return [
                        'title'=> "Create new DynamicReports",
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'modelReferrals' => $modelReferrals,
                            'reports_list' => $reports_list,
                            'regions' => $regions,
                            'projects' => $projects,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }
            }else{
                return [
                    'title'=> "Create new DynamicReports",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $reports_columns = ReportHelper::getColumnName($model->report->name);
                $query_filters = '';
                if(isset($model->region_id) && !empty($model->region_id))
                {
                    $query_filters .= ' AND '.$reports_columns['region_id'] .' = '. $model->region_id;
                }
                if(isset($model->area_id) && !empty($model->area_id))
                {
                    $query_filters .= ' AND '.$reports_columns['area_id'] .' = '. $model->area_id;
                }
                if(isset($model->branch_id) && !empty($model->branch_id))
                {
                    $query_filters .= ' AND '.$reports_columns['branch_id'] .' = '. $model->branch_id;
                }
                if(isset($model->project_id) && !empty($model->project_id))
                {
                    $query_filters .= ' AND '.$reports_columns['project_id'] .' = '. $model->project_id;
                }
                if($model->report_defination_id==14) {
                    if (isset($model->notification) && !empty($model->notification)) {
                        $date = explode(' - ', $model->notification);
                        $query_filters .= ' and ' . $reports_columns['date_disbursed'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['date_disbursed'] . ' <= "' . strtotime($date[1].'23:59:59').'" ';
                    }
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' and ' . $reports_columns['receive_date'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['receive_date'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    }
                }

                if($model->report_defination_id==14) {
                    $query_filters .= ' GROUP BY loans.branch_id order by loans.region_id asc';
                }

                if($model->report_defination_id==21) {
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' and ' . $reports_columns['date_disbursed'] . ' >= "' . strtotime($date[0]) . '" AND ' . $reports_columns['date_disbursed'] . ' <= "' . strtotime($date[1].'23:59:59').'"';
                    }
                    $query_filters .= ' GROUP BY t.id';
                }

                if(!in_array($model->report_defination_id, [14,21])) {
                    if (isset($model->report_date) && !empty($model->report_date)) {
                        $date = explode(' - ', $model->report_date);
                        $query_filters .= ' HAVING ' . $reports_columns['date'] . ' >= "' . $date[0] . '" AND ' . $reports_columns['date'] . ' <= "' . $date[1] . ' 23:59:59"';
                    }
                }

                $model->sql_filters = $query_filters;
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $date1=strtotime(date('Y-m-d'));
                $date2=strtotime(date('Y-m-d 23-59-59'));
                //check if report already exists
                if (isset($model->report_date) && !empty($model->report_date)) {
                    $dynamic_report = DynamicReports::find()->where([
                        'sql_filters' => $model->sql_filters,
                        'report_defination_id' => $model->report_defination_id,
                        'status' => '1',
                        'deleted' => 0
                    ])->andWhere(['between', 'created_at',$date1,$date2])->one();
                    if (!empty($dynamic_report)) {
                        $model->file_path = $dynamic_report->file_path;
                        $model->status = '1';
                    }
                }
                /*if(in_array(Yii::$app->user->identity->role->item_name,array('ADMIN','PM'))){
                    $model->is_approved=1;
                }*/
                if($model->report_defination_id==11){
                    $model->file = UploadedFile::getInstance($model, 'file');
                    $random = Rand(1111, 9999);
                    if($model->validate()) {
                        $model->file->saveAs(ImageHelper::getAttachmentPath().'/dynamic_reports/' . 'ledger' . '/' . $model->file->baseName . '.' . $model->file->extension);
                        $model->uploaded_file = $model->file->name;
                    }
                    if($model->save()){
                        return $this->redirect(['view', 'id' => $model->id]);
                    }else{
                        return $this->render('create', [
                            'model' => $model,
                            'modelReferrals' => $modelReferrals,
                            'reports_list' => $reports_list,
                            'regions' => $regions,
                            'projects' => $projects,
                        ]);
                    }
                } else {
                    return $this->render('create', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'modelReferrals' => $modelReferrals,
                    'reports_list' => $reports_list,
                    'regions' => $regions,
                    'projects' => $projects,
                ]);
            }
        }

    }


    public function actionCreateAcc()
    {
        $request = Yii::$app->request;
        $model = new DynamicReports();
        $modelReferrals = ArrayHelper::map(StructureHelper::getReferrals(), 'id', 'name');

        $reports_list = ArrayHelper::map(ReportDefinations::find()->where(['id'=>16])->all(),'id','name');

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new DynamicReports",
                    'content'=>$this->renderAjax('create_acc', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post())){
                $reports_columns = ReportHelper::getColumnName($model->report->name);
                $query_filters = '';

                if(isset($model->region_id) && !empty($model->region_id))
                {
                    $query_filters .= ' AND '.$reports_columns['region_id'] .' = '. $model->region_id;
                }
                if(isset($model->area_id) && !empty($model->area_id))
                {
                    $query_filters .= ' AND '.$reports_columns['area_id'] .' = '. $model->area_id;
                }
                if(isset($model->branch_id) && !empty($model->branch_id))
                {
                    $query_filters .= ' AND '.$reports_columns['branch_id'] .' = '. $model->branch_id;
                }
                if(isset($model->project_id) && !empty($model->project_id))
                {
                    $query_filters .= ' AND '.$reports_columns['project_id'] .' = '. $model->project_id;
                }

                $model->sql_filters = $query_filters;
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $date1=strtotime(date('Y-m-d'));
                $date2=strtotime(date('Y-m-d 23-59-59'));
                //check if report already exists
                if (isset($model->report_date) && !empty($model->report_date)) {
                    $dynamic_report = DynamicReports::find()->where([
                        'sql_filters' => $model->sql_filters,
                        'report_defination_id' => $model->report_defination_id,
                        'status' => '1',
                        'deleted' => 0
                    ])->andWhere(['between', 'created_at',$date1,$date2])->one();
                    if (!empty($dynamic_report)) {
                        $model->file_path = $dynamic_report->file_path;
                        $model->status = '1';
                    }
                }
                /*if(in_array(Yii::$app->user->identity->role->item_name,array('ADMIN','PM'))){
                    $model->is_approved=1;
                }*/

                $folder='account';
                $model->file = UploadedFile::getInstance($model, 'file');
                $random = Rand(1111, 9999);
                if($model->validate()) {
                    $model->file->saveAs(ImageHelper::getAttachmentPath().'/dynamic_reports/' . $folder . '/' . $random.'_'.$model->file->baseName . '.' . $model->file->extension);
                    $model->uploaded_file = $random.'_'.$model->file->name;
                }
                if($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new DynamicReports",
                        'content' => '<span class="text-success">Create DynamicReports success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    return [
                        'title'=> "Create new DynamicReports",
                        'content'=>$this->renderAjax('create_acc', [
                            'model' => $model,
                            'modelReferrals' => $modelReferrals,
                            'reports_list' => $reports_list,
                            'regions' => $regions,
                            'projects' => $projects,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                    ];
                }
            }else{
                return [
                    'title'=> "Create new DynamicReports",
                    'content'=>$this->renderAjax('create_acc', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $reports_columns = ReportHelper::getColumnName($model->report->name);
                $query_filters = '';
                if(isset($model->region_id) && !empty($model->region_id))
                {
                    $query_filters .= ' AND '.$reports_columns['region_id'] .' = '. $model->region_id;
                }
                if(isset($model->area_id) && !empty($model->area_id))
                {
                    $query_filters .= ' AND '.$reports_columns['area_id'] .' = '. $model->area_id;
                }
                if(isset($model->branch_id) && !empty($model->branch_id))
                {
                    $query_filters .= ' AND '.$reports_columns['branch_id'] .' = '. $model->branch_id;
                }
                if(isset($model->project_id) && !empty($model->project_id))
                {
                    $query_filters .= ' AND '.$reports_columns['project_id'] .' = '. $model->project_id;
                }

                $model->sql_filters = $query_filters;
                $model->visibility = 'all';
                $model->notification = 'all';
                $model->created_by = Yii::$app->user->getId();
                $date1=strtotime(date('Y-m-d'));
                $date2=strtotime(date('Y-m-d 23-59-59'));
                //check if report already exists
                if (isset($model->report_date) && !empty($model->report_date)) {
                    $dynamic_report = DynamicReports::find()->where([
                        'sql_filters' => $model->sql_filters,
                        'report_defination_id' => $model->report_defination_id,
                        'status' => '1',
                        'deleted' => 0
                    ])->andWhere(['between', 'created_at',$date1,$date2])->one();
                    if (!empty($dynamic_report)) {
                        $model->file_path = $dynamic_report->file_path;
                        $model->status = '1';
                    }
                }
                /*if(in_array(Yii::$app->user->identity->role->item_name,array('ADMIN','PM'))){
                    $model->is_approved=1;
                }*/
                if($model->report_defination_id==16){
                    $model->file = UploadedFile::getInstance($model, 'file');
                    $random = Rand(1111, 9999);
                    if($model->validate()) {
                        $model->file->saveAs(ImageHelper::getAttachmentPath().'/dynamic_reports/' . 'account' . '/' . $random.'_'.$model->file->baseName . '.' . $model->file->extension);
                        $model->uploaded_file = $model->file->name;
                    }
                    if($model->save()){
                        return $this->redirect(['view', 'id' => $model->id]);
                    }else{
                        return $this->render('create_acc', [
                            'model' => $model,
                            'modelReferrals' => $modelReferrals,
                            'reports_list' => $reports_list,
                            'regions' => $regions,
                            'projects' => $projects,
                        ]);
                    }
                } else {
                    return $this->render('create_acc', [
                        'model' => $model,
                        'modelReferrals' => $modelReferrals,
                        'reports_list' => $reports_list,
                        'regions' => $regions,
                        'projects' => $projects,
                    ]);
                }
            } else {
                return $this->render('create_acc', [
                    'model' => $model,
                    'modelReferrals' => $modelReferrals,
                    'reports_list' => $reports_list,
                    'regions' => $regions,
                    'projects' => $projects,
                ]);
            }
        }

    }

    /**
     * Updates an existing DynamicReports model.
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
                    'title'=> "Update DynamicReports #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "DynamicReports #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "Update DynamicReports #".$id,
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

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->deleted = 1;
        $model->save();
        return $this->redirect(['index']);

        /*if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {

            return $this->redirect(['index']);
        }*/


    }

    public function actionExports($folder,$file_name)
    {
        //$file_path = Yii::$app->basePath.'/web/'.$folder.'/'.$file_name;
        $file_path = ImageHelper::getAttachmentPath().'/'.$folder.'/'.$file_name;
        if(file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        }
        else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    protected function findModel($id)
    {
        if (($model = DynamicReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionFetchFilterList()
    {

    }
}
