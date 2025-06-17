<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_details_disabled_sidb".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property string $disability
 * @property string $nature
 * @property string $physical_disability
 * @property string $visual_disability
 * @property string $communicative_disability
 * @property string $disabilities_instruments
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications $application
 * @property Loans $loan
 */
class ProjectsSidb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_sidb';
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
            [['is_khidmat_card_holder','application_id', 'disability', 'nature', 'physical_disability', 'visual_disability', 'communicative_disability', 'disabilities_instruments'], 'required'],
            [['application_id', 'loan_id', 'assigned_to', 'created_by', 'updated_by','is_khidmat_card_holder'], 'integer'],
            [['disability', 'nature', 'physical_disability', 'visual_disability', 'communicative_disability', 'disabilities_instruments'], 'string', 'max' => 20],
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
            'disability' => 'Disability',
            'nature' => 'Nature',
            'physical_disability' => 'Physical Disability',
            'visual_disability' => 'Visual Disability',
            'communicative_disability' => 'Communicative Disability',
            'disabilities_instruments' => 'Disabilities Instruments',
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
                $this->updated_by = isset($this->updated_by)?$this->updated_by:Yii::$app->user->getId();
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
