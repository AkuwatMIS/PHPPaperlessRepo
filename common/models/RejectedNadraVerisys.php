<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "notification_logs".
 *
 * @property int $id
 * @property int $application_id
 * @property int $branch_id
 * @property int $area_id
 * @property int $region_id
 * @property int $member_info_id
 * @property int $status
 * @property string $reject_reason
 * @property string $remarks
 * @property string $rejected_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications[] $applications
 * @property MemberInfo[] $info
 */
class RejectedNadraVerisys extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rejected_nadra_verisys';
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
            [['application_id','member_info_id'], 'required'],
            [['branch_id', 'area_id', 'region_id', 'status', 'reject_reason', 'remarks'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch',
            'area_id' => 'Area',
            'region_id' => 'Region',
            'status' => 'Status',
            'reject_reason' => 'Reject Reason',
            'rejected_date' => 'Reject Date',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfo(){
        return $this->hasOne(MemberInfo::className(), ['id' => 'member_info_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['id' => 'application_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

}
