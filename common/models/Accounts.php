<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "accounts".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $acc_no
 * @property string $bank_info
 * @property string $funding_line
 * @property string $purpose
 * @property string $dt_opening
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Branches $branch
 * @property BranchAccountMapping[] $branchAccountMappings
 * @property Transactions[] $transactions
 */
class Accounts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accounts';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id', 'acc_no', 'bank_info', 'funding_line', 'purpose', 'dt_opening', 'assigned_to'], 'required'],
            [['branch_id', 'assigned_to', 'created_by', 'updated_by','dt_opening'], 'integer'],
            [['acc_no'], 'string', 'max' => 30],
            [['bank_info'], 'string', 'max' => 100],
            [['funding_line'], 'string', 'max' => 20],
            [['purpose'], 'string', 'max' => 10],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'acc_no' => 'Acc No',
            'bank_info' => 'Bank Info',
            'funding_line' => 'Funding Line',
            'purpose' => 'Purpose',
            'dt_opening' => 'Dt Opening',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getBranchAccountMappings()
    {
        return $this->hasMany(BranchAccountMapping::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transactions::className(), ['account_id' => 'id']);
    }
}
