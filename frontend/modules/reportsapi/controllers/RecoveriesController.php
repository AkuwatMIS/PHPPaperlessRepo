<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Parsers\ReportsParser\ApiParser;
use common\models\Recoveries;
use yii\rest\ActiveController;
use Yii;
use common\models\Users;
use yii\web\Response;

/**
 * Site controller
 */
class RecoveriesController extends ActiveController
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

    public function actionPostRecovery(){
        $response = [];
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $post_token = $headers->get('post_token');
        $version_code   = $headers->get('version_code');

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        if (!empty($access_token)) {
            if (!empty($post_token)) {
                $user = Users::find()->where(['last_login_token' => $access_token,'post_token' => $post_token,'id'=>$input['user_id']])->one();
                if ($user) {

                    $analytics['user_id'] = $user->id;
                    $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                    $analytics['type'] = 'reports';
                    AnalyticsHelper::create($analytics);

                    if(isset($input['loan_id'])){
                        if(isset($input['credit'])){
                            if(isset($input['recv_date'])){
                                //if(isset($input['receipt_no'])){
                                    if(isset($input['mdp'])){
                                        $model = new Recoveries();
                                        $recv_date = $input['recv_date'];
                                        $input['recv_date'] = date('Y-m-d',strtotime($input['recv_date']));
                                        $recoveries['Recoveries'] = $input;
                                        $sanction_no = explode('-',$recoveries['Recoveries']['sanction_no']);
                                        $recoveries['Recoveries']['receipt_no'] = $sanction_no[0].'-'.round(microtime(true));
                                        $model->load($recoveries);

                                        /*print_r($model);
                                        die();*/

                                        if($model->load($recoveries)){
                                            $model->amount= $input['credit'];
                                            $model->receive_date= strtotime( $input['recv_date']);
                                            if($model->save()){
                                                $response['meta']['success'] = true;
                                                $response['meta']['code'] = 200;
                                                $response['meta']['message'] = 'Recovery has posted successfully';
                                                $response['meta']['receipt_no'] = $model->receipt_no;
                                                $response['meta']['receipt'] = ApiParser::parseReceipt($model);
                                                $response['meta']['footer'] = isset($user->fullname) ? 'Received By: '.$user->fullname.', '.date('d-M-Y h:i A',strtotime($recv_date)) : '';
                                                $branch_name = isset($model->branch->name) ? $model->branch->name : '';
                                                $district_name = isset($model->branch->district->name) ? $model->branch->district->name : '';
                                                $response['meta']['sub_footer'] = $branch_name.' - '.$district_name;
                                                $response['meta']['note'] = ' تمام انسان آزاد اور حقوق و عزت کے اعتبار سے برابر پیدا ہوئے ہیں۔ انہیں ضمیر اور عقل ودیعت ہوئی ہے۔ اس لئے انہیں ایک دوسرے کے ساتھ بھائی چارے کا سلوک کرنا چاہیئے۔';
                                            }
                                            else{
                                                $error = '';
                                                foreach ($model->getErrors() as $m){
                                                    $error = $m[0];
                                                }
                                                $response['meta']['success'] = false;
                                                $response['meta']['message'] = $error;
                                                $response['meta']['code'] = 500;
                                                //throw new \yii\web\HttpException(500, Yii::t('app',$error),500);
                                            }
                                        }else{
                                            $response['meta']['success'] = false;
                                            $response['meta']['message'] = 'Data not load successfully!';
                                            $response['meta']['code'] = 500;
                                            //throw new \yii\web\HttpException(500, Yii::t('app','Data not load successfully!'),500);
                                        }

                                    } else{
                                        $response['meta']['success'] = false;
                                        $response['meta']['message'] = 'MDP is required!';
                                        $response['meta']['code'] = 500;
                                        //throw new \yii\web\HttpException(500, Yii::t('app','MDP is required!'),500);
                                    }
                                /*} else{
                                    $response['meta']['success'] = false;
                                    $response['meta']['message'] = 'Receipt no. is required!';
                                    $response['meta']['code'] = 500;
                                    //throw new \yii\web\HttpException(500, Yii::t('app','Receipt no. is required!'),500);
                                }*/
                            } else{
                                $response['meta']['success'] = false;
                                $response['meta']['message'] = 'Recv Date is required!';
                                $response['meta']['code'] = 500;
                                //throw new \yii\web\HttpException(500, Yii::t('app','Recv Date is required!'),500);
                            }
                        }
                        else{
                            $response['meta']['success'] = false;
                            $response['meta']['message'] = 'Amount is required!';
                            $response['meta']['code'] = 500;
                            //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                        }
                    }
                    else{
                        $response['meta']['success'] = false;
                        $response['meta']['message'] = 'Loan ID is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Loan ID is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'Invalid access token or post token!';
                    $response['meta']['code'] = 401;
                    //throw new \yii\web\HttpException(401, Yii::t('app','Invalid access token or post token!'),401);
                }
            } else {
                $response['meta']['success'] = false;
                $response['meta']['message'] = 'post token is required!';
                $response['meta']['code'] = 500;
                //throw new \yii\web\HttpException(500, Yii::t('app','post token is required!'),500);
            }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['message'] = 'access token is required!';
            $response['meta']['code'] = 500;
            //throw new \yii\web\HttpException(500, Yii::t('app','access token is required!'),500);
        }
        return $response;
    }


    public function actionGetRecoveriescih()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $key    = !empty($input['type'])?($input['type']):'';
        $value     = !empty($input['value'])?($input['value']):'';

        $user = Users::find()->where(['last_login_token' => $access_token])->one();
        $borrowers = array();
        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $recoveries = Recoveries::find()->limit(10)->all();
            if(!empty($recoveries)){

                $response['meta']['success']    = true;
                $response['meta']['code']       = 200;
                $response['data']['message']    = "Get Recoveries Detail";
                $response['data']['details']  = ApiParser::parseRecoveriescih($recoveries);

            }else{
                $response['meta']['success']    = false;
                $response['meta']['code']       = 404;
                $response['data']['message']    = "No Active Loan Found";

            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}
