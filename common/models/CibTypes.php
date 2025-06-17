<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cib_types".
 *
 * @property int $id
 * @property string $name
 * @property string $logo
 * @property string $description
 * @property string $environment
 * @property string $url_live
 * @property string $url_staging
 * @property string $api_key_1
 * @property string $api_key_2
 * @property string $auth_token
 * @property int $status
 * @property int $last_login_at
 */
class CibTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cib_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'environment', 'url_live', 'url_staging', 'api_key_1'], 'required'],
            [['status', 'last_login_at'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['logo', 'description', 'url_live', 'url_staging', 'api_key_1', 'api_key_2'], 'string', 'max' => 50],
            [['environment'], 'string', 'max' => 10],
            [['username','password'], 'string', 'max' => 100],
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
            'logo' => 'Logo',
            'description' => 'Description',
            'environment' => 'Environment',
            'url_live' => 'Url Live',
            'url_staging' => 'Url Staging',
            'api_key_1' => 'Api Key 1',
            'api_key_2' => 'Api Key 2',
            'auth_token' => 'Auth Token',
            'status' => 'Status',
            'last_login_at' => 'Last Login At',
        ];
    }
}
