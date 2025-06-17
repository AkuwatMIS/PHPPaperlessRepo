<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fields".
 *
 * @property int $id
 * @property string $name
 * @property int $team_id
 * @property string $description
 * @property int $status
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Teams $team
 */
class Fields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fields';
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
            [['name', 'team_id'/*, 'created_at', 'updated_at'*/], 'required'],
            [['team_id', 'assigned_to', 'created_by', 'updated_by'/*, 'created_at', 'updated_at'*/], 'integer'],
            [['description'], 'string'],
           /* [['name'], 'string', 'max' => 10],*/
            /*[['status'], 'string', 'max' => 3],*/
            /*[['deleted'], 'string', 'max' => 1],*/
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teams::className(), 'targetAttribute' => ['team_id' => 'id']],
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
            'team_id' => 'Team ID',
            'description' => 'Description',
            'status' => 'Status',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "1";
                $this->assigned_to = 0;
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
                $this->updated_by = 0;
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    public function getUserStructureMapping()
    {
        return $this->hasOne(UserStructureMapping::className(), ['obj_id'=>'id'])->andOnCondition(['obj_type'=>'field']);
    }
}
