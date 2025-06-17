<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "banks".
 *
 * @property int $id
 * @property string $bank_name
 * @property string $branch_detail
 * @property string $branch_code
 * @property string $swift_code
 * @property string $environment
 * @property string $base_url
 * @property string $api_key_1
 * @property string $api_key_2
 * @property string $api_key_3
 * @property string $api_key_4
 * @property string $api_key_5
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class Banks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banks';
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
            [['bank_name', 'branch_code', 'swift_code', 'environment', 'base_url', 'api_key_1', 'assigned_to', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['bank_name', 'base_url', 'api_key_1', 'api_key_2', 'api_key_3', 'api_key_4', 'api_key_5'], 'string', 'max' => 50],
            [['branch_detail'], 'string', 'max' => 255],
            [['branch_code', 'swift_code'], 'string', 'max' => 20],
            [['environment'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bank_name' => 'Bank Name',
            'branch_detail' => 'Branch Detail',
            'branch_code' => 'Branch Code',
            'swift_code' => 'Swift Code',
            'environment' => 'Environment',
            'base_url' => 'Base Url',
            'api_key_1' => 'Api Key 1',
            'api_key_2' => 'Api Key 2',
            'api_key_3' => 'Api Key 3',
            'api_key_4' => 'Api Key 4',
            'api_key_5' => 'Api Key 5',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
