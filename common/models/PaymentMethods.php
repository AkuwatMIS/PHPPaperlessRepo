<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_methods".
 *
 * @property int $id
 * @property string $name
 * @property string $environment
 * @property string $live_url
 * @property string $api_key
 * @property string $auth_token
 * @property int $status
 */
class PaymentMethods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_methods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['environment'], 'string', 'max' => 20],
            [['live_url', 'api_key'], 'string', 'max' => 50],
            [['auth_token'], 'string', 'max' => 255],
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
            'environment' => 'Environment',
            'live_url' => 'Live Url',
            'api_key' => 'Api Key',
            'auth_token' => 'Auth Token',
            'status' => 'Status',
        ];
    }
}
