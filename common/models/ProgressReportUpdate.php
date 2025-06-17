<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "progress_report_update".
 *
 * @property int $id
 * @property int $report_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class ProgressReportUpdate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'progress_report_update';
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
            [['report_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'required'],
            [['report_id', 'region_id', 'area_id', 'branch_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_id' => 'Report ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'status' => 'Status',
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
                if($this->region_id==null){
                    $this->region_id=0;
                }
                if($this->area_id==null){
                    $this->area_id=0;
                }
                if($this->branch_id==null){
                    $this->branch_id=0;
                }
                $this->status=0;
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
                $this->updated_by = 0;
            } else {
               // $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getProgressreport()
    {
        return $this->hasOne(ProgressReports::className(), ['id' => 'report_id']);
    }

}
