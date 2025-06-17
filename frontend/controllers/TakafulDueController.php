<?php
namespace frontend\controllers;
use common\models\search\TakafulDueSearch;
use Yii;
use common\components\Helpers\ExportHelper;
use common\models\search\TakafulPaidSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\components\Helpers\StructureHelper;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class TakafulDueController extends Controller
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
    public function actionIndex(){
        $params = Yii::$app->request->queryParams;
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);


        if (isset($_GET['export']) && $_GET['export'] == 'export') {

            $connection = Yii::$app->getDb();
            $this->layout = 'csv';
            $headers = array('Region', 'Area', 'Branch', 'Name', 'Parentage', 'Cnic', 'Sanction No', 'Address', 'Mobile No', 'Due date', 'Disbursement Date', 'OLP', 'Takaful Amount','Over Due Amount');
            $searchModel = new TakafulDueSearch();
            $query = $searchModel->search($_GET, true);
            Yii::$app->Permission->getSearchFilterQuery($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            $data = $query->all();

            $groups = array();
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['region'] = isset($g['region']['name']) ? $g['region']['name'] : '';;
               $groups[$i]['area_id'] = isset($g['area']['name']) ? $g['area']['name'] : '';
               $groups[$i]['branch_id'] = isset($g['branch']['name']) ? $g['branch']['name'] : '';
                $groups[$i]['full_name'] = isset($g['loan']['application']['member']['full_name']) ? $g['loan']['application']['member']['full_name'] : '';
                $groups[$i]['parentage'] =isset($g['loan']['application']['member']['parentage']) ? $g['loan']['application']['member']['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['loan']['application']['member']['cnic']) ? $g['loan']['application']['member']['cnic'] : '';
                $groups[$i]['sanction_no'] = isset($g['loan']['sanction_no']) ?$g['loan']['sanction_no'] : '';
                $groups[$i]['address'] = isset($g['loan']['application']['member']['membersAddresses'][0]['address']) ? $g['loan']['application']['member']['membersAddresses'][0]['address'] : '';
                $groups[$i]['phone'] = isset($g['loan']['application']['member']['membersPhones'][0]['phone']) ? $g['loan']['application']['member']['membersPhones'][0]['phone'] : '';
                $groups[$i]['due_date'] = isset($g['overdue_date']) ?date('Y-m-d', $g['overdue_date']) : '';
                $groups[$i]['date_disbursed'] = isset($g['disb_date']) ?date('Y-m-d', $g['disb_date']) : '';
                $groups[$i]['olp'] = isset($g['olp']) ?$g['olp'] : '';
                $groups[$i]['takaful'] = isset($g['takaful_amnt']) ?$g['takaful_amnt'] : '';
               $groups[$i]['over_due'] = isset($g['overdue_amnt']) ?$g['overdue_amnt'] : '';
                $i++;

            }
            ExportHelper::ExportCSV('TakafulDueList-Report-' , $headers, $groups);
            die();
        }

        $searchModel = new TakafulDueSearch();
        if(Yii::$app->request->get()){
            $dataProvider=$searchModel->search(Yii::$app->request->get());

        } else {
            $dataProvider = [];
        }

        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches' => $branches,
            'regions' => $regions
        ]);

    }
}
