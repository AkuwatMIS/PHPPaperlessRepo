<?php

namespace frontend\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\FireBaseHelper;
use common\components\Helpers\StructureHelper;
use common\components\Parsers\ApiParser;
use common\components\ViewFormHelper;
use common\models\Devices;
use common\models\Members;
use common\models\Notifications;
use common\models\NotificationsLogs;
use common\models\Operations;
use common\models\Post;
use common\models\Projects;
use common\models\UserDevices;
use Yii;
use common\models\Applications;
use common\models\search\ApplicationsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\db\Query;

/**
 * ApplicationsController implements the CRUD actions for Applications model.
 */
class NotificationsController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            /*'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],*/
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user_id = 2;
        $application_id = 600;
        $group_id = 500;
        $loan_id = 400;
        $project_id = 0;
        ActionsHelper::insertActions('application',$project_id,$application_id,$user_id);      //after application sync
        ActionsHelper::insertActions('appraisal',$project_id,$application_id,$user_id,1);      //after appraisal sync
        ActionsHelper::insertActions('verification',$project_id,$application_id,$user_id,1);   //after verification done
        ActionsHelper::insertActions('group',$project_id,$group_id,$user_id);            //after group create/update + project_id,group_id,created_by
        ActionsHelper::insertActions('lac',$project_id,$loan_id,$user_id);              //after lac approved + project_id,loan_id,created_by
        die('done');

    }

}
