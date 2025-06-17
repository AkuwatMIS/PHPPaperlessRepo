<?php

namespace frontend\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\StructureHelper;
use common\models\DisbursementDetails;
use common\models\Donations;
use common\models\LoanTranches;
use common\models\Recoveries;
use common\models\search\DonationsSearch;
use common\models\search\LoansSearch;
use common\models\search\LoanTranchesSearch;
use common\models\search\RecoveriesSearch;
use common\models\UserStructureMapping;
use Yii;
use common\models\Members;
use common\models\Applications;
use common\models\AppraisalsAgriculture;
use common\models\AppraisalsBusiness;
use common\models\AppraisalsHousing;
use common\models\Loans;
use common\models\search\ApplicationsSearch;
use common\models\SocialAppraisal;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class CompositeUpdatesController extends Controller
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
        return $this->render('index');
    }

    public function actionMemberSearch()
    {
        $members = new Members();
        $model = new Applications();
        $request = Yii::$app->request;

        if ($model->load($request->post())) {

            $member_id = $request->post()['Applications']['member_id'];
            $validateApplication = self::actionValidateApplication($member_id);

            if ($validateApplication == 0) {
                return $this->redirect(['members/update', 'id' => $member_id]);
            } else {
                Yii::$app->session->setFlash('warning', 'اس ممبر کو اپ ڈیٹ نہیں کیا جا سکتا، کیونکہ اس کی درخواست منظور ہو چکی ہے!');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->render('member_search', [
            'members' => $members,
            'model' => $model
        ]);
    }

    // ============================Application=============================

    public function actionSearchApplicationNic($q = null, $id = null)
    {
        $loginUser =  \Yii::$app->user->identity->getId();
        if(\Yii::$app->user->identity->designation_id == 8){
            $userMappings = UserStructureMapping::find()
                ->where(['user_id'=>$loginUser])
                ->andWhere(['obj_type'=>'area'])
                ->select(['user_id','obj_id'])
                ->one();

        }

        $cur_date = strtotime(date('Y-m-d'));
        $six_month_back_date = strtotime(date("Y-m-d", strtotime("-8 Months")));
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $result = array();
            if (strpos($q, '-') !== false) {
                $query = Applications::find()->select('applications.id, members.full_name , members.cnic, applications.application_no')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->orFilterWhere(['like', 'members.cnic', $q])
                    ->andFilterWhere(['!=', 'applications.deleted', '1'])
                    ->andFilterWhere(['applications.status' => 'pending'])
                    ->andFilterWhere(['between', 'applications.application_date', $six_month_back_date, $cur_date]);
                if (isset($userMappings) && !empty($userMappings)){
                    $query->andFilterWhere(['applications.area_id' => $userMappings->obj_id]);
                }
            } else {
                $query = Applications::find()->select('applications.id,members.cnic,members.full_name,applications.application_no')
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->orFilterWhere(['like', 'applications.application_no', $q])
                    ->andFilterWhere(['!=', 'applications.deleted', '1'])
                    ->andFilterWhere(['applications.status' => 'pending'])
                    ->andFilterWhere(['applications.area_id' => $userMappings->obj_id])
                    ->andFilterWhere(['between', 'applications.application_date', $six_month_back_date, $cur_date]);
                if (isset($userMappings) && !empty($userMappings)){
                    $query->andFilterWhere(['applications.area_id' => $userMappings->obj_id]);
                }
            }

            $query = $query->orderBy(['applications.created_at' => SORT_DESC])->asArray()->all();
            foreach ($query as $k => $v) {
                $result[$k]['id'] = $v['id'];
                $result[$k]['text'] = '<strong>Application No</strong>: ' . $v['application_no'] . ' <strong>Name</strong>: ' . $v['full_name'] . ' <strong>CNIC</strong>: ' . $v['cnic'];
            }
            $out['results'] = $result;
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Applications::findOne($id)->form_no];
        }
        //echo json_encode($out);
        return $out;
    }

    public function actionApplicationSearch()
    {
        $model = new Applications();
        $request = Yii::$app->request;
        $searchModel = new ApplicationsSearch();

        if ($model->load($request->post())) {
            if (isset($request->post()['Applications']['application_no']) && isset($request->post()['Applications']['cnic'])) {
                $validateApplication = self::actionValidateLoan($request->post()['Applications'], 'app_no');

                if ($validateApplication == 0) {
                    Yii::$app->request->queryParams = $request->post();
                    $dataProvider = $searchModel->searchApp(Yii::$app->request->queryParams);
                } else {
                    Yii::$app->session->setFlash('warning', 'اس درخواست پر قرضہ بن گیا ہے، اب کچھ بھی تبدیل نہیں کیا جا سکتا، آگے بڑھنے کے لیے ایڈمن سے رابطہ کریں۔');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            } else {
                Yii::$app->session->setFlash('warning', 'CNIC and Application no both are required');
                return $this->redirect(Yii::$app->request->referrer);
            }

        } else {
            $dataProvider = [];
        }

        return $this->render('application_search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionValidateApplication($id)
    {
        $app = Applications::find()
            ->where(['member_id' => $id])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->one()
            ->toArray();

        if (!empty($app) && $app != null) {
            if ($app['status'] == 'approved') {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }

    // ==============================Appraisal===============================

    public function actionAppraisalSearch()
    {
        $modelApplication = new Applications();
        $request = Yii::$app->request;

        if ($modelApplication->load($request->post())) {
            $id = $request->post()['Applications']['id'];
            $appraisal_id = $request->post()['Applications']['appraisal_id'];
            $validateApplication = self::actionValidateLoan($id, 'id');

            if ($validateApplication == 0) {
                if ($appraisal_id == 1) {
                    return $this->redirect(['update-social-appraisal', 'id' => $id]);
                } elseif ($appraisal_id == 2) {
                    return $this->redirect(['update-business-appraisal', 'id' => $id]);
                } elseif ($appraisal_id == 3) {
                    Yii::$app->session->setFlash('warning', 'Agriculture Appraisals are not allowed to update!');
                    return $this->redirect(Yii::$app->request->referrer);
                } elseif ($appraisal_id == 4) {
                    return $this->redirect(['update-housing-appraisal', 'id' => $id]);
                } else {
                    Yii::$app->session->setFlash('warning', 'No data found against this appraisal');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            } else {
                Yii::$app->session->setFlash('warning', 'اس درخواست پر قرضہ بن گیا ہے، اب کچھ بھی تبدیل نہیں کیا جا سکتا، آگے بڑھنے کے لیے ایڈمن سے رابطہ کریں۔');
                return $this->redirect(Yii::$app->request->referrer);
            }

        }

        return $this->render('appraisal_search', [
            'model' => $modelApplication
        ]);
    }

    public function actionUpdateSocialAppraisal($id)
    {
        $request = Yii::$app->request;
        $model = SocialAppraisal::find()->where(['application_id' => $id])->one();
        if (!empty($model) && $model != null) {
            if ($model->load($request->post())) {
                $model->poverty_index = (isset($model->poverty_index) && !empty($model->poverty_index)) ? $model->poverty_index : '0';
                $model->business_income = (!empty($model->business_income) ? $model->business_income : '0');
                $model->job_income = (!empty($model->job_income) ? $model->job_income : '0');
                $model->total_expenses = $model->educational_expenses + $model->medical_expenses + $model->kitchen_expenses + $model->utility_bills + $model->other_expenses;
                $model->total_household_income = $model->business_income + $model->job_income + $model->house_rent_income + $model->other_income;
                $model->total_family_members = $model->ladies + $model->gents;
                $model->date_of_maturity = strtotime($model->date_of_maturity);
                $model->loan_amount = !empty($model->loan_amount) ? $model->loan_amount : 0;
                $model->house_rent_amount = !empty($model->house_rent_amount) ? $model->house_rent_amount : 0;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Appraisal Data successfully updated!');
                    return $this->redirect(['appraisal-search']);
                } else {
                    Yii::$app->session->setFlash('error', 'Appraisal not updated, something went wrong, please contact admin!');
                    return $this->redirect(['appraisal-search']);
                }
            }
            return $this->render('_appraisal_social_form', [
                'model' => $model
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Appraisal Not Found!');
            return $this->redirect(['appraisal-search']);
        }

    }

    public function actionUpdateHousingAppraisal($id, $cnic)
    {
        $request = Yii::$app->request;
        $model = AppraisalsHousing::find()->where(['application_id' => $id])->one();
        if (!empty($model) && $model != null) {
            if ($model->load($request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Appraisal Data successfully updated!');
                    return $this->redirect(['appraisal-search']);
                } else {
                    Yii::$app->session->setFlash('error', 'Appraisal not updated, something went wrong, please contact admin!');
                    return $this->redirect(['appraisal-search']);
                }
            }
            return $this->render('_appraisal_housing_form', [
                'model' => $model
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Appraisal Not Found!');
            return $this->redirect(['appraisal-search']);
        }
    }

    public function actionUpdateBusinessAppraisal($id, $cnic)
    {
        $request = Yii::$app->request;
        $model = AppraisalsBusiness::find()->where(['application_id' => $id])->one();
        if (!empty($model) && $model != null) {
            if ($model->load($request->post())) {

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Appraisal Data successfully updated!');
                    return $this->redirect(['appraisal-search']);
                } else {
                    Yii::$app->session->setFlash('error', 'Appraisal not updated, something went wrong, please contact admin!');
                    return $this->redirect(['appraisal-search']);
                }
            }
            return $this->render('_appraisal_business_form', [
                'model' => $model
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Appraisal Not Found!');
            return $this->redirect(['appraisal-search']);
        }
    }

    public function actionUpdateAgricultureAppraisal($id, $cnic)
    {
        $model = AppraisalsAgriculture::find()->where(['application_id' => $id])->one();
        if (!empty($model) && $model != null) {
            if ($model->load($model->post())) {
                if ($model->save()) {
                } else {
                    Yii::$app->session->setFlash('warning', 'Something went wrong, please contact admin!');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
            return $this->render('_appraisal_agriculture_form', [
                'model' => $model
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Appraisal Not Found!');
            return $this->redirect(['appraisal-search']);
        }
    }

    // ===============================Loan management=========================
    public function actionCompositeLoanSearch()
    {
        $searchModel = new LoansSearch();
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $params = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->searchLoan($params);
        }
        $data = ['searchModel' => $searchModel, 'dataProvider' => $dataProvider];
        return $this->render('loan_index', [
            'data' => $data
        ]);
    }

    public function actionLoanDelete($id)
    {
        $request = Yii::$app->request;
        $model = Loans::find()->where(['id' => $id])->one();
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save()) {
            \Yii::$app
                ->db
                ->createCommand()
                ->delete('loan_tranches', ['loan_id' => $id])
                ->execute();

            \Yii::$app
                ->db
                ->createCommand()
                ->delete('loans', ['id' => $id])
                ->execute();

            Yii::$app->db->createCommand()
                ->update('applications', ['is_lock' => 0], ['id' => $model->application_id])
                ->execute();

            Yii::$app->session->setFlash('success', 'Loan and dependencies deleted successfully!');
            return $this->redirect(Yii::$app->request->referrer);

        } else {
            Yii::$app->session->setFlash('warning', 'Something went wrong, please contact admin!');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionCompositeRejectLoan($id)
    {
        $loan = Loans::find()->where(['id' => $id])->one();
        $session = Yii::$app->session;
        if (isset($loan) && !empty($loan)) {
            if (in_array($loan->project_id, StructureHelper::trancheProjectsReject())) {
                $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no asc')->all();
                if (count($tranch_reject) > 1) {
                    $tranche_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->orderBy('tranch_no desc')->all();
                    if ($tranche_reject[0]->status > 0) {
                        $tranche_reject[0]->status = 9;
                        $tranche_reject[0]->date_disbursed = 0;
                        $tranche_reject[0]->disbursement_id = 0;
                        if ($tranche_reject[0]->save()) {
                            $loan->disbursed_amount = $tranche_reject[1]->tranch_amount;
                            $loan->date_disbursed = $tranche_reject[1]->date_disbursed;
                            $loan->disbursement_id = $tranche_reject[1]->disbursement_id;
                            if ($loan->save(false)) {
                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranche_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                foreach ($disbursement_details as $d) {
                                    $d->status = 2;
                                    $d->updated_by = 1;
                                    if (!$d->save()) {
                                        print_r($d->getErrors());
                                        die();
                                    }
                                }
                                if ($loan->status != 'grant') {

                                    FixesHelper::ledger_regenerate($loan);
                                }
                                $loan->status = 1;
                                $loan->save();
                            }
                            $session->addFlash('success', 'Loan Permanently rejected successfully.');
                        } else {
                            $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                        }
                    }
                } else {
                    if ($tranch_reject[0]->status > 0) {
                        $tranch_reject[0]->status = 9;
                        $tranch_reject[0]->date_disbursed = 0;
                        $tranch_reject[0]->disbursement_id = 0;
                        if ($tranch_reject[0]->save()) {
                            $loan->status = 'rejected';
                            $loan->disbursed_amount = 0;
                            $loan->date_disbursed = 0;
                            $loan->disbursement_id = 0;
                            if ($loan->save()) {
                                $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject[0]->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                                foreach ($disbursement_details as $d) {
                                    $d->status = 2;
                                    $d->updated_by = 1;
                                    if (!$d->save()) {
                                        print_r($d->getErrors());
                                        die();
                                    }
                                }
                                $connection = \Yii::$app->db;
                                $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                                $connection->createCommand($schdl_delete)->execute();
                            }
                            $session->addFlash('success', 'Loan Permanently rejected successfully.');
                        } else {
                            $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                        }
                    }
                }
            } else {
                $tranch_reject = LoanTranches::find()->where(['loan_id' => $loan->id])->andWhere(['>', 'status', 0])->one();
                if ($tranch_reject->status > 0) {
                    $tranch_reject->status = 9;
                    $tranch_reject->date_disbursed = 0;
                    $tranch_reject->disbursement_id = 0;
                    if ($tranch_reject->save()) {
                        $loan->status = 'rejected';
                        $loan->disbursed_amount = 0;
                        $loan->date_disbursed = 0;
                        $loan->disbursement_id = 0;
                        if ($loan->save()) {
                            $disbursement_details = DisbursementDetails::find()->where(['tranche_id' => $tranch_reject->id, 'deleted' => 0])->andWhere(['not in', 'status', [2]])->all();
                            foreach ($disbursement_details as $d) {
                                $d->status = 2;
                                $d->updated_by = 1;
                                if (!$d->save()) {
                                    print_r($d->getErrors());
                                    die();
                                }
                            }
                            $connection = \Yii::$app->db;
                            $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
                            $connection->createCommand($schdl_delete)->execute();
                        }
                        $session->addFlash('success', 'Loan Permanently rejected successfully.');
                    } else {
                        $session->addFlash('error', 'Loan not Permanently rejected successfully.');
                    }
                }
            }
            $application = Applications::find()->where(['id' => $loan->application_id])->one();
            if (!empty($application) && $application != null) {
                $application->status = 'rejected';
                $application->save();
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    // ===============================Loan Tranche management=========================
    public function actionCompositeTrancheSearch()
    {
        $searchModel = new LoanTranchesSearch();
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = array();
        } else {
            $dataProvider = $searchModel->searchTranche(Yii::$app->request->queryParams);
        }

        return $this->render('tranche-search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCompositeCreateLoanTranche()
    {
        $request = Yii::$app->request;
        $model = new LoanTranches();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new Loan Tranches",
                    'content' => $this->renderAjax('tranche_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->date_disbursed = (isset($model->date_disbursed) && !empty($model->date_disbursed)) ? strtotime($model->date_disbursed) : 0;
                $model->cheque_date = (isset($model->cheque_date) && !empty($model->cheque_date)) ? strtotime($model->cheque_date) : 0;
                $model->tranch_date = (isset($model->tranch_date) && !empty($model->tranch_date)) ? strtotime($model->tranch_date) : 0;
                $model->start_date = (isset($model->start_date) && !empty($model->start_date)) ? strtotime($model->start_date) : 0;
                if ($model->save()) {
                    $loan = Loans::find()->where(['id' => $model->loan_id])->one();
                    if (!empty($loan) && $loan != null) {
                        $loan->loan_amount += $model->tranch_amount;
                        if ($loan->save()) {
                            ActionsHelper::insertActions('loan_tranches', $loan->project_id, $model->id, $model->created_by);
                        } 

                    }
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new Loan Tranches",
                        'content' => '<span class="text-success">Create Loan Tranches success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['tranche_form'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    return [
                        'title' => "Create new Loan Tranches",
                        'content' => $this->renderAjax('tranche_form', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new Loan Tranches",
                    'content' => $this->renderAjax('tranche_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                if ($model->save()) {
                    return $this->redirect(['composite-tranche=search']);
                } else {
                    print_r($model->errors);
                    die();
                }
            } else {
                return $this->render('tranche_form', [
                    'model' => $model,
                ]);
            }
        }

    }

    public function actionCompositeUpdateTranche($id)
    {
        $request = Yii::$app->request;
        $model = LoanTranches::find()->where(['id' => $id])->one();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update LoanTranches #" . $id,
                    'content' => $this->renderAjax('tranche_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {
                $model->date_disbursed = strtotime($model->date_disbursed);
                $model->cheque_date = strtotime($model->cheque_date);
                $model->tranch_date = strtotime($model->tranch_date);
                $model->start_date = strtotime($model->start_date);
                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create new LoanTranches",
                        'content' => '<span class="text-success">Update LoanTranches success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a('Create More', ['tranche_form'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                } else {
                    print_r($model->getErrors());
                    die();
                    return [
                        'title' => "Update LoanTranches #" . $id,
                        'content' => $this->renderAjax('tranche_form', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                    ];
                }
            } else {
                return [
                    'title' => "Update LoanTranches #" . $id,
                    'content' => $this->renderAjax('tranche_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $model->date_disbursed = strtotime($model->date_disbursed);
                $model->cheque_date = strtotime($model->cheque_date);
                $model->tranch_date = strtotime($model->tranch_date);
                $model->start_date = strtotime($model->start_date);
                if ($model->save()) {
                    return $this->redirect(['loans/view', 'id' => $model->loan_id]);
                } else {
                    return $this->render('tranche_form', [
                        'model' => $model,
                    ]);
                }

            } else {
                return $this->render('tranche_form', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionCompositeDeleteTranche($id)
    {
        $req = LoanTranches::find()->where(['id' => $id])->one();
        $loan = Loans::find()->where(['id' => $req->loan_id])->one();
        if (!empty($loan) && $loan != null) {
            $loan->loan_amount = ($loan->loan_amount - $req->tranch_amount);
            $loan->save();
        }

        $req->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    // ===============================Composite Recovery Management===========

    public function actionCompositeRecoveryIndex()
    {
        $params = Yii::$app->request->queryParams;

        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {
            $params['RecoveriesSearch']['receive_date'] = date("Y-m-d H:i:s", strtotime("midnight", strtotime(date('d-m-Y')))) . ' - ' . date('Y-m-d H:i:s', strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
        }

        $searchModel = new RecoveriesSearch();
        $searchModel->load($params);

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = array();
        } else {
            $dataProvider = $searchModel->searchComposite(Yii::$app->request->queryParams);
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('recovery_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches' => $branches,
            'regions' => $regions,
            'areas' => $areas,
            'projects' => $projects,
        ]);
    }

    public function actionCompositeRecoveryDelete($id)
    {
        $request = Yii::$app->request;
        $model = Recoveries::find()->where(['id'=> $id])->one();

//        if (!in_array($model->source, ['branch', '1', 'cc'])) {
//            $model->addError('source', 'You are not allowed to delete bank recoveries.');
//        }
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save(false)) {
            $loan = Loans::findOne(['id' => $model->loan_id]);
            FixesHelper::fix_schedules_update($loan);
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['composite-recovery-index']);
        }


    }

    public function actionCompositeUpdateRecovery($id)
    {
        $request = Yii::$app->request;
        $model = Recoveries::find()->where(['id'=> $id])->one();
        $projects = ArrayHelper::map(StructureHelper::getProjects(),'id','name');
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Recoveries #".$id,
                    'content'=>$this->renderAjax('update_recovery', [
                        'model' => $model,
                        'projects' => $projects,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post())){
                $model->receive_date = strtotime($model->receive_date);
                if($model->save(false)){
                    Yii::$app->session->setFlash('success', "Recovery Updated Successfully!");
                    return [
                        'title'=> "Update Recoveries #".$id,
                        'content'=> "Recovery Updated Successfully",
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                    ];
                } else {
                    return [
                        'title'=> "Update Recoveries #".$id,
                        'content'=>$this->renderAjax('update_recovery', [
                            'model' => $model,
                            'projects' => $projects,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }

            }else{
                return [
                    'title'=> "Update Recoveries #".$id,
                    'content'=>$this->renderAjax('update_recovery', [
                        'model' => $model,
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
            if ($model->load($request->post()) /*&& $model->save()*/) {
                $model->receive_date = strtotime($model->receive_date);
                if($model->save(false)) {
                    $loan = Loans::findOne(['id' => $model->loan_id]);
                    FixesHelper::fix_schedules_update($loan);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                else{
                    return $this->render('update_recovery', [
                        'model' => $model,
                        'projects' => $projects,
                    ]);
                }
            } else {
                return $this->render('update_recovery', [
                    'model' => $model,
                    'projects' => $projects,
                ]);
            }
        }
    }

    // ===============================Composite Donation========================================

    public function actionCompositeDonationIndex()
    {
        $params = Yii::$app->request->queryParams;

        if (!isset($params['RecoveriesSearch']['receive_date']) || empty($params['RecoveriesSearch']['receive_date'])) {
            $params['RecoveriesSearch']['receive_date'] = date("Y-m-d H:i:s", strtotime("midnight", strtotime(date('d-m-Y')))) . ' - ' . date('Y-m-d H:i:s', strtotime("tomorrow", strtotime(date('d-m-Y'))) - 1);
        }

        $searchModel = new DonationsSearch();
        $searchModel->load($params);

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = array();
        } else {
            $dataProvider = $searchModel->searchComposite(Yii::$app->request->queryParams);
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        return $this->render('donation_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches' => $branches,
            'regions' => $regions,
            'areas' => $areas,
            'projects' => $projects,
        ]);
    }

    public function actionCompositeDonationDelete($id)
    {
        $request = Yii::$app->request;
        $model = Donations::where(['id', $id])->one();

        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save(false)) {
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['composite-donation-index']);
        }


    }

    //    =======================================================================================


    public function actionValidateLoan($q, $type)
    {
        if ($type == 'id') {
            return Loans::find()
                ->select(['loans.*'])
                ->innerJoin('applications', 'applications.id = loans.application_id')
                ->where(['applications.id' => $q])
                ->count();
        } elseif ($type == 'app_no') {

            $app_no = $q['application_no'];
            $cnic = $q['cnic'];

            $count = Loans::find()
                ->select(['loans.*'])
                ->innerJoin('applications', 'applications.id = loans.application_id')
                ->innerJoin('members', 'members.id = applications.member_id')
                ->where(['applications.application_no' => $app_no])
                ->andWhere(['members.cnic' => $cnic])
                ->count();

            return $count;
        } else {
            return 9;
        }
    }

}
