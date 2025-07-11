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

    // php yii notifications/notify-borrower
    public function actionNotifyBorrower()
    {
        $status = ['collected','loan completed'];
        $date = date("Y-m-d", strtotime("+1 month"));

        $subQuery = "(SELECT member_id, phone 
               FROM members_phone 
               WHERE is_current = 1 AND phone_type = 'Mobile') AS phone_sub";

        $query = Members::find()
            ->select([
                'members.id',
                'members.full_name',
                'members.parentage',
                'members.cnic',
                'member_info.cnic_expiry_date',
                'phone_sub.phone'
            ])
            ->innerJoin('member_info', 'member_info.member_id = members.id')
            ->innerJoin($subQuery, 'phone_sub.member_id = members.id')
            ->innerJoin('applications', 'applications.member_id = members.id')
            ->innerJoin('loans', 'loans.application_id = applications.id')
            ->where('DATE(member_info.cnic_expiry_date) = DATE("'.$date.'")')
            ->andWhere(['in', 'loans.status', $status])
            ->groupBy('members.id')
            ->limit(10)
            ->asArray()
            ->all();

        foreach ($query as $q) {

            $mobile = $q->phone ?? null;

            if ($mobile) {
                $msg = "Your CNIC is one month to expired . Please update it.";

                echo $mobile;
                echo '-----';
                echo $msg;
                die();

                $sms = SmsHelper::Sendsms($mobile, $msg);

                $sms_log = new SmsLogs();

                $sms_log->member_id = $q->id;
                $sms_log->phone = $mobile;
                $sms_log->message = $msg;
                $sms_log->sent_at = date('Y-m-d H:i:s');

                if (isset($sms->corpsms[0]->type) && $sms->corpsms[0]->type === 'Success') {
                    $sms_log->status = 1;
                    $sms_log->sent_count = ($sms_log->sent_count ?? 0) + 1;
                } else {
                    $sms_log->status = 2;
                }

                $sms_log->save(false); // Use save(true) if validation is needed
            } else {
                // Optionally log or skip invalid numbers
                continue;
            }
        }
    }
}