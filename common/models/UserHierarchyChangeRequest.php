<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_hierarchy_change_request".
 *
 * @property int $id
 * @property int $user_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $status
 * @property int $created_by
 * @property int $assigned_to
 * @property int $recommended_by
 */
class UserHierarchyChangeRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_hierarchy_change_request';
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
            [['user_id', 'region_id', 'status', 'created_by', 'assigned_to'], 'required'],
            [['user_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'created_by', 'assigned_to', 'recommended_by'], 'integer'],
            [['status'], 'string', 'max' => 20],
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
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'status' => 'Status',
            'created_by' => 'Created By',
            'assigned_to' => 'Assigned To',
            'recommended_by' => 'Recommended By',
        ];
    }
    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
