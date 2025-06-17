<?php

namespace common\models;

use phpDocumentor\Reflection\Types\Null_;
use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "fund_requests_details".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $project_id
 * @property int $fund_request_id
 * @property int $total_loans
 * @property int $total_amount
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class FundRequestsDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_requests_details';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'project_id', 'fund_request_id', 'total_loans', 'total_requested_amount'], 'required'],
            [['branch_id', 'project_id', 'fund_request_id', 'total_loans', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted','payment_method_id'], 'integer'],
            [['total_requested_amount','total_approved_amount'], 'number'],
            [['cheque_no','status'], 'string', 'max' => 50],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'project_id' => 'Project ID',
            'fund_request_id' => 'Fund Request ID',
            'total_loans' => 'Total Loans',
            'total_requested_amount' => 'Total Requested Amount',
            'total_approved_amount' => 'Total Approved Amount',
            'cheque_no' => 'Cheque No',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "1";
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    public function beforeValidate()
    {
        if($this->status=='fund available' && empty($this->cheque_no)){
            $this->addError('cheque_no','Cheque No can not be blank if fund available.');
            return false;
        }
        return true;
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
