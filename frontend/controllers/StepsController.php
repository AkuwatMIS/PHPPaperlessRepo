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
use common\models\search\ApplicationActionsSearch;
use common\models\search\StepsSearch;
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

class StepsController extends Controller
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
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        $searchModel = new StepsSearch();

        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = ['Name', 'CNIC',  'Region', 'Area', 'Branch','Application Date','Application Create Date','Project', 'CIB Verification','Nadra','PMT','Account Verification'];
            $groups = array();

            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
            foreach ($data as $g) {
              // echo '<pre>';
             // print_r($g);
              //die();

                $groups[$i]['full_name'] = isset($g['member']['full_name']) ? $g['member']['full_name'] : '';
                $groups[$i]['cnic'] = isset($g['member']['cnic']) ? $g['member']['cnic'] : '';
                $groups[$i]['region_id'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['application_date'] = isset($g['application_date'])?\common\components\Helpers\StringHelper::dateFormatter($g['application_date']):'';
                $groups[$i]['created_at'] =  isset($g['created_at'])?\common\components\Helpers\StringHelper::dateFormatter($g['created_at']):'';
                $groups[$i]['project_id'] = isset($g['project']['name']) ? $g['project']['name'] : '';;
                $groups[$i]['cib_verification'] = !empty($g['cib']['status']) ? 'Yes' : 'No';
                $groups[$i]['nadra']=!empty($g['member']['nadraDoc'])? 'Yes' : 'No';
                if (!in_array($g['project_id'], \common\components\Helpers\StructureHelper::kamyaabPakitanProjects())) {
                    $groups[$i]['pmtStatus']='N/R';
                }else{
                $groups[$i]['pmtStatus']=!empty($g['pmtStatus']['status'])? 'Yes' : 'No';
                }
                if (!in_array($g['project_id'],\common\components\Helpers\StructureHelper::accountVerifyProjects())) {
                    $groups[$i]['accountVerification']='N/R';
                }elseif(isset($g['loan']['accountVerification'])==null){
                    $groups[$i]['accountVerification']='No';}
                else{
                $groups[$i]['accountVerification']=!empty($g['loan']['accountVerification']['status'])? 'Yes' : 'No';
                }
                $i++;
            }
            ExportHelper::ExportCSV('Applications', $headers, $groups);
            die();
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

        if (empty(Yii::$app->request->queryParams)) {
            $dataProvider = [];
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,


            'projects' => $projects,
        ]);
    }


}