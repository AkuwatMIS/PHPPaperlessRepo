<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "recovery_errors".
 *
 * @property int $id
 * @property int $recovery_files_id
 * @property int $branch_id
 * @property int $area_id
 * @property int $region_id
 * @property string $source
 * @property string $sanction_no
 * @property string $recv_date
 * @property string $credit
 * @property string $receipt_no
 * @property string $balance
 * @property string $error_description
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 */
class RecoveryErrors extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recovery_errors';
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
            [['recovery_files_id', 'recv_date', 'receipt_no', 'error_description', 'assigned_to', 'created_by'/*, 'created_at'*/], 'required'],
            [['recovery_files_id', 'branch_id', 'area_id', 'region_id', 'assigned_to', 'created_by'], 'integer'],
            [['source', 'comments', 'status'], 'string'],
            [[/*'recv_date',*/ 'created_at', 'updated_at','bank_branch_name', 'bank_branch_code', 'sanction_no', 'cnic'], 'safe'],
            [['credit', 'balance'], 'number'],
            [['sanction_no', 'bank_branch_code'], 'string', 'max' => 50],
            [['bank_branch_name'], 'string', 'max' => 255],
            [['receipt_no','cnic'], 'string', 'max' => 100],
            [['error_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recovery_files_id' => 'Recovery Files ID',
            'branch_id' => 'Branch ID',
            'area_id' => 'Area ID',
            'region_id' => 'Region ID',
            'bank_branch_name' => 'Bank Branch Name',
            'bank_branch_code' => 'Bank Branch Code',
            'source' => 'Source',
            'sanction_no' => 'Sanction No',
            'recv_date' => 'Recv Date',
            'credit' => 'Credit',
            'receipt_no' => 'Receipt No',
            'balance' => 'Balance',
            'error_description' => 'Error Description',
            'comments' => 'Comments',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function getRecoveryFile()
    {
        return $this->hasOne(RecoveryFiles::className(), ['id' => 'recovery_files_id']);
    }
}
