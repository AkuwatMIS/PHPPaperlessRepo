<?php
namespace console\controllers;

use common\models\Actions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Branches;
use common\models\Donations;
use common\models\ProgressReports;
use common\models\Users;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class ActionsController extends Controller
{


    public function actionDonation()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_name = 'donations.csv';
        $file_path = Yii::getAlias('@anyname').'/frontend/web'.'/uploads/'.$file_name;
        $myfile = fopen($file_path, "r");


        while (($fileop = fgetcsv($myfile)) !== false) {

            $donation = Donations::find()->where(['receipt_no' => $fileop[0], 'receive_date' => 1561939200, 'deleted' => 0])->one();
            if (isset($donation)) {
                $donation->receive_date = $fileop[1];
                $donation->created_by = 1;
                if (!($donation->save(false))) {
                    print_r($donation->getErrors());

                    print_r($fileop[0]);
                }
            } else {
                print_r($fileop[0]);
            }
        }
    }
    public function actionPermission()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        //echo Yii::getAlias('@anyname');
        //Yii::$app->db->createCommand()->truncateTable('actions')->execute();
        Actions::deleteAll(['module_type' => 'frontend']);
        $controllerlist = [];
        if ($handle = opendir(Yii::getAlias('@anyname').'/frontend/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);
        $fulllist = [];
        foreach ($controllerlist as $controller):
            $con = str_replace('Controller', '', substr($controller, 0, -4));
            $handle = fopen(Yii::getAlias('@anyname').'/frontend/controllers/' . $controller, "r");
            $array = [];
            $act = null;
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)):
                        if (strlen($display[1]) > 2):
                            $model = new Actions();
                            $auth_item = new AuthItem(null);
                            $model->module = strtolower($con);
                            $array[] = preg_split('/(?=[A-Z])/', $display[1]);
                            foreach ($array as $arr) {
                                $act = implode('-', $arr);
                            }
                            $model->action = trim(strtolower($act), '-');
                            $auth_item->name = 'frontend_'.$model->action.$model->module;
                            $auth_item->type = 2;
                            $auth = Yii::$app->authManager;
                            if(!($auth->getPermission($auth_item->name)))
                            {
                                $createPermission = $auth->createPermission($auth_item->name);
                                $createPermission->description = $auth_item->name;
                                $auth->add($createPermission);
                            }

                            $fulllist[strtolower($con)][] = strtolower($display[1]);
                            $model->module_type = 'frontend';
                            $model->save();

                        endif;
                    endif;
                }
            }
            fclose($handle);
        endforeach;
    }

    public function actionPermissionApi()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        //echo Yii::getAlias('@anyname');
        //Yii::$app->db->createCommand()->truncateTable('actions')->execute();
        Actions::deleteAll(['module_type' => 'api']);
        $controllerlist = [];
        if ($handle = opendir(Yii::getAlias('@anyname').'/frontend/modules/api/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);
        $fulllist = [];

        foreach ($controllerlist as $controller):
            $con = str_replace('Controller', '', substr($controller, 0, -4));
            $handle = fopen(Yii::getAlias('@anyname').'/frontend/modules/api/controllers/' . $controller, "r");
            $array = [];
            $act = null;
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)):
                        if (strlen($display[1]) > 2):
                            $model = new Actions();
                            $auth_item = new AuthItem(null);
                            $model->module = strtolower($con);
                            $array[] = preg_split('/(?=[A-Z])/', $display[1]);
                            foreach ($array as $arr) {
                                $act = implode('-', $arr);
                            }
                            $model->action = trim(strtolower($act), '-');
                            $auth_item->name = 'api_'.$model->action.$model->module;
                            $auth_item->type = 2;
                            $auth = Yii::$app->authManager;
                            if(!($auth->getPermission($auth_item->name)))
                            {
                                $createPermission = $auth->createPermission($auth_item->name);
                                $createPermission->description = $auth_item->name;
                                $auth->add($createPermission);
                            }

                            $fulllist[strtolower($con)][] = strtolower($display[1]);
                            $model->module_type = 'api';
                            $model->save();

                        endif;
                    endif;
                }
            }
            fclose($handle);
        endforeach;
    }

    public function actionApplication()
    {
        $application_no = (string)rand(99, 99999999);
        print_r('old : '.$application_no);
        $c = 0;
        while(true){
            $application = Applications::find()->where(['application_no'=> $application_no])->one();

            if($application){
                $c++;
                $application_no = (string)rand(99, 99999999);
            }else{
                break;
            }
        }
        print_r(' new: '.$application_no);
        print_r(' count: '.$c);
        die();
    }

}