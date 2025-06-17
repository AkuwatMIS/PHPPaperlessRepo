<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "members_address".
 *
 * @property int $id
 * @property int $member_id
 * @property string $address
 * @property string $address_type
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
class MembersAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members_address';
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
            [['member_id', 'address', 'address_type'], 'required'],
            [['member_id', 'assigned_to', 'created_by', 'updated_by','is_current','deleted'], 'integer'],
            [['address'], 'string', 'min' => 30],
            [['address'], 'string', 'max' => 255],
            [['address_type'], 'string', 'max' => 50],
            [['address'], 'match', 'pattern' => "/^[a-zA-Z0-9 ]+(?:\s[a-zA-Z0-9 ]+)*$/"],
           // [['is_current'], 'string', 'max' => 3],
            //[['deleted'], 'string', 'max' => 1],
            //[['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
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
            'address' => 'Address',
            'address_type' => 'Address Type',
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
