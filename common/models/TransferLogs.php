<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transfer_logs".
 *
 * @property int $id
 * @property string $obj_type
 * @property int $transfer_from
 * @property int $transfer_to
 * @property int $created_by
 * @property string $transfer_details
 * @property int $created_at
 * @property int $updated_at
 */
class TransferLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer_logs';
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
            [['obj_type', 'transfer_from', 'transfer_to', 'created_by'], 'required'],
            [['transfer_from', 'transfer_to', 'created_by'], 'integer'],
            [['created_at', 'updated_at', 'transfer_details'], 'safe'],
            [['obj_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_type' => 'Obj Type',
            'transfer_from' => 'Transfer From',
            'transfer_to' => 'Transfer To',
            'transfer_details' => 'Transfer Details',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
