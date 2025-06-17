<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "branch_requests".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property string $type
 * @property string $name
 * @property string $uc
 * @property string $address
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
 * @property string $status
 * @property int $cr_division_id
 * @property string $remarks
 * @property string $recommended_on
 * @property int $recommended_by
 * @property string $recommended_remarks
 * @property string $approved_on
 * @property int $approved_by
 * @property string $approved_remarks
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Areas $area
 * @property Cities $city
 * @property Countries $country
 * @property CreditDivisions $crDivision
 * @property Districts $district
 * @property Divisions $division
 * @property Provinces $province
 * @property Regions $region
 */
class BranchRequests extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $recommended_remarks;
    public $approved_remarks;
    public $reviewed_remarks;
    public $emails;
    public $email_list_id;
    public $sender_email;
    public static function tableName()
    {
        return 'branch_requests';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className()];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'area_id', 'name', 'uc', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'latitude', 'longitude', 'village','cr_division_id'], 'required'],
            [['region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'cr_division_id',  'assigned_to', 'created_by', 'updated_by','branch_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['description', 'remarks','village'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['name','action'], 'string', 'max' => 50],
            [['uc'], 'string', 'max' => 25],
            [['address'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            //[['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
           // [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            //[['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            //[['cr_division_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreditDivisions::className(), 'targetAttribute' => ['cr_division_id' => 'id']],
            //[['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => Districts::className(), 'targetAttribute' => ['district_id' => 'id']],
            //[['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Divisions::className(), 'targetAttribute' => ['division_id' => 'id']],
            //[['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provinces::className(), 'targetAttribute' => ['province_id' => 'id']],
            //[['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            ['opening_date', 'validateOpeningDateConvertToIneger'],
            [['effective_date','reject_reason','projects','emails','email_list_id','sender_email'], 'safe'],
            ['effective_date', 'validateEffectiveDateConvertToIneger'],
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
            'uc' => 'Uc',
            'village' => 'Village',
            'address' => 'Address',
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
            'remarks' => 'Remarks',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function validateOpeningDateConvertToIneger($attribute){

        if(gettype($this->opening_date) == 'string') {
            $this->opening_date = strtotime($this->opening_date);
        }
    }
    public function validateEffectiveDateConvertToIneger($attribute){

        if(gettype($this->effective_date) == 'string') {
            $this->effective_date = strtotime($this->effective_date);
        }
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->status = 'created';
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
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
    public function getCreditDivision()
    {
        return $this->hasOne(CreditDivisions::className(), ['id' => 'cr_division_id']);
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
    public function getTehsil()
    {
        return $this->hasOne(Tehsils::className(), ['id' => 'tehsil_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
    public function getActions()
    {
        return $this->hasMany(BranchRequestActions::className(), ['parent_id' => 'id']);
    }
    /*
     *
     */
    public static function generateCode($districtId)
    {
        if($districtId) {
            $dis = Districts::findOne($districtId);

            if($dis && $dis->code) {
                $count = 0;
                $code = 0;
                $branchCount = Branches::find()->where(['district_id' => $districtId])->count();
                $count = $branchCount+1;
                //$count=sprintf('%02d', $count);
                $count = $count<10 ? "0$count" : $count;
                $code = $dis->code . $count;
                while(true){
                    $branch = Branches::find()->where(['code'=>$code])->one();

                    if($branch){
                        $code++;
                    }else{
                        break;
                    }
                }
                return $code;
            }
        }
        return null;

    }
}
