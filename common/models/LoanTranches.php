<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "loan_tranches".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $tranch_no
 * @property int $amount
 * @property int $date_disbursed
 * @property int $disbursement_id
 * @property string $cheque_no
 * @property int $fund_request_id
 * @property int $tranch_date
 * @property string $status
 * @property int $deleted
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Loans[] $loan
 */
class LoanTranches extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $sanction_no;

    public static function tableName()
    {
        return 'loan_tranches';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['tranch_no','tranch_amount','date_disbursed','tranch_charges_amount','disbursement_id','cheque_no','status'],
                'table' => "loan_tranches_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'tranch_no', 'tranch_amount'], 'required'],
            [['platform','loan_id', 'tranch_no', 'disbursement_id', 'fund_request_id', 'status', 'deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['tranch_amount','tranch_charges_amount','total_expenses'], 'number'],
            [['cheque_no','attendance_status'], 'string', 'max' => 100],
            [['sanction_no','cheque_date','date_disbursed','tranch_date','start_date'], 'safe'],
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
            'sanction_no' => 'Sanction No',
            'tranch_no' => 'Tranch No',
            'tranch_amount' => 'Tranch Amount',
            'date_disbursed' => 'Date Disbursed',
            'disbursement_id' => 'Disbursement ID',
            'cheque_no' => 'Cheque No',
            'fund_request_id' => 'Fund Request ID',
            'tranch_date' => 'Tranch Date',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getFundRequest()
    {
        return $this->hasOne(FundRequests::className(), ['id' => 'fund_request_id']);
    }

    public function getDisbursement()
    {
        return $this->hasOne(Disbursements::className(), ['id' => 'disbursement_id']);
    }

    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
    }
    public function getBatch()
    {
        return $this->hasOne(ProjectFundDetail::className(), ['id' => 'batch_id']);
    }
    public function getPublish()
    {
        return $this->hasOne(DisbursementDetails::className(), ['tranche_id' => 'id'])->andOnCondition(['disbursement_details.deleted' => 0]);
    }
    public function getPayment()
    {
        return $this->hasOne(LoansDisbursement::className(), ['tranche_id' => 'id']);
    }
}
