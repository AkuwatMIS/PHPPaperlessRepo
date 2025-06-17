<?php

namespace frontend\controllers;

use common\models\Applications;
use Yii;
use common\models\AppraisalsBusiness;
use common\models\search\AppraisalsBusinessSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;
/**
 * AppraisalsBusinessController implements the CRUD actions for AppraisalsBusiness model.
 */
class AppraisalsBusinessController extends Controller
{
    /**
     * @inheritdoc
     */
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
        $searchModel = new AppraisalsBusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "AppraisalsBusiness #" . $id,
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
     * Creates a new AppraisalsBusiness model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new AppraisalsBusiness();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new AppraisalsBusiness",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new AppraisalsBusiness",
                    'content' => '<span class="text-success">Create AppraisalsBusiness success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                ];
            } else {
                return [
                    'title' => "Create new AppraisalsBusiness",
                    'content' => $this->renderAjax('create', [
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
                return $this->render('create', [
                    'model' => $model,
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
        if(isset($model->fixed_business_assets) && !empty($model->fixed_business_assets)){
//        $fixed_business_assets = explode(',', $model->fixed_business_assets);
            $fixed_business_assets = str_replace(',', ' ', $model->fixed_business_assets);
            $model->fixed_business_assets = $fixed_business_assets;
            $fixed_business_assets_dropdown = \common\components\Helpers\ListHelper::getListsData($model->application->activity->name . '-fixed_business_assets');
        }

        if(isset($model->running_capital) && !empty($model->running_capital)){
//        $running_capital = explode(',', $model->running_capital);
            $running_capital = str_replace(',', ' ', $model->running_capital);
            $model->running_capital = $running_capital;
            $running_capital_dropdown = \common\components\Helpers\ListHelper::getListsData($model->application->activity->name . '-running_capital');
        }


        if(isset($model->business_expenses) && !empty($model->business_expenses)){
//        $business_expenses = explode(',', $model->business_expenses);
            $business_expenses = str_replace(',', ' ', $model->business_expenses);
            $model->business_expenses = $business_expenses;
            $business_expenses_dropdown = \common\components\Helpers\ListHelper::getListsData($model->application->activity->name . '-business_expenses');

        }

        if(isset($model->new_required_assets) && !empty($model->new_required_assets)){
//        $new_required_assets = explode(',', $model->new_required_assets);
            $new_required_assets = str_replace(',', ' ', $model->new_required_assets);
            $model->new_required_assets = $new_required_assets;
            $new_required_assets_dropdown = \common\components\Helpers\ListHelper::getListsData($model->application->activity->name . '-new_required_assets');
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update AppraisalsBusiness #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "AppraisalsBusiness #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update AppraisalsBusiness #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            if ($model->load($request->post())/* && $model->save()*/) {
                $application = Applications::find()->select('bzns_cond')->where(['id' => $model->application_id])->one();

//                $fixed_business_assets = "";
//                $business_expenses = "";
//                $new_required_assets = "";
//                $running_capital = "";
//                if (!empty($request->post()['AppraisalsBusiness']['fixed_business_assets'])) {
//                    foreach ($request->post()['AppraisalsBusiness']['fixed_business_assets'] as $asset => $status) {
//                        if ($fixed_business_assets != "") {
//                            $fixed_business_assets .= "," . $status;
//                        } else {
//                            $fixed_business_assets .= $status;
//                        }
//                    }
//                }
//                if (!empty($request->post()['AppraisalsBusiness']['business_expenses'])) {
//                    foreach ($request->post()['AppraisalsBusiness']['business_expenses'] as $asset => $status) {
//                        if ($business_expenses != "") {
//                            $business_expenses .= "," . $status;
//                        } else {
//                            $business_expenses .= $status;
//                        }
//                    }
//                }
//                if (!empty($request->post()['AppraisalsBusiness']['new_required_assets'])) {
//                    foreach ($request->post()['AppraisalsBusiness']['new_required_assets'] as $asset => $status) {
//                        if ($new_required_assets != "") {
//                            $new_required_assets .= "," . $status;
//                        } else {
//                            $new_required_assets .= $status;
//                        }
//                    }
//                }
//                if (!empty($request->post()['AppraisalsBusiness']['running_capital'])) {
//                    foreach ($request->post()['AppraisalsBusiness']['running_capital'] as $asset => $status) {
//                        if ($running_capital != "") {
//                            $running_capital .= "," . $status;
//                        } else {
//                            $running_capital .= $status;
//                        }
//                    }
//                }
                $fba = $request->post()['AppraisalsBusiness']['fixed_business_assets'];
                $be = $request->post()['AppraisalsBusiness']['business_expenses'];
                $nra = $request->post()['AppraisalsBusiness']['new_required_assets'];
                $rc = $request->post()['AppraisalsBusiness']['running_capital'];

                $model->fixed_business_assets=str_replace(' ', ',', trim($fba));
                $model->running_capital=str_replace(' ', ',', trim($be));
                $model->new_required_assets=str_replace(' ', ',', trim($nra));
                $model->business_expenses=str_replace(' ', ',', trim($rc));

//                $model->fixed_business_assets = $fixed_business_assets;
//                $model->running_capital = $running_capital;
//                $model->business_expenses = $business_expenses;
//                $model->new_required_assets = $new_required_assets;
                if ($application->bzns_cond == 'new') {
                    if (!isset($model->new_required_assets) || (!isset($model->new_required_assets_amount))) {
                        $model->addError('new_required_assets', 'If Business is new then New Required Assets can not be empty');
                        $model->addError('new_required_assets', 'If Business is new then New Required Assets Total can not be empty');
                    }
                } else if ($application->bzns_cond == 'old') {
                    if (!isset($model->fixed_business_assets) || !isset($model->fixed_business_assets_amount)) {
                        $model->addError('fixed_business_assets', 'If Business is old then Fixed Business Assets can not be empty');
                        $model->addError('fixed_business_assets_amount', 'If Business is old then Fixed Business Assets Total can not be empty');
                    }
                    if (!isset($model->running_capital) || (!isset($model->running_capital_amount))) {
                        $model->addError('running_capital', 'If Business is old then Running Capital can not be empty');
                        $model->addError('running_capital_amount', 'If Business is old then Running Capital Total can not be empty');
                    }
                    if (!isset($model->business_expenses) || (!isset($model->business_expenses_amount))) {
                        $model->addError('business_expenses', 'If Business is old then Business Expenses can not be empty');
                        $model->addError('business_expenses_amount', 'If Business is old then Business Expenses Total can not be empty');
                    }
                }
                if (empty($model->getErrors())) {
                    if ($model->save()) {
                        return $this->redirect(['/applications/view', 'id' => $model->application_id]);
                    }
                } else {
                    return $this->render('update', [
                        'model' => $model,
                        'fixed_business_assets_dropdown' => $fixed_business_assets_dropdown,
                        'running_capital_dropdown' => $running_capital_dropdown,
                        'business_expenses_dropdown' => $business_expenses_dropdown,
                        'new_required_assets_dropdown' => $new_required_assets_dropdown,
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'fixed_business_assets_dropdown' => $fixed_business_assets_dropdown,
                    'running_capital_dropdown' => $running_capital_dropdown,
                    'business_expenses_dropdown' => $business_expenses_dropdown,
                    'new_required_assets_dropdown' => $new_required_assets_dropdown,
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
     * Delete multiple existing AppraisalsBusiness model.
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
     * Finds the AppraisalsBusiness model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppraisalsBusiness the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppraisalsBusiness::findOne($id)) !== null) {
            $application=Applications::find()->where(['id'=>$model->application_id])->one();
            if(empty($application->loan)){
                return $model;
            }else{
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
