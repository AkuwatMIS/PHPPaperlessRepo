<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "emergency_loans".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class EmergencyLoans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emergency_loans';
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
            [['loan_id',/* 'created_at', 'updated_at'*/], 'required'],
            [['donated_date',/* 'created_at', 'updated_at'*/], 'safe'],
            [['loan_id','member_id','city_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
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
            'status' => 'Status',
            'donated_date' => 'Donated Date',
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
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
    }
    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
}
