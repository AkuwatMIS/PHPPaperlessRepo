<?php

namespace frontend\controllers;

use app\models\Branches;
use common\components\AwpHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\StructureHelper;
use common\components\LoanHelper;
use common\models\AwpProjectMapping;
use common\models\AwpRecoveryPercentage;
use common\models\BranchProjects;
use common\models\BranchProjectsMapping;
use common\models\ProgressReportDetails;
use common\models\ProgressReports;
use PhpParser\Node\Expr\Array_;
use Yii;
use common\models\Awp;
use common\models\search\AwpSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\components\RbacHelper;
use yii\web\UnauthorizedHttpException;

/**
 * AwpController implements the CRUD actions for Awp model.
 */
class AwpController extends Controller
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
     * Lists all Awp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AwpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = Yii::$app->Permission->getProjectList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        return $this->render('index_awp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects
        ]);
    }


    /**
     * Displays a single Awp model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Awp #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Awp model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateMonthWise()
    {

        $request = Yii::$app->request;
        $branch_id = 0;
        $model = new Awp();
        $projects = [];
        $branch_ids = array(145, 45, 46, 48, 50, 392, 393, 26, 51, 52, 284, 44, 745, 32, 742, 743, 744, 41, 42, 43, 12, 741, 660, 659, 661, 455, 439, 445, 446, 580, 390, 285, 161, 377, 270);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        if ($request->post()) {
            $post = $request->post();
            $branch_id = isset($post['branch_id']) ? $post['branch_id'] : 0;
            if ($branch_id != 0) {
                $model = Awp::find()->where(['branch_id' => $branch_id])->groupBy('month')->all();
            }
            ///////////////
            if (!empty($post['Awp'])) {
                foreach ($post['Awp'] as $awp) {

                    foreach ($awp as $awp_key => $awp_value) {

                        $awp = Awp::find()->where(['id' => $awp_value['id']])->one();

                        $awp->monthly_recovery = $awp_value['monthly_recovery'];
                        $awp->no_of_loans = $awp_value['no_of_loans'];
                        $awp->avg_loan_size = $awp_value['avg_loan_size'];
                        // $awp->amount_disbursed = $awp_value['amount_disbursed'];
                        $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
                        $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);
                        if ($awp->save()) {
                        } else {
                            print_r($awp->getErrors());
                            die();
                        }
                    }
                }
                $branch_id = $awp->branch_id;
                $model = Awp::find()->where(['branch_id' => $branch_id])->groupBy('month')->all();
            }
        }


        return $this->render('create_awp_month_wise', [
            'model' => $model,
            'branches' => $branches,
            'branch_id' => $branch_id,
            'projects' => $projects
        ]);

    }

    public function actionCreateYearly_($awp_id=0)
    {

        $request = Yii::$app->request;
        $branch_id = 0;
        $model = new Awp();
        $projects = [];
        //$branch_ids = array(145, 45, 46, 48, 50, 392, 393, 26, 51, 52, 284, 44, 745, 32, 742, 743, 744, 41, 42, 43, 12, 741, 660, 659, 661, 455, 439, 445, 446, 580, 390, 285, 161, 377, 270);
        $branch_ids = array(8,21,23,32,33,34,35,36,37,39,41,42,43,152,266,267,348,358,360,361,406,412,413,422,539,607,608,609,689,690,691,692,693,717,718,151,222,28,267,358,136,412,413,539,422,406,31,717,718,151,222,28,267,358,136,412,413,539,422,406,31,433,431);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

        if($request->isAjax) {
            $awp_id = $_GET['id'];
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                $model = Awp::find()->where(['id'=>$awp_id])->one();
                return [
                    'title'=> "Update Awp",
                    'content'=>$this->renderAjax('awp-single-update', [
                        'model' => $model,
                        'branch_id' => $model->branch_id
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])


                ];
            }
            return $this->renderAjax('awp-single-update', [
                'model' => $model,
            ]);
        }else if($model->load($request->post()) && $request->post()['single_post']=='single_awp'){
            $single_post = $request->post()['Awp'];

            $awp = Awp::find()->where(['id' => $single_post['id']])->one();

            $awp->monthly_recovery = $single_post['monthly_recovery'];
            $awp->no_of_loans = isset($single_post['no_of_loans'])?$single_post['no_of_loans']:0;
            $awp->avg_loan_size = isset($single_post['avg_loan_size'])?$single_post['avg_loan_size']:0;
            // $awp->amount_disbursed = $awp_value['amount_disbursed'];
            $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
            $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);
            $awp->status='1';
            ////Yearly figures
            $awp->avg_recovery = isset($single_post['avg_recovery']) ? $single_post['avg_recovery'] : 0;
            $awp->monthly_olp = $single_post['monthly_olp'];
            $awp->active_loans = $single_post['active_loans'];
            $awp->monthly_closed_loans = isset($single_post['monthly_closed_loans']) ? $single_post['monthly_closed_loans'] : 0;
            if ($awp->save()) {
                $awp_all = Awp::find()->where(['branch_id' => $awp->branch_id,'project_id'=>$awp->project_id])->andWhere(['>', 'month', $awp->month])->all();
                $i = 0;
                $closing_olp=0;
                $active_loans=0;
                foreach ($awp_all as $d) {
                    if ($i == 0) {
                        $d->monthly_olp = ($awp->disbursement_amount + $awp->monthly_olp) - $awp->monthly_recovery;
                        $d->active_loans = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;
                    } else {
                        $d->monthly_olp = $closing_olp;
                        $d->active_loans = $active_loans;
                    }
                    $closing_olp = ($d->disbursement_amount) + ($d->monthly_olp) - ($d->monthly_recovery);
                    $active_loans = ($d->active_loans) + ($d->no_of_loans) - ($d->monthly_closed_loans);
                    $i++;
                    if ($d->save()) {
                    } else {
                        print_r($d->getErrors());
                        die();
                    }
                }
            } else {
                print_r($awp->getErrors());
                die('a');
            }
        }

        if ($request->post()) {
            $post = $request->post();
            $branch_id = isset($post['branch_id']) ? $post['branch_id'] : 0;

            if ($branch_id != 0) {
                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>'1'])->groupBy('month')->all();
            }

            if (!empty($post['Awp']) && count($post['Awp']) != 1) {
                /*echo'<pre>';
                print_r(($post['Awp']));
                die();
                $i = 0;
                $j = 0;
                $closing_olp = [];
                $active_loans = [];*/
                /*foreach ($post['Awp'] as $awp) {
                    $j = 0;
                    $fund_required = 0;
                    $expected_recovery = 0;
                    $avg_recovery = 0;
                    foreach ($awp as $awp_key => $awp_value) {

                        $awp = Awp::find()->where(['id' => $awp_value['id']])->one();

                        $awp->monthly_recovery = $awp_value['monthly_recovery'];
                        $awp->no_of_loans = isset($awp_value['no_of_loans'])?$awp_value['no_of_loans']:0;
                        $awp->avg_loan_size = isset($awp_value['avg_loan_size'])?$awp_value['avg_loan_size']:0;
                        // $awp->amount_disbursed = $awp_value['amount_disbursed'];
                        $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
                        $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);
                        $awp->status='1';
                        ////Yearly figures
                        $awp->avg_recovery = isset($awp_value['avg_recovery']) ? $awp_value['avg_recovery'] : 0;

                        if ($i == 0) {
                            $awp->monthly_olp = $awp_value['monthly_olp'];
                            $awp->active_loans = $awp_value['active_loans'];

                        } else {
                            $awp->monthly_olp = $closing_olp[$i - 1][$j];
                            $awp->active_loans = $active_loans[$i - 1][$j];
                        }
                        if (\common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'PSIC')) {
                            $awp->monthly_recovery = isset($awp_value['monthly_recovery']) ? $awp_value['monthly_recovery'] : 0;
                        } else {
                            $awp->monthly_recovery = $awp->active_loans * $awp->avg_recovery;
                        }
                        $awp->monthly_closed_loans = isset($awp_value['monthly_closed_loans']) ? $awp_value['monthly_closed_loans'] : 0;
                        $monthly_recovery = isset($awp->monthly_recovery) ? $awp->monthly_recovery : 0;
                        $monthly_olp = isset($awp->monthly_olp) ? $awp->monthly_olp : 0;
                        $amount_disbursed = isset($awp->disbursement_amount) ? $awp->disbursement_amount : 0;
                        $closing_olp[$i][$j] = ($amount_disbursed + $monthly_olp) - $monthly_recovery;
                        //$closing_olp[$i][$j] = ($awp->disbursement_amount + $awp_mapping->monthly_olp) - $awp_mapping->monthly_recovery;
                        $active_loans[$i][$j] = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;

                        ///
                        /*echo'<pre>';
                        print_r($closing_olp[$i][$j]);
                        die();*/
                /* if ($awp->save()) {

                 } else {
                     print_r($awp->getErrors());
                     die('a');
                 }
                 $j++;
             }
             $i++;
         }*/
                /*if ($awp->branch_id == 92){
                    Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
                    $file_path = Yii::getAlias('@anyname') . '/frontend/web/uploads' . '/awp.txt';
                    $myfile = fopen($file_path, "w") or die("Unable to open file!");
                    file_put_contents($file_path, print_r($post['Awp'], true));
                    fclose($myfile);
                }*/
                $branch_id = $awp->branch_id;
                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>'1'])->groupBy('month')->all();

            } else if (!empty($post['Awp']) && count($post['Awp']) == 1) {
                $arrData = array_values($post['Awp']);

                foreach($arrData[0] as $awp_data) {
                    $awp = Awp::find()->where(['id' => $awp_data['id']])->one();
                    $awp->monthly_recovery = $awp_data['monthly_recovery'];
                    $awp->no_of_loans = $awp_data['no_of_loans'];
                    $awp->avg_loan_size = $awp_data['avg_loan_size'];
                    $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
                    $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);

                    $awp->monthly_olp = $awp_data['monthly_olp'];
                    $awp->active_loans = $awp_data['active_loans'];
                    $awp->monthly_closed_loans = $awp_data['monthly_closed_loans'];

                    $awp->avg_recovery = isset($awp_data['avg_recovery']) ? $awp_data['avg_recovery'] : 0;
                    if (\common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'PSIC')) {
                        $awp->monthly_recovery = isset($awp_data['monthly_recovery']) ? $awp_data['monthly_recovery'] : 0;
                    } else {
                        $awp->monthly_recovery = $awp->active_loans * $awp->avg_recovery;
                    }

                    $awp->status='1';
                    if ($awp->save()) {

                    } else {
                        print_r($awp->getErrors());
                        die('a');
                    }
                    $awp_all = Awp::find()->where(['branch_id' => $awp->branch_id,'project_id'=>$awp->project_id])->andWhere(['>', 'month', $awp->month])->all();
                    $i = 0;
                    $closing_olp=0;
                    $active_loans=0;
                    foreach ($awp_all as $d) {
                        if ($i == 0) {
                            $d->monthly_olp = ($awp->disbursement_amount + $awp->monthly_olp) - $awp->monthly_recovery;
                            $d->active_loans = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;
                        } else {
                            $d->monthly_olp = $closing_olp;
                            $d->active_loans = $active_loans;
                        }
                        $d->monthly_recovery = $d->active_loans * $d->avg_recovery;
                        $d->no_of_loans = $awp->no_of_loans;
                        $d->avg_loan_size = $awp->avg_loan_size;
                        $d->disbursement_amount = $awp->disbursement_amount;
                        $d->funds_required = round($d->disbursement_amount - $d->monthly_recovery);
                        $closing_olp = ($d->disbursement_amount) + ($d->monthly_olp) - ($d->monthly_recovery);
                        $active_loans = ($d->active_loans) + ($d->no_of_loans) - ($d->monthly_closed_loans);
                        $i++;
                        $d->status=1;
                        if ($d->save()) {

                        } else {
                            print_r($d->getErrors());
                            die();
                        }
                    }
                }
                $branch_id = $awp->branch_id;
                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>'1'])->groupBy('month')->all();
            }
        }

        return $this->render('create_awp_yearly', [
            'model' => $model,
            'branches' => $branches,
            'branch_id' => $branch_id,
            'projects' => $projects
        ]);

    }
    public function actionCreateYearly()
    {
        $request = Yii::$app->request;
        $branch_id = 0;
        $model = new Awp();
        $projects = [];
        //$branch_ids = array(145, 45, 46, 48, 50, 392, 393, 26, 51, 52, 284, 44, 745, 32, 742, 743, 744, 41, 42, 43, 12, 741, 660, 659, 661, 455, 439, 445, 446, 580, 390, 285, 161, 377, 270);
        $branch_ids = array(8,21,23,32,33,34,35,36,37,39,41,42,43,152,266,267,348,358,360,361,406,412,413,422,539,607,608,609,689,690,691,692,693,717,718,151,222,28,267,358,136,412,413,539,422,406,31,717,718,151,222,28,267,358,136,412,413,539,422,406,31,433,431);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        if ($request->post()) {
            $post = $request->post();
            //echo '<pre>';print_r($post);die();
            $branch_id = isset($post['branch_id']) ? $post['branch_id'] : 0;

            if ($branch_id != 0) {

                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>1])->groupBy('month')->all();

            }

            if (!empty($post['Awp']) && count($post['Awp']) != 1) {

                $i = 0;
                $j = 0;
                $closing_olp = [];
                $active_loans = [];
                foreach ($post['Awp'] as $awp) {
                    $j = 0;
                    $fund_required = 0;
                    $expected_recovery = 0;
                    $avg_recovery = 0;
                    foreach ($awp as $awp_key => $awp_value) {
                        $awp = Awp::find()->where(['id' => $awp_value['id']])->one();
                        if(!empty($awp) && $awp!=null){
                            $awp->monthly_recovery = $awp_value['monthly_recovery'];
                            $awp->no_of_loans = isset($awp_value['no_of_loans'])?$awp_value['no_of_loans']:0;
                            $awp->avg_loan_size = isset($awp_value['avg_loan_size'])?$awp_value['avg_loan_size']:0;
                            // $awp->amount_disbursed = $awp_value['amount_disbursed'];
                            $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
                            $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);
                            $awp->status=1;
                            ////Yearly figures
                            $awp->avg_recovery = isset($awp_value['avg_recovery']) ? $awp_value['avg_recovery'] : 0;

                            if ($i == 0) {
                                $awp->monthly_olp = isset($awp_value['monthly_olp'])?$awp_value['monthly_olp']:0;
                                $awp->active_loans = isset($awp_value['active_loans'])?$awp_value['active_loans']:0;

                            } else {
                                $awp->monthly_olp = isset($closing_olp[$i - 1][$j])?$closing_olp[$i - 1][$j]:0;
                                $awp->active_loans = isset($active_loans[$i - 1][$j])?$active_loans[$i - 1][$j]:0;
                            }
                            if (\common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'PSIC')) {
                                $awp->monthly_recovery = isset($awp_value['monthly_recovery']) ? $awp_value['monthly_recovery'] : 0;
                            } else {
                                $awp->monthly_recovery = $awp->active_loans * $awp->avg_recovery;
                            }
                            $awp->monthly_closed_loans = isset($awp_value['monthly_closed_loans']) ? $awp_value['monthly_closed_loans'] : 0;
                            $monthly_recovery = isset($awp->monthly_recovery) ? $awp->monthly_recovery : 0;
                            $monthly_olp = isset($awp->monthly_olp) ? $awp->monthly_olp : 0;
                            $amount_disbursed = isset($awp->disbursement_amount) ? $awp->disbursement_amount : 0;
                            $closing_olp[$i][$j] = ($amount_disbursed + $monthly_olp) - $monthly_recovery;
                            //$closing_olp[$i][$j] = ($awp->disbursement_amount + $awp_mapping->monthly_olp) - $awp_mapping->monthly_recovery;
                            $active_loans[$i][$j] = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;

                            if ($awp->save()) {
                            } else {
                                print_r($awp->getErrors());
                                die('a');
                            }
                            $j++;
                        }
                    }
                    $i++;
                }
                /*if ($awp->branch_id == 92){
                    Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
                    $file_path = Yii::getAlias('@anyname') . '/frontend/web/uploads' . '/awp.txt';
                    $myfile = fopen($file_path, "w") or die("Unable to open file!");
                    file_put_contents($file_path, print_r($post['Awp'], true));
                    fclose($myfile);
                }*/
                $branch_id = $awp->branch_id;
                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>1])->groupBy('month')->all();

            } else if (!empty($post['Awp']) && count($post['Awp']) == 1) {
                $arrData = array_values($post['Awp']);

                foreach($arrData[0] as $awp_data) {
                    $awp = Awp::find()->where(['id' => $awp_data['id']])->one();
                    $awp->monthly_recovery = $awp_data['monthly_recovery'];
                    $awp->no_of_loans = $awp_data['no_of_loans'];
                    $awp->avg_loan_size = $awp_data['avg_loan_size'];
                    $awp->disbursement_amount = $awp->no_of_loans * $awp->avg_loan_size;
                    $awp->funds_required = ($awp->disbursement_amount - $awp->monthly_recovery);

                    $awp->monthly_olp = $awp_data['monthly_olp'];
                    $awp->active_loans = $awp_data['active_loans'];
                    $awp->monthly_closed_loans = $awp_data['monthly_closed_loans'];

                    $awp->avg_recovery = isset($awp_data['avg_recovery']) ? $awp_data['avg_recovery'] : 0;
                    if (\common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'PSIC')) {
                        $awp->monthly_recovery = isset($awp_data['monthly_recovery']) ? $awp_data['monthly_recovery'] : 0;
                    } else {
                        $awp->monthly_recovery = $awp->active_loans * $awp->avg_recovery;
                    }

                    $awp->status=1;
                    if ($awp->save()) {

                    } else {
                        print_r($awp->getErrors());
                        die('a');
                    }
                    $awp_all = Awp::find()->where(['branch_id' => $awp->branch_id,'project_id'=>$awp->project_id])->andWhere(['>', 'month', $awp->month])->all();
                    $i = 0;
                    $closing_olp=0;
                    $active_loans=0;
                    foreach ($awp_all as $d) {
                        if ($i == 0) {
                            $d->monthly_olp = ($awp->disbursement_amount + $awp->monthly_olp) - $awp->monthly_recovery;
                            $d->active_loans = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;
                        } else {
                            $d->monthly_olp = $closing_olp;
                            $d->active_loans = $active_loans;
                        }
                        $d->monthly_recovery = $d->active_loans * $d->avg_recovery;
                        $d->no_of_loans = $awp->no_of_loans;
                        $d->avg_loan_size = $awp->avg_loan_size;
                        $d->disbursement_amount = $awp->disbursement_amount;
                        $d->funds_required = round($d->disbursement_amount - $d->monthly_recovery);
                        $closing_olp = ($d->disbursement_amount) + ($d->monthly_olp) - ($d->monthly_recovery);
                        $active_loans = ($d->active_loans) + ($d->no_of_loans) - ($d->monthly_closed_loans);
                        $i++;
                        $d->status=1;
                        if ($d->save()) {

                        } else {
                            print_r($d->getErrors());
                            die();
                        }
                    }
                }
                $branch_id = $awp->branch_id;
                $model = Awp::find()->where(['branch_id' => $branch_id,'status'=>'1'])->groupBy('month')->all();
            }
        }
        return $this->render('create_awp_yearly', [
            'model' => $model,
            'branches' => $branches,
            'branch_id' => $branch_id,
            'projects' => $projects
        ]);

    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $branch_id = 0;
        $model = new Awp();
        $projects = [];
        $branch_ids = array(145, 45, 46, 48, 50, 392, 393, 26, 51, 52, 284, 44, 745, 32, 742, 743, 744, 41, 42, 43, 12, 741, 660, 659, 661, 455, 439, 445, 446, 580, 390, 285, 161, 377, 270);

        $branches = \common\components\RbacHelper::searchBranchListIdWise(Yii::$app->controller->id, Yii::$app->controller->action->id);

        if ($request->post()) {
            $post = $request->post();
            $branch_id = isset($post['branch_id']) ? $post['branch_id'] : 0;
            if ($branch_id != 0) {
                $model = Awp::find()->where(['branch_id' => $branch_id])->all();
                $projects = BranchProjects::find()->where(['branch_id' => $branch_id])->all();
            }
            if (!empty($post['Awp']) && count($post['Awp']) == 1) {

                $awp = Awp::find()->where(['id' => $post['Awp']['01']['id']])->one();
                $awp->monthly_olp = $post['Awp']['01']['monthly_olp'];
                $awp->active_loans = $post['Awp']['01']['active_loans'];
                $awp->monthly_closed_loans = $post['Awp']['01']['monthly_closed_loans'];
                $awp->monthly_recovery = $post['Awp']['01']['active_loans'] * $awp->avg_recovery;
                $awp->no_of_loans = $post['Awp']['01']['no_of_loans'];
                $awp->avg_loan_size = $post['Awp']['01']['avg_loan_size'];
                $awp->amount_disbursed = $post['Awp']['01']['amount_disbursed'];
                $awp->funds_required = ($awp->amount_disbursed - $awp->monthly_recovery);
                $awp->status = 1;
                if ($awp->save()) {

                } else {
                    print_r($awp->getErrors());
                    die();
                }
                if (isset($request->post()['AwpProjectMapping'])) {
                    $awp_mapping_data = $request->post()['AwpProjectMapping'];
                    $mothly_recovery_total = 0;
                    $monthly_closed_loans_tatal = 0;

                    foreach ($awp_mapping_data as $data) {
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                        foreach ($data as $d) {

                            $awp_mapping = AwpProjectMapping::find()->where(['id' => $d['id']])->one();
                            $awp_mapping->monthly_olp = $d['monthly_olp'];
                            $awp_mapping->monthly_closed_loans = $d['monthly_closed_loans'];
                            $monthly_closed_loans_tatal += $d['monthly_closed_loans'];
                            $awp_mapping->active_loans = $d['active_loans'];
                            $awp_mapping->no_of_loans = $d['no_of_loans'];
                            $awp_mapping->avg_loan_size = $d['avg_loan_size'];
                            $awp_mapping->avg_recovery = $d['avg_recovery'];
                            if (\common\components\AwpHelper::getProject($awp_mapping->project_id)['name'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\AwpHelper::getProject($awp_mapping->project_id)['name'] == 'PSIC')) {
                                $awp_mapping->monthly_recovery = isset($d['monthly_recovery']) ? $d['monthly_recovery'] : 0;
                                $mothly_recovery_total += $awp_mapping->monthly_recovery;
                            } else {
                                $awp_mapping->monthly_recovery = $d['active_loans'] * $awp_mapping->avg_recovery;
                                $mothly_recovery_total += $awp_mapping->monthly_recovery;
                            }
                            $awp_mapping->disbursement_amount = $awp_mapping->no_of_loans * $awp_mapping->avg_loan_size;
                            $awp_mapping->funds_required = ($awp_mapping->disbursement_amount) - ($awp_mapping->monthly_recovery);
                            $fund_required += $awp_mapping->funds_required;
                            $expected_recovery += $awp_mapping->monthly_recovery;
                            $avg_recovery += $awp_mapping->avg_recovery;
                            if ($awp_mapping->save()) {
                                $awp = Awp::find()->where(['id' => $awp_mapping->awp_id])->one();
                                $awp->funds_required = $fund_required;
                                $awp->monthly_recovery = $expected_recovery;
                                $awp->avg_recovery = $avg_recovery;
                                $awp->save();
                            } else {
                                print_r($awp_mapping->getErrors());
                                die();
                            }
                        }
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                    }
                }
                $awp->monthly_recovery = $mothly_recovery_total;
                $awp->monthly_closed_loans = $monthly_closed_loans_tatal;

                if ($awp->save()) {

                } else {
                    print_r($awp->getErrors());
                    die();
                }
                $awp_all = Awp::find()->where(['branch_id' => $post['Awp']['01']['branch_id']])->andWhere(['!=', 'month', $post['Awp']['01']['month']])->all();

                $i = 0;
                $ii = 0;
                $closing_olp1 = [];
                $active_loans1 = [];

                foreach ($awp_all as $d) {
                    if ($i == 0) {
                        $d->monthly_olp = ($awp->amount_disbursed + $awp->monthly_olp) - $awp->monthly_recovery;
                        $d->active_loans = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;
                    } else {
                        $d->monthly_olp = $closing_olp;
                        $d->active_loans = $active_loans;
                    }
                    //}
                    $d->monthly_recovery = $d['active_loans'] * $awp->avg_recovery;
                    $d->no_of_loans = $awp->no_of_loans;
                    $d->avg_loan_size = $awp->avg_loan_size;
                    $amount_disbursed = $awp->amount_disbursed;
                    $d->amount_disbursed = $awp->amount_disbursed;
                    $d->status = 1;
                    $d->funds_required = round($d->amount_disbursed - $d->monthly_recovery);
                    $i++;
                    if ($d->save()) {

                    } else {
                        print_r($d->getErrors());
                        die();
                    }
                    if (isset($post['AwpProjectMapping']) && !empty($post['AwpProjectMapping'])) {
                        $mothly_recovery_total = 0;
                        $monthly_closed_loans_tatal = 0;
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                        foreach ($post['AwpProjectMapping']['01'] as $p_map) {

                            $awp_mapping = AwpProjectMapping::find()->where(['awp_id' => $d->id, 'project_id' => $p_map['project_id']])->one();

                            $awp_mapping->no_of_loans = $p_map['no_of_loans'];
                            $awp_mapping->avg_loan_size = $p_map['avg_loan_size'];
                            $awp_mapping->avg_recovery = isset($p_map['avg_recovery']) ? $p_map['avg_recovery'] : 0;

                            if (in_array($d->branch_id, $branch_ids)) {
                                $awp_mapping->monthly_closed_loans = $p_map['monthly_closed_loans'];
                            }

                            $monthly_closed_loans_tatal += $awp_mapping->monthly_closed_loans;

                            $awp_mapping->disbursement_amount = $awp_mapping->no_of_loans * $awp_mapping->avg_loan_size;
                            $amount_disbursed = $awp_mapping->disbursement_amount;
                            if ($ii == 0) {
                                $awp_mapping->monthly_olp = (($p_map['disbursement_amount'] + $p_map['monthly_olp']) - $p_map['monthly_recovery']);
                                $awp_mapping->active_loans = (($p_map['active_loans'] + $p_map['no_of_loans']) - $p_map['monthly_closed_loans']);

                            } else {
                                $awp_mapping->monthly_olp = $closing_olp1[$ii - 1][$p_map['project_id']];
                                $awp_mapping->active_loans = $active_loans1[$ii - 1][$p_map['project_id']];
                            }
                            if (\common\components\AwpHelper::getProjectcode($awp_mapping->project_id)['code'] == 'Kissan' || (in_array($d->branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($awp_mapping->project_id)['code'] == 'PSIC')) {
                                $awp_mapping->monthly_recovery = $p_map['monthly_recovery'];
                                $mothly_recovery_total += $awp_mapping->monthly_recovery;

                            } else {
                                $awp_mapping->monthly_recovery = $awp_mapping->active_loans * $awp_mapping->avg_recovery;
                                $mothly_recovery_total += $awp_mapping->monthly_recovery;
                            }
                            $closing_olp1[$ii][$p_map['project_id']] = ($amount_disbursed + $awp_mapping->monthly_olp) - $awp_mapping['monthly_recovery'];

                            $active_loans1[$ii][$p_map['project_id']] = ($awp_mapping->active_loans + $awp_mapping->no_of_loans) - $awp_mapping->monthly_closed_loans;
                            $awp_mapping->funds_required = ($awp_mapping->disbursement_amount - $awp_mapping->monthly_recovery);
                            $fund_required += $awp_mapping->funds_required;
                            $expected_recovery += $awp_mapping->monthly_recovery;
                            $avg_recovery += $awp_mapping->avg_recovery;
                            if ($awp_mapping->save()) {
                                $awp = Awp::find()->where(['id' => $awp_mapping->awp_id])->one();
                                $awp->funds_required = $fund_required;
                                $awp->monthly_recovery = $expected_recovery;
                                $awp->avg_recovery = $avg_recovery;
                                $awp->save();
                            } else {
                                print_r($awp_mapping->getErrors());
                                die();
                            }
                        }
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                    }
                    if (in_array($d->branch_id, $branch_ids)) {
                        $d->monthly_recovery = $mothly_recovery_total;
                        $d->monthly_closed_loans = $monthly_closed_loans_tatal;
                    }
                    $d->monthly_recovery = $mothly_recovery_total;
                    $d->monthly_closed_loans = $monthly_closed_loans_tatal;
                    $closing_olp = ($awp->amount_disbursed) + ($d->monthly_olp) - ($d->monthly_recovery);
                    $active_loans = ($d->active_loans) + ($d->no_of_loans) - ($d->monthly_closed_loans);
                    if ($d->save()) {

                    } else {
                        print_r($d->getErrors());
                        die();
                    }
                    $ii++;
                }
                $branch_id = $post['Awp']['01']['branch_id'];
                $model = Awp::find()->where(['branch_id' => $branch_id])->all();
                $projects = BranchProjects::find()->where(['branch_id' => $branch_id])->all();
            } else if (!empty($post['Awp']) && count($post['Awp']) != 1) {
                $closing_olp = 0;
                $active_loans = 0;
                $i = 0;
                $awp_data = $request->post()['Awp'];

                foreach ($awp_data as $d) {
                    $awp = Awp::find()->where(['id' => $d['id']])->one();

                    if ($i == 0) {
                        $awp->monthly_olp = $d['monthly_olp'];
                        $awp->active_loans = $d['active_loans'];
                    } else {
                        $awp->monthly_olp = $closing_olp;
                        $awp->active_loans = $active_loans;
                    }
                    $awp->monthly_closed_loans = $d['monthly_closed_loans'];
                    $awp->monthly_recovery = $d['monthly_recovery'];
                    $awp->no_of_loans = isset($d['no_of_loans']) ? $d['no_of_loans'] : 0;
                    $awp->avg_loan_size = isset($d['avg_loan_size']) ? $d['avg_loan_size'] : 0;
                    $amount_disbursed = isset($d['amount_disbursed']) ? $d['amount_disbursed'] : 0;
                    $awp->amount_disbursed = $amount_disbursed;
                    $closing_olp = ($amount_disbursed + $awp->monthly_olp) - $awp->monthly_recovery;
                    $active_loans = ($awp->active_loans + $awp->no_of_loans) - $awp->monthly_closed_loans;
                    $awp->funds_required = ($awp->amount_disbursed) - ($awp->monthly_recovery);
                    $awp->status = 1;
                    $i++;
                    if ($awp->save()) {

                    } else {
                        print_r($awp->getErrors());
                        die();
                    }
                }
                if (isset($request->post()['AwpProjectMapping'])) {
                    $awp_mapping_data = $request->post()['AwpProjectMapping'];
                    $i = 0;
                    $j = 0;
                    $closing_olp = [];
                    $active_loans = [];
                    $expected_recovery = 0;
                    $fund_required = 0;
                    $avg_recovery = 0;
                    foreach ($awp_mapping_data as $data) {
                        $j = 0;
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                        foreach ($data as $d) {

                            $awp_mapping = AwpProjectMapping::find()->where(['id' => $d['id']])->one();
                            $awp_mapping->no_of_loans = isset($d['no_of_loans']) ? $d['no_of_loans'] : 0;
                            $awp_mapping->avg_loan_size = isset($d['avg_loan_size']) ? $d['avg_loan_size'] : 0;
                            $awp_mapping->avg_recovery = isset($d['avg_recovery']) ? $d['avg_recovery'] : 0;
                            if (($awp_mapping->avg_loan_size == 0) || ($awp_mapping->no_of_loans == 0)) {
                                $awp_mapping->no_of_loans = 0;
                                $awp_mapping->avg_loan_size = 0;
                                //die('here');
                            }
                            $awp_mapping->disbursement_amount = $awp_mapping->no_of_loans * $awp_mapping->avg_loan_size;
                            $amount_disbursed = ($awp_mapping->no_of_loans) * ($awp_mapping->avg_loan_size);
                            if ($i == 0) {
                                $awp_mapping->monthly_olp = $d['monthly_olp'];
                                $awp_mapping->active_loans = $d['active_loans'];

                            } else {
                                $awp_mapping->monthly_olp = $closing_olp[$i - 1][$j];
                                $awp_mapping->active_loans = $active_loans[$i - 1][$j];
                            }
                            if (\common\components\AwpHelper::getProjectcode($awp_mapping->project_id)['code'] == 'Kissan' || (in_array($awp->branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($awp_mapping->project_id)['code'] == 'PSIC')) {
                                $awp_mapping->monthly_recovery = isset($d['monthly_recovery']) ? $d['monthly_recovery'] : 0;
                            } else {
                                $awp_mapping->monthly_recovery = $awp_mapping->active_loans * $awp_mapping->avg_recovery;
                            }
                            $awp_mapping->monthly_closed_loans = isset($d['monthly_closed_loans']) ? $d['monthly_closed_loans'] : 0;
                            $monthly_recovery = isset($d['monthly_recovery']) ? $d['monthly_recovery'] : 0;
                            $monthly_olp = isset($d['monthly_olp']) ? $d['monthly_olp'] : 0;
                            $closing_olp[$i][$j] = ($amount_disbursed + $monthly_olp) - $monthly_recovery;
                            $closing_olp[$i][$j] = ($amount_disbursed + $awp_mapping->monthly_olp) - $awp_mapping->monthly_recovery;

                            $active_loans[$i][$j] = ($awp_mapping->active_loans + $awp_mapping->no_of_loans) - $awp_mapping->monthly_closed_loans;
                            $awp_mapping->funds_required = ($awp_mapping->disbursement_amount - $awp_mapping->monthly_recovery);
                            $fund_required += $awp_mapping->funds_required;
                            $expected_recovery += $awp_mapping->monthly_recovery;
                            $avg_recovery += $awp_mapping->avg_recovery;
                            if ($awp_mapping->save()) {
                                $awp = Awp::find()->where(['id' => $awp_mapping->awp_id])->one();
                                $awp->funds_required = $fund_required;
                                $awp->monthly_recovery = $expected_recovery;
                                $awp->avg_recovery = $avg_recovery;
                                $awp->save();
                            } else {
                                print_r($awp_mapping->getErrors());
                                die();
                            }
                            $j++;
                        }
                        $fund_required = 0;
                        $expected_recovery = 0;
                        $avg_recovery = 0;
                        $i++;
                    }
                }
                $branch_id = $post['Awp']['01']['branch_id'];
                $model = Awp::find()->where(['branch_id' => $branch_id])->all();
                $projects = BranchProjects::find()->where(['branch_id' => $branch_id])->all();
            }
        }
        return $this->render('create', [
            'model' => $model,
            'branches' => $branches,
            'branch_id' => $branch_id,
            'projects' => $projects
        ]);
    }


    /**
     * Updates an existing Awp model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update Awp #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Awp #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update Awp #" . $id,
                    'content' => $this->renderAjax('update', [
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
     * Delete an existing Awp model.
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

    /**
     * Delete multiple existing Awp model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
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
     * Finds the Awp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Awp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Awp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBranchprojects()
    {
        $out = [];
        $out1 = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $branch_id = $parents[0];
                $branch = AwpHelper::getBranchidfromcode($branch_id);
                $a = AwpHelper::getBranchprojects($branch['id']);

                for ($i = 0; $i < count($a); $i++) {
                    $b = $a[$i]['project_id'];
                    $out1 = AwpHelper::getProjectname($b);
                    array_push($out, $out1[0]);

                }

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionUpdateIslock()
    {
        $request = Yii::$app->request;
        $awp = Awp::find()->where(['branch_id' => $request->post()['Awp']['branch_id']])->andWhere(['>','month','2019-06'])->all();
        foreach ($awp as $as) {
            $as->status = 0;
            $as->is_lock = 1;
            $as->save();
        }
    }

    public function actionAwpProjectWise()
    {
        $params = Yii::$app->request->post();
        $searchModel = new AwpSearch();
        $dataProvider = $searchModel->searchprojectwise($params);
        /* $months=array("2018-07"=>"2018-07",
             "2018-08"=>"2018-08",
             "2018-09"=>"2018-09",
             "2018-10"=>"2018-10",
             "2018-11"=>"2018-11",
             "2018-12"=>"2018-12",
             "2019-01"=>"2019-01",
             "2019-02"=>"2019-02",
             "2019-03"=>"2019-03",
             "2019-04"=>"2019-04",
             "2019-05"=>"2019-05",
             "2019-06"=>"2019-06",

         );*/
        $months = Awp::find()->select('month as id,month as name')->where('actual_no_of_loans != "' . 0 . '"')->distinct()->asArray()->all();
        $months = ArrayHelper::map($months, 'name', 'name');
        if (!empty($params['AwpSearch']['month'])) {
            $date = date('Y-M', strtotime($params['AwpSearch']['month']));
        } else {
            $date = date('Y-M');
        }
        $olp_date = isset($searchModel->month) ? date('d,M-Y', (strtotime('-1 months', strtotime($searchModel->month)))) : date('d,M-Y', strtotime('last day of last month'));

        return $this->render('awp_project_wise', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'months' => $months,
            'date' => $date,
            'olp_date' => $olp_date,
        ]);
    }

    public function actionAwpReport()
    {

        $params = Yii::$app->request->post();
        $searchAwp = new AwpSearch();
        $awpdata = $searchAwp->searchawpreport($params);
        $awp_report = \common\components\Helpers\AwpHelper::parse_json_awp($awpdata);
        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');

        //$regions = Yii::$app->Permission->getRegionListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        //$projects = Yii::$app->Permission->getProjectListNameWise(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');


        //$regions = RbacHelper::searchRegionList(Yii::$app->controller->id,Yii::$app->controller->action->id);
        //$projects = RbacHelper::searchProjectListById(Yii::$app->controller->id,Yii::$app->controller->action->id);

        /*  $progress_report_date = strtotime($progress_report_dates_array[$searchProgress->progress_report_id]);
          $progress_report_project = ($project_id != 0) ? $projects[$project_id] : 'overall';*/

        return $this->render('awp_report', [
            'searchModel' => $searchAwp,
            'awp_report' => $awp_report,
            'branches' => $branches,
            'areas' => $areas,
            'projects' => $projects,
            'regions' => $regions
        ]);
    }

    public function actionAwpProjectWiseBudget()
    {
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $groups = array();
            $params = Yii::$app->request->post();
            $searchModel = new AwpSearch();
            $query = $searchModel->searchprojectwisebudget($params, true);
            $months = Awp::find()->select('month as id,month as name')->where('actual_no_of_loans != "' . 0 . '"')->distinct()->asArray()->all();
            $months = ArrayHelper::map($months, 'name', 'name');
            if (!empty($params['AwpSearch']['month'])) {
                $date = date('M-Y', strtotime($params['AwpSearch']['month']));
            } else {
                $date = date('M-Y');
            }
            $olp_date = isset($searchModel->month) ? date('d M,Y', (strtotime('-1 months', strtotime($searchModel->month)))) : date('d M,Y', strtotime('last day of last month'));
            $headers = ["Funding Source","Project Name", "Project Funds(PKR)", "Funds Received(PKR)", "OLP(PKR) as on" . $olp_date . "", "Disbursement Target(PKR)", "Expected Recovery(PKR)", "Funds Available(PKR)"];
            //$data = $query->getModels();
            $i = 0;
            foreach ($query as $g) {
                $groups[$i]['fund_source'] = isset($g['fund_source']) ? $g['fund_source'] : '';
                $groups[$i]['projec_name'] = isset($g['name']) ? $g['name'] : '';
                $groups[$i]['total_fund'] = isset($g['total_fund']) ? $g['total_fund'] : '';
                $groups[$i]['fund_received'] = isset($g['fund_received']) ? $g['fund_received'] : '';
                $groups[$i]['olp'] = isset($g['olp']) ? $g['olp'] : '';
                $groups[$i]['disbursement_amount'] = isset($g['disbursement_amount']) ? $g['disbursement_amount'] : '';
                $groups[$i]['expected_recovery'] = isset($g['expected_recovery']) ? $g['expected_recovery'] : '';
                $groups[$i]['dund_required'] = ($g['fund_received'] - $g['olp'] - $g['disbursement_amount'] + $g['expected_recovery']);
                $i++;
            }
            ExportHelper::ExportCSV('Project-wise AWP.csv', $headers, $groups);
            die();
        }

        $params = Yii::$app->request->post();
        $searchModel = new AwpSearch();
        $dataProvider = $searchModel->searchprojectwisebudget($params, false);
        $months = Awp::find()->select('month as id,month as name')->where('actual_no_of_loans != "' . 0 . '"')->distinct()->asArray()->all();
        $months = ArrayHelper::map($months, 'name', 'name');
        if (!empty($params['AwpSearch']['month'])) {
            $date = date('M-Y', strtotime($params['AwpSearch']['month']));
        } else {
            $date = date('M-Y');
        }
        $olp_date = isset($searchModel->month) ? date('d M,Y', (strtotime('-1 months', strtotime($searchModel->month)))) : date('d M,Y', strtotime('last day of last month'));

        return $this->render('awp_project_wise_budget', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'months' => $months,
            'date' => $date,
            'olp_date' => $olp_date,
        ]);
    }
    public function actionOverdueDetail($branch_id){
        $months=[
            "2022-06" => "2022-06",
            "2022-07" => "2022-07",
            "2022-08" => "2022-08",
            "2022-09" => "2022-09",
            "2022-10" => "2022-10",
            "2022-11" => "2022-11",
            "2022-12" => "2022-12",
//            "2023-01" => "2023-01",
//            "2023-02" => "2023-02",
//            "2023-03" => "2023-03",
//            "2023-04" => "2023-04",
//            "2023-05" => "2023-05"
        ];
        $array=[];
        foreach($months as $month){
            $progres_report = ProgressReports::find()->where(['project_id' => 0])->andWhere(['between','report_date',strtotime(date('Y-m-t', strtotime($month))),strtotime(date('Y-m-t-23-59', strtotime($month)))])->one();
            if (!empty($progres_report)) {
                $progress_report_details = ProgressReportDetails::find()->where(['progress_report_id' => $progres_report->id, 'branch_id' => $branch_id])->one();

                if (!empty($progress_report_details)) {
                    $arr=array('month'=>$month,'overdue'=>$progress_report_details->overdue_borrowers);
                    array_push($array,$arr);
                }
                else{
                    $arr=array('month'=>$month,'overdue'=>0);
                    array_push($array,$arr);
                }
            }
            else{
                $arr=array('month'=>$month,'overdue'=>0);
                array_push($array,$arr);
            }
        }
        $overdue_detail=($array);
        $this->layout = 'main_simple_js';
        return $this->render('overdueDetails', [
            'model' => $overdue_detail,
            'branch_id'=>$branch_id
        ]);
    }
    public function actionLastYearDetail($branch_id){
        $model = AwpRecoveryPercentage::find()
            ->select(['sum(recovery_count) as recovery_count','sum(recovery_one_to_ten) as recovery_one_to_ten','sum(	recovery_eleven_to_twenty) as 	recovery_eleven_to_twenty'
                ,'sum(recovery_twentyone_to_thirty) as recovery_twentyone_to_thirty'])->where(['between','month','2018-07','2019-06'])->andWhere(['branch_id'=>$branch_id])->one();
        $this->layout = 'main_simple_js';
        return $this->render('last_year_detail', [
            'model' => $model,
            'branch_id'=>$branch_id
        ]);
    }
}
