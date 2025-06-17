<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "branch_projects_mapping".
 *
 * @property int $id
 * @property int $project_id
 * @property int $account_id
 * @property int $branch_id
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Branches $branch
 * @property Projects $project
 */
class BranchProjectsMapping extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'branch_projects_mapping';
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
            [['project_id', 'branch_id', 'assigned_to'], 'required'],
            [['project_id', 'branch_id', 'assigned_to', 'created_by', 'updated_by','account_id'], 'integer'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'branch_id' => 'Branch ID',
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
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
