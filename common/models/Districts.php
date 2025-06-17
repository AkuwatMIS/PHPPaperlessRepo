<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "districts".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $division_id
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BranchRequests[] $branchRequests
 * @property Branches[] $branches
 * @property Divisions $division
 * @property ProgressReportDetails[] $progressReportDetails
 */
class Districts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'districts';
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
            [['name', 'assigned_to', 'created_by'], 'required'],
            [['division_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 10],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Divisions::className(), 'targetAttribute' => ['division_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'division_id' => 'Division ID',
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
    public function getBranchRequests()
    {
        return $this->hasMany(BranchRequests::className(), ['district_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(Branches::className(), ['district_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Divisions::className(), ['id' => 'division_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgressReportDetails()
    {
        return $this->hasMany(ProgressReportDetails::className(), ['district_id' => 'id']);
    }
}
