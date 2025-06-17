<?php

namespace common\models;

use Ratchet\Wamp\Exception;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "branches".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property string $type
 * @property string $name
 * @property string $code
 * @property string $uc
 * @property string $village
 * @property string $address
 * @property string $mobile
 * @property int $city_id
 * @property int $tehsil_id
 * @property int $district_id
 * @property int $division_id
 * @property int $province_id
 * @property int $country_id
 * @property double $latitude
 * @property double $longitude
 * @property string $description
 * @property string $opening_date
 * @property int $status
 * @property int $cr_division_id
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Accounts[] $accounts
 * @property Cities[] $city
 * @property ArchiveReports[] $archiveReports
 * @property Loans[] $loans
 */
class Branches extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $no_of_members;
    public $no_of_applications;
    public $no_of_social_appraisals;
    public $no_of_business_appraisals;
    public $no_of_verifications;
    public $no_of_groups;
    public $no_of_loans;
    public $no_of_fund_requests;
    public $no_of_disbursements;
    public $no_of_recoveries;
    public $report_date;
    public $platform;

    public $coordinates;
    public static function tableName()
    {
        return 'branches';
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
            [['region_id', 'area_id', 'name', 'uc', 'village', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'latitude', 'longitude',/* 'cr_division_id',*/ 'assigned_to','opening_date', 'created_by'], 'required'],
            [['status','region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'cr_division_id', 'assigned_to', 'created_by'], 'integer'],
            [['type', 'name', 'code', 'uc', 'village', 'address', 'mobile', 'description'], 'string'],
            [['latitude', 'longitude'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'type' => 'Type',
            'name' => 'Name',
            'code' => 'Code',
            'uc' => 'Uc',
            'village' => 'Village',
            'address' => 'Address',
            'mobile' => 'Mobile',
            'city_id' => 'City ID',
            'tehsil_id' => 'Tehsil ID',
            'district_id' => 'District ID',
            'division_id' => 'Division ID',
            'province_id' => 'Province ID',
            'country_id' => 'Country ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'description' => 'Description',
            'opening_date' => 'Opening Date',
            'status' => 'Status',
            'cr_division_id' => 'Cr Division ID',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Accounts::className(), ['branch_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(BranchProjectsMapping::className(), ['branch_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchiveReports()
    {
        return $this->hasMany(ArchiveReports::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['branch_id' => 'id']);
    }
    public function getCrdivision(){
        return $this->hasOne(CreditDivisions::className(), ['id' => 'cr_division_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }
    public function getDistrict()
    {
        return $this->hasOne(Districts::className(), ['id' => 'district_id']);
    }
    public function getDivision()
    {
        return $this->hasOne(Divisions::className(), ['id' => 'division_id']);
    }
    public function getProvince()
    {
        return $this->hasOne(Provinces::className(), ['id' => 'province_id']);
    }
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }
    public function getTeams()
    {
        return $this->hasMany(Teams::className(), ['branch_id' => 'id']);
    }

    public function addProjects($project_ids){
        $projects = explode(',',$project_ids);
        foreach ($projects as $project){
            $branch_projects = new BranchProjectsMapping();
            $branch_projects->branch_id = $this->id;
            $branch_projects->project_id = $project;
            $branch_projects->assigned_to = Yii::$app->user->getId();
            $branch_projects->created_by = Yii::$app->user->getId();
            if(!$branch_projects->save()){
                print_r($branch_projects->getErrors());
                die();
            }
        }
    }

}
