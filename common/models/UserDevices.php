<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_devices".
 *
 * @property int $id
 * @property int $user_id
 * @property int $device_id
 * @property int $created_at
 */
class UserDevices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_devices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'device_id'], 'required'],
            [['user_id', 'device_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'created_at' => 'Created At',
        ];
    }

}
