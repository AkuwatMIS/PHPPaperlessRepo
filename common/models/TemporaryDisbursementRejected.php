<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "disbursement_rejected".
 *
 * @property int $id
 * @property int $disbursement_detail_id
 * @property string $reject_reason
 * @property int $tranche_no
 * @property string $file_path
 * @property int $created_by
 * @property int $is_verified
 * @property int $verified_by
 * @property int $verfied_at
 * @property int $review_by
 * @property int $review_at
 *
 * @property int $deleted_by
 * @property int $deleted_at
 * @property int $deleted
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property DisbursementDetails[] $disbursement
 */
class TemporaryDisbursementRejected extends \yii\db\ActiveRecord
{
    public $file;
    public $borrower_account_no;
    public $tranch_amount;
    public $project_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temporary_disbursement_rejected';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['disbursement_detail_id', 'created_by'], 'required'],
            [['disbursement_detail_id', 'created_by', 'is_verified', 'verified_by', 'verfied_at', 'status', 'created_at', 'updated_at','tranche_no','review_by','review_at'], 'integer'],
            [['reject_reason'], 'string'],
            [['file','tranch_amount','borrower_account_no','project_name'], 'safe'],
            [['file_path'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'disbursement_detail_id' => 'Disbursement Detail ID',
            'reject_reason' => 'Reject Reason',
            'tranche_no' => 'Tranche No',
            'file_path' => 'Attachment',
            'created_by' => 'Created By',
            'review_by' => 'Review By',
            'is_verified' => 'Is Verified',
            'verified_by' => 'Verified By',
            'verfied_at' => 'Verfied At',
            'review_at' => 'Review At',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getDisbursement()
    {
        return $this->hasOne(DisbursementDetails::className(), ['id' => 'disbursement_detail_id']);
    }
}
