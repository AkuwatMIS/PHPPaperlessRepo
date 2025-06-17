<?php

namespace backend\controllers;

use common\components\Helpers\ConfigurationsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use common\models\MembersAccount;
use common\models\MembersAddress;
use common\models\MembersPhone;
use common\models\Regions;
use Illuminate\Support\Arr;
use Yii;
use common\models\Members;
use common\models\search\MembersSearch;
use common\models\Model;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class MembersController extends Controller
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
     * Lists all Members models.
     * @return mixed
     */
    public function actionIndex()
    {
        /*$date1 = date('Y-m-10', '1526108334');
        print_r($date1);
        die();*/

        $searchModel = new MembersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'array'=>([
                'branches'=>$branches,
            ]),
        ]);
    }
    public function actionIndexSearch()
    {
        $regions=ArrayHelper::map(Regions::find()->all(),'id','name');
        $searchModel = new MembersSearch();
        if(empty(Yii::$app->request->queryParams)){
            $dataProvider=array();
        }else{
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');
        return $this->render('index_search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions'=>$regions,
            'array'=>([
                'branches'=>$branches,
            ]),
        ]);
    }

    /**
     * Displays a single Members model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $parentage_type=array("father"=>"Father","husband"=>"Husband");
        $gender=array("m"=>"Male","f"=>"Female","t"=>"Transgender");
        $configurations = ConfigurationsHelper::getConfig($id,"member");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("member");
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Members #".$id,
                'content'=>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                    'array'=>([
                        'gender'=>$gender,
                        'parentage_type'=>$parentage_type,
                        'configurations'=>$configurations,
                        'global_configurations'=>$global_configurations,
                    ]),
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'array'=>([
                    'gender'=>$gender,
                    'parentage_type'=>$parentage_type,
                    'configurations'=>$configurations,
                    'global_configurations'=>$global_configurations,
                ]),
            ]);
        }
    }

    /**
     * Creates a new Members model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Members();
        $parentage_type=array("father"=>"Father","husband"=>"Husband");
        $gender=array("m"=>"Male","f"=>"Female","t"=>"Transgender");
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Members",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'gender'=>$gender,
                            'parentage_type'=>$parentage_type
                        ]),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Members",
                    'content'=>'<span class="text-success">Create Members success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "Create new Members",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'array'=>([
                            'gender'=>$gender,
                            'parentage_type'=>$parentage_type
                        ]),
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
                    'array'=>([
                        'gender'=>$gender,
                        'parentage_type'=>$parentage_type
                    ]),
                ]);
            }
        }

    }

    /**
     * Updates an existing Members model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $membersAddress = $model->membersAddresses;
        $membersPhone = $model->membersPhones;
        $membersEmail = $model->membersEmails;
        $membersAccount=$model->membersAccount;
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        //$active_loans=MemberHelper::checkActiveLoan($model->cnic);

        $address_count = count($membersAddress);
        $phone_count = count($membersPhone);
        if($address_count == 0){
            $memberAddress = new MembersAddress();
            $membersAddress = array_fill(0, 2, $memberAddress);
        }
        if($address_count == 1){
            $membersAddress[0] = $membersAddress[0];
            $membersAddress[1] = new MembersAddress();
        }
        if($phone_count == 0){
            $memberPhone = new MembersPhone();
            $membersPhone = array_fill(0, 2, $memberPhone);
        }
        if($phone_count == 1){
            $membersPhone[0] = $membersPhone[0];
            $membersPhone[1] = new MembersPhone();
        }
        /*if(!empty($active_loans)){
            $model->addError('cnic','Active Loan exists against this cnic');
            if($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($request->isGet) {
                    return [
                        'title' => "Update Members #" . $id,
                        'content' => $this->renderAjax('update', [
                            'model' => $model,
                            'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                            'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                            'membersEmail' => $membersEmail,
                            'branches' => $branches,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                    ];
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'branches' => $branches,
                ]);
            }
        }*/
        $configurations = ConfigurationsHelper::getConfig($id,"member");
        $global_configurations = ConfigurationsHelper::getConfigGlobal("member");
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Members #".$id,
                    'content'=>$this->renderAjax('update',  [
                        'model' => $model,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                        'branches' => $branches,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post())){
                $membersAccount=new MembersAccount();
                $membersPhone = Model::createMultiple(MembersPhone::classname());
                Model::loadMultiple($membersPhone, Yii::$app->request->post());
                $membersAddress = Model::createMultiple(MembersAddress::classname());
                Model::loadMultiple($membersAddress, Yii::$app->request->post());
                $membersAccount->load($request->post());
                $transaction = Yii::$app->db->beginTransaction();
                $region_area=StructureHelper::getRegionAreaFromBranch($model->branch_id);
                $model->region_id = $region_area['region_id'];
                $model->area_id = $region_area['area_id'];
                /* if(!empty($model->family_member_cnic)){
                     $family_member_cnic_check=MemberHelper::checkActiveLoan($model->family_member_cnic);
                     if(!empty($family_member_cnic_check)){
                         $model->addError('family_member_cnic',"Already have active loan against family member cnic");
                     }
                     $family_member_cnic=MemberHelper::checkActiveLoanFamilyMember($model->family_member_cnic);
                     if(!empty($family_member_cnic)){
                         $model->addError('family_member_cnic',"Family member cnic already exists against active loan");
                     }
                 }
                 $family_member_cnic_=MemberHelper::checkActiveLoanFamilyMember($model->cnic);
                 if(!empty($family_member_cnic_)){
                     $model->addError('cnic',"This cnic already exists against a family member cnic of active loan");
                 }*/
                if (!empty($model->getErrors())) {
                    return $this->render('update', [
                        'model' => $model,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                        'branches' => $branches,
                    ]);
                }
                else {
                    if ($flag = $model->save()) {

                        foreach ($membersPhone as $memberPhone) {
                            $memberPhone->member_id = $model->id;
                            $memberPhone->is_current = 1;
                            if (empty(MembersPhone::find()->where(['member_id' => $model->id, 'phone_type' => $memberPhone->phone_type, 'phone' => $memberPhone->phone])->one())) {
                                $member_aphones_ = MembersPhone::find()->where(['member_id' => $model->id, 'phone_type' => $memberPhone->phone_type])->all();
                                foreach ($member_aphones_ as $phone_model) {
                                    $phone_model->is_current = 0;
                                    $phone_model->save(false);
                                }
                                if (!empty($memberPhone->phone)) {
                                    if (!($flag = $memberPhone->save())) {
                                        $transaction->rollback();
                                    }
                                }
                            }
                        }

                        foreach ($membersAddress as $memberAddress) {
                            $memberAddress->member_id = $model->id;
                            $memberAddress->is_current = 1;
                            if (empty(MembersAddress::find()->where(['member_id' => $model->id, 'address_type' => $memberAddress->address_type, 'address' => $memberAddress->address])->one())) {
                                $members_addresses_ = MembersAddress::find()->where(['member_id' => $model->id, 'address_type' => $memberAddress->address_type])->all();
                                foreach ($members_addresses_ as $address_model) {
                                    $address_model->is_current = 0;
                                    $address_model->save();
                                }
                                if (!($flag = $memberAddress->save())) {
                                    $transaction->rollback();
                                }
                            }
                        }

                        if(!empty($request->post()['MembersAccount']['bank_name']) && !empty($request->post()['MembersAccount']['account_no']) && !empty($request->post()['MembersAccount']['title'])){
                            $membersAccount->member_id = $model->id;
                            $membersAccount->is_current = 1;
                            $account_save=MemberHelper::saveMemberAccount($membersAccount);
                            if(!$account_save){
                                $transaction->rollback();
                            }
                        }
                    }
                }
                if (isset($flag) && !empty($flag)) {
                    $transaction->commit();
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Members #".$id,
                        'content'=>$this->renderAjax('view', [
                            'model' => $this->findModel($id),
                            'array'=>([
                                'configurations'=>$configurations,
                                'global_configurations'=>$global_configurations,
                            ]),
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                    ];
                }else {
                    $transaction->rollBack();
                    return [
                        'title'=> "Update Members #".$id,
                        'content'=>$this->renderAjax('update', [
                            'model' => $model,
                            'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                            'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                            'membersEmail' => $membersEmail,
                            'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                            'branches' => $branches,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }
            }else{
                return [
                    'title'=> "Update Members #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                        'branches' => $branches,
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
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                    'branches' => $branches,
                ]);
            }
        }
    }

    /**
     * Delete an existing Members model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $member = $this->findModel($id);
        $member->deleted = 1;
        $member->deleted_by = Yii::$app->user->getId();
        $member->deleted_at = strtotime(date('Y-m-d'));
        $member->save();

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
     * Delete multiple existing Members model.
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
     * Finds the Members model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Members the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Members::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLogs($id = null ,$field = null)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        /*print_r($model);
        die();*/
        if($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Log Members #" . $id,
                    'header'=> [
                        'close' =>['display'=> 'none'],
                    ],
                    'content' => $this->renderAjax('logs', [
                        'id' => $id,
                        'field' => $field,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]).
                        Html::a('Back',['view','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }
        }

        return $this->render('logs', [
            'id' => $id,
            'field' => $field,
        ]);
    }

    public function actionMembersLogs()
    {
        $request = Yii::$app->request;
        if($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Log Members",
                    'header'=> [
                        'close' =>['display'=> 'none'],
                    ],
                    'content' => $this->renderAjax('logs', [
                        'id' => '',
                        'field' => '',
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                ];
            }
        }
        return $this->render('logs', [
            'id' => '',
            'field' => '',
        ]);
    }
}
