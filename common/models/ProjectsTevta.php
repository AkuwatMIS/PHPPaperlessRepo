<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_details_tevta".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property string $education_code
 * @property string $home_ownership_status
 * @property string $business_segment
 * @property string $business_type
 * @property string $institute_name
 * @property string $type_of_diploma
 * @property string $duration_of_diploma
 * @property string $year
 * @property string $pbte_or_ttb
 * @property string $registration_no
 * @property string $roll_no
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications $application
 * @property Loans $loan
 */
class ProjectsTevta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_tevta';
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
            [['application_id', 'loan_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['institute_name'], 'string', 'max' => 150],
            [['type_of_diploma', 'duration_of_diploma'], 'string', 'max' => 100],
            [['year'], 'string', 'max' => 50],
            [['pbte_or_ttb', 'registration_no', 'roll_no'], 'string', 'max' => 255],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            //[['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loan_id' => 'id']],
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
            'institute_name' => 'Institute Name',
            'type_of_diploma' => 'Type Of Diploma',
            'duration_of_diploma' => 'Duration Of Diploma',
            'year' => 'Year',
            'pbte_or_ttb' => 'Pbte Or Ttb',
            'registration_no' => 'Registration No',
            'roll_no' => 'Roll No',
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
    /*public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
    }*/
}
