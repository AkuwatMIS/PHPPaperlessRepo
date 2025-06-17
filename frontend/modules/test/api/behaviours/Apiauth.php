<?php
namespace frontend\modules\test\api\behaviours;
    /**
     * @link http://www.yiiframework.com/
     * @copyright Copyright (c) 2008 Yii Software LLC
     * @license http://www.yiiframework.com/license/
     */

//namespace yii\filters\auth;
use app\models\ApiKeys;
use common\components\Helpers\AnalyticsHelper;
use Yii;
use yii\filters\auth\AuthMethod;
//use common\models\HaikuApps;

/**
 * QueryParamAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Apiauth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    public $exclude = [];
    public $callback = [];


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $headers = Yii::$app->getRequest()->getHeaders();

        $accessToken=NULL;
        if(isset($_GET['access_token'])){
            $accessToken=$_GET['access_token'];
        }else {
            $accessToken = $headers->get('x-access_token');
        }

        if(empty($accessToken)){

            if(isset($_GET['access-token'])){
                $accessToken=$_GET['access-token'];
            }else {
                $accessToken = $headers->get('x-access-token');
            }
        }

       // $accessToken = $request->get($this->tokenParam);

             /*
              if(isset($_POST['access-token'])) {

                  $accessToken = $_POST['access-token'];
                  //echo $accessToken;
                  //exit;
              }
             */

        //echo $accessToken;
        //exit;



        /*
        if(isset($_SERVER['HTTP_X_ACCESS_TOKEN'])) {

            $accessToken=$_SERVER['HTTP_X_ACCESS_TOKEN'];
        }
        */
        //echo "AT:".$accessToken;
        //  exit;
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {

            Yii::$app->api->sendFailedResponse(400,'Invalid Access token');

            // $this->handleFailure($response);
        }


        return null;
    }

    public function beforeAction($action)
    {
        //echo "okk";
        //exit;
        $headers = Yii::$app->getRequest()->getHeaders();
        $api_key = $headers->get('x-api-key');
        $key = '';
        if(!isset($api_key)){
            Yii::$app->api->sendFailedResponse(400,'Api key is required');
        }else{
            $key = \common\models\ApiKeys::find()->where(['api_key'=>$api_key])->one();
        }

        if(!$key){
            Yii::$app->api->sendFailedResponse(400,'Api key not found');
        }

        $version_code = $headers->get('version_code');
        $version = '';
        if(!isset($version_code)){
            Yii::$app->api->sendFailedResponse(426,'Please download latest app from play store');
        }else{
            $version = \common\models\Versions::find()->where(['type' => 'app_version'])->andWhere(['<=','version_no',$version_code])->one();
        }

        if(!$version){
            Yii::$app->api->sendFailedResponse(426,'Please download latest app from play store');
        }

        if (in_array($action->id, $this->exclude)&&
            !isset($_GET['access-token']))
        {
            //Yii::$app->api->sendFailedResponse("error1");
           // Yii::$app->api->sendSuccessResponse(["nice1"]);
           // exit;
            return true;
        }

        //if (!$this->verifyApp())
        //    Yii::$app->api->sendFailedResponse('Invalid Request(App not verified)');


        if (in_array($action->id, $this->callback)&&
            !isset($_GET['access-token']))
        {
            //Yii::$app->api->sendFailedResponse("error1");
            // Yii::$app->api->sendSuccessResponse(["nice1"]);
            // exit;
            return true;
        }



        $response = $this->response ?: Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ?: Yii::$app->getUser(),
            $this->request ?: Yii::$app->getRequest(),
            $response
        );

        if ($identity !== null) {
            $analytics['user_id'] = $identity->getId();
            $analytics['api'] = $action->controller->id.'/'.$action->id;
            $analytics['type'] = 'paperless';
            AnalyticsHelper::create($analytics);
            return true;
        } else {
            $this->challenge($response);
            $this->handleFailure($response);

            Yii::$app->api->sendFailedResponse(400,'Invalid Request');
            //return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        Yii::$app->api->sendFailedResponse(400,'Invalid Access token');
        //throw new UnauthorizedHttpException('You are requesting with an invalid credential.');
    }

}
