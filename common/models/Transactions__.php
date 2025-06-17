<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property string $type
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $amount
 * @property string $tax
 * @property int $account_id
 * @property string $deposit_slip_no
 * @property string $deposit_date
 * @property int $deposited_by
 * @property string $status
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CihTransactionsMapping[] $cihTransactionsMappings
 * @property Operations[] $operations
 * @property Accounts $account
 * @property Areas $area
 * @property Branches $branch
 * @property Regions $region
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transactions';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }

    public $bank_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'loan_id', 'branch_id', 'created_by', 'receive_date' , 'bnk_dep_date', 'dt_entry'], 'integer'],
            [['branch_id', 'acc_no', 'credit', 'receive_date', 'narration'], 'required'],
            [['debit', 'credit'], 'number'],
            [['narration'], 'string'],
            [['acc_no'], 'string', 'max' => 30],
            [['bank_rct_no', 'receipt_no'], 'string', 'max' => 20],
            [['funding_line'], 'string', 'max' => 5],
            [['bank_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'loan_id' => 'Loan ID',
            'branch_id' => 'Branch ID',
            'acc_no' => 'Acc No',
            'debit' => 'Debit',
            'credit' => 'Credit',
            'receive_date' => 'Receive Date',
            'bnk_dep_date' => 'Bnk Dep Date',
            'bank_rct_no' => 'Bank Rct No',
            'receipt_no' => 'Receipt No',
            'dt_entry' => 'Dt Entry',
            'narration' => 'Narration',
            'funding_line' => 'Funding Line',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else{
            return true;
        }
    }

    public function getUser(){
        return $this->hasOne(Users::className(),['id'=>'created_by']);
    }
}
