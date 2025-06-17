<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "emails_list".
 *
 * @property int $id
 * @property string $name
 * @property string $sender_email
 * @property int $status
 * @property int $deleted
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class EmailsList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emails_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'sender_email', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'required'],
            [['status', 'deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['sender_email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sender_email' => 'Sender Email',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
