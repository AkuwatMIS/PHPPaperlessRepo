<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_info".
 *
 * @property int $id
 * @property int $member_id
 * @property int $cnic_expiry_date
 * @property int $cnic_issue_date
 * @property string $mother_name
 * @property string $disability_nature
 * @property string $disability_details
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 */

class MemberInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [TimestampBehavior::className(),
        ];
    }
    public static function tableName()
    {
        return 'member_info';
    }

    public $is_life_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', /*'cnic_expiry_date',*/ 'cnic_issue_date' /*'created_by', 'created_at'*/], 'required'],
            [['member_id', /*'cnic_expiry_date', 'cnic_issue_date',*/ 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['mother_name'], 'string', 'max' => 50],
            [['disability_nature'], 'string', 'max' => 50],
            [['disability_details'], 'string', 'max' => 255],
            [['cnic_expiry_date', 'cnic_issue_date'],'string'],
            [['cnic_expiry_date'], 'date', 'format' => 'php:Y-m-d'],
            [['is_life_time'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'cnic_expiry_date' => 'CNIC Expiry Date',
            'cnic_issue_date' => 'CNIC Issue Date',
            'disability_nature' => 'Disability nature',
            'disability_details' => 'Disability details',
            'mother_name' => 'Mother Name',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            }/*else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }*/
            return true;
        } else {
            return false;
        }
    }

    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }
}
