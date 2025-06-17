<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "sms_logs".
 *
 * @property int $id
 * @property string $sms_type
 * @property int $user_id
 * @property int $type_id
 * @property string $number
 * @property string $created_at
 * @property string $updated_at
 */
class SmsLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_logs';
    }
    /*public function behaviors()
    {
            return [TimestampBehavior::className(),];

    }*/
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sms_type', 'user_id', 'type_id', 'number'], 'required'],
            [['user_id', 'type_id'], 'integer'],
            [['created_at','updated_at','sent_count'], 'safe'],
            [['number'], 'number'],
            [['sms_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sms_type' => 'Sms Type',
            'user_id' => 'User ID',
            'type_id' => 'Type ID',
            'number' => 'Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
