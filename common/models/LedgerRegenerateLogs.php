<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ledger_regenerate_logs".
 *
 * @property int $id
 * @property int $loan_id
 * @property string $reason
 * @property int $status
 * @property int $created_at
 * @property int $update_at
 */
class LedgerRegenerateLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ledger_regenerate_logs';
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', /*'created_at'*/], 'required'],
            [['loan_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['reason'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'reason' => 'Reason',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
