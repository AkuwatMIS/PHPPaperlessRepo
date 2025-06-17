<?php

namespace frontend\controllers;

use common\components\Helpers\CacheHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\BaAssets;
use common\models\BaDetails;
use common\models\search\ApplicationsSearch;
use Yii;
use common\models\AppraisalsBusiness;
use common\models\search\BusinessAppraisalSearch;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

/**
 * BusinessAppraisalController implements the CRUD actions for AppraisalsBusiness model.
 */
class BusinessAppraisalController extends Controller
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
     * Lists all AppraisalsBusiness models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new BusinessAppraisalSearch();
        $key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }
        $regions=Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions'=>$regions
        ]);
    }


    /**
     * Displays a single AppraisalsBusiness model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "AppraisalsBusiness #".$id,
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
     * Creates a new AppraisalsBusiness model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new AppraisalsBusiness();
        $ba_details=new \common\models\BaDetails();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new AppraisalsBusiness",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new AppraisalsBusiness",
                    'content'=>'<span class="text-success">Create AppraisalsBusiness success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new AppraisalsBusiness",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else {
            /*
            *   Process for non-ajax request
            */
            if ($request->isPost) {
                $model->load($request->post());
                $transaction=Yii::$app->db->beginTransaction();
                $model->business_type = isset($model->application->activity_id)?$model->application->activity_id:'';
                $flag=true;
                if($request->post()['AppraisalsBusiness']['business']=='new'){
                    if(empty($request->post()['AppraisalsBusiness']['ba_new_required_assets']) || (empty($request->post()['AppraisalsBusiness']['ba_new_required_assets_total']))){
                        $model->addError('ba_new_required_assets','If Business is new then New Required Assets can not be empty');
                        $model->addError('ba_new_required_assets_total','If Business is new then New Required Assets Total can not be empty');
                        $flag=false;

                    }
                }
                else if($request->post()['AppraisalsBusiness']['business']=='old'){
                    if(empty($request->post()['AppraisalsBusiness']['ba_fixed_buiness_assets']) || (empty($request->post()['AppraisalsBusiness']['ba_fixed_buiness_assets_total'])) ){
                        $model->addError('ba_fixed_buiness_assets','If Business is old then Fixed Business Assets can not be empty');
                        $model->addError('ba_fixed_buiness_assets_total','If Business is old then Fixed Business Assets Total can not be empty');
                        $flag=false;
                    }
                    if (empty($request->post()['AppraisalsBusiness']['ba_running_capital']) || (empty($request->post()['AppraisalsBusiness']['ba_running_capital_total']))){
                        $model->addError('ba_running_capital','If Business is old then Running Capital can not be empty');
                        $model->addError('ba_running_capital_total','If Business is old then Running Capital Total can not be empty');

                        $flag=false;
                    }
                    if (empty($request->post()['AppraisalsBusiness']['ba_business_expenses' ]) || (empty($request->post()['AppraisalsBusiness']['ba_business_expenses_total']))){
                        $model->addError('ba_business_expenses','If Business is old then Business Expenses can not be empty');
                        $model->addError('ba_business_expenses_total','If Business is old then Business Expenses Total can not be empty');

                        $flag=false;
                    }
                }
                if($flag==false){
                    return $this->render('create', [
                        'model' => $model,
                        'ba_details'=>$ba_details,
                    ]);
                }
                if ($model->save()) {
                    if (!empty($request->post()['AppraisalsBusiness']['ba_fixed_buiness_assets'])) {
                        $ba_asset = new BaAssets();
                        foreach ($request->post()['AppraisalsBusiness']['ba_fixed_buiness_assets'] as $asset) {
                            $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                        }
                        $ba_asset->ba_id = $model->id;
                        $ba_asset->application_id = $model->application_id;
                        $ba_asset->type = 'ba_fixed_business_assets';
                        $ba_asset->total_amount = isset($request->post()['AppraisalsBusiness']['ba_fixed_business_assets_total'])?$request->post()['AppraisalsBusiness']['ba_fixed_business_assets_total']:0;
                        $ba_asset->type = 'fixed_business_assets';
                        $ba_asset->total_amount = isset($request->post()['BusinessAppraisal']['ba_fixed_business_assets_total'])?$request->post()['BusinessAppraisal']['ba_fixed_business_assets_total']:0;
                        $ba_asset->created_by = Yii::$app->user->getId();
                        $ba_asset->assigned_to = Yii::$app->user->getId();
                        if(!$flag =$ba_asset->save()){
                            $transaction->rollBack();
                        }
                    }
                    if (!empty($request->post()['AppraisalsBusiness']['ba_running_capital'])) {

                        $ba_asset = new BaAssets();
                        foreach ($request->post()['AppraisalsBusiness']['ba_running_capital'] as $asset) {
                            $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                        }
                        $ba_asset->ba_id = $model->id;
                        $ba_asset->application_id = $model->application_id;
                        $ba_asset->type = 'running_capital';
                        $ba_asset->total_amount = isset($request->post()['AppraisalsBusiness']['ba_running_capital_total'])?$request->post()['AppraisalsBusiness']['ba_running_capital_total']:0;
                        $ba_asset->created_by = Yii::$app->user->getId();
                        $ba_asset->assigned_to = Yii::$app->user->getId();
                        if(!$flag =$ba_asset->save()){
                            $transaction->rollBack();
                        }
                    }
                    if (!empty($request->post()['AppraisalsBusiness']['ba_business_expenses'])) {
                        $ba_asset = new BaAssets();
                        foreach ($request->post()['AppraisalsBusiness']['ba_business_expenses'] as $asset) {
                            $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                        }
                        $ba_asset->ba_id = $model->id;
                        $ba_asset->application_id = $model->application_id;
                        $ba_asset->type = 'business_expenses';
                        $ba_asset->total_amount = isset($request->post()['AppraisalsBusiness']['ba_business_expenses_total'])?$request->post()['AppraisalsBusiness']['ba_business_expenses_total']:0;
                        $ba_asset->created_by = Yii::$app->user->getId();
                        $ba_asset->assigned_to = Yii::$app->user->getId();
                        if(!$flag =$ba_asset->save()){
                            $transaction->rollBack();
                        }
                    }
                    if (!empty($request->post()['AppraisalsBusiness']['ba_new_required_assets'])) {
                        $ba_asset = new BaAssets();
                        foreach ($request->post()['AppraisalsBusiness']['ba_new_required_assets'] as $asset) {
                            $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                        }
                        $ba_asset->ba_id = $model->id;
                        $ba_asset->application_id = $model->application_id;
                        $ba_asset->type = 'new_required_assets';
                        $ba_asset->total_amount =isset($request->post()['AppraisalsBusiness']['ba_new_required_assets_total'])?$request->post()['AppraisalsBusiness']['ba_new_required_assets_total']:0;
                        $ba_asset->created_by = Yii::$app->user->getId();
                        $ba_asset->assigned_to = Yii::$app->user->getId();
                        if(!$flag =$ba_asset->save()){
                            $transaction->rollBack();
                        }
                    }

                    if (!empty($request->post()['BaDetails'])) {
                        $ba_details=new BaDetails();
                        $ba_details->load($request->post());
                        $ba_details->ba_id=$model->id;
                        $ba_details->application_id = $model->application_id;
                        if(!$flag =$ba_details->save()){
                            $transaction->rollBack();
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        ////Application Actions
                        $action_model = ApplicationActions::find()->where(['parent_id' => $model->application_id])->andWhere(['action'=>'business_appraisal'])->one();
                        if (!empty($action_model)) {
                            $action_model->status = 1;
                            $action_model->save();
                        }
                        $application=Applications::find()->where(['id'=>$model->application_id])->one();
                        $application->status='approved';
                        $application->save();

                        $action_model = new ApplicationActions();
                        $action_model->parent_id = $model->application_id;
                        $action_model->user_id =Yii::$app->user->getId();
                        $action_model->action = "approved/rejected";
                        $action_model->status='1';
                        $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                        $action_model->save();

                        $action_model = new ApplicationActions();
                        $action_model->parent_id = $model->application_id;
                        $action_model->user_id = Yii::$app->user->getId();
                        $action_model->action = "group_formation";
                        $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                        $action_model->save();

                        /*$action_model = new ApplicationActions();
                        $action_model->parent_id = $model->application_id;
                        $action_model->user_id =Yii::$app->user->getId();
                        $action_model->action = "family_member_info";
                        $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                        $action_model->status=1;
                        $action_model->save();*/

                        return $this->redirect(['applications/view', 'id' => $model->application_id]);
                    } else {

                        return $this->render('create', [
                            'model' => $model,
                            'ba_details' => $ba_details,
                        ]);
                    }
                }
                else {
                    return $this->render('create', [
                        'model' => $model,
                        'ba_details'=>$ba_details,
                    ]);
                }
            }
            else {
                return $this->render('create', [
                    'model' => $model,
                    'ba_details'=>$ba_details
                ]);
            }
        }
       
    }

    /**
     * Updates an existing AppraisalsBusiness model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $ba_details=BaDetails::find()->where(['application_id'=>$model->application_id])->one();
        $ba_assets_business = BaAssets::find()->select(['assets_list','total_amount'])->where(['type' => 'fixed_business_assets', 'application_id' => $model->application_id])->asArray()->one();
        $fixed_business_assets_dropdown=\common\components\Helpers\ListHelper::getLists($model->application->activity->name.'-fixed_business_assets');
        if (!empty($ba_assets_business)) {

            $ba_fixed_business_assets = explode(',', $ba_assets_business['assets_list']);
            $model->ba_fixed_buiness_assets_total=$ba_assets_business['total_amount'];
            $model->ba_fixed_buiness_assets=$ba_fixed_business_assets;
        }
        $running_capital = BaAssets::find()->select(['assets_list','total_amount'])->where(['type' => 'running_capital', 'application_id' => $model->application_id])->asArray()->one();
        $running_capital_dropdown=\common\components\Helpers\ListHelper::getLists($model->application->activity->name.'-running_capital');
        if (!empty($running_capital)) {

            $running_capital_assets = explode(',', $running_capital['assets_list']);
            $model->ba_running_capital_total=$running_capital['total_amount'];
            $model->ba_running_capital=$running_capital_dropdown;
        }
        $new_required_assets = BaAssets::find()->select(['assets_list','total_amount'])->where(['type' => 'new_required_assets', 'application_id' => $model->application_id])->asArray()->one();
        $new_required_dropdown=\common\components\Helpers\ListHelper::getLists($model->application->activity->name.'-new_required_assets');
        if (!empty($new_required_assets)) {

            $new_required_assetss = explode(',', $new_required_assets['assets_list']);
            $model->ba_new_required_assets=$new_required_assetss;
            $model->ba_new_required_assets_total=$new_required_assets['total_amount'];
        }
        $business_expenses = BaAssets::find()->select(['assets_list','total_amount'])->where(['type' => 'business_expenses', 'application_id' => $model->application_id])->asArray()->one();
        $business_expenses_dropdown=\common\components\Helpers\ListHelper::getLists($model->application->activity->name.'-business_expenses');
        if (!empty($business_expenses)) {
            $business_expenses_assets = explode(',', $business_expenses['assets_list']);
            $model->ba_business_expenses=$business_expenses_assets;
            $model->ba_business_expenses_total=$business_expenses['total_amount'];
        }
        /*echo '<pre>';
                print_r($model->ba_fixed_buiness_assets);
                die();*/
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update AppraisalsBusiness #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "AppraisalsBusiness #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update AppraisalsBusiness #".$id,
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
                $flag=true;
                ////
                $transaction=Yii::$app->db->beginTransaction();
                if($request->post()['BusinessAppraisal']['business']=='new'){
                    if(empty($request->post()['BusinessAppraisal']['ba_new_required_assets']) || (empty($request->post()['BusinessAppraisal']['ba_new_required_assets_total']))){
                        $model->addError('ba_new_required_assets','If Business is new then New Required Assets can not be empty');
                        $model->addError('ba_new_required_assets_total','If Business is new then New Required Assets Total can not be empty');
                        $flag=false;

                    }
                }
                else if($request->post()['BusinessAppraisal']['business']=='old'){
                    if(empty($request->post()['BusinessAppraisal']['ba_fixed_buiness_assets']) || (empty($request->post()['BusinessAppraisal']['ba_fixed_buiness_assets_total'])) ){
                        $model->addError('ba_fixed_buiness_assets','If Business is old then Fixed Business Assets can not be empty');
                        $model->addError('ba_fixed_buiness_assets_total','If Business is old then Fixed Business Assets Total can not be empty');
                        $flag=false;
                    }
                    if (empty($request->post()['BusinessAppraisal']['ba_running_capital']) || (empty($request->post()['BusinessAppraisal']['ba_running_capital_total']))){
                        $model->addError('ba_running_capital','If Business is old then Running Capital can not be empty');
                        $model->addError('ba_running_capital_total','If Business is old then Running Capital Total can not be empty');

                        $flag=false;
                    }
                    if (empty($request->post()['BusinessAppraisal']['ba_business_expenses' ]) || (empty($request->post()['BusinessAppraisal']['ba_business_expenses_total']))){
                        $model->addError('ba_business_expenses','If Business is old then Business Expenses can not be empty');
                        $model->addError('ba_business_expenses_total','If Business is old then Business Expenses Total can not be empty');

                        $flag=false;
                    }
                }
                if($flag==false){
                    return $this->render('create', [
                        'model' => $model,
                        'ba_details'=>$ba_details,
                    ]);
                }
                if ($model->save()) {
                    if (!empty($request->post()['BusinessAppraisal']['ba_fixed_buiness_assets'])) {
                        $ba_asset = $ba_asset =BaAssets::find()->where(['type'=>'ba_fixed_buiness_assets','application_id'=>$model->application_id])->one();
                        if(!empty($ba_asset)) {
                            foreach ($request->post()['BusinessAppraisal']['ba_fixed_buiness_assets'] as $asset) {
                                $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                            }
                            $ba_asset->ba_id = $model->id;
                            $ba_asset->application_id = $model->application_id;
                            $ba_asset->type = 'fixed_business_assets';
                            $ba_asset->total_amount = isset($request->post()['BusinessAppraisal']['ba_fixed_business_assets_total']) ? $request->post()['BusinessAppraisal']['ba_fixed_business_assets_total'] : 0;
                            $ba_asset->created_by = Yii::$app->user->getId();
                            $ba_asset->assigned_to = Yii::$app->user->getId();
                            if (!$flag = $ba_asset->save()) {
                                $transaction->rollBack();
                            }
                        }
                    }
                    if (!empty($request->post()['BusinessAppraisal']['ba_running_capital'])) {

                        $ba_asset =BaAssets::find()->where(['type'=>'ba_running_capital','application_id'=>$model->application_id])->one();
                        if(!empty($ba_asset)){
                            foreach ($request->post()['BusinessAppraisal']['ba_running_capital'] as $asset) {
                                $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                            }
                            $ba_asset->ba_id = $model->id;
                            $ba_asset->application_id = $model->application_id;
                            $ba_asset->type = 'running_capital';
                            $ba_asset->total_amount = isset($request->post()['BusinessAppraisal']['ba_running_capital_total'])?$request->post()['BusinessAppraisal']['ba_running_capital_total']:0;
                            $ba_asset->created_by = Yii::$app->user->getId();
                            $ba_asset->assigned_to = Yii::$app->user->getId();
                            if(!$flag =$ba_asset->save()){
                                $transaction->rollBack();
                            }
                        }

                    }
                    if (!empty($request->post()['BusinessAppraisal']['ba_business_expenses'])) {
                        $ba_asset = BaAssets::find()->where(['type'=>'ba_business_expenses','application_id'=>$model->application_id])->one();
                        if (!empty($ba_asset)) {
                            foreach ($request->post()['BusinessAppraisal']['ba_business_expenses'] as $asset) {
                                $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                            }
                            $ba_asset->ba_id = $model->id;
                            $ba_asset->application_id = $model->application_id;
                            $ba_asset->type = 'business_expenses';
                            $ba_asset->total_amount = isset($request->post()['BusinessAppraisal']['ba_business_expenses_total']) ? $request->post()['BusinessAppraisal']['ba_business_expenses_total'] : 0;
                            $ba_asset->created_by = Yii::$app->user->getId();
                            $ba_asset->assigned_to = Yii::$app->user->getId();
                            if (!$flag = $ba_asset->save()) {
                                $transaction->rollBack();
                            }
                        }
                    }
                    if (!empty($request->post()['BusinessAppraisal']['ba_new_required_assets'])) {
                        $ba_asset =  BaAssets::find()->where(['type'=>'ba_new_required_assets','application_id'=>$model->application_id])->one();
                        if (!empty($ba_asset)) {
                            foreach ($request->post()['BusinessAppraisal']['ba_new_required_assets'] as $asset) {
                                $ba_asset->assets_list = $ba_asset->assets_list . $asset . ',';
                            }
                            $ba_asset->ba_id = $model->id;
                            $ba_asset->application_id = $model->application_id;
                            $ba_asset->type = 'new_required_assets';
                            $ba_asset->total_amount = isset($request->post()['BusinessAppraisal']['ba_new_required_assets_total']) ? $request->post()['BusinessAppraisal']['ba_new_required_assets_total'] : 0;
                            $ba_asset->created_by = Yii::$app->user->getId();
                            $ba_asset->assigned_to = Yii::$app->user->getId();
                            if (!$flag = $ba_asset->save()) {
                                $transaction->rollBack();
                            }
                        }
                    }

                    if (!empty($request->post()['BaDetails'])) {
                        $ba_details=BaDetails::find()->where(['ba_id'=>$model->id])->one();
                        $ba_details->load($request->post());
                        $ba_details->ba_id=$model->id;
                        $ba_details->application_id = $model->application_id;
                        if(!$flag =$ba_details->save()){
                            $transaction->rollBack();
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['applications/view', 'id' => $model->application_id]);
                    }

                } else {

                        return $this->render('create', [
                            'model' => $model,
                            'ba_details' => $ba_details,
                        ]);
                    }
                ///





                //return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'ba_details'=>$ba_details,
                    'fixed_business_assets_dropdown'=>$fixed_business_assets_dropdown,
                    'running_capital_dropdown'=>$running_capital_dropdown,
                    'new_required_dropdown'=>$new_required_dropdown,
                    'business_expenses_dropdown'=>$business_expenses_dropdown,
                ]);
            }
        }
    }

    /**
     * Delete an existing AppraisalsBusiness model.
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
     * Delete multiple existing AppraisalsBusiness model.
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
     * Finds the AppraisalsBusiness model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppraisalsBusiness the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppraisalsBusiness::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionActivity($id)
    {
       $application=Applications::find()->where(['id'=>$id])->one();
        $business_appraisal['activity'] = isset($application->activity->name)?$application->activity->name:'';
        $business_appraisal['ba_fixed_business_assets']='';
        $business_appraisal['ba_running_capital']='';
        $business_appraisal['ba_business_expense']='';
        $business_appraisal['ba_new_required_assets']='';

        $fixed_business_assets=\common\components\Helpers\ListHelper::getLists($business_appraisal['activity'].'-fixed_business_assets');
        foreach ($fixed_business_assets as $key => $value){
            $business_appraisal['ba_fixed_business_assets'].='<option value='.$key.'>'.$value.'</option>';
        }
        $running_capital=\common\components\Helpers\ListHelper::getLists($business_appraisal['activity'].'-running_capital');
        foreach ($running_capital as $key => $value){
            $business_appraisal['ba_running_capital'].='<option value='.$key.'>'.$value.'</option>';

        }
        $business_expense=\common\components\Helpers\ListHelper::getLists($business_appraisal['activity'].'-business_expenses');
        foreach ($business_expense as $key => $value){
            $business_appraisal['ba_business_expense'].='<option value='.$key.'>'.$value.'</option>';

        }
        $new_required_assets=\common\components\Helpers\ListHelper::getLists($business_appraisal['activity'].'-new_required_assets');
        foreach ($new_required_assets as $key => $value){
            $business_appraisal['ba_new_required_assets'].='<option value='.$key.'>'.$value.'</option>';

        }
        return json_encode($business_appraisal);
    }
    public function actionSearchApplication($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $result = array();
            $query = new Query();
            $query->select('applications.id, applications.application_no,members.full_name,members.cnic')
                ->from('applications')
                ->join('inner join','members','members.id=applications.member_id')
                //->join('left join','business_appraisal','business_appraisal.application_id=applications.id')
                ->join('inner join','application_actions','application_actions.parent_id=applications.id')
                ->filterWhere(['like', 'applications.application_no', $q])
                ->orFilterWhere(['like', 'members.cnic', $q])
                ->orFilterWhere(['like', 'members.full_name', $q])
                ->andFilterWhere(['!=','applications.deleted','1'])
                ->andFilterWhere(['=','application_actions.action','business_appraisal'])
                ->andFilterWhere(['=','application_actions.status','0'])
                ->andFilterWhere(['>=','application_actions.expiry_date',time()])
                //->andFilterWhere(['is','business_appraisal.application_id',new \yii\db\Expression('null')])

                ->orderBy(['applications.created_at' => SORT_DESC]);

            $command = $query->createCommand();
            $data = $command->queryAll();

            foreach($data as $k=>$v) {
                $result[$k]['id'] = $v['id'];
                $result[$k]['text'] = '<strong>Application No</strong>: ' . $v['application_no'] . ' <strong>Member Name</strong>: ' . $v['full_name'] . ' <strong>CNIC</strong>: ' . $v['cnic'];
            }
            $out['results'] = $result;

        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Applications::findOne($id)->form_no];
        }
        return $out;
    }
    /*public function actionVerification()
    {
        $searchModel = new ApplicationsSearch();
        $dataProvider = $searchModel->searchunverifiedlist(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, 'applications', 'index',$this->rbac_type);

        $regions_by_id = Yii::$app->Permission->getRegionList('applications', 'index',$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionListNameWise('applications', 'index',$this->rbac_type);
        $areas = Yii::$app->Permission->getAreaListNameWise('applications', 'index',$this->rbac_type);
        $branches = Yii::$app->Permission->getBranchListNameWise('applications', 'index',$this->rbac_type);
        $projects = Yii::$app->Permission->getProjectListNameWise('applications', 'index',$this->rbac_type);

        return $this->render('verification', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions_by_id,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects,
        ]);
    }

    public function actionVerify($id = 0)
    {
        if (Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            if ($request['member_check'] == 1 && $request['application_check'] == 1 && $request['social_appraisal_check'] == 1 && $request['business_appraisal_check'] == 1 && $request['documents_check'] == 1) {
                $application=Applications::find()->where(['id'=>$id])->one();
                $application->status='approved';
                $application->save();
                $verify_status = ApplicationActions::find()->where(['parent_id' => $id, 'action' => 'approved/rejected'])->one();
                $verify_status->status = 1;
                if($verify_status->save()){
                    $action_model = new ApplicationActions();
                    $action_model->parent_id = $id;
                    $action_model->user_id = Yii::$app->user->getId();
                    $action_model->action = "group_formation";
                    $action_model->expiry_date = strtotime(date("Y-m-d H:i:s",time() ) . " +1 month");
                    $action_model->save();
                }
            }
            return $this->redirect('verification');
        }
        $model = Applications::findOne($id);
        return $this->render('_verify-form', [
            'model' => $model

        ]);
    }*/
}
