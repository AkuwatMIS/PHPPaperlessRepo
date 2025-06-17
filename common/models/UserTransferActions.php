<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_transfer_actions".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $user_id
 * @property string $action
 * @property int $status
 * @property string $remarks
 * @property int $pre_action
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class UserTransferActions extends \yii\db\ActiveRecord
{
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
        return 'user_transfer_actions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'user_id', 'action'], 'required'],
            [['parent_id', 'user_id', 'status', 'pre_action', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remarks'], 'string'],
            [['action'], 'string', 'max' => 20],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'user_id' => 'User ID',
            'action' => 'Action',
            'status' => 'Status',
            'remarks' => 'Remarks',
            'pre_action' => 'Pre Action',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function setValues($parent_id,$action,$user_id,$status=0){
        $this->parent_id = $parent_id;
        $this->user_id = $user_id;
        $this->status = $status;
        $this->action = $action;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

}
