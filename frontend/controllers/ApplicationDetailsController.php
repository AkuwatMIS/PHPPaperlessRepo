<?php

namespace frontend\controllers;


use common\components\DBSchemaHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\DBHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\CibHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\GeneralHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationActions;
use common\models\ApplicationDetails;
use common\models\ApplicationsCib;
use common\models\Appraisals;
use common\models\AuthItemChild;
use common\models\Branches;
use common\models\Cities;
use common\models\Documents;
use common\models\FilesApplication;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\MembersAccount;
use common\models\Operations;
use common\models\ProjectAppraisalsMapping;
use common\models\Projects;
use common\models\Provinces;
use common\models\Regions;
use common\models\search\ApplicationDetailsSearch;
use common\models\User;
use common\models\Users;
use common\models\Visits;
use Imagine\Image\Box;
use Imagine\Image\Point;
use kartik\mpdf\Pdf;
use phpDocumentor\Reflection\Types\Array_;
use Yii;
use common\models\Applications;
use common\models\search\ApplicationsSearch;
use yii\data\SqlDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * ApplicationsController implements the CRUD actions for Applications model.
 */
class ApplicationDetailsController extends Controller
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
     * Lists all ApplicationDetails models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new ApplicationDetailsSearch();
        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $params = Yii::$app->request->queryParams;


            $dataProvider = $searchModel->search($params);

            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);


    }

    public function actionExportPmtIndex()
    {
        $model = new ApplicationDetails();
        $request = Yii::$app->request;
        if ($model->load($request->post())) {


            $status = ($request->post()['ApplicationDetails']['status']);
            $connection = Yii::$app->getDb();
            $filename = "pmt-application_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');
            $delimiter = ",";
            if ($status == 0) {
                $fields = array('Application No', 'Region', 'Area', 'Branch', 'Name', 'Parenatge', 'Cnic','Project', 'Poverty Level', 'Pmt Lock Date');
                fputcsv($f, $fields, $delimiter);
                $result = [];
                $sql = "SELECT
                    `applications`.`id` application_id,  
                    `regions`.`name` AS `region`,
                    `areas`.`name` AS `area`,
                    `branches`.`name` AS `branch`,
                    `members`.`full_name` AS `name`,
                    `members`.`parentage` AS `parentage`,
                    `members`.`cnic` AS `cnic` ,
                    `projects`.`name` AS `project`
                FROM
                    application_details
                INNER JOIN
                    applications
                ON
                    applications.id = application_details.application_id AND applications.project_id IN(105, 106, 132) AND applications.status != 'rejected'
               INNER JOIN
                    projects
                ON
                    projects.id = applications.project_id
                INNER JOIN
                    members
                ON
                    members.id = applications.member_id
                INNER JOIN
                    branches
                ON
                    branches.id = applications.branch_id
                INNER JOIN
                    areas
                ON
                    areas.id = branches.area_id
                INNER JOIN
                    regions
                ON
                    regions.id = branches.region_id
                WHERE
                    application_details.status = 0
                    AND application_details.deleted = 0
                    AND applications.deleted = 0
                    AND application_details.parent_type = 'member'";

                $command = $connection->createCommand($sql);
                $result = $command->queryAll();

                if (!empty($result)) {
                    foreach ($result as $r) {
                        fputcsv($f, $r, $delimiter);
                    }
                }
            } elseif ($status == 1) {
                $ab = (explode("-", $request->post()['ApplicationDetails']['created_at']));
                $start_date = (strtotime($ab[0]));
                $end_date = (strtotime($ab[1]));
                $fields = array('Application No', 'Region', 'Area', 'Branch', 'Name', 'Parentage', 'CNIC', 'Project','Poverty Level', 'Pmt Lock Date');
                fputcsv($f, $fields, $delimiter);
                $result = [];
                $sql = "SELECT
                `applications`.`id` application_id,
                `regions`.`name` AS `region`,
                `areas`.`name` AS `area`,
                `branches`.`name` AS `branch`,
                `members`.`full_name` AS `name`,
                `members`.`parentage` AS `parentage`,
                `members`.`cnic` AS `cnic`,
                `projects`.`name` AS `project`,
                application_details.poverty_score,
       FROM_UNIXTIME(application_details.action_date,'%Y %D %M ')
            FROM
                application_details
            INNER JOIN
                applications
            ON
                applications.id = application_details.application_id AND applications.project_id IN(105, 106,132) AND applications.status != 'rejected'
            INNER JOIN
                 projects
            ON
                 projects.id = applications.project_id
            INNER JOIN
                members
            ON
                members.id = application_details.parent_id
            INNER JOIN
                branches
            ON
                branches.id = applications.branch_id
            INNER JOIN
                areas
            ON
                areas.id = branches.area_id
            INNER JOIN
                regions
            ON
                regions.id = branches.region_id
            WHERE
                application_details.status = 1
                and application_details.created_at >= $start_date and application_details.created_at <= $end_date
                AND application_details.deleted = 0
                AND applications.deleted = 0
                AND application_details.parent_type = 'member'";
                $command = $connection->createCommand($sql);
                $result = $command->queryAll();

                if (!empty($result)) {
                    foreach ($result as $r) {
                        fputcsv($f, $r, $delimiter);
                    }
                }

            }

            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
            die();
        }
        return $this->render('export-index', ['model' => $model]);
    }

    public function actionImportPmtIndex()
    {
        $model = new ApplicationDetails();
        $request = Yii::$app->request;
        if ($_POST) {
            if (!empty($_FILES['cnic_file'])) {
                $dispatchData = GeneralHelper::csvToArray($_FILES['cnic_file']['tmp_name']);
                foreach ($dispatchData as $d) {
                    $application_model = Applications::find()->select(['id', 'member_id'])->where(['id' => $d['Application No']])->one();
                    if (!empty($application_model) && $application_model != null) {
                        $extModel = ApplicationDetails::find()
                            ->where(['application_id' => $application_model->id])
                            ->andWhere(['parent_type' => 'member'])
                            ->andWhere(['parent_id' => $application_model->member_id])
                            ->one();
                        if (!empty($extModel) && $extModel != null) {
                            $extModel->poverty_score = $d['Poverty Level'];
                            $extModel->action_date = strtotime($d['Pmt Lock Date']);
                            $extModel->status = 1;
                            if ($extModel->save()) {
                                Yii::$app->session->setFlash('success', "Data have been  updated successfully.");
                            } else {
                                var_dump($model->getErrors());
                                die();
                                Yii::$app->session->setFlash('error', "Data not saved.");
                            }
                        } elseif ($application_model->status != 'rejected') {
                            $model = new ApplicationDetails();
                            $model->poverty_score = $d['Poverty Level'];
                            $model->application_id = $application_model->id;
                            $model->parent_type = 'member';
                            $model->parent_id = $application_model->member_id;
                            $model->status = 1;
                            $model->action_date = strtotime($d['Pmt Lock Date']);
                            if ($model->save()) {
                                Yii::$app->session->setFlash('success', "Data have been  uploaded successfully.");
                            } else {
                                var_dump($model->getErrors());
                                die();
                                Yii::$app->session->setFlash('error', "Data not saved.");
                            }
                        }

                    }


                }


            }

        }
        return $this->render('import-index', ['model' => $model]);
    }

}


