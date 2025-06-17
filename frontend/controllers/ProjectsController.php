<?php

namespace frontend\controllers;
use common\components\Helpers\ImageHelper;
use common\models\Activities;
use common\models\Lists;
use common\models\Products;
use common\models\ProgressReportDetails;
use common\models\ProgressReports;
use common\models\Areas;
use common\models\Branches;
use common\models\Fields;
use common\models\ProjectCharges;
use common\models\ProjectFiles;
use common\models\Projects;
use common\models\search\ProjectsSearch;
use common\models\Teams;
use common\models\ViewSectionFields;
use yii\web\UploadedFile;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class ProjectsController extends Controller
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
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new ProjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider = $searchModel->searchServiceCharges(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!in_array(Yii::$app->user->id, [2011, 2007, 5507])) {
            Yii::$app->session->setFlash('error', 'You are not authorized to perform this action.');
            return Yii::$app->response->redirect(Yii::$app->request->referrer);
        }
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $date = strtotime('last day of previous month');

        $report = ProgressReports::find()
            ->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])
            ->andWhere(['<=', 'report_date', $date])
            ->andWhere(['gender' => '0'])
            ->andWhere(['project_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $progress_report_data = ProgressReportDetails::find()
            ->select(['sum(cum_disb) as cum_disb', 'sum(olp_amount) as olp_amount'])
            ->where(['progress_report_id' => $report->id])
            ->one();

        // Load the posted form data
        if ($model->load($request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file) {
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $savePath = ImageHelper::getAttachmentPath() . '/project_files/' . $file_name;

                // Ensure directory exists
                if (!is_dir(dirname($savePath))) {
                    mkdir(dirname($savePath), 0777, true);
                }
                $model->file->saveAs($savePath);
            }

            if ($model->save()) {
                // Save new fund receive record
                $fundReceive = new ProjectFiles();
                $fundReceive->project_id = $model->id;
                $fundReceive->amount = $model->current_fund_receive;
                $fundReceive->file_path = $file_name;
                $fundReceive->status = 0;
                $fundReceive->created_by = Yii::$app->user->id;
                $fundReceive->created_at = time();
                $fundReceive->save();

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'project_model' => $model,
            'progress_report_data' => $progress_report_data,
        ]);
    }

    public function actionDownloadFile($id)
    {
        $file = ProjectFiles::findOne($id);
        if (!$file || !$file->file_path) {
            throw new NotFoundHttpException("File not found.");
        }

        $fullPath = ImageHelper::getAttachmentPath() . '/project_files/' . $file->file_path;

        if (file_exists($fullPath)) {
            return Yii::$app->response->sendFile($fullPath);
        } else {
            throw new NotFoundHttpException("File does not exist on server.");
        }
    }

    public function actionApproveFile($id)
    {
        $file = ProjectFiles::findOne($id);
        if (!$file) {
            throw new NotFoundHttpException("File not found.");
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Approve file
            $file->status = 1; // Approved
            $file->approved_by = Yii::$app->user->id;
            $file->approved_at = time();

            if (!$file->save(false)) {
                throw new \Exception("Failed to update file approval.");
            }

            // Update project fund
            $project = $this->findModel($file->project_id);
            if (!$project) {
                throw new \Exception("Related project not found.");
            }

            $project->fund_received += (float)$file->amount;

            if (!$project->save(false)) {
                throw new \Exception("Failed to update project fund.");
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'File approved and project fund updated successfully.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Transaction failed: ' . $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['view', 'id' => $file->project_id]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "ProjectCharges #".$id,
                'content'=>$this->renderAjax('view', [
                    'model' => $this->findModel($id)
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            $projectFiles = ProjectFiles::find()->where(['project_id'=>$id])->all();
            return $this->render('view', [
                'model' => $this->findModel($id),
                'modelFiles' => $projectFiles
            ]);
        }
    }

    public function actionServiceChargesCreate($id)
    {
        $request = Yii::$app->request;
        $model = new ProjectCharges();
        $project_model = $this->findModel($id);
        $date =  strtotime('last day of previous month');
        $report = ProgressReports::find()->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])->andWhere(['<=','report_date',$date ])->andWhere(['gender' => '0'])->andWhere(['project_id' => $id])->orderBy(['id'=>SORT_DESC])->one();
        $progress_report_data = ProgressReportDetails::find()->select('sum(cum_disb) as cum_disb,sum(olp_amount) as olp_amount')->where(['progress_report_id' => $report->id])->one();



        if ($model->load($request->post())) {
            $model->project_id = $id;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'project_model' => $project_model,
                    'progress_report_data' => $progress_report_data,
                    'date' => $date
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'project_model' => $project_model,
                'progress_report_data' => $progress_report_data,
                'date' => $date
            ]);
        }

    }

    public function actionServiceChargesUpdate($id)
    {
        $request = Yii::$app->request;
        $model = ProjectCharges::find()->where(['project_id' => $id])->orderBy(['id' => SORT_DESC])->one();
        $date =  strtotime('last day of previous month');
        $report = ProgressReports::find()->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])->andWhere(['<=','report_date',$date ])->andWhere(['gender' => '0'])->andWhere(['project_id' => $id])->orderBy(['id'=>SORT_DESC])->one();
        $progress_report_data = ProgressReportDetails::find()->select('sum(cum_disb) as cum_disb,sum(olp_amount) as olp_amount')->where(['progress_report_id' => $report->id])->one();
        $project_model = $this->findModel($id);
        if ($model->load($request->post())) {
            if($model->save())
            {
                return $this->redirect(['view', 'id' => $model->project_id]);
            }
            else {
                return $this->render('update', [
                    'model' => $model,
                    'project_model' => $project_model,
                    'progress_report_data' => $progress_report_data,
                    'date' => $date
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'project_model' => $project_model,
                'progress_report_data' => $progress_report_data,
                'date' => $date
            ]);
        }

    }

    public function actionServiceChargesView($id)
    {
        $request = Yii::$app->request;
        $date =  strtotime('last day of previous month');
        $report = ProgressReports::find()->where(['status' => 1, 'do_delete' => 0, 'deleted' => 0])->andWhere(['<=','report_date',$date ])->andWhere(['gender' => '0'])->andWhere(['project_id' => $id])->orderBy(['id'=>SORT_DESC])->one();
        $progress_report_data = ProgressReportDetails::find()->select('sum(cum_disb) as cum_disb,sum(olp_amount) as olp_amount')->where(['progress_report_id' => $report->id])->one();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "ProjectCharges #".$id,
                'content'=>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                    'progress_report_data' => $progress_report_data,
                    'date' => $date
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'progress_report_data' => $progress_report_data,
                'date' => $date
            ]);
        }
    }

    public function actionForm($id)
    {
        $project = Projects::findOne($id);
        $project_details = ViewSectionFields::find()->where(['table_name'=>$project->project_table])->orderBy('sort_order asc')->all();
        $array = array();
        foreach ($project_details as $project_detail){
            $array[] = [
                /*"table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                "column"=> isset($project_detail->field) ? $project_detail->field : '',
                "question_id"=> isset($project_detail->sectionFieldsConfigs[0]->value) ? $project_detail->sectionFieldsConfigs[0]->value : '',
                "is_mandatory"=> true,
                "is_hidden"=> false,
                "type"=> isset($project_detail->sectionFieldsConfigs[1]->value) ? $project_detail->sectionFieldsConfigs[1]->value : '',
                "format"=> isset($project_detail->sectionFieldsConfigs[2]->value) ? $project_detail->sectionFieldsConfigs[2]->value : '',
                "label"=> "",
                "place_holder"=> isset($project_detail->sectionFieldsConfigs[3]->value) ? $project_detail->sectionFieldsConfigs[3]->value : "",
                "default_value"=> "",
                "mask"=> "",
                "answers"=> isset($project_detail->sectionFieldsConfigs[8]->value) ? $project_detail->sectionFieldsConfigs[8]->value : "",
                "answers_values"=> self::getAnswersValues(isset($project_detail->sectionFieldsConfigs[8]->value) ? $project_detail->sectionFieldsConfigs[8]->value : ""),
                "default_visibility"=> isset($project_detail->sectionFieldsConfigs[4]->value) ? $project_detail->sectionFieldsConfigs[4]->value : "",
                "style"=> array(
                    "width"=> isset($project_detail->sectionFieldsConfigs[6]->value) ? $project_detail->sectionFieldsConfigs[6]->value : "",
                    "height"=> isset($project_detail->sectionFieldsConfigs[7]->value) ? $project_detail->sectionFieldsConfigs[7]->value : ""
                ),
                "constraints"=> [],
                "dependent_question"=> [
                ],*/
                "table" => isset($project_detail->table_name) ? $project_detail->table_name : '',
                "column"=> isset($project_detail->field) ? $project_detail->field : '',
                "question_id"=> isset($project_detail->sectionFieldConfigQuestionid->value) ? $project_detail->sectionFieldConfigQuestionid->value : '',
                "is_mandatory"=>true,
                "is_hidden"=> false,
                "type"=> isset($project_detail->sectionFieldConfigType->value) ? $project_detail->sectionFieldConfigType->value : '',
                "format"=> isset($project_detail->sectionFieldConfigFormat->value) ? $project_detail->sectionFieldConfigFormat->value : 'text',
                "label"=> "",
                "place_holder"=> isset($project_detail->sectionFieldConfigPlaceholder->value) ? $project_detail->sectionFieldConfigPlaceholder->value : "",
                "default_value"=> "",
                "mask"=> "",
                "answers"=> isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : "",
                "answers_values"=> self::getAnswersValues(isset($project_detail->sectionFieldConfigAnswer->value) ? $project_detail->sectionFieldConfigAnswer->value : ""),
                "default_visibility"=> isset($project_detail->sectionFieldConfigVisible->value) ? $project_detail->sectionFieldConfigVisible->value : "",
                "style"=> array(
                    "width"=> isset($project_detail->sectionFieldConfigWidth->value) ? $project_detail->sectionFieldConfigWidth->value : "",
                    "height"=> isset($project_detail->sectionFieldConfigHeight->value) ? $project_detail->sectionFieldConfigHeight->value : ""
                ),
                "constraints"=> [],
                "dependent_question"=> [
                    /*"question_id" => "et_institute_name",
                    "actions" => array(
                        "action" => "update_content",
                        "visibility" => "visible",
                        "visible_on_answers" => "TEVTA"
                    )*/
                ],
            ];
        }
        \Yii::$app->response->data = json_encode($array);
        //echo json_encode($array);
    }

    public function actionCroptypes($crop_type)
    {
        $array = array();
        if($crop_type == 'rabi'){
            $array = array('wheat' => 'Wheat (گندم)', 'barley' => 'Barley (جو)', 'oats'=> 'oats (جٔی)', 'chickpea' => 'chickpea (سفید چنا)', 'linseed'=> 'Linseed (السی)', 'mustard' => 'Mustard (سرسوں)', 'arhar'=>'Arhar (pulses)(مسور)', 'soyabean'=>'Soyabean (سویا بین)', 'potato'=>'Potato (آلو)');
        }else if($crop_type == 'kharif'){
            $array = array('rice' => 'Rice (چاول)', 'maize' => 'Maize (مکٔی)', 'sorghum'=>'Sorghum (جوار)', 'pearl'=>'Pearl millet/bajra (باجرہ)', 'millet'=>'Finger millet/ragi (راگی)', 'sugarcane'=>'Sugar cane (گنا)', 'groundnut'=>'Groundnut (مونگ پھلی)', 'cotton'=>'Cotton (کپاس)');
        }
        echo json_encode($array);
    }

    protected static function getAnswersValues($answers_values){
        $array = array();
        if($answers_values != ''){
            $list = Lists::find()->where(['list_name'=>$answers_values])->orderBy('sort_order asc')->all();
            foreach ($list as $l){
                $array[] = [
                    'value' => $l->value,
                    'label' => $l->label
                ];
            }
        }
        return $array;
    }

    public function actionGetAnswersValues($answers_values){
        $array = array();
        if($answers_values != ''){
            $list = Lists::find()->where(['list_name'=>$answers_values])->all();
            foreach ($list as $l){
                $array[] = [
                    'value' => $l->value,
                    'label' => $l->label
                ];
            }
        }
        return json_encode($array);
    }

    protected function findModel($id)
    {
        if (($model = Projects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
