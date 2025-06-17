<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\JsonHelper;
use common\models\EmergencyLoans;
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
class DonationController extends ActiveController
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

    public function actionSyncdata()
    {
        $donations = json_decode(file_get_contents('php://input'), true);
        $headers = Yii::$app->request->headers;
        $response = [];
        $data = [];
        foreach ($donations as $donation)
        {
            $flag = false;
            if($donation['city_id'] > 0)
            {
                $emergency_loans = EmergencyLoans::find()->where(['city_id' => $donation['city_id'],'status' => 0])->orderBy('created_at')->limit($donation['no_of_families'])->all();
                if(isset($emergency_loans) && !empty($emergency_loans)) {
                    foreach ($emergency_loans as $loan) {
                        $flag = true;
                        $loan->donor_id = $donation['user_id'];
                        $loan->donated_date = $donation['donated_date'];
                        $loan->status = 1;
                        if (!$loan->save()) {
                            $flag = false;
                        }
                    }
                }
            } else {
                $emergency_loans = EmergencyLoans::find()->where(['status' => 0])->orderBy('created_at')->limit($donation['no_of_families'])->all();
                foreach ($emergency_loans as $loan)
                {
                    $flag = true;
                    $loan->donor_id = $donation['user_id'];
                    $loan->donated_date = $donation['date'];
                    $loan->status = 1;
                    if(!$loan->save())
                    {
                        $flag = false;
                    }
                }

            }
            if($flag)
            {
                array_push($data,$donation['id']);
            }
        }

       if(isset($donations)){

            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['response'] = $data;
        }
        else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['data']['message'] = "invalid access token or already logout";
       }

       return $response;
    }
}
