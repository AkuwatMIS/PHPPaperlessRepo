<?php

namespace console\controllers;

use common\components\Helpers\ImageHelper;
use common\models\Actions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Branches;
use common\models\Donations;
use common\models\DynamicReports;
use common\models\MemberInfo;
use common\models\Members;
use common\models\ProgressReports;
use common\models\Users;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class MemberInfoController extends Controller
{



    public function actionMember()
    {
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [17]])->all();
        foreach ($dynamic_reports as $report) {
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'member_info' . '/' . $report->uploaded_file;
            $myfile = fopen($file_path, "r");
            $flag = false;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $member = Members::find()->where(['cnic' => $fileop[0]])->one();
                    if (!empty($member)) {
                        $memberCnic = Members::find()->select('id')->where(['cnic' => $fileop[0], 'deleted' => 0])->one();
                        if (isset($memberCnic)) {
                            $model=MemberInfo::find()->where(['member_id'=>$member->id])->one();
                            if(empty($model)){
                                $model = new MemberInfo();
                            }
                            $model->member_id = $member->id;
                            $model->cnic_issue_date =  date('Y-m-d',strtotime($fileop[1]));
                            if (!isset($fileop[2]) || empty($fileop[2])) {
                                $model->cnic_expiry_date = null;
                            } else {
                                $model->cnic_expiry_date = date('Y-m-d',strtotime($fileop[2]));
                            }
                            if (!isset($fileop[3]) || empty($fileop[3])) {
                                $model->mother_name = null;
                            } else {
                                $model->mother_name = $fileop[3];
                            }
                            $model->created_by = 1;
                            if (!($model->save(false))) {
                                print_r($model->getErrors());
                                die();
                            }
                        } else {
                            print_r($fileop[0]);
                            die();
                        }
                    }
                }
                $flag = true;
            }
            $report->status = 1;
            $report->save();
        }
    }


    public function actionFixMemberName()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/members-data.csv';
        $myfile = fopen($file_path, "r");
        while (($fileop = fgetcsv($myfile)) !== false) {
            $member = Members::find()->where(['cnic' => trim($fileop[0])])->one();
            if ($member) {
                $member->full_name = trim($fileop[1]);
                $member->parentage = trim($fileop[2]);
                $member->gender    = trim($fileop[3]);
                $member->save(false);
                echo 'member-updated'. PHP_EOL;
            }else{
                echo 'member-not-exists'. PHP_EOL;
            }
        }
    }

    public function actionFixApplicationDate()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/application_fix_date.csv';
        $myfile = fopen($file_path, "r");
        while (($fileop = fgetcsv($myfile)) !== false) {
           
            $application = Applications::find()->where(['id' => trim($fileop[0])])->one();
            if (!empty($application) && $application!=null) {
                //$application->application_date = trim($fileop[1]);
                //$application->save();
                $date = trim($fileop[1]);
                $id = trim($fileop[0]);
                Yii::$app->db->createCommand('UPDATE applications SET application_date ='.$date.' WHERE id ='.$id.' ')->execute();
                echo 'application-updated'. PHP_EOL;
            }else{
                echo 'application-not-exists'. PHP_EOL;
            }
        }
    }
}