<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "arc_account_report_details".
 *
 * @property string $id
 * @property string $arc_account_report_id
 * @property string $division_id
 * @property string $region_id
 * @property string $area_id
 * @property string $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $branch_code
 * @property int $country_id
 * @property int $province_id
 * @property string $division
 * @property int $district_id
 * @property int $city_id
 * @property string $objects_count
 * @property int $disbursed_applications
 * @property int $rejected_applications
 * @property string $amount
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class ArcAccountReportDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arc_account_report_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['arc_account_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'objects_count', 'amount'], 'required'],
            [['arc_account_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'country_id', 'province_id', 'district_id', 'city_id', 'objects_count', 'disbursed_applications', 'rejected_applications', 'amount', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['branch_code'], 'string', 'max' => 10],
            [['division'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'arc_account_report_id' => 'Arc Account Report ID',
            'division_id' => 'Division ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'branch_code' => 'Branch Code',
            'country_id' => 'Country ID',
            'province_id' => 'Province ID',
            'division' => 'Division',
            'district_id' => 'District ID',
            'city_id' => 'City ID',
            'objects_count' => 'Objects Count',
            'disbursed_applications' => 'Disbursed Applications',
            'rejected_applications' => 'Rejected Applications',
            'amount' => 'Amount',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(Districts::className(), ['id' => 'district_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Divisions::className(), ['id' => 'division_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Provinces::className(), ['id' => 'province_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    public function getReport()
    {
        return $this->hasOne(ArcAccountReports::className(), ['id' => 'arc_account_report_id']);
    }

    public function getAccountReport()
    {
        return $this->hasOne(ArcAccountReports::className(), ['id' => 'arc_account_report_id']);
    }

    public function set_branch_data($branch_id,$branches_list)
    {
        $this->branch_id = $branch_id;
        $this->branch_code = $branches_list[$branch_id]['branch_code'];
        $this->division_id = $branches_list[$branch_id]['cr_division_id'];
        $this->province_id = $branches_list[$branch_id]['province_id'];
        $this->division_id = isset($branches_list[$branch_id]['division_id'])?$branches_list[$branch_id]['division_id']:1;
        $this->region_id = $branches_list[$branch_id]['region_id'];
        $this->area_id = $branches_list[$branch_id]['area_id'];
        $this->country_id = $branches_list[$branch_id]['country_id'];
        $this->district_id = $branches_list[$branch_id]['district_id'];
        $this->city_id = $branches_list[$branch_id]['city_id'];
        $this->field_id = 0;
        $this->team_id = 0;
        $this->assigned_to = 0;
        $this->created_by = 0;
        $this->updated_by = 0;
        $this->created_at = strtotime(date('Y-m-d'));
        $this->updated_at = strtotime(date('Y-m-d'));
    }
}
