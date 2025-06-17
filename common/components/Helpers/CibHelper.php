<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\CibTypes;
use common\models\ConnectionBanks;
use Yii;

class CibHelper
{

    public static function actionCib($applications, $type)
    {
        $cred_model = CibTypes::find()->where(['name' => $type])->one();
        $_url='url_'.$cred_model->environment;
        $url=$cred_model->$_url;
        $helper = '\common\components\Helpers\\' .'Cib'. $type.'Helper';
        $result = $helper::login($url, $cred_model);
        $login_token=isset($result['data'])?$result['data']:'';
        if ($result['success']) {
            foreach ($applications as $app) {
                if($type == 'Tasdeeq'){
                    $body = \common\components\Helpers\CibTasdeeqHelper::parseRequestBody($app);
                    $result = \common\components\Helpers\CibTasdeeqHelper::getReport($url, $body, $login_token);
                }else{
                    $body = $helper::parseRequetBody($app);
                    $result = $helper::getReport($url, $body, $login_token);
                }
                self::updateCibResponse($app, $result,$cred_model->id);
                echo $app->id;
                echo '</br>';
            }
        }else{
            return $result['data'];
        }
    }

    public static function actionCibDataCheck($applications, $type)
    {
        $helper = '\common\components\Helpers\\' .'Cib'. $type.'Helper';
        $body = $helper::parseRequestBody($applications);
        $result = $helper::getReport($type, $body);
        //$result = $helper::getReportV4($type, $body);
        return $result;
    }

    public static function updateCibResponse($app, $response, $cib_type)
    {
        $cib_model = ApplicationsCib::find()->where(['application_id' => $app->id])->one();
        if (!empty($cib_model)) {
            $cib_model->cib_type_id = $cib_type;
        }else{
            $cib_model=new ApplicationsCib();
            $cib_model->application_id = $app->id;
            $cib_model->cib_type_id = $cib_type;
        }
        $cib_model->response = json_encode($response['data']);
        if($response['success']){
            $cib_model->status = 1;
        }else{
            $cib_model->status = 3;
        }
        $cib_model->save(false);
        //print_r($cib_model->application_id);
    }


    public static function actionDataCheckPassword($action,$type)
    {
       $helper = '\common\components\Helpers\\' .'Cib'. $type.'Helper';
        if($action == 'reset'){
            $result = $helper::getChangePassword($type);
            return $result;
        }else{
            $result = $helper::getExpiryPassword($type);
            return $result;
        }
    }

}