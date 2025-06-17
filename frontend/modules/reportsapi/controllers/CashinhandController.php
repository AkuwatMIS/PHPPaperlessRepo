<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\models\CihTransactionsMapping;
use common\models\TransactionsCih;
use common\models\Users;
use yii\db\Exception;
use yii\rest\ActiveController;
use Yii;
use yii\web\Response;

/**
 * Site controller
 */
class CashinhandController extends ActiveController
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

    public function actionPostRecoverycashinhand()
    {
        $response = [];
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->one();
        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            if (isset($input['branch_id'])) {
                if (isset($input['deposit_slip_no'])) {
                    if (isset($input['amount'])) {
                        if (isset($input['account_id'])) {
                            if (isset($input['picture'])) {
                                if (isset($input['recovery_ids'])) {
                                    $model = new TransactionsCih();

                                    $transactionsCih['TransactionsCih'] = $input;
                                    $model->recovery_ids = $transactionsCih['TransactionsCih']['recovery_ids'];

                                    if ($model->load($transactionsCih)) {
                                        $model->created_by = $user->id;
                                        $model->type = 'Recovery';
                                        $model->set_values();
                                        $transaction = Yii::$app->db->beginTransaction();

                                        try  {
                                            $fail = false;
                                            if ($model->save()) {
                                                foreach($model->recovery_ids as $recovery_id){
                                                    $model_cih_transactions_mapping = new CihTransactionsMapping();

                                                    $model_cih_transactions_mapping->cih_type_id = $recovery_id;
                                                    $model_cih_transactions_mapping->transaction_id = $model->id;
                                                    $model_cih_transactions_mapping->type = 'recv';
                                                    if(!$model_cih_transactions_mapping->save()){
                                                        $fail = true;
                                                        $transaction->rollBack();
                                                        $error = '';
                                                        foreach ($model_cih_transactions_mapping->getErrors() as $m) {
                                                            $error = $m[0];
                                                        }
                                                        $response['meta']['success'] = false;
                                                        $response['meta']['message'] = $error;
                                                        $response['meta']['code'] = 500;
                                                        break;
                                                    }
                                                }
                                                if(!$fail){
                                                    $transaction->commit();
                                                    $response['meta']['success'] = true;
                                                    $response['meta']['code'] = 200;
                                                    $response['meta']['message'] = 'Recovery CIH has posted successfully';
                                                }
                                            } else {
                                                $transaction->rollBack();
                                                $error = '';
                                                foreach ($model->getErrors() as $m) {
                                                    $error = $m[0];
                                                }
                                                $response['meta']['success'] = false;
                                                $response['meta']['message'] = $error;
                                                $response['meta']['code'] = 500;
                                            }

                                        } catch (Exception $e) {
                                            $transaction->rollBack();
                                            $error = '';
                                            foreach ($model->getErrors() as $m) {
                                                $error = $m[0];
                                            }
                                            $response['meta']['success'] = false;
                                            $response['meta']['message'] = $error;
                                            $response['meta']['code'] = 500;
                                        }
                                    } else {
                                        $response['meta']['success'] = false;
                                        $response['meta']['message'] = 'Data not load successfully!';
                                        $response['meta']['code'] = 500;
                                        //throw new \yii\web\HttpException(500, Yii::t('app','Data not load successfully!'),500);
                                    }
                                } else {
                                    $response['meta']['success'] = false;
                                    $response['meta']['message'] = 'recoveries_ids is required!';
                                    $response['meta']['code'] = 500;
                                    //throw new \yii\web\HttpException(500, Yii::t('app','MDP is required!'),500);
                                }
                            } else {
                                $response['meta']['success'] = false;
                                $response['meta']['message'] = 'Bank deposit slip picture is required!';
                                $response['meta']['code'] = 500;
                                //throw new \yii\web\HttpException(500, Yii::t('app','MDP is required!'),500);
                            }
                        } else {
                            $response['meta']['success'] = false;
                            $response['meta']['message'] = 'Bank account is required!';
                            $response['meta']['code'] = 500;
                            //throw new \yii\web\HttpException(500, Yii::t('app','MDP is required!'),500);
                        }
                    } else {
                        $response['meta']['success'] = false;
                        $response['meta']['message'] = 'Deposit amount is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Recv Date is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'Bank deposit slip number is is required!';
                    $response['meta']['code'] = 500;
                    //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                }
            } else {
                $response['meta']['success'] = false;
                $response['meta']['message'] = 'Branch ID is required!';
                $response['meta']['code'] = 500;
                //throw new \yii\web\HttpException(500, Yii::t('app','Loan ID is required!'),500);
            }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
            //throw new \yii\web\HttpException(500, Yii::t('app','access token is required!'),500);
        }
        return $response;
    }
}

