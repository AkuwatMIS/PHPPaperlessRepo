<?php

namespace frontend\controllers;

use common\components\Helpers\ListHelper;
use common\components\Helpers\TemplateHelper;
use common\models\Events;
use common\models\Lists;
use kartik\mpdf\Pdf;
use Yii;
use common\models\Templates;
use common\models\search\TemplatesSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * TemplatesController implements the CRUD actions for Templates model.
 */
class NadraVerisysController extends Controller
{
    public $rbac_type = 'frontend';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Templates models.
     * @return mixed
     */

    public static function arrToStr($arr)
    {
        $result = '(';
        foreach ($arr as $c) {
            $result .= '"' . $c['cnic'] . '",';
        }
        $result = rtrim($result, ", ");
        $result .= ')';

        return $result;
    }

    public function actionCompleted()
    {
        if ($_POST) {
            if (!empty($_FILES['cnic'])) {
                $dispatchData = $this->csvToArray($_FILES['cnic']['tmp_name']);
                $connection = Yii::$app->getDb();
                $filename = "completed-members-nadra-verisys-data_" . date('Y-m-d') . ".csv";
                $f = fopen('php://memory', 'w');
                $delimiter = ",";
                $fields = array('application no','application date','created_at','upload_at', 'application status', 'nadra status', 'cnic', 'cnic issue date', 'cnic expiry date');
                fputcsv($f, $fields, $delimiter);
                $result = [];

                if (!empty($dispatchData)) {
                    $branchArray = self::arrToStr($dispatchData);
                    $command = $connection->createCommand("
                              SELECT
                                    applications.application_no,
                                    applications.application_date,
                                    applications.created_at,
                                    nadra_verisys.upload_at,
                                    applications.status app_status,
                                    nadra_verisys.status nadra_status,
                                    members.cnic,
                                    IF(member_info.cnic_issue_date IS NULL or member_info.cnic_issue_date = '', 0, member_info.cnic_issue_date) issue_date,
                                    IF(member_info.cnic_expiry_date IS NULL or member_info.cnic_expiry_date = '', 0, member_info.cnic_expiry_date) expiry_date
                                FROM
                                applications
                                INNER JOIN
                                    members
                                  ON
                                    members.id = applications.member_id
                                INNER JOIN
                                    member_info
                                ON
                                    member_info.member_id = members.id
                                INNER JOIN
                                
                                    nadra_verisys
                                ON
                                    nadra_verisys.application_id = applications.id
                                WHERE
                                    applications.application_date > 1643677199 AND members.cnic in $branchArray AND nadra_verisys.document_type = 'nadra_document' and applications.deleted='0'
                       ");
                    $result = $command->queryAll();
                }
                if(!empty($result)){
                    foreach ($result as $r){
                        if($r['nadra_status'] == 1){
                            $r['nadra_status'] = 'Completed';
                        }else{
                            $r['nadra_status'] = 'Pending';
                        }

                        if(isset($r['application_date']) && !empty($r['application_date'])){
                            $r['application_date'] = date('Y-m-d',$r['application_date']);
                        }
                        if(isset($r['created_at']) && !empty($r['created_at'])){
                            $r['created_at'] = date('Y-m-d',$r['created_at']);
                        }

                        if(isset($r['upload_at']) && !empty($r['upload_at'])){
                            $r['upload_at'] = date('Y-m-d',$r['upload_at']);
                        }
                        fputcsv($f, $r, $delimiter);
                    }
                }

                fseek($f, 0);
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '";');
                fpassthru($f);
                die();
            }
        }
        return $this->render('nadraVerysis');
    }


    public function actionPending()
    {
        if ($_POST) {
            if (!empty($_FILES['cnic'])) {
                $dispatchData = $this->csvToArray($_FILES['cnic']['tmp_name']);
                $connection = Yii::$app->getDb();
                $filename = "pending-members-nadra-verisys-data_" . date('Y-m-d') . ".csv";
                $f = fopen('php://memory', 'w');
                $delimiter = ",";
                $fields = array('application no','application date', 'application status', 'nadra status', 'cnic', 'cnic issue date', 'cnic expiry date');
                fputcsv($f, $fields, $delimiter);
                $result = [];

                if (!empty($dispatchData)) {
                    $branchArray = self::arrToStr($dispatchData);
                    $command = $connection->createCommand("
                                SELECT
                                    applications.application_no,
                                    applications.application_date,
                                     applications.status app_status,
                                    nadra_verisys.status nadra_status,
                                    members.cnic,
                                    IF(member_info.cnic_issue_date IS NULL or member_info.cnic_issue_date = '', 0, member_info.cnic_issue_date) issue_date,
                                    IF(member_info.cnic_expiry_date IS NULL or member_info.cnic_expiry_date = '', 0, member_info.cnic_expiry_date) expiry_date
                               FROM
                                applications
                                INNER JOIN
                                    members
                                  ON
                                    members.id = applications.member_id
                                INNER JOIN
                                    member_info
                                ON
                                    member_info.member_id = members.id
                                WHERE
                                    applications.application_date > 1643677199 AND nadra_verisys.document_type = 'nadra_document' AND members.cnic in $branchArray
                       ");
                $result = $command->queryAll();
                }

                if(!empty($result)){
                    foreach ($result as $r){
                        if($r['nadra_status'] == 1){
                            $r['nadra_status'] = 'Completed';
                        }else{
                            $r['nadra_status'] = 'Pending';
                        }

                        if(isset($r['application_date']) && !empty($r['application_date'])){
                            $r['application_date'] = date('Y-m-d',$r['application_date']);
                        }
                        fputcsv($f, $r, $delimiter);
                    }
                }

                fseek($f, 0);
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '";');
                fpassthru($f);
                die();
            }
        }
        return $this->render('nadraVerysisPending');
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = array();
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

}
