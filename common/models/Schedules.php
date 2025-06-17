<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "schedules".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property int $branch_id
 * @property int $charges_credit
 * @property int $charges_schdl_amount
 * @property string $due_date
 * @property string $schdl_amnt
 * @property string $overdue
 * @property string $overdue_log
 * @property string $advance
 * @property string $advance_log
 * @property string $due_amnt
 * @property int $credit_tax
 * @property string $credit
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Recoveries[] $recoveries
 * @property Applications $application
 * @property Branches $branch
 * @property Loans $loan
 */
class Schedules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedules';
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
            [['application_id'], 'required'],
            [['platform','application_id', 'loan_id', 'branch_id', 'assigned_to', 'created_by', 'updated_by','charges_schdl_amount'], 'integer'],
            [['schdl_amnt', 'overdue', 'overdue_log', 'advance', 'advance_log', 'due_amnt', 'credit','charges_schdl_amnt_tax','credit_tax','charges_credit'], 'number'],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loan_id' => 'id']],
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
            'due_date' => 'Due Date',
            'schdl_amnt' => 'Schdl Amnt',
            'overdue' => 'Overdue',
            'overdue_log' => 'Overdue Log',
            'advance' => 'Advance',
            'advance_log' => 'Advance Log',
            'due_amnt' => 'Due Amnt',
            'credit' => 'Credit',
            'assigned_to' => 'Assigned To',
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
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by =isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecoveries()
    {
        return $this->hasMany(Recoveries::className(), ['schedule_id' => 'id'])->andOnCondition(['recoveries.deleted'=>0]);
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
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
    }
}
