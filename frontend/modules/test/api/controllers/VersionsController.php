<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\search\VerificationSearch;
use common\models\SocialAppraisal;
use common\models\Verification;
use common\models\Versions;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;
use Yii;


class VersionsController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'configuration' => ['GET'],
                    ],
                ],
            ];
    }

    public function actionConfiguration()
    {
        $versions = Versions::find()->all();
        if(isset($versions)) {
            $response = ApiParser::parseVersions($versions);
            return $this->sendSuccessResponse(200, $response);
        } else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }
}