<?php

namespace console\controllers;

use common\components\Helpers\SmsHelper;
use common\models\Applications;
use common\models\Members;
use common\models\Recoveries;
use common\models\search\MembersSearch;
use common\models\SmsLogs;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class NotificationsController extends Controller
{
//notifications/send
    public function actionSend()
    {

        $sms_logs = SmsLogs::find()->where(['status' => 0])->andWhere(['in', 'sms_type', ['application', 'recovery']])->limit(1000)->all();

        foreach ($sms_logs as $id) {
            $batch[] = $id['id'];
        }
        $batch = implode(",", $batch);

        Yii::$app->db->createCommand('UPDATE sms_logs SET status = 10 WHERE id IN(' . $batch . ')')->execute();

        foreach ($sms_logs as $sms_log) {


            if ($sms_log->sms_type == 'application') {
                $model = Applications::findOne(['id' => $sms_log->type_id]);
                $msg = SmsHelper::getApplicationText($model);
                $mobile = isset($model->member->membersMobile->phone) ? $model->member->membersMobile->phone : '';
            } else {
                $model = Recoveries::findOne(['id' => $sms_log->type_id]);
                $msg = SmsHelper::getRecoveryText($model);
                $mobile = isset($model->loan->application->member->membersMobile->phone) ? $model->loan->application->member->membersMobile->phone : '';
            }
            if ($sms_log->sent_count == 0) {
                $sms = SmsHelper::Sendsms($mobile, $msg);
                if ($sms->corpsms[0]->type == 'Success') {
                    $sms_log->status = 1;
                    $sms_log->sent_count = $sms_log->sent_count + 1;
                } else {
                    $sms_log->status = 2;
                }
                $sms_log->save();
            }
        }

    }

    public function actionSendBackup()
    {


        $sms_logs = SmsLogs::find()->where(['status' => 0])->andWhere(['in', 'sms_type', ['application', 'recovery']])->limit(1000)->all();

        foreach ($sms_logs as $sms_log) {
            if ($sms_log->sms_type == 'application') {
                $model = Applications::findOne(['id' => $sms_log->type_id]);
                $msg = SmsHelper::getApplicationText($model);
                $mobile = isset($model->member->membersMobile->phone) ? $model->member->membersMobile->phone : '';
            } else {
                $model = Recoveries::findOne(['id' => $sms_log->type_id]);
                $msg = SmsHelper::getRecoveryText($model);
                $mobile = isset($model->loan->application->member->membersMobile->phone) ? $model->loan->application->member->membersMobile->phone : '';
            }
            $sms = SmsHelper::SendUrdusms($mobile, $msg);
            if ($sms->corpsms[0]->type == 'Success') {
                $sms_log->status = 1;
            } else {
                $sms_log->status = 2;
            }
            $sms_log->save();
        }

    }

    public function actionNotifyBorrower()
    {
        $status = 'collected';
        $date = date("Y-m-d", strtotime("+1 month"));

        $query = Members::find()->select(['members.id', 'members.full_name', 'members.parentage', 'members.parentage_type', 'members.cnic',
            'members.gender', 'members.dob', 'members.education', 'members.marital_status', 'members.status', 'members.is_lock', 'members.religion', 'members.created_at',
            'members.region_id', 'members.area_id', 'members.branch_id', 'members.team_id', 'members.field_id'
            ])
            ->innerJoin('member_info', 'member_info.member_id=members.id')
            ->innerJoin('applications', 'applications.member_id=members.id')
            ->innerJoin('loans', 'loans.application_id=applications.id')
            ->where('DATE(member_info.cnic_expiry_date) <= DATE("' . $date . '")')
            ->andWhere(['=', 'loans.status', $status])
            ->get();
    }
}