<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dynamic_reports".
 *
 * @property int $id
 * @property int $report_defination_id
 * @property string $filters
 * @property string $visibility
 * @property string $notification
 * @property int $created_by
 * @property int $created_at
 * @property int $status
 * @property int $pmt_score
 */
class DynamicReports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    /*public $region_id;
    public $area_id;
    public $branch_id;
    public $project_id;
    public $report_date;*/
    public $file;
    public $referral_id;
    public static function tableName()
    {
        return 'dynamic_reports';
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
            [['report_defination_id', 'visibility', 'notification', 'created_by'], 'required'],
            [['report_defination_id', 'created_by', 'created_at','updated_at', 'status','is_approved'], 'integer'],
            [['sql_filters','file_path','uploaded_file'], 'string', 'max' => 300],
            [['visibility', 'notification'], 'string', 'max' => 200],
            [['region_id','area_id','branch_id','project_id','report_date','referral_id','pmt_score'], 'safe'],
            [['file'], 'file', 'extensions' => 'csv', 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_defination_id' => 'Report Defination ID',
            'sql_filters' => 'Sql Filters',
            'visibility' => 'Visibility',
            'notification' => 'Notification',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'file_path' => 'File Path',
            'pmt_score' => 'PMT'
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getReport()
    {
        return $this->hasOne(ReportDefinations::className(), ['id' => 'report_defination_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
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
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
