<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "disbursement_rejected".
 *
 * @property int $id
 * @property int $project_id
 * @property int $disbursement_detail_id
 * @property string $reject_reason
 * @property string $borrower_name
 * @property string $borrower_cnic
 * @property string $sanction_no
 * @property string $deposit_slip_no
 * @property int $deposit_date
 * @property string $deposit_bank
 * @property string $file_path
 * @property int $deposit_amount
 * @property int $created_by
 * @property int $is_verified
 * @property int $verified_by
 * @property int $verfied_at
 * @property int $loan_amount
 *
 * @property int $deleted_by
 * @property int $deleted_at
 * @property int $is_deleted
 *
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property DisbursementDetails[] $disbursement
 * @property Projects[] $project
 */
class DisbursementRejected extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'disbursement_rejected';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['disbursement_detail_id', 'deposit_slip_no', 'deposit_date', 'deposit_bank', 'deposit_amount', 'created_by'], 'required'],
            [['project_id','disbursement_detail_id', 'deposit_amount', 'created_by', 'is_verified', 'verified_by', 'verfied_at', 'status', 'created_at', 'updated_at', 'deleted_by', 'deleted_at', 'is_deleted','loan_amount'], 'integer'],
            [['reject_reason','borrower_name','borrower_cnic','sanction_no'], 'string'],
            [['file'], 'safe'],
            [['file_path'], 'string', 'max' => 100],
            [['deposit_slip_no'], 'string', 'max' => 10],
            [['deposit_bank'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'disbursement_detail_id' => 'Disbursement Detail ID',
            'borrower_name' => 'Borrower Name',
            'borrower_cnic' => 'Borrower CNIC',
            'sanction_no' => 'Sanction No',
            'loan_amount' => 'Loan Amount',
            'reject_reason' => 'Reject Reason',
            'deposit_slip_no' => 'Deposit Slip No',
            'deposit_date' => 'Deposit Date',
            'deposit_bank' => 'Deposit Bank',
            'deposit_amount' => 'Deposit Amount',
            'file_path' => 'Attachment',
            'created_by' => 'Created By',
            'is_verified' => 'Is Verified',
            'verified_by' => 'Verified By',
            'verfied_at' => 'Verfied At',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getDisbursement()
    {
        return $this->hasOne(DisbursementDetails::className(), ['id' => 'disbursement_detail_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
