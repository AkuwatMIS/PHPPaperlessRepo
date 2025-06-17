<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;


use common\components\Helpers\FixesHelper;
use common\components\Helpers\MemberHelper;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\FilesApplication;
use common\models\LedgerRegenerateLogs;
use common\models\Loans;
use common\models\Members;
use common\models\MembersPhone;
use common\models\ProjectsDisabled;
use yii\console\Controller;
use Yii;

class FilesApplicationController extends Controller
{
    public function actionAddFiles()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/LCHS_CNIC.csv';
        $myfile = fopen($file_path, "r");
        $flag=false;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if($flag==true) {
                $application = Applications::find()->select(['applications.id'])
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->andWhere(['=','members.cnic',$fileop[1]])
                    ->andWhere(['=','applications.deleted',0])
                    ->andWhere(['=','applications.project_id',52])
                    ->orderBy('applications.id desc')
                    ->one();
                if(!empty($application)){
                    $files_loan=new FilesApplication();
                    $files_loan->application_id=$application->id;
                    $files_loan->file_path=$fileop[0];
                    $files_loan->type='CIB-datacheck';
                    $files_loan->status=0;
                    $files_loan->created_by=1;
                    $files_loan->save();
                }
            }
            $flag=true;
        }
    }
    public function actionAddFilesCib()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/LCHS_CNIC.csv';
        $myfile = fopen($file_path, "r");
        $flag = false;
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($flag == true) {
                $application = Applications::find()->select(['applications.id'])
                    ->join('inner join', 'members', 'members.id=applications.member_id')
                    ->andWhere(['=', 'members.cnic', $fileop[1]])
                    ->andWhere(['=', 'applications.deleted', 0])
                    ->andWhere(['=', 'applications.project_id', 52])
                    ->orderBy('applications.id desc')
                    ->one();
                if (!empty($application)) {
                    $cib=ApplicationsCib::find()->where(['application_id'=>$application->id])->one();
                    if(empty($cib)){
                        $files_loan = new ApplicationsCib();
                        $files_loan->application_id = $application->id;
                        $files_loan->cib_type_id = 2;
                        $files_loan->fee = 0;
                        $files_loan->receipt_no = '0';
                        $files_loan->status = 1;
                        $files_loan->type = 1;
                        $files_loan->file_path = $fileop[0];
                        $files_loan->created_by = 1;
                        $files_loan->save();
                    }
                }
            }
            $flag = true;
        }
    }
}


