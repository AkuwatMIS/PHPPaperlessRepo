<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "connection_banks".
 *
 * @property int $id
 * @property string $bank_name
 * @property string $bank_code
 * @property string $description
 * @property int $charges
 * @property int $assigned_to
 * @property int $created_by
 * @property int $created_at
 * @property int $upated_at
 */
class ConnectionBanks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'connection_banks';
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
            [['bank_name', 'bank_code', 'charges', 'assigned_to', 'created_by'/*, 'created_at', 'upated_at'*/], 'required'],
            [['description'], 'string'],
            [['charges', 'assigned_to', 'created_by', 'created_at', 'upated_at'], 'integer'],
            [['bank_name', 'bank_code'], 'string', 'max' => 100],
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
            'bank_code' => 'Bank Code',
            'description' => 'Description',
            'charges' => 'Charges',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'upated_at' => 'Upated At',
        ];
    }
}
