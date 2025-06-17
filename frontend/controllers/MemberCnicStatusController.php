<?php
namespace frontend\controllers;
use common\models\MemberInfo;
use common\models\Members;
use common\models\RejectedNadraVerisys;
use common\models\search\MemberCnicStatusSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use Yii;

class MemberCnicStatusController extends Controller{

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
                                        if(Yii::$app->user->isGuest){
                                                return Yii::$app->response->redirect(['site/login']);
                                        }else {
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
     * Lists all Members models.
     * @return mixed
     */
        public function actionIndex(){
                $searchModel= new MemberCnicStatusSearch();
                if(empty(Yii::$app->request->queryParams)){
                        $dataProvider=[];
                }else {
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                        Yii::$app->Permission->getSearchFilter($dataProvider,Yii::$app->controller->id,Yii::$app->controller->action->id,$this->rbac_type);
                }
                $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type);
                return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'regions' => $regions,
                ]);
        }
        }

