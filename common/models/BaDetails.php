<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ba_details".
 *
 * @property int $id
 * @property int $ba_id
 * @property int $application_id
 * @property string $business_income
 * @property string $job_income
 * @property string $house_rent_income
 * @property string $other_income
 * @property string $total_income
 * @property string $expected_increase_in_income
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Applications $application
 * @property BusinessAppraisal $ba
 */
class BaDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ba_details';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['total_income','expected_increase_in_income','business_income'],
                'table' => "business_appraisal_logs",
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
            [['ba_id', 'application_id', 'total_income', 'expected_increase_in_income','business_income'], 'required'],
            [['ba_id', 'application_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['business_income', 'job_income', 'house_rent_income', 'other_income', 'total_income', 'expected_increase_in_income'], 'number'],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['ba_id'], 'exist', 'skipOnError' => true, 'targetClass' => BusinessAppraisal::className(), 'targetAttribute' => ['ba_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ba_id' => 'Ba ID',
            'application_id' => 'Application ID',
            'business_income' => 'Business Income',
            'job_income' => 'Job Income',
            'house_rent_income' => 'House Rent Income',
            'other_income' => 'Other Income',
            'total_income' => 'Total Income',
            'expected_increase_in_income' => 'Expected Increase In Income',
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
    public function getBa()
    {
        return $this->hasOne(BusinessAppraisal::className(), ['id' => 'ba_id']);
    }
}
