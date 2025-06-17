<?php

namespace common\models;

use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fund_requests".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property string $fund_request_amount
 * @property string $cheque_no
 * @property int $approved_by
 * @property int $approved_on
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class FundRequests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_requests';
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
            [['branch_id', 'requested_amount','total_loans'], 'required'],
            [['platform','region_id', 'area_id', 'branch_id', 'approved_by', 'approved_on','processed_by','processed_on','assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted','total_loans'], 'integer'],
            [['requested_amount', 'approved_amount'], 'number'],
            [['status'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'requested_amount' => 'Requested Amount',
            'approved_amount' => 'Approved Amount',
            'approved_by' => 'Approved By',
            'approved_on' => 'Approved On',
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
                $branch = StructureHelper::getBranch($this->branch_id);
                $this->region_id = isset($branch->region_id) ? $branch->region_id : 0;
                $this->area_id = isset($branch->area_id) ? $branch->area_id : 0;
                $this->status = isset($this->status) ? $this->status : "pending";
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by)?$this->updated_by:Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequestDetails()
    {
        return $this->hasMany(FundRequestsDetails::className(), ['fund_request_id' => 'id']);
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
    public function getCreateUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
    public function getRecommendUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'approved_by']);
    }
    public function getProcessUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'processed_by']);
    }
}
