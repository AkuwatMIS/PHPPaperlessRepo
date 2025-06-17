<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "notification_logs".
 *
 * @property int $id
 * @property string $notification_type
 * @property string $message
 * @property string $title
 * @property string $sub_title
 * @property string $ticker_text
 * @property int $device_id
 * @property string $push_notification_id
 * @property string $response
 * @property string $status
 * @property string $error_description
 * @property int $send_by
 * @property int $send_to
 * @property string $created_at
 * @property string $updated_at
 */
class NotificationLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_logs';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_type', 'message', 'title', 'sub_title', 'ticker_text', 'device_id', 'push_notification_id', 'response', 'status', 'send_by', 'send_to'], 'required'],
            [['device_id', 'send_by', 'send_to'], 'integer'],
            /*[['created_at', 'updated_at'], 'safe'],*/
            [['notification_type', 'response', 'status', 'error_description'], 'string', 'max' => 20],
            [['message','title', 'sub_title', 'ticker_text',], 'string', 'max' => 100],
            [['push_notification_id'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notification_type' => 'Notification Type',
            'message' => 'Message',
            'title' => 'Title',
            'sub_title' => 'Sub Title',
            'ticker_text' => 'Ticker Text',
            'device_id' => 'Device ID',
            'push_notification_id' => 'Push Notification ID',
            'response' => 'Response',
            'status' => 'Status',
            'error_description' => 'Error Description',
            'send_by' => 'Send By',
            'send_to' => 'Send To',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
