<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loans_disbursement".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $tranche_id
 * @property int $payment_method_id
 * @property int $created_at
 * @property int $updated_at
 */
class LoansDisbursement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loans_disbursement';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'tranche_id', 'payment_method_id'/*, 'created_at'*/], 'required'],
            [['loan_id', 'tranche_id', 'payment_method_id', 'created_at', 'updated_at','created_by'], 'integer'],
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
            'tranche_id' => 'Tranche ID',
            'payment_method_id' => 'Payment Method ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
}
