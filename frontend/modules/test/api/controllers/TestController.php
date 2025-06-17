<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\ProgressReports;
use common\models\reports\Overduelist;
use common\models\search\VerificationSearch;
use common\models\SocialAppraisal;
use common\models\Verification;
use common\models\Versions;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;
use Yii;


class TestController extends RestController
{
    /*public $rbac_type = 'api';
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
    }*/

    public function actionGraph($type)
    {
        $data = [];

        $data[] = ['x'=>'20-12-2018','y'=>'200'];
        $data[] = ['x'=>'21-12-2018','y'=>'1000'];
        $data[] = ['x'=>'22-12-2018','y'=>'200'];
        $data[] = ['x'=>'23-12-2018','y'=>'100'];
        $data[] = ['x'=>'24-12-2018','y'=>'700'];
        $data[] = ['x'=>'25-12-2018','y'=>'1200'];
        $response['range']=['max' => '1200', 'min' => '100'];
        $response['legends']=['x' => 'Date', 'y' => '100'];
        $response['coordinates']=$data;
        return $this->sendSuccessResponse(200, $response);

    }

    public function actionOverdue($key, $value)
    {
        $graph_data = [];
        $max_amount = 0;
        /*$date2 = strtotime(date('Y-m-01'));
        $date1 = strtotime(date('Y-m-t', ($date2)));*/
        $query = ProgressReports::find()->select('report_date,branches.name as branch_name,branch_id,period,overdue_amount')->where(['period' => 'monthly'])->joinWith('progressReportDetails')->joinWith('progressReportDetails.branch');
        if($key == 'branch_id') {
            $query->andWhere(['branch_id' => $value]);
        } else if ($key == 'date')
        {
            $query->andWhere(['between', 'report_date', strtotime($value), strtotime(date('Y-m-d 23:59:59',strtotime($value)))]);
        }
        $overdue_data = $query->asArray()->all();
        foreach ($overdue_data as $data)
        {

            if($data['overdue_amount'] > $max_amount)
            {
                $max_amount = $data['overdue_amount'];
            }
            if($key == 'branch_id') {
                $graph_data[] = ['x' => date('Y-m-d', $data['report_date']), 'y' => $data['overdue_amount']];
            }
            else if ($key == 'date')
            {
                $graph_data[] = ['x' => $data['branch_name'], 'y' => $data['overdue_amount']];
            }
        }
        $response['maximum']=$max_amount;
        $response['coordinates']=$graph_data;
        return $this->sendSuccessResponse(200, $response);

    }
}