<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_transfers".
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int $division_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $status
 * @property string $remarks
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class UserTransfers extends \yii\db\ActiveRecord
{
    public $recommend_remarks;
    public $approved_remarks;
    public $hr_acceptance_remarks;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];
    }
    public static function tableName()
    {
        return 'user_transfers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['remarks'], 'string'],
            [['type', 'status'], 'string', 'max' => 20],
            [['role'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'role' => 'Role',
            'division_id' => 'Division ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'status' => 'Status',
            'remarks' => 'Remarks',
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
                $this->status = 0;
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

    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }

    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getActions()
    {
        return $this->hasMany(UserTransferActions::className(), ['parent_id' => 'id']);
    }

}
