<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "archive_reports".
 *
 * @property int $id
 * @property string $report_name
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property int $project_id
 * @property int $activity_id
 * @property int $product_id
 * @property string $date_filter
 * @property string $source
 * @property string $gender
 * @property string $file_path
 * @property int $status
 * @property int $requested_by
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $do_delete
 *
 * @property Activities $activity
 * @property Areas $area
 * @property Branches $branch
 * @property Products $product
 * @property Projects $project
 * @property Regions $region
 */
class ArchiveReports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive_reports';
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
            [['report_name', 'source'/*, 'requested_by', 'assigned_to', 'created_by'*//*, 'created_at', 'updated_at'*/,'branch_codes'], 'required'],
            [['region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'project_id', 'activity_id', 'product_id', 'status', 'requested_by', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'do_delete'], 'integer'],
            [['report_name', 'file_path'], 'string', 'max' => 255],
            [['date_filter'], 'string', 'max' => 100],
            [['source'], 'string', 'max' => 20],
            [['gender'], 'string', 'max' => 5],
            /*[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activities::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_name' => 'Report Name',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'project_id' => 'Project ID',
            'activity_id' => 'Activity ID',
            'product_id' => 'Product ID',
            'date_filter' => 'Date Filter',
            'source' => 'Source',
            'gender' => 'Gender',
            'file_path' => 'File Path',
            'status' => 'Status',
            'requested_by' => 'Requested By',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'do_delete' => 'Do Delete',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activities::className(), ['id' => 'activity_id']);
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
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }
    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
             if($this->isNewRecord) {
                $this->branch_id = isset($this->branch_id) ? $this->branch_id : 0;
                $this->area_id = isset($this->area_id) ? $this->area_id : 0;
                $this->assigned_to = Yii::$app->user->getId();
                $this->requested_by = Yii::$app->user->getId();
                $this->created_by = Yii::$app->user->getId();
                $this->status = 0;
                $this->do_delete = 0;
                return true;
            }
            return true;
        }
        else {
            return false;
        }
    }
}
