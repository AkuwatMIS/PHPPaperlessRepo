<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "appraisals_livestock".
 *
 * @property int $id
 * @property int $application_id
 * @property string $animal_type
 * @property string $business_type
 * @property string $business_condition
 * @property string $business_place
 * @property string $business_address
 * @property string $used_land_type
 * @property float $used_land_size
 * @property int $available_amount
 * @property int $required_amount
 * @property string $running_capital
 * @property string $new_assets
 * @property int $monthly_income
 * @property int $expected_income
 * @property int $deleted
 * @property string $status
 * @property string $description
 * @property int $assigned_to
 * @property double $latitude
 * @property double $longitude
 * @property int $approved_by
 * @property int $approved_on
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Applications $application
 */
class AppraisalsLivestock extends \yii\db\ActiveRecord
{
    public $coordinates;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals_livestock';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['approved_by','approved_on'],
                'table' => "appraisals_agriculture_logs",
            ]
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id','expected_income','animal_type','business_type','business_address','available_amount','required_amount','used_land_size','monthly_income','running_capital','new_assets'], 'required'],
            [['used_land_size','required_amount','monthly_income','expected_income','deleted','assigned_to','available_amount'], 'integer'],
            [['status','description','animal_type','business_type','business_condition','business_place','business_address','running_capital','new_assets','used_land_type'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'animal_type' => 'Animal Type',
            'business_type' => 'Business Type',
            'business_condition' => 'Business Condition',
            'business_place' => 'Business Place',
            'business_address' => 'Business Address',
            'used_land_type' => 'Used Land Type',
            'used_land_size' => 'Used Land Size',
            'required_amount' => 'Required Amount',
            'available_amount' => 'Available Amount',
            'running_capital' => 'Running Capital',
            'new_assets' => 'New Assets',
            'monthly_Income' => 'Monthly Income',
            'expected_income' => 'Expected Income',
            'description' => 'description',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'status' => 'Status',
            'assigned_to' => 'Assigned To',
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
                $this->status = isset($this->status) ? $this->status : "incomplete";
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
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    public function set_values($request)
    {


        $resources_animal_type='';
        $resources_business_type='';
        $this->load($request);
        if(isset($request['animal_type'])) {
            foreach ($request['animal_type'] as $asset => $status) {
                if ($resources_animal_type != "") {
                    $resources_animal_type .= "," . $asset;
                } else {
                    $resources_animal_type .= $asset;
                }
            }
        }

        if(isset($request['business_type'])) {
            foreach ($request['business_type'] as $asset => $status) {
                if ($resources_business_type != "") {
                    $resources_business_type .= "," . $asset;
                } else {
                    $resources_business_type .= $asset;
                }
            }
        }

        $running_asset = [];
        $required_asset = [];

        if(!empty($request['fishing']) && !empty($request['fishing_count'])){
            $a = [
                'animal_type'=>$request['fishing'],
                'animal_count'=>$request['fishing_count']
            ];
            $running_asset[1]=$a;
        }
        if(!empty($request['poultry']) && !empty($request['poultry_count'])){
            $b = [
                'animal_type'=>$request['poultry'],
                'animal_count'=>$request['poultry_count']
            ];
            $running_asset[2]=$b;
        }
        if(!empty($request['buffalo']) && !empty($request['buffalo_count'])){
            $c = [
                'animal_type'=>$request['buffalo'],
                'animal_count'=>$request['buffalo_count']
            ];
            $running_asset[3]=$c;
        }
        if(!empty($request['cow']) && !empty($request['cow_count'])){
            $d = [
                'animal_type'=>$request['cow'],
                'animal_count'=>$request['cow_count']
            ];
            $running_asset[4]=$d;
        }
        if(!empty($request['goat']) && !empty($request['goat_count'])){
            $b = [
                'animal_type'=>$request['goat'],
                'animal_count'=>$request['goat_count']
            ];
            $running_asset[5]=$b;
        }
        if(!empty($request['sheep']) && !empty($request['sheep_count'])){
            $c = [
                'animal_type'=>$request['sheep'],
                'animal_count'=>$request['sheep_count']
            ];
            $running_asset[6]=$c;
        }
        if(!empty($request['others']) && !empty($request['others_count'])){
            $d = [
                'animal_type'=>$request['others'],
                'animal_count'=>$request['others_count']
            ];
            $running_asset[7]=$d;
        }

        //=====================================================================



        if(!empty($request['fishing_buy']) && !empty($request['fishing_buy_count']) && !empty($request['fishing_buy_amount'])){
            $a = [
                'animal_type'=>$request['fishing_buy'],
                'animal_count'=>$request['fishing_buy_count'],
                'animal_amount'=>$request['fishing_buy_amount']
            ];
            $required_asset[1]=$a;
        }
        if(!empty($request['poultry_buy']) && !empty($request['poultry_buy_count']) && !empty($request['poultry_buy_amount'])){
            $b = [
                'animal_type'=>$request['poultry_buy'],
                'animal_count'=>$request['poultry_buy_count'],
                'animal_amount'=>$request['poultry_buy_amount']
            ];
            $required_asset[2]=$b;
        }
        if(!empty($request['buffalo_buy']) && !empty($request['buffalo_buy_count']) && !empty($request['buffalo_buy_amount'])){
            $c = [
                'animal_type'=>$request['buffalo_buy'],
                'animal_count'=>$request['buffalo_buy_count'],
                'animal_amount'=>$request['buffalo_buy_amount']
            ];
            $required_asset[3]=$c;
        }
        if(!empty($request['cow_buy']) && !empty($request['cow_buy_count']) && !empty($request['cow_buy_amount'])){
            $d = [
                'animal_type'=>$request['cow_buy'],
                'animal_count'=>$request['cow_buy_count'],
                'animal_amount'=>$request['cow_buy_amount']
            ];
            $required_asset[4]=$d;
        }
        if(!empty($request['goat_buy']) && !empty($request['goat_buy_count']) && !empty($request['goat_buy_amount'])){
            $b = [
                'animal_type'=>$request['goat_buy'],
                'animal_count'=>$request['goat_buy_count'],
                'animal_amount'=>$request['goat_buy_amount']

            ];
            $running_asset[2]=$b;
        }
        if(!empty($request['sheep_buy']) && !empty($request['sheep_buy_count']) && !empty($request['sheep_buy_amount'])){
            $c = [
                'animal_type'=>$request['sheep_buy'],
                'animal_count'=>$request['sheep_buy_count'],
                'animal_amount'=>$request['sheep_buy_amount']
            ];
            $running_asset[3]=$c;
        }
        if(!empty($request['others_buy']) && !empty($request['others_buy_count']) && !empty($request['others_buy_amount'])){
            $d = [
                'animal_type'=>$request['others_buy'],
                'animal_count'=>$request['others_buy_count'],
                'animal_amount'=>$request['others_buy_amount']
            ];
            $running_asset[4]=$d;
        }

        //==================================================================

        $running_capital = \yii\helpers\Json::encode($running_asset);
        $required_capital = \yii\helpers\Json::encode($required_asset);

        $this->application_id=$request['Applications']['id'];

        $this->animal_type=$resources_animal_type;
        $this->business_type=$resources_business_type;

        $this->business_condition=$request['AppraisalsLivestock']['business_condition'];
        $this->business_place=$request['AppraisalsLivestock']['business_place'];
        $this->business_address=$request['AppraisalsLivestock']['business_address'];
        $this->used_land_type=$request['AppraisalsLivestock']['used_land_type'];
        $this->used_land_size=$request['AppraisalsLivestock']['used_land_size'];
        $this->available_amount=$request['AppraisalsLivestock']['available_amount'];
        $this->required_amount=$request['AppraisalsLivestock']['required_amount'];
        $this->monthly_income=$request['AppraisalsLivestock']['monthly_income'];
        $this->expected_income=$request['AppraisalsLivestock']['expected_income'];
        $this->running_capital = $running_capital;

        if(!empty($required_capital)){
            $this->new_assets = $required_capital;
        }else{
            $this->addError('new assets', 'New assets required to fill!');
        }

        $this->longitude=0;
        $this->latitude=0;
        $this->status='pending';
    }
}
