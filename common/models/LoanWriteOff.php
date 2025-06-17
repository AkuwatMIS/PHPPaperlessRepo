<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loan_write_off".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $recovery_id
 * @property int $amount
 * @property string $cheque_no
 * @property string $voucher_no
 * @property string $bank_name
 * @property string $bank_account_no
 * @property int $type
 * @property string $reason
 * @property string $deposit_slip_no
 * @property string $borrower_name
 * @property int $borrower_cnic
 * @property string $who_will_work
 * @property string $other_name
 * @property int $other_cnic
 * @property int $write_off_date
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Applications $application
 * @property Members $member
 * @property Loans $loan
 *
 * @property Projects $project
 * @property Regions $region
 * @property Areas $area
 * @property Branches $branch
 */
class LoanWriteOff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_write_off';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className()];

    }
     public $sanction_no;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'amount', 'cheque_no', 'type', /*'reason'*//*, 'created_by', 'created_at'*/], 'required'],
            [['recovery_id', 'amount', 'type', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['cheque_no', 'voucher_no', 'bank_account_no', 'reason'], 'string', 'max' => 30],
            [['bank_name'], 'string', 'max' => 20],
            [['loan_id','sanction_no','deposit_slip_no','borrower_name','who_will_work','other_name','other_cnic','borrower_cnic','write_off_date','status'], 'safe'],
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
            'recovery_id' => 'Recovery ID',
            'amount' => 'Amount',
            'cheque_no' => 'Cheque No',
            'voucher_no' => 'Voucher No',
            'bank_name' => 'Bank Name',
            'bank_account_no' => 'Bank Account No',
            'type' => 'Type',
            'reason' => 'Reason',
            'deposit_slip_no' => 'Deposit slip/advice Number',
            'borrower_name' => 'Borrower Name',
            'borrower_cnic' => 'Borrower CNIC',
            'who_will_work' => 'Relation with borrower',
            'other_name' => 'Name',
            'other_cnic' => 'Cnic',
            'write_off_date' => 'Date',
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
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id'])->andOnCondition(['loans.deleted'=>'0']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
