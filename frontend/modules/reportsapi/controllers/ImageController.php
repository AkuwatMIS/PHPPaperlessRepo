<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Parsers\ReportsParser\ApiParser;
use common\components\Helpers\ImageHelper;
use common\models\Recoveries;
use yii\rest\ActiveController;
use Yii;
use common\models\Users;
use yii\web\Response;

/**
 * Site controller
 */
class ImageController extends ActiveController
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

    public function actionUpload(){
        $response = [];
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');

       /* if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }*/
        if (!empty($access_token)) {
            $user = Users::find()->where(['last_login_token' => $access_token])->one();
                if ($user) {
                    $analytics['user_id'] = $user->id;
                    $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                    $analytics['type'] = 'reports';
                    AnalyticsHelper::create($analytics);
                    if(isset($input['parent_id'])){
                        if(isset($input['parent_type'])){
                            if(isset($input['image_name'])){
                                    if(isset($input['image_code'])){
                                        if(ImageHelper::imageUpload($input['parent_id'],$input['parent_type'],'reports',$input['image_name'],$input['image_code']))
                                        {
                                            $response['meta']['success'] = true;
                                            $response['meta']['code'] = 200;
                                            $response['meta']['message'] = 'Image save successfully';
                                        }
                                        else{
                                            $response['meta']['success'] = false;
                                            $response['meta']['message'] = 'Image not save';
                                            $response['meta']['code'] = 500;
                                        }
                                    } else{
                                        $response['meta']['success'] = false;
                                        $response['meta']['message'] = 'Image Base Code is required!';
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
                                $response['meta']['message'] = 'Image Name is required!';
                                $response['meta']['code'] = 500;
                                //throw new \yii\web\HttpException(500, Yii::t('app','Recv Date is required!'),500);
                            }
                        }
                        else{
                            $response['meta']['success'] = false;
                            $response['meta']['message'] = 'Parent Type is required!';
                            $response['meta']['code'] = 500;
                            //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                        }
                    }
                    else{
                        $response['meta']['success'] = false;
                        $response['meta']['message'] = 'Parent ID is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Loan ID is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'Invalid access token!';
                    $response['meta']['code'] = 401;
                    //throw new \yii\web\HttpException(401, Yii::t('app','Invalid access token or post token!'),401);
                }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['message'] = 'access token is required!';
            $response['meta']['code'] = 500;
            //throw new \yii\web\HttpException(500, Yii::t('app','access token is required!'),500);
        }
        return $response;
    }




}
