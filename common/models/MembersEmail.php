<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "members_email".
 *
 * @property int $id
 * @property int $member_id
 * @property string $email
 * @property int $is_current
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Members $member
 */
class MembersEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members_email';
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
            [['member_id', 'email'], 'required'],
            [['member_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','deleted','is_current'], 'integer'],
            [['email'], 'string', 'max' => 100],
            //[['is_current'], 'string', 'max' => 3],
            //[['deleted'], 'string', 'max' => 1],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'email' => 'Email',
            'is_current' => 'Is Current',
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
                $this->is_current = 1;
                $this->assigned_to = Yii::$app->user->getId();
                $this->created_by = Yii::$app->user->getId();
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
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }
}
