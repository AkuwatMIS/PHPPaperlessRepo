<?php
namespace frontend\controllers;
use Yii;
use common\components\Helpers\ExportHelper;
use common\models\search\TakafulPaidSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\components\Helpers\StructureHelper;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class TakafulPaidController extends Controller{
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
    public function actionIndex(){
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        $searchModel = new TakafulPaidSearch();
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = ['Name', 'Parentage','CNIC',  'Region', 'Area', 'Branch','Project','Sanction Number','Takaful Receipt No','Takaful Receive Date','Loan Amount','Takaful Amount'];
            $groups = array();

            $query = $searchModel->search($_GET, true);
            //Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();
            $i = 0;
          // echo '<pre>';
          //  print_r($data);
          //  die();
            foreach ($data as $g) {
               //  echo '<pre>';
              //  print_r($g['application']['member']['full_name']);
              // die();

                $groups[$i]['full_name'] = isset($g['application']['member']['full_name']) ? $g['application']['member']['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['application']['member']['parentage']) ? $g['application']['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['application']['member']['cnic']) ? $g['application']['member']['cnic'] : '';
                $groups[$i]['region_id'] = isset($g['region']['name']) ? $g['region']['name'] : '';
                $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
                $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['project_id'] = isset($g['project']['name']) ? $g['project']['name'] : '';
                $groups[$i]['sanction_no'] = isset($g['loan']['sanction_no']) ? $g['loan']['sanction_no'] : '';
                $groups[$i]['takaful_recepit'] = isset($g ['receipt_no']) ? $g['receipt_no'] : '';
                $groups[$i]['takaful_receive_date'] = isset($g['receive_date'])?\common\components\Helpers\StringHelper::dateFormatter($g['receive_date']):'';;
                $groups[$i]['loan_amount'] = isset($g['loan']['loan_amount']) ? $g['loan']['loan_amount'] : '';
                $groups[$i]['takaful_amount'] = isset($g['credit']) ? $g['credit'] : '';



                $i++;
            }
            ExportHelper::ExportCSV('TakafulPaid', $headers, $groups);
            die();
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $projects = ArrayHelper::map(StructureHelper::getProjects(), 'id', 'name');

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