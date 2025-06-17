<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "projects_psic".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property int $diploma_holder
 * @property string $institute_name
 * @property string $other_name
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class ProjectsPsic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects_psic';
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
            [['application_id', 'diploma_holder'], 'required'],
            [['application_id', 'loan_id', 'diploma_holder', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['institute'], 'string', 'max' => 20],
            [['other_name'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'loan_id' => 'Loan ID',
            'diploma_holder' => 'Diploma Holder',
            'institute' => 'Institute Name',
            'other_name' => 'Other Name',
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
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }
}
