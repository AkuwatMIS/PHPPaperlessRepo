<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location_logs".
 *
 * @property int $user_id
 * @property int $device_id
 * @property double $latitude
 * @property double $longitude
 * @property string $created_on
 */
class LocationLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'device_id', 'latitude', 'longitude', 'created_on'], 'required'],
            [['user_id', 'device_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['created_on'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'created_on' => 'Created On',
        ];
    }
}
