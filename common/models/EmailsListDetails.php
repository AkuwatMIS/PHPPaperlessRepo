<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "emails_list_details".
 *
 * @property int $id
 * @property int $email_list_id
 * @property string $receiver_email
 * @property int $status
 * @property int $deleted
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class EmailsListDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emails_list_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_list_id', 'receiver_email', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'required'],
            [['email_list_id', 'status', 'deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['receiver_email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_list_id' => 'Email List ID',
            'receiver_email' => 'Receiver Email',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
