<?php

namespace console\controllers;

use common\components\Helpers\SmsHelper;
use common\models\Applications;
use common\models\Recoveries;
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
            if($sms_log->sent_count == 0) {
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
}