<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\FundRequestHelper;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;

use common\models\FundRequests;
use common\models\FundRequestsDetails;
use common\models\GroupActions;

use common\models\LoanActions;
use common\models\Loans;

use common\models\Operations;
use common\models\search\FundRequestsSearch;

use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class OperationsController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

//           'apiauth' => [
//               'class' => Apiauth::className(),
//               'exclude' => [],
//               'callback'=>[]
//           ],
//            'access' => [
//                'class' => AccessControl::className(),
//                'denyCallback' => function ($rule, $action) {
//                    JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
//                },
//                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
//            ],
//            'verbs' => [
//                'class' => Verbcheck::className(),
//                'actions' => [
//                    'posttakaful' => ['POST']
//                ],
//            ],

        ];
    }


    protected function findModel($id)
    {
        if (($model = Operations::findOne(['id' => $id,'deleted' =>0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}