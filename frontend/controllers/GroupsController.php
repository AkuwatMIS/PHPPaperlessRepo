<?php

namespace frontend\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Appraisals;
use common\models\Branches;
use common\models\GroupActions;
use common\models\Guarantors;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\ProjectAppraisalsMapping;
use common\models\search\ApplicationsSearch;
use Ratchet\App;
use Yii;
use common\models\Groups;
use common\models\search\GroupsSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

/**
 * GroupsController implements the CRUD actions for Groups model.
 */
class GroupsController extends Controller
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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
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
     * Lists all Groups models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['GroupsSearch']);
            $groups = array();
            $searchModel = new GroupsSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['grp_no'] = $g['grp_no'];
                $groups[$i]['group_name'] = $g['group_name'];
                $groups[$i]['grp_type'] = $g['grp_type'];
                $groups[$i]['region_id'] = isset($g->region->name) ? $g->region->name : '';
                $groups[$i]['area_id'] = isset($g->area->name) ? $g->area->name : '';
                $groups[$i]['branch_id'] = isset($g->branch->name) ? $g->branch->name : '';
                $groups[$i]['team_id'] = isset($g->team->name) ? $g->team->name : '';
                $groups[$i]['field_id'] = isset($g->field->name) ? $g->field->name : '';
                $i++;
            }
            ExportHelper::ExportCSV('groups.csv', $headers, $groups);
            die();
        }
        $searchModel = new GroupsSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        $regions_by_id = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions_by_id' => $regions_by_id,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
        ]);
    }

    /**
     * Displays a single Applications model.
     * @param integer $id
     * @return mixed
     */
    public function actionMemberDetails($id)
    {
        $this->layout = 'main_simple_js';
        return $this->render('memberDetails', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Groups model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $regions = Yii::$app->Permission->getRegionListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Groups #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {

            $applications = new ActiveDataProvider([
                'query' => Applications::find()->where(['group_id' => $id]),
            ]);

            return $this->render('view', [
                'model' => $this->findModel($id),
                'applications' => $applications,
                'regions' => $regions,
                'areas' => $areas,
                'branches' => $branches,
            ]);
        }
    }

    /**
     * Creates a new Groups model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionIndgroup()
    {
        $flag = true;
        if (Yii::$app->request->post()) {
            $request = Yii::$app->request->post();

            $branch_id = $team_id = $field_id = 0;
            $group = new Groups();
            $application = Applications::find()->where(['id' => $request['Applications']['id']])->one();
            $group->load(Yii::$app->request->post());
            $group->grp_type = 'IND';
            $group->branch_id = $application->branch_id;
            $group->team_id = $application->team_id;
            $group->field_id = $application->field_id;
            $branch = Branches::findOne($application->branch_id);
            $transaction = Yii::$app->db->beginTransaction();
            $group = GroupHelper::createGroup($branch, $group);
            if ($group->id) {
                //$application=Applications::find()->where(['id'=>$request['Applications']['id']])->one();
                $application->group_id = $group->id;
                $application->is_lock = 0;

                if ($application->save(false)) {
                    $guar = [];
                    $loan_check = 0;
                    if (in_array($application->project_id, [59, 60])) {
                        $loan_check = 1;
                    }

                    //$_gaurantors = \common\models\Model::createMultiple(Guarantors::classname());
                    //\common\models\Model::loadMultiple($_gaurantors, Yii::$app->request->post());
                    $guarantorsArray = Yii::$app->request->post()['Guarantors'];
                    $checkGuarantors = GroupHelper::checkGuarantors($guarantorsArray, $application);

                    //  if($checkGuarantors){

                    foreach ($request['Guarantors'] as $key => $guarantor) {
                        if (!empty($guarantor['cnic'])) {
                            $guarantor_save = GroupHelper::saveGuarantor($guarantor, $group->id, 1, $loan_check, $application->id);
                            $modelGuarantor = Guarantors::find()
                                ->where(['cnic' => $guarantor['cnic'], 'deleted' => 0])
                                ->one();

                            if ($modelGuarantor) {

                                $activeLoanGuarantor = Loans::find()
                                    ->innerJoin('applications', 'applications.id=loans.application_id')
                                    ->innerJoin('guarantors', 'guarantors.group_id=applications.group_id')
                                    ->where(['guarantors.cnic' => $guarantor['cnic']])
                                    ->andWhere(['in', 'loans.status', ['collected', 'not collected', 'pending']])
                                    ->exists();

                                if ($activeLoanGuarantor) {
                                    $activeLoanGroup = Loans::find()
                                        ->innerJoin('applications', 'applications.id=loans.application_id')
                                        ->innerJoin('guarantors', 'guarantors.group_id=applications.group_id')
                                        ->innerJoin('groups', 'groups.id=loans.group_id')
                                        ->where(['guarantors.cnic' => $guarantor['cnic']])
                                        ->andWhere(['in', 'loans.status', ['collected', 'not collected', 'pending']])
                                        ->select(['loans.*'])
                                        ->one();

                                    if(!empty($activeLoanGroup)){
                                        $grpNo = $activeLoanGroup->group->grp_no;
                                    }else{
                                        $grpNo= '';
                                    }
                                    $guarantor_save->addError('id', 'Guarantor CNIC ' . $guarantor['cnic'] . 'As is already attached with an active loan with following group no: '.$grpNo);
                                    $flag = false;
                                }

                                $guarantorLoanAppIds = Loans::find()
                                    ->select('applications.id')
                                    ->innerJoin('applications', 'applications.id = loans.application_id')
                                    ->innerJoin('guarantors', 'guarantors.group_id = applications.group_id')
                                    ->where(['guarantors.cnic' => $guarantor['cnic']])
                                    ->column();

                                $guarantorLoanAppIds = array_map('intval', $guarantorLoanAppIds);

                                $queryApplication = Applications::find()
                                    ->select(['applications.id'])
                                    ->innerJoin('guarantors', 'guarantors.group_id = applications.group_id')
                                    ->where(['guarantors.cnic' => $guarantor['cnic']])
                                    ->andWhere(['in', 'applications.status', ['approved']]);

                                if (!empty($guarantorLoanAppIds)) {
                                    $queryApplication->andWhere(['not in', 'applications.id', $guarantorLoanAppIds]);
                                }

                                $activeAppGuarantor = $queryApplication->one();

                                if (!empty($activeAppGuarantor) && $activeAppGuarantor!=null && $activeAppGuarantor->id!=$application->id) {
                                    $flag = false;
                                    $queryApplicationGroup = Applications::find()
                                        ->select(['applications.*'])
                                        ->innerJoin('guarantors', 'guarantors.group_id = applications.group_id')
                                        ->innerJoin('groups', 'groups.id=applications.group_id')
                                        ->where(['guarantors.cnic' => $guarantor['cnic']])
                                        ->andWhere(['in', 'applications.status', ['approved']]);
                                         if (!empty($guarantorLoanAppIds)) {
                                             $queryApplicationGroup->andWhere(['not in', 'applications.id', $guarantorLoanAppIds]);
                                         }
                                    $activeAppGroup = $queryApplicationGroup->one();

                                    if(!empty($activeAppGroup)){
                                        $grpNo = $activeAppGroup->group->grp_no;
                                    }else{
                                        $grpNo= '';
                                    }

                                    $guarantor_save->addError('id', 'This CNIC ' . $guarantor['cnic'] . 'As Guarantor is already attached with an approved application with following group no: '.$grpNo);
                                }
                                // Save guarantor if all checks passed

                                if (isset($guarantor_save->id)) {
                                    $guar[] = $guarantor_save;
                                } else {
                                    $flag = false;
                                    $guar[] = $guarantor_save;
                                }
                            } else {
                                if (isset($guarantor_save->id)) {
                                    $guar[] = $guarantor_save;
                                } else {
                                    $guarantor_save->addError('cnic', 'Guarantor not saved!');
                                    $flag = false;
                                    $guar[] = $guarantor_save;
                                }
                            }
                        }
                    }

                    /* }else{
                         $model = new Guarantors();
                         $model->reject_reason='guarantor criteria does not matched';
                         $model->addError('cnic', 'Applicant Guarantor criteria does not matched, So Group not generated.');
                         $flag = false;
                         $guar[] = $model;
                     }*/
                    /*foreach ($_gaurantors as $key=>$guarantor){
                        $guarantor->group_id=$group->id;
                        $guarantor_save=GroupHelper::saveGuarantor($guarantor);
                        if(isset($guarantor_save->id)) {

                        }
                        else{
                            $flag=false;
                            foreach($guarantor_save as $_key=>$_val){
                                $_gaurantors[$key]->addError($_key, $_val[0]);
                            }
                        }
                    }*/
                    if ($flag == false) {
                        $transaction->rollBack();
                        if (isset($guarantor_save->reject_reason) && !empty($guarantor_save->reject_reason)) {
                            $application->status = 'rejected';
                            $application->reject_reason = $guarantor_save->reject_reason;
                            $application->save();
                        }

                        return $this->render('_ind-group-form', [
                            'model' => $guar,
                            'application' => $application,
                            'group' => $group
                        ]);
                    } else {
                        $transaction->commit();
                    }
                    ActionsHelper::updateAction('application', $application->id, 'group_formation');
                    ActionsHelper::insertActions('group', 0, $group->id, $group->created_by);

                    return $this->redirect(['view', 'id' => $group->id]);
                }
            } else {
                print_r($group->getErrors());
                die();
            }
        } else {
            $request = Yii::$app->request->get();
            $application = Applications::findOne($request['applications']);
            $model[] = new Guarantors();
            $model[] = new Guarantors();
            $group = new Groups();
            return $this->render('_ind-group-form', [
                'model' => $model,
                'application' => $application,
                'group' => $group
            ]);
        }

    }

    public function actionCreate()
    {

        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new Groups();
        $searchModel = new Applications();

        if (!isset($_SESSION['applications'])) {
            $_SESSION['applications'] = array();

        }
        $applications = $session['applications'];

        if (isset($request->post()['Applications'])) {

            $data = $request->post();

            foreach ($applications as $a) {
                if ($data['Applications']['type'] == 'application_no') {
                    if ($a->application_no == $data['Applications']['application_no']) {
                        $session->addFlash('error', 'Application already added');
                        return $this->redirect('/groups/create');
                    }
                } else {
                    if ($a->member->cnic == $data['Applications']['application_no']) {
                        $session->addFlash('error', 'Application already added');
                        return $this->redirect('/groups/create');
                    }
                }


            }
            if ($data['Applications']['type'] == 'application_no') {
                $application = Applications::find()->where(['application_no' => $data['Applications']['application_no'], 'deleted' => 0])->andWhere(['!=', 'applications.status', 'rejected']);
                Yii::$app->Permission->getSearchFilterQuery($application, 'applications', 'index', $this->rbac_type);
                $application = $application->one();
            } else {
                //die('aa');
                $application = Applications::find()
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->andFilterWhere(['=', 'members.cnic', $data['Applications']['application_no']])
                    ->andFilterWhere(['=', 'applications.deleted', 0])
                    ->andFilterWhere(['!=', 'applications.status', 'rejected'])
                    ->orderBy(['id' => SORT_DESC]);
                Yii::$app->Permission->getSearchFilterQuery($application, 'applications', 'index', $this->rbac_type);
                $application = $application->one();
            }
            if (!empty($application)) {

                if ($application->status != 'approved') {
                    $session->addFlash('error', 'Application is not approved');
                    return $this->redirect('/groups/create');
                }
                /*$appraisals=ProjectAppraisalsMapping::find()->where(['project_id'=>$application->project_id])->all();
                foreach($appraisals as $appraisal){
                    $appr=Appraisals::findOne($appraisal->appraisal_id);
                    $action=ApplicationActions::find()->where(['parent_id'=>$application->id,'action'=>$appr->name,'status'=>1])->one();
                    if(empty($action)){
                        $session->addFlash('error', $appr->name.' is not done');
                        return $this->redirect('/groups/create');
                    }

                }*/
                $application_appr_action = ApplicationActions::find()->where(['parent_id' => $application->id, 'action' => 'approved/rejected'])->one();
                if (empty($application_appr_action) || $application_appr_action->status == 0) {
                    $session->addFlash('error', 'Application is not approved');
                    return $this->redirect('/groups/create');
                }
                if (in_array($application->project_id, StructureHelper::trancheProjectsList())) {
                    $image = Images::find()->where(['parent_id' => $application->id, 'image_type' => 'property_document_1'])->one();
//                    $member_image_front = Images::find()->where(['parent_id' => $application->member->id,'image_type'=>'cnic_front'])->one();
//                    $member_image_back = Images::find()->where(['parent_id' => $application->member->id,'image_type'=>'cnic_back'])->one();
//                    $profile_image = Images::find()->where(['parent_id' => $application->member->id,'image_type'=>'profile_pic'])->one();
                    if (empty($image)) {
                        $session->addFlash('error', 'Application Documents Missing');
                        return $this->redirect('/groups/create');
                    }
//                    elseif (empty($member_image_front) || empty($member_image_back)){
//                        $session->addFlash('error', 'Memebrs Documents Missing');
//                        return $this->redirect('/groups/create');
//                    }else if(empty($profile_image)){
//                        $session->addFlash('error', "Memebr's Profile Picture Missing");
//                        return $this->redirect('/groups/create');
//                    }
                }

//                if(in_array($application->project_id,StructureHelper::verifyProjectsDocument())){
                $member_image_front = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'cnic_front'])->one();
                $member_image_back = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'cnic_back'])->one();
                $profile_image = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'profile_pic'])->one();
                if ((empty($profile_image) || empty($member_image_front) || empty($member_image_back))) {
                    $session->addFlash('error', 'Memebrs Documents Missing');
                    return $this->redirect('/groups/create');
                }
//                }
                /*if(empty($application->agricultureAppraisal) && $application->project_id == 3){
                    $session->addFlash('error', 'Agriculture Appraisal is not done');
                    return $this->redirect('/groups/create');
                }*/
                if ($application->group_id != 0) {
                    $session->addFlash('error', 'Group is already assigned');
                    return $this->redirect('/groups/create');
                }
                if (count($applications) == 0) {
                    $_SESSION['branch'] = $application->branch_id;
                } else {
                    $application_b = Applications::find()->where(['id' => $application->id])->one();
                    if ($application_b->branch_id != $_SESSION['branch']) {
                        $session->addFlash('error', 'Application is not of same branch');
                        return $this->redirect('/groups/create');
                    }
                }
                $applications[] = $application;
                $session['applications'] = $applications;
                $session->addFlash('success', 'Application added successfully');
            } else {
                $session->addFlash('error', 'Application No not found');
            }
        }
        if (isset($request->post()['Groups']) && $model->load($request->post())) {

            if (count($applications) > 0) {
                $group_size = GroupHelper::validateGroupsize($applications);
                if (!$group_size) {
                    $session->addFlash('error', 'Group Size is not valid');
                    return $this->redirect('/groups/create');
                }

                if (count($applications) == 1) {
                    $as = $applications;
                    unset($session['applications']);
                    return $this->redirect(array('indgroup', 'request' => $request->post(), 'applications' => $as['0']->id));
                    // return $this->redirect('indgroup?id='.$model);
                }
                $branch_id = $team_id = $field_id = 0;
                foreach ($applications as $a) {
                    $branch_id = $a->branch_id;
                    $team_id = $a->team_id;
                    $field_id = $a->field_id;
                    if ($a->group_id > 0) {
                        $str = 'Form No. ' . $a->application_no . ' has already been allocated.';
                        $session->addFlash('error', $str);
                    }
                }

                $model->grp_type = count($applications) > 1 ? 'GRP' : 'IND';
                $model->branch_id = $branch_id;
                $model->team_id = $team_id;
                $model->field_id = $field_id;
                $branch = Branches::findOne($branch_id);

                $model = GroupHelper::createGroup($branch, $model);
                if ($model->id) {
                    foreach ($applications as $a) {
                        $a->group_id = $model->id;
                        $a->is_lock = 0;
                        if ($a->save()) {
                            ////Application Actions
                        }
                    }
                    ActionsHelper::insertActions('group', 0, $model->id, $model->created_by);
                    unset($session['applications']);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $session->addFlash('error', 'There is nothing to add to the group. Please add some form no. to be further allocated in a group.');
            }

        } else {

            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel
            ]);
        }
    }

    /**
     * Delete multiple existing Groups model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $session = Yii::$app->session;
        $applications = $session['applications'];
        foreach ($applications as $key => $a) {
            if ($a->id == $id) {
                unset($applications[$key]);
                $session['applications'] = $applications;
            }
        }
        $session->addFlash('success', 'Application Remove Successfully');
        return $this->redirect('/groups/create');
    }

    /**
     * Delete multiple existing Groups model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemovemember($id, $grp_id)
    {

        $session = Yii::$app->session;
        $applications = $session['applications'];

        foreach ($applications as $key => $a) {
            if ($a->id == $id) {
                $app_model = Applications::find()->where(['id' => $id])->one();

                if (!empty($app_model)) {
                    $app_model->group_id = 0;
                    $app_model->save(false);
                }
                unset($applications[$key]);
                $session['applications'] = $applications;
            }
        }
        $session->addFlash('success', 'Application Remove Successfully');
        return $this->redirect('/groups/update?id=' . $grp_id);
    }

    /**
     * Updates an existing Groups model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionIndUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            $flag = true;
            $request = Yii::$app->request->post();
            $application = Applications::find()->where(['id' => $request['Applications']['id']])->one();
            foreach ($request['Guarantors'] as $g) {
                $guarantor_save = GroupHelper::saveGuarantor($g, $id, 1, 0, $application->id);
                if (!empty($guarantor_save->getErrors())) {
                    $flag = false;
                }
                $guarantors[] = $guarantor_save;
                /*$guarantor=Guarantors::find()->where(['id'=>$g['id'],'deleted'=>'0'])->one();
                $guarantor->name=$g['name'];
                $guarantor->parentage=$g['parentage'];
                $guarantor->cnic=$g['cnic'];
                $guarantor->phone=$g['phone'];
                $guarantor->address=$g['address'];
                $guarantor->save();*/
            }
            if ($flag == true) {
                return $this->redirect('/groups/view?id=' . $id);
            } else {
                return $this->render('_ind-group-update', [
                    'model' => $model,
                    'guarantors' => $guarantors

                ]);
            }

        }
        $model = $this->findModel($id);
        $guarantors = Guarantors::find()->where(['group_id' => $model->id, 'deleted' => '0'])->all();

        return $this->render('_ind-group-update', [
            'model' => $model,
            'guarantors' => $guarantors

        ]);
    }

    public function actionUpdate($id)
    {

        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = $this->findModel($id);
        $searchModel = new Applications();

        if (isset($_SESSION['applications']) && !empty($_SESSION['applications'])) {
            /*echo'<pre>';
            print_r(count($session['applications']));
            die('a');*/
        } else {
            /*echo'<pre>';
            print_r(count($session['applications']));
            die('ab');*/
            $_SESSION['applications'] = $model->applications;
            //$_SESSION['branch']=$model->branch_id;
        }

        $applications = $session['applications'];

        if (isset($request->post()['Applications'])) {

            $data = $request->post();

            foreach ($applications as $a) {
                if ($data['Applications']['type'] == 'application_no') {
                    if ($a->application_no == $data['Applications']['application_no']) {
                        $session->addFlash('error', 'Application already added');
                        return $this->redirect('/groups/create');
                    }
                } else {
                    if ($a->member->cnic == $data['Applications']['application_no']) {
                        $session->addFlash('error', 'Application already added');
                        return $this->redirect('/groups/create');
                    }
                }


            }
            if ($data['Applications']['type'] == 'application_no') {
                $application = Applications::find()->where(['application_no' => $data['Applications']['application_no'], 'deleted' => 0]);
                Yii::$app->Permission->getSearchFilterQuery($application, 'applications', 'index', $this->rbac_type);
                $application = $application->one();
            } else {
                //die('aa');
                $application = Applications::find()
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->andFilterWhere(['=', 'members.cnic', $data['Applications']['application_no']])
                    ->andFilterWhere(['=', 'applications.deleted', 0])
                    ->orderBy(['created_at' => SORT_DESC]);
                Yii::$app->Permission->getSearchFilterQuery($application, 'applications', 'index', $this->rbac_type);
                $application = $application->one();
            }
            if (!empty($application)) {


                if ($application->status != 'approved') {
                    $session->addFlash('error', 'Application is not approved');
                    return $this->redirect('/groups/update?id=' . $id);
                }
                /*$appraisals=ProjectAppraisalsMapping::find()->where(['project_id'=>$application->project_id])->all();
                foreach($appraisals as $appraisal){
                    $appr=Appraisals::findOne($appraisal->appraisal_id);
                    $action=ApplicationActions::find()->where(['parent_id'=>$application->id,'action'=>$appr->name,'status'=>1])->one();
                    if(empty($action)){
                        $session->addFlash('error', $appr->name.' is not done');
                        return $this->redirect('/groups/update?id='.$id);
                    }

                }*/
                $application_appr_action = ApplicationActions::find()->where(['parent_id' => $application->id, 'action' => 'approved/rejected'])->one();
                if (empty($application_appr_action) || $application_appr_action->status == 0) {
                    $session->addFlash('error', 'Application is not approved');
                    return $this->redirect('/groups/update?id=' . $id);
                }
                if (in_array($application->project_id, StructureHelper::trancheProjects())) {
                    $image = Images::find()->where(['parent_id' => $application->id, 'image_type' => 'property_document_1'])->one();
                    $member_image_front = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'cnic_front'])->one();
                    $member_image_back = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'cnic_back'])->one();
                    $profile_image = Images::find()->where(['parent_id' => $application->member->id, 'image_type' => 'profile_pic'])->one();
                    if (empty($image)) {
                        $session->addFlash('error', 'Application Documents Missing.');
                        return $this->redirect('/groups/update?id=' . $id);
                    } elseif (empty($member_image_front) || empty($member_image_back)) {
                        $session->addFlash('error', 'Members Documents Missing.');
                        return $this->redirect('/groups/update?id=' . $id);
                    } else if (empty($profile_image)) {
                        $session->addFlash('error', "Memebr's Profile Picture Missing");
                        return $this->redirect('/groups/update?id=' . $id);
                    }
                }
                if ($application->group_id != 0) {
                    $session->addFlash('error', 'Group is already assigned');
                    return $this->redirect('/groups/update?id=' . $id);


                }
                if (isset($application->loan) && !empty($application->loan)) {
                    $session->addFlash('error', 'Loan is created against this application');
                    return $this->redirect('/groups/update?id=' . $id);


                } /*if(count($applications)==0){
                    $_SESSION['branch']=$application->branch_id;
                }*/
                else {
                    $application_b = Applications::find()->where(['id' => $application->id])->one();
                    if ($application_b->branch_id != $model->branch_id) {
                        $session->addFlash('error', 'Application is not of same branch');
                        return $this->redirect('/groups/update?id=' . $id);


                    }
                }

                $applications[] = $application;
                $session['applications'] = $applications;

                $session->addFlash('success', 'Application added successfully');
            } else {
                $session->addFlash('error', 'Application No not found');
            }
        }

        if (isset($request->post()['Groups']) /*&& $model->load($request->post())*/) {
            $group_size = GroupHelper::validateGroupSize($applications);
            if (!$group_size) {
                $session->addFlash('error', 'Group Size is not valid');
                return $this->redirect('/groups/update?id=' . $id);
            }
            foreach ($applications as $app) {
                $app->group_id = $id;
                $app->save(false);
            }
            unset($session['applications']);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * Delete an existing Groups model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    public function actionDeleteIndGrp($id)
    {
        $model = Groups::find()->where(['id' => $id])->one();
        $guarantiors = Guarantors::find()->where(['group_id' => $model->id, 'deleted' => '0'])->all();
        foreach ($guarantiors as $guarantior) {
            $guarantior->deleted = 1;
        }
        $model->deleted = 1;
        $model->save();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple existing Groups model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the Groups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groups::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
