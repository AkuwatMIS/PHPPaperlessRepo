<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "email_logs".
 *
 * @property int $id
 * @property string $type
 * @property string $sender_email
 * @property string $receiver_email
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 */
class EmailLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'sender_email', 'receiver_email', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['created_by', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 50],
            [['sender_email', 'receiver_email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'sender_email' => 'Sender Email',
            'receiver_email' => 'Receiver Email',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
