<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "members_phone".
 *
 * @property int $id
 * @property int $member_id
 * @property string $phone
 * @property string $phone_type
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
class MembersPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members_phone';
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
            [['member_id', /*'phone',*/ 'phone_type'], 'required'],
            [['member_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone_type'], 'string', 'max' => 50],
           // [['is_current'], 'string', 'max' => 3],
            //[['deleted'], 'string', 'max' => 1],
            //[['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
            ['phone', 'validatePhone'],
            //[['phone'], 'match', 'pattern' => "/^[0-9]{12}$/"],
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
            'phone' => 'Phone',
            'phone_type' => 'Phone Type',
            'is_current' => 'Is Current',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }

    public function validatePhone($attribute)
    {
        if ($this->isNewRecord) {
            if (!(preg_match("/^[0-9]{12}$/", $this->phone))) {
                $this->addError('phone', 'Phone is invalid.');
            }
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->is_current = 1;
                $this->assigned_to = isset($this->assigned_to)? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by =  isset($this->created_by)? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by =  isset($this->updated_by)? $this->updated_by :Yii::$app->user->getId();
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
