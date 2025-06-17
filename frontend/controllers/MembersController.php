<?php

namespace frontend\controllers;

use common\components\Helpers\BlacklistHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use common\models\Applications;
use common\models\Cities;
use common\models\Images;
use common\models\Loans;
use common\models\MemberInfo;
use common\models\MembersAccount;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\NadraVerisys;
use common\models\search\MembersSearch;
use Yii;
use common\models\Members;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\models\Model;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class MembersController extends Controller
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
     * Lists all Members models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['MembersSearch']);
            $groups = array();
            $searchModel = new MembersSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['full_name'] = isset($g['full_name']) ? $g['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['parentage']) ? $g['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['cnic']) ? $g['cnic'] : '';
                $groups[$i]['cnic_issue_date'] = date('Y-m-d', isset($g['info']['cnic_issue_date']) ? strtotime($g['info']['cnic_issue_date']) : 0);
                $groups[$i]['cnic_expiry_date'] = date('Y-m-d', isset($g['info']['cnic_expiry_date']) ? strtotime($g['info']['cnic_expiry_date']) : 0);
                $groups[$i]['dob'] = date('Y-m-d', isset($g['dob']) ? $g['dob'] : 0);
                $groups[$i]['education'] = isset($g['education']) ? $g['education'] : '';
                $groups[$i]['marital_status'] = isset($g['marital_status']) ? $g['marital_status'] : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';
                $groups[$i]['region_id'] = isset($g->region->name) ? $g->region->name : '';
                $groups[$i]['area_id'] = isset($g->area->name) ? $g->area->name : '';
                $groups[$i]['branch_id'] = isset($g->branch->name) ? $g->branch->name : '';
                $groups[$i]['team_id'] = isset($g->team->name) ? $g->team->name : '';
                $groups[$i]['field_id'] = isset($g->field->name) ? $g->field->name : '';
                $i++;
            }
            ExportHelper::ExportCSV('members.csv', $headers, $groups);
            die();
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $searchModel = new MembersSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
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
        $query = Members::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Displays a single Members model.
     * @param integer $id
     * @return mixed
     */
    public function actionSearch($id)
    {
        $members = Members::find()->select('id,full_name,region_id,area_id,branch_id,team_id,field_id')->where(['id' => $id])->one();
        $member['id'] = $members->id;
        $member['region_id'] = $members->region_id;
        $member['area_id'] = $members->area_id;
        $member['branch_id'] = $members->branch_id;
        $member['team_id'] = $members->team_id;
        $member['field_id'] = $members->field_id;
        return json_encode($member);
    }

    public function actionUploadPhoto($type)
    {
        $path = '@frontend/web/uploads/temp/';
        $url = '/uploads/temp';
        $uploadParam = 'file';
        $maxSize = 2097152;
        $extensions = 'jpeg, jpg, png, gif';
        $width = 200;
        $height = 200;
        $jpegQuality = 100;
        $pngCompressionLevel = 1;
        $url = rtrim($url, '/') . '/';
        $path = rtrim(Yii::getAlias($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName($uploadParam);

            $model = new DynamicModel(compact($uploadParam));
            $model->addRule($uploadParam, 'image', [
                'maxSize' => $maxSize,
                'tooBig' => Yii::t('yii', 'TOO_BIG_ERROR', ['size' => $maxSize / (1024 * 1024)]),
                'extensions' => explode(', ', $extensions),
                'wrongExtension' => Yii::t('yii', 'EXTENSION_ERROR', ['formats' => $extensions])
            ])->validate();

            if ($model->hasErrors()) {
                $result = [
                    'error' => $model->getFirstError($uploadParam)
                ];
            } else {
                $model->{$uploadParam}->name = rtrim($type, '/') . '_' . rand(111111, 999999) . '.' . $model->{$uploadParam}->extension;
                $request = Yii::$app->request;

                $width = $request->post('width', $width);
                $height = $request->post('height', $height);

                $image = Image::crop(
                    $file->tempName . $request->post('filename'),
                    intval($request->post('w')),
                    intval($request->post('h')),
                    [$request->post('x'), $request->post('y')]
                )->resize(
                    new Box($width, $height)
                );

                if (!file_exists($path) || !is_dir($path)) {
                    $result = [
                        'error' => Yii::t('yii', 'ERROR_NO_SAVE_DIR')];
                } else {
                    $saveOptions = ['jpeg_quality' => $jpegQuality, 'png_compression_level' => $pngCompressionLevel];
                    if ($image->save($path . $model->{$uploadParam}->name, $saveOptions)) {
                        $result = [
                            'filelink' => $url . $model->{$uploadParam}->name
                        ];
                    } else {
                        $result = [
                            'error' => Yii::t('yii', 'ERROR_CAN_NOT_UPLOAD_FILE')
                        ];
                    }
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        } else {
            throw new BadRequestHttpException(Yii::t('yii', 'ONLY_POST_REQUEST'));
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
        if (isset($request->post()['Members']['id']) && !empty($request->post()['Members']['id'])) {
            $members = $this->findModel($request->post()['Members']['id']);

            $membersAddress = $members->membersAddresses;
            $membersPhone = $members->membersPhones;
            $membersEmail = $members->membersEmails;
            $membersAccount = $members->membersAccounts;
            $member_info = MemberInfo::find()->where(['member_id' => $members->id])->one();
            if (empty($member_info)) {
                $member_info = new MemberInfo();
            }

            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

            $address_count = count($membersAddress);
            $phone_count = count($membersPhone);
            if ($address_count == 0) {
                $memberAddress = new MembersAddress();
                $membersAddress = array_fill(0, 2, $memberAddress);
            }
            if ($address_count == 1) {
                $membersAddress[0] = $membersAddress[0];
                $membersAddress[1] = new MembersAddress();
            }
            if ($phone_count == 0) {
                $memberPhone = new MembersPhone();
                $membersPhone = array_fill(0, 2, $memberPhone);
            }
            if ($phone_count == 1) {
                $membersPhone[0] = $membersPhone[0];
                $membersPhone[1] = new MembersPhone();
            }
            /*
            *   Process for non-ajax request
            */
            if ($members->load($request->post())) {

                $membersPhone = Model::createMultiple(MembersPhone::classname());
                Model::loadMultiple($membersPhone, Yii::$app->request->post());
                $membersAddress = Model::createMultiple(MembersAddress::classname());
                Model::loadMultiple($membersAddress, Yii::$app->request->post());
                $transaction = Yii::$app->db->beginTransaction();
                $region_area = StructureHelper::getRegionAreaFromBranch($members->branch_id);
                $members->region_id = $region_area['region_id'];
                $members->area_id = $region_area['area_id'];
                $members = MemberHelper::preConditionsMember($members);
                if (!empty($members->getErrors())) {
                    return $this->render('update', [
                        'members' => $members,
                        'member_info' => $member_info,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,
                        'branches' => $branches,
                    ]);
                } else {
                    foreach ($membersPhone as $memberPhone) {
                        $memberPhone->member_id = $members->id;
                        $memberPhone->is_current = 1;
                        $phone_save = MemberHelper::saveMemberPhone($memberPhone);
                        if (!$phone_save) {
                            $transaction->rollback();
                        }
                    }

                    foreach ($membersAddress as $memberAddress) {
                        $memberAddress->member_id = $members->id;
                        $memberAddress->is_current = 1;
                        $address_save = MemberHelper::saveMemberAddress($memberAddress);
                        if (!$address_save) {
                            $transaction->rollback();
                        }
                    }
                    $member_info = MemberInfo::find()->where(['member_id' => $members->id])->one();
                    if (empty($member_info)) {
                        $member_info = new MemberInfo();
                    }
                    $member_info->load(Yii::$app->request->post());
                    $member_info->member_id = $members->id;

                    if ($member_info->is_life_time == 'on') {
                        $member_info->cnic_expiry_date = null;
                    } else {
                        $expiry_date = $member_info->cnic_issue_date;
                        $new_expiry_date = date('Y-m-d', strtotime('+10 year', strtotime($expiry_date)));
                        $member_info->cnic_expiry_date = $new_expiry_date;
                    }
                    if (!$member_info) {
                        $transaction->rollback();
                    }
                }
                if (isset($flag) && !empty($flag)) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $members->id]);
                } else {
                    $transaction->rollBack();
                    return $this->render('update', [
                        'members' => $members,
                        'member_info' => $member_info,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,
                        'branches' => $branches,
                    ]);
                }
            }
            return $this->render('update', [
                'members' => $members,
                'member_info' => $member_info,
                'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                'membersEmail' => $membersEmail,
                'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,
                'branches' => $branches,
            ]);

        }
        $members = new Members();
        $memberAddress = new MembersAddress();
        $memberPhone = new MembersPhone();
        $membersEmail = new MembersEmail();
        $membersAccount = new MembersAccount();
        $member_info = new MemberInfo();
        $image = new Images();

        $membersPhone = array_fill(0, 2, $memberPhone);
        $membersAddress = array_fill(0, 2, $memberAddress);

        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        /*$image->image_name = UploadedFile::getInstance($image, 'image_name');
        echo '<pre>';
        print_r($request->post());
        print_r($image);
        $image->image_name->saveAs('uploads/' . $image->image_name->baseName . '.' . $image->image_name->extension);
        die();*/
        if ($members->load($request->post())) {

            $membersPhone = Model::createMultiple(MembersPhone::classname());
            Model::loadMultiple($membersPhone, Yii::$app->request->post());
            $membersAddress = Model::createMultiple(MembersAddress::classname());
            Model::loadMultiple($membersAddress, Yii::$app->request->post());
            $membersAccount->load($request->post());
            $transaction = Yii::$app->db->beginTransaction();
            $region_area = StructureHelper::getRegionAreaFromBranch($members->branch_id);
            $members->region_id = $region_area['region_id'];
            $members->area_id = $region_area['area_id'];
            $members->profile_pic = 'profile_pic';
            //$members=MemberHelper::preConditionsMember($members);
            if (!empty($members->getErrors())) {
                return $this->render('create', [
                    'image' => $image,
                    'members' => $members,
                    'member_info' => $member_info,
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,
                    'branches' => $branches,
                ]);
            } else {
                if ($flag = $members->save()) {
                    foreach ($membersPhone as $memberPhone) {
                        $memberPhone->member_id = $members->id;
                        $memberPhone->is_current = 1;
                        $phone_save = MemberHelper::saveMemberPhone($memberPhone);
                        if (!$phone_save) {
                            $transaction->rollback();
                        }
                    }
                    foreach ($membersAddress as $memberAddress) {
                        $memberAddress->member_id = $members->id;
                        $memberAddress->is_current = 1;
                        $address_save = MemberHelper::saveMemberAddress($memberAddress);
                        if (!$address_save) {
                            $transaction->rollback();
                        }
                    }
                    if (!empty($request->post()['MembersAccount']['bank_name']) && !empty($request->post()['MembersAccount']['account_no']) && !empty($request->post()['MembersAccount']['title'])) {
                        $membersAccount->member_id = $members->id;
                        $membersAccount->is_current = 1;
                        $account_save = MemberHelper::saveMemberAccount($membersAccount);
                        if (!$account_save) {
                            $transaction->rollback();
                        }
                    } else if (!empty($request->post()['MembersAccount']['bank_name']) && ($request->post()['MembersAccount']['bank_name'] == 'cheque') && !empty($request->post()['MembersAccount']['title'])) {

                        $membersAccount->member_id = $members->id;
                        $membersAccount->is_current = 1;
                        $account_save = MemberHelper::setPreviousAccountsInactive($membersAccount);

                        if (!$account_save) {
                            $transaction->rollback();
                        } else {
                            $membersAccount->save();
                        }
                    }
                    $member_info->load(Yii::$app->request->post());
                    $member_info->member_id = $members->id;
                    if ($member_info->is_life_time == 'on') {
                        $member_info->cnic_expiry_date = null;
                    } else {
                        $expiry_date = $member_info->cnic_issue_date;
                        $new_expiry_date = date('Y-m-d', strtotime('+10 year', strtotime($expiry_date)));
                        $member_info->cnic_expiry_date = $new_expiry_date;
                    }
                    if (!$member_info->save()) {
                        $transaction->rollback();
                    }
                }
            }
            if (isset($flag) && !empty($flag)) {
                /*    $image_model = new Images();
                    $image_model->parent_id = $members->id;
                    $image_model->parent_type = "members";
                    $image_model->image_type = "profile";
                    $image_model->image_name = str_replace('/uploads/temp/', '', $members->profile_pic);
                    $image_model->save();
                    $path = Yii::$app->basePath . '/web/uploads/' . $image_model->parent_type . '/' . $image_model->parent_id . '/';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    rename(Yii::$app->basePath . '/web' . $members->profile_pic, $path . $image_model->image_name);

                    $image_model = new Images();
                    $image_model->parent_id = $members->id;
                    $image_model->parent_type = "members";
                    $image_model->image_type = "cnic_front";
                    $image_model->image_name = str_replace('/uploads/temp/', '', $members->cnic_front);
                    $image_model->save();

                    $path = Yii::$app->basePath . '/web/uploads/' . $image_model->parent_type . '/' . $image_model->parent_id . '/';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    rename(Yii::$app->basePath . '/web' . $members->cnic_front, $path . $image_model->image_name);

                    $image_model = new Images();
                    $image_model->parent_id = $members->id;
                    $image_model->parent_type = "members";
                    $image_model->image_type = "cnic_back";
                    $image_model->image_name = str_replace('/uploads/temp/', '', $members->cnic_back);
                    $image_model->save();

                    $path = Yii::$app->basePath . '/web/uploads/' . $image_model->parent_type . '/' . $image_model->parent_id . '/';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    rename(Yii::$app->basePath . '/web' . $members->cnic_back, $path . $image_model->image_name);

                   */
                $transaction->commit();
                return $this->redirect(['view', 'id' => $members->id]);
            } else {
                $transaction->rollBack();
                return $this->render('create', [
                    'image' => $image,
                    'members' => $members,
                    'member_info' => $member_info,
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'branches' => $branches,
                    'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,

                ]);
            }
        } else {
            return $this->render('create', [
                'image' => $image,
                'members' => $members,
                'member_info' => $member_info,
                'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                'membersEmail' => $membersEmail,
                'membersAccount' => (empty($membersAccount)) ? [new MembersAccount] : $membersAccount,
                'branches' => $branches,
            ]);
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
        $members = $this->findModel($id);
        $membersAddress = $members->membersAddresses;
        $membersPhone = $members->membersPhones;
        $membersEmail = $members->membersEmails;
        $membersAccount = $members->membersAccount;
        $member_info = MemberInfo::find()->where(['member_id' => $members->id])->one();
        if (empty($member_info)) {
            $member_info = new MemberInfo();
        }
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $active_loans = MemberHelper::checkActiveLoan($members->cnic);

        $address_count = count($membersAddress);
        $phone_count = count($membersPhone);
        if ($address_count == 0) {
            $memberAddress = new MembersAddress();
            $membersAddress = array_fill(0, 2, $memberAddress);
        }
        if ($address_count == 1) {
            $membersAddress[0] = $membersAddress[0];
            $membersAddress[1] = new MembersAddress();
        }
        if ($phone_count == 0) {
            $memberPhone = new MembersPhone();
            $membersPhone = array_fill(0, 2, $memberPhone);
        }
        if ($phone_count == 1) {
            $membersPhone[0] = $membersPhone[0];
            $membersPhone[1] = new MembersPhone();
        }


        if (!empty($active_loans)) {
            $members->addError('cnic', 'Active Loan exists against this cnic');
            return $this->render('update', [
                'members' => $members,
                'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                'membersEmail' => $membersEmail,
                'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                'branches' => $branches,
                'member_info' => $member_info,
            ]);
        }

        /*
        *   Process for non-ajax request
        */
        if ($members->load($request->post())) {
            $application = Applications::find()->where(['in','status',['pending','approved']])
                ->andWhere(['member_id'=>$id])
                ->one();
            if(!empty($application)){
                $loans = Loans::find()->where(['application_id'=>$application->id])->one();
                if(empty($loans)){
                    $members->addError('cnic', 'Pending Application exists against this cnic');
                    return $this->render('update', [
                        'members' => $members,
                        'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                        'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                        'membersEmail' => $membersEmail,
                        'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                        'branches' => $branches,
                        'member_info' => $member_info,
                    ]);
                }
            }

            $cnic_change_check = Members::findOne($id);
            if ($cnic_change_check->cnic != $members->cnic) {
                $members->addError('cnic', "Memebrs CNIC can not be changed");
            }
            $membersAccount = new MembersAccount();
            $membersPhone = Model::createMultiple(MembersPhone::classname());
            Model::loadMultiple($membersPhone, Yii::$app->request->post());
            $membersAddress = Model::createMultiple(MembersAddress::classname());
            Model::loadMultiple($membersAddress, Yii::$app->request->post());
            $membersAccount->load($request->post());
            $transaction = Yii::$app->db->beginTransaction();

            $members->team_id = !empty($members->team_id) ? $members->team_id : 0;
            $members->field_id = !empty($members->field_id) ? $members->field_id : 0;
            /*$region_area=StructureHelper::getRegionAreaFromBranch($members->branch_id);
            $members->region_id = $region_area['region_id'];
            $members->area_id = $region_area['area_id'];*/
            //$members=MemberHelper::preConditionsMember($members);
            if (!empty($members->getErrors())) {
                return $this->render('update', [
                    'members' => $members,
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                    'branches' => $branches,
                    'member_info' => $member_info,
                ]);
            } else {
                if ($flag = $members->save()) {
                    foreach ($membersPhone as $memberPhone) {
                        $memberPhone->member_id = $members->id;
                        $memberPhone->is_current = 1;
                        $phone_save = MemberHelper::saveMemberPhone($memberPhone);
                        if (!$phone_save) {
                            $transaction->rollback();
                        }
                    }
                    foreach ($membersAddress as $memberAddress) {
                        $memberAddress->member_id = $members->id;
                        $memberAddress->is_current = 1;
                        $address_save = MemberHelper::saveMemberAddress($memberAddress);
                        if (!$address_save) {
                            $transaction->rollback();
                        }
                    }
                    if (!empty($request->post()['MembersAccount']['bank_name']) && !empty($request->post()['MembersAccount']['account_no']) && !empty($request->post()['MembersAccount']['title'])) {
                        $membersAccount->member_id = $members->id;
                        $membersAccount->is_current = 1;
                        $account_save = MemberHelper::saveMemberAccount($membersAccount);
                        if (!$account_save) {
                            $transaction->rollback();
                        }
                    } else if (!empty($request->post()['MembersAccount']['bank_name']) && ($request->post()['MembersAccount']['bank_name'] == 'cheque') && !empty($request->post()['MembersAccount']['title'])) {

                        $membersAccount->member_id = $members->id;
                        $membersAccount->is_current = 1;
                        $account_save = MemberHelper::setPreviousAccountsInactive($membersAccount);

                        if (!$account_save) {
                            $transaction->rollback();
                        } else {
                            $membersAccount->save();
                        }
                    }
                    $member_info->load(Yii::$app->request->post());
                    $member_info->member_id = $members->id;
                    if ($member_info->is_life_time == 'on') {
                        $member_info->cnic_expiry_date = null;
                    }
                    if (!$member_info->save()) {
                        $transaction->rollback();
                    }
                }
            }
            if (isset($flag) && !empty($flag)) {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $members->id]);
            } else {
                $transaction->rollBack();
                return $this->render('update', [
                    'members' => $members,
                    'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
                    'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
                    'membersEmail' => $membersEmail,
                    'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
                    'branches' => $branches,
                    'member_info' => $member_info,
                ]);
            }
        }
        return $this->render('update', [
            'members' => $members,
            'membersAddress' => (empty($membersAddress)) ? [new MembersAddress] : $membersAddress,
            'membersPhone' => (empty($membersPhone)) ? [new MembersPhone] : $membersPhone,
            'membersEmail' => $membersEmail,
            'membersAccount' => (empty($membersAccount[0])) ? new MembersAccount : $membersAccount[0],
            'branches' => $branches,
            'member_info' => $member_info,
        ]);

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
        //$this->findModel($id)->delete();
        $member = $this->findModel($id);
        $member->deleted = 1;
        $member->deleted_by = Yii::$app->user->getId();
        $member->deleted_at = strtotime(date('Y-m-d'));
        $member->save();
        return $this->redirect(['index']);
    }

    public function actionGetCityId($name)
    {
        $city = Cities::find()->where(['name' => ltrim($name)])->one();
        $response['status_type'] = "success";
        $response['id'] = (!empty($city)) ? $city->id : '0';
        return json_encode($response);
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

    public function actionLogs($id = null, $field = null)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {

            // Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {

                return $this->renderAjax('logs', [
                    'id' => $id,
                    'field' => $field,
                ]);
                /*return [
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
                ];*/
            }
        } else {

            return $this->render('logs', [
                'id' => $id,
                'field' => $field,
            ]);
        }
    }

    public function actionUploadImage()
    {
        $post = Yii::$app->request->post();
        $image_model = new Images();
        $image_type = $post['Images']['image_type'];
        $parent_id = $post['Images']['parent_id'];
        $parent_type = $post['Images']['parent_type'];
        $rand = rand(111111, 999999);

        if (Yii::$app->request->isPost) {
            $data = file_get_contents($_FILES['Images']['tmp_name']['image_data']);
            $data = base64_encode($data);
            $image_name1 = $image_type . '_' . rand(111111, 999999) . '.png';
            $flag = ImageHelper::imageUpload($parent_id, $parent_type, $image_type, $image_name1, $data);
            if ($flag) {
                $response['status_type'] = "success";
                $response['data']['message'] = "Saved";
            } else {
                $response['status_type'] = "error";
                $response['errors'] = 'Not Saved!';
            }
        } else {
            $response['status_type'] = "error";
            $response['errors'] = 'Not Saved!';
        }
        return json_encode($response);
    }

    public function actionCnicCheck($cnic)
    {
        /*$blacklist_member = BlacklistHelper::checkBlacklist($cnic);
        if(!empty($blacklist_member)){
            $response['status_type'] = "error";
            $response['data'] = $blacklist_member;
        }else{*/
        $member = Members::find()->where(['cnic' => $cnic])->asArray()->one();
        if (!empty($member)) {
            $home_address = MembersAddress::find()->where(['member_id' => $member['id'], 'is_current' => 1, 'address_type' => 'home', 'deleted' => 0])->asArray()->one();
            $business_address = MembersAddress::find()->where(['member_id' => $member['id'], 'is_current' => 1, 'address_type' => 'business', 'deleted' => 0])->asArray()->one();
            $landline_phone = MembersPhone::find()->where(['member_id' => $member['id'], 'is_current' => 1, 'phone_type' => 'phone', 'deleted' => 0])->asArray()->one();
            $mobile_phone = MembersPhone::find()->where(['member_id' => $member['id'], 'is_current' => 1, 'phone_type' => 'Mobile', 'deleted' => 0])->asArray()->one();
            $account_no = MembersAccount::find()->where(['member_id' => $member['id'], 'is_current' => 1, 'deleted' => 0])->asArray()->one();
            $info = MemberInfo::find()->where(['member_id' => $member['id']])->asArray()->one();
            $member['dob'] = date('Y-m-d', $member['dob']);
            $response['status_type'] = "success";
            $response['data'] = $member;
            $response['address']['home'] = $home_address;
            $response['address']['business'] = $business_address;
            $response['phone']['landline'] = $landline_phone;
            $response['phone']['mobile'] = $mobile_phone;
            $response['account']['account'] = $account_no;
            $response['info']['info'] = !empty($info) ? $info : [];

        } else {
            $response['status_type'] = "failure";
        }
        //}
        return json_encode($response);
    }

    public function actionApplicationCheck($id, $project_id = 0)
    {
        $flag = true;
        $member = Members::findOne(['id' => $id]);
        $member->validate();
        $member_info = MemberInfo::find()->where(['member_id' => $member->id])->one();

        if (!empty($member->getErrors())) {
            $response['status_type'] = "error";
            $response['message'] = 'Please update the member to Proceed..&nbsp;&nbsp;&nbsp;' . '<a target="blank" class="btn btn-sm btn-primary" role="button" href=/members/update?id=' . $member->id . '>' . 'Update' . '</a>';
            $flag = false;
        } else if (empty($member->homeAddress) || empty($member->businessAddress)) {
            $response['status_type'] = "error";
            $response['message'] = 'Please update the member to Proceed..&nbsp;&nbsp;&nbsp;' . '<a target="blank" class="btn btn-sm btn-primary" role="button" href=/members/update?id=' . $member->id . '>' . 'Update' . '</a>';
            $flag = false;
        } else if (empty($member_info)) {
            $response['status_type'] = "error";
            $response['message'] = 'Please update the member to Proceed..&nbsp;&nbsp;&nbsp;' . '<a target="blank" class="btn btn-sm btn-primary" role="button" href=/members/update?id=' . $member->id . '>' . 'Update' . '</a>';
            $flag = false;
        } else if ($project_id != 0 && /*in_array($project_id,StructureHelper::trancheProjects()) && */
            empty($member->membersAccount)) {
            $response['status_type'] = "error";
            $response['message'] = 'Member do not have any active account no.Update member before proceed furthure:' . '<a href=/members/update?id=' . $member->id . '>' . $member->cnic . '</a>';
            $flag = false;
        } else if (is_null($member->membersAccounts->account_type) || empty($member->membersAccounts->account_type)) {
            $response['status_type'] = "error";
            $response['message'] = 'Please Update member account to proceed further.' . '<a href=/members/update?id=' . $member->id . '>' . $member->cnic . '</a>';
            $flag = false;
        } else if (isset($member->membersAccounts->account_type)) {
            $response['status_type'] = "info";
            $response['message'] = 'Member Account Type is ' . $member->membersAccounts->bank_name . '.You can update member account if you want to.' . '<a href=/members/update?id=' . $member->id . '>' . $member->cnic . '</a>';
            $flag = false;
        } else {
            foreach ($member->applications as $app) {
                /*if(($app->status=='pending' || $app->status=='approved') && empty($app->loan) && $app->deleted=='0'){
                    $response['status_type']="error";
                    $response['message'] = 'Application against this member is already in-process against Application Number:' .'<a href=/applications/view?id='.$app->id.'>'.$app->application_no.'</a>';
                    $flag=false;
                }
                else if(!empty($app->loan) && $app->loan->status!='loan completed' && $app->loan->status!='not collected'){

                    $response['status_type']="error";
                    $response['message'] = 'Already have active loan against this member with Sanction Number: ' .'<a href=/loans/view?id='.$app->loan->id.'>'.$app->loan->sanction_no.'</a>';
                    $flag=false;
                }*/
                /*if(!empty($app->loan) && $app->loan->status!='loan completed' && $app->loan->status!='not collected'){

                    $response['status_type']="error";
                    $response['message'] = 'Already have active loan against this member with Sanction Number: ' .'<a href=/loans/view?id='.$app->loan->id.'>'.$app->loan->sanction_no.'</a>';
                    $flag=false;
                }*/
            }
        }
        if ($flag == true) {
            $response['status_type'] = "success";
        }
        return json_encode($response);
    }

    public function actionAddDocument($id)
    {
        $request = Yii::$app->request;
        $member = $this->findModel($id);
        $post = Yii::$app->request->post();
        $image_model = new Images();
        $image_type = $post['Images']['image_type'];
        $parent_id = $post['Images']['parent_id'];
        $parent_type = $post['Images']['parent_type'];
        $rand = rand(111111, 999999);


        if (Yii::$app->request->isPost) {

            $data = file_get_contents($_FILES['Images']['tmp_name']['image_data']);
            $data = base64_encode($data);
            if ($image_type == 'nadra_document') {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.pdf';
            } else {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.png';
            }
            $flag = ImageHelper::imageUpload($parent_id, $parent_type, $image_type, $image_name1, $data);
            if ($flag) {
                return $this->redirect(['view', 'id' => $member->id]);
            } else {
                return $this->render('add-document', [
                    'model' => $image_model,
                    'member' => $member,
                ]);
            }

        } else {
            return $this->render('add-document', [
                'model' => $image_model,
                'member' => $member,
            ]);
        }
    }

    public function actionAddNadraDocument($id)
    {
        $request = Yii::$app->request;
        $member = $this->findModel($id);
        $post = Yii::$app->request->post();
        $image_model = new Images();
        $image_type = $post['Images']['image_type'];
        $parent_id = $post['Images']['parent_id'];
        $parent_type = $post['Images']['parent_type'];
        $rand = rand(111111, 999999);

        if (Yii::$app->request->isPost) {

            $data = file_get_contents($_FILES['Images']['tmp_name']['image_data']);
            $data = base64_encode($data);
            if ($image_type == 'nadra_document') {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.pdf';
            } else {
                $image_name1 = $image_type . '_' . rand(111111, 999999) . '.png';
            }
            $flag = ImageHelper::imageUpload($parent_id, $parent_type, $image_type, $image_name1, $data);
            if ($flag) {
                return $this->redirect(['view', 'id' => $member->id]);
            } else {
                return $this->render('add-document', [
                    'model' => $image_model,
                    'member' => $member,
                ]);
            }

        } else {
            return $this->render('add-document', [
                'model' => $image_model,
                'member' => $member,
            ]);
        }
    }

    /*public function actionAddDocument($id)
    {
        $request=Yii::$app->request;
        $member=$this->findModel($id);
        $post=Yii::$app->request->post();
        $image_model=new Images();
        $image_type=$post['Images']['image_type'];
        $parent_id=$post['Images']['parent_id'];
        $parent_type=$post['Images']['parent_type'];
        $rand=rand(111111, 999999);



        if (Yii::$app->request->isPost) {

            $image = Images::find()->where(['parent_id' => $parent_id, 'parent_type' => $parent_type, 'image_type' => $image_type])->one();

            if (isset($image)) {
                $file = UploadedFile::getInstance($image, 'image_data');
                $img = $image->image_name;
                $image->image_name = $image_type . '_' . $rand;
            } else {
                $image = new Images();
                $file = UploadedFile::getInstance($image, 'image_data');
                $image->parent_id = $parent_id;
                $image->parent_type = $parent_type;
                $image->image_type = $image_type;
                $image->image_name = $image_type . '_' . $rand.'.'.$file->getExtension();
            }
            $flag = $image->save();

            if ($flag) {
                if ($image->image_data = UploadedFile::getInstance($image, 'image_data')) {

                    if (isset($img)) {
                        unlink('uploads/' . $parent_type . '/' . $parent_id . '/' . $img.'.'.$file->getExtension());
                    } else {
                        FileHelper::createDirectory('uploads/' . $parent_type . '/' . $parent_id);
                    }
                    $image->image_data->saveAs('uploads/' . $parent_type . '/' . $parent_id . '/' . $image_type . '_' . $rand . '.'.$file->getExtension());

                    return $this->redirect(['view', 'id' => $member->id]);

                }
            } else {
                return $this->render('add-document', [
                    'model' => $image_model,
                    'member'=>$member,
                ]);
            }
        } else {
            return $this->render('add-document', [
                'model' => $image_model,
                'member'=>$member,

            ]);
        }

    }*/
    public function actionPdf($id)
    {
        $model = Members::findOne($id);
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'nadra_document']);
        $attachment_path = ImageHelper::getAttachmentPath() . '/uploads/members/' . $model->id . '/' . $image->image_name;

        // This will need to be the path relative to the root of your app.
        // Might need to change '@app' for another alias
        $completePath = $attachment_path;
        if (file_exists($completePath)) {
            return Yii::$app->response->sendFile($completePath, $image->image_name);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }
}
