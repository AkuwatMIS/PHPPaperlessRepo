<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Blacklist;
use common\models\Loans;
use common\models\Members;
use common\models\Recoveries;
use yii\rest\ActiveController;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

/**
 * Site controller
 */
class BlackListController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                //'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],

                'languages' => [
                    'en',
                    'de',
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        //unset($actions['index']);
        return $actions;
    }

    public function actionReason()
    {
        $headers = Yii::$app->request->getBodyParams();
        $response = [];
        $cnic = $headers['cnic'];

        $data = Blacklist::find()->select(['reason' , 'reject_reason' , 'description'])->where(['cnic'=>$cnic])->one();

        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['meta']['message'] = "Borrower Data";
            $response['data']['reason'] = $data->reason;
            $response['data']['reject_reason'] = $data->reject_reason;
            $response['data']['description'] = $data->description;
        }
        else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['meta']['message'] = "CNIC Not Found in Black-List";
       }
        return JsonHelper::asJson($response);
    }
    public function actionLoans()
    {
        $headers = Yii::$app->request->getBodyParams();
        $response = [];
        $cnic = $headers['cnic'];

        $loans = Loans::find()
                        ->select(['loans.status','loan_amount' , 'disbursed_amount','sanction_no','loans.id'])
                        ->joinWith('application')
                        ->joinWith('application.member')
                        ->andWhere(['in','loans.status',['collected','loan completed']])
                        ->andWhere(['members.cnic'=>$cnic])->all();
        foreach ($loans as $key => $loan ){

            $id = $loan->id;
            $recoveries  = Yii::$app->db->createCommand('SELECT sum(amount) as amt FROM recoveries where loan_id = "'.$id.'" ')->queryAll();
            $data[$key]['status'] = $loan->status;
            $data[$key]['loan_amount'] = $loan->loan_amount;
            $data[$key]['disbursed_amount'] = $loan->disbursed_amount;
            $data[$key]['sanction_no'] = $loan->sanction_no;
            if($loan->status == 'collected') {
                $data[$key]['amount_pending'] = $loan->disbursed_amount - $recoveries[0]['amt'] ;
            }
        }

        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['meta']['message'] = "Borrower Loan Data";
            $response['data'] = $data;
        }
        else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['meta']['message'] = "Loan Not Found";
        }
        return JsonHelper::asJson($response);
    }

}
