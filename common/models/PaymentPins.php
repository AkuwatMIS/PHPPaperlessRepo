<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_pins".
 *
 * @property int $id
 * @property int $disbursement_details_id
 * @property string $pin
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 */
class PaymentPins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_pins';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['disbursement_details_id', 'pin', 'created_by'], 'required'],
            [['disbursement_details_id', 'created_at', 'updated_at', 'created_by'], 'integer'],
            [['pin'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'disbursement_details_id' => 'Disbursement Details ID',
            'pin' => 'Pin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
        ];
    }
}
