<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "progress_report_details".
 *
 * @property int $id
 * @property int $progress_report_id
 * @property int $division_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property int $country_id
 * @property int $province_id
 * @property int $district_id
 * @property int $city_id
 * @property string $branch_code
 * @property string $gender
 * @property int $no_of_loans
 * @property int $family_loans
 * @property int $female_loans
 * @property int $active_loans
 * @property string $cum_disb
 * @property string $cum_due
 * @property string $cum_recv
 * @property int $overdue_borrowers
 * @property string $overdue_amount
 * @property string $overdue_percentage
 * @property string $par_amount
 * @property string $par_percentage
 * @property string $not_yet_due
 * @property string $olp_amount
 * @property string $recovery_percentage
 * @property string $cih
 * @property string $mdp
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Areas $area
 * @property Branches $branch
 * @property Cities $city
 * @property Countries $country
 * @property Districts $district
 * @property Divisions $division
 * @property ProgressReports $progressReport
 * @property Provinces $province
 * @property Regions $region
 */
class ProgressReportDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'progress_report_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['progress_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'gender', 'no_of_loans', 'family_loans', 'female_loans', 'active_loans', 'cum_disb', 'cum_due', 'cum_recv', 'overdue_borrowers', 'overdue_amount', 'overdue_percentage', 'par_amount', 'par_percentage', 'not_yet_due', 'olp_amount', 'cih', 'mdp', 'assigned_to', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['progress_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'country_id', 'province_id', 'district_id', 'city_id', 'no_of_loans','members_count', 'family_loans', 'female_loans', 'active_loans', 'cum_disb', 'cum_due', 'cum_recv', 'overdue_borrowers', 'overdue_amount', 'par_amount', 'not_yet_due', 'olp_amount', 'cih', 'mdp', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['overdue_percentage', 'par_percentage', 'recovery_percentage'], 'number'],
            [['branch_code'], 'string', 'max' => 10],
            [['gender'], 'string', 'max' => 6],
            [['deleted'], 'string', 'max' => 1],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => Districts::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Divisions::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['progress_report_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgressReports::className(), 'targetAttribute' => ['progress_report_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provinces::className(), 'targetAttribute' => ['province_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'progress_report_id' => 'Progress Report ID',
            'division_id' => 'Division ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'country_id' => 'Country ID',
            'province_id' => 'Province ID',
            'district_id' => 'District ID',
            'city_id' => 'City ID',
            'branch_code' => 'Branch Code',
            'gender' => 'Gender',
            'no_of_loans' => 'No Of Loans',
            'members_count' => 'Members Count',
            'family_loans' => 'Family Loans',
            'female_loans' => 'Female Loans',
            'active_loans' => 'Active Loans',
            'cum_disb' => 'Cum Disb',
            'cum_due' => 'Cum Due',
            'cum_recv' => 'Cum Recv',
            'overdue_borrowers' => 'Overdue Borrowers',
            'overdue_amount' => 'Overdue Amount',
            'overdue_percentage' => 'Overdue Percentage',
            'par_amount' => 'Par Amount',
            'par_percentage' => 'Par Percentage',
            'not_yet_due' => 'Not Yet Due',
            'olp_amount' => 'Olp Amount',
            'recovery_percentage' => 'Recovery Percentage',
            'cih' => 'Cih',
            'mdp' => 'Mdp',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
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
    public function getProgressReport()
    {
        return $this->hasOne(ProgressReports::className(), ['id' => 'progress_report_id']);
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
        return $this->hasOne(ProgressReports::className(), ['id' => 'progress_report_id']);
    }

    /**
     *
     */


    /**
     *
     */
    public function getProgress()
    {
        return $this->hasOne(ProgressReports::className(), ['id' => 'progress_report_id']);
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
