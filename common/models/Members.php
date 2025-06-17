<?php

namespace common\models;

use common\components\Helpers\ConfigHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "members".
 *
 * @property int $id
 * @property string $full_name
 * @property string $parentage
 * @property string $parentage_type
 * @property string $cnic
 * @property string $gender
 * @property string $dob
 * @property string $education
 * @property string $marital_status
 * @property string $family_no
 * @property string $family_head
 * @property string $family_member_name
 * @property string $family_member_cnic
 * @property string $religion
 * @property string $birthplace
 * @property string $profile_pic
 * @property string $referral_id
 * @property int $status
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications[] $applications
 * @property MembersAddress[] $membersAddresses
 * @property MembersAccount[] $membersAccounts
 * @property MembersEmail[] $membersEmails
 * @property MembersLogs[] $membersLogs
 * @property MembersPhone[] $membersPhones
 * @property MemberInfo[] $memberInfo
 */
class Members extends \yii\db\ActiveRecord
{
    public $cnic_back;
    public $cnic_front;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['deleted','cnic','status','region_id','area_id','branch_id', 'team_id', 'field_id',],
                'table' => "members_logs",
                //'ignored' => ['updated_at'],
            ]/*,
            'ConfigsBehavior' => [
                'class' => 'common\behavior\ConfigsBehavior',
            ]*/
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['family_member_cnic','full_name', 'parentage', 'parentage_type', 'cnic', 'gender', 'dob', 'education', 'marital_status', 'religion','is_disable','team_id', 'field_id'], 'required'],
            [['platform','region_id', 'area_id', 'branch_id', 'team_id', 'field_id','assigned_to', 'created_by','deleted', 'updated_by','is_disable', 'deleted_at','deleted_by'], 'integer'],
            [['birthplace','full_name', 'parentage', 'parentage_type', 'family_no', 'family_member_name'], 'string', 'max' => 50],
            [['cnic', 'education'], 'string', 'max' => 20],
            [['disability_type'], 'string', 'max' => 30],
            [['gender'], 'string', 'max' => 6],
            [['marital_status'/*,'status'*/], 'string', 'max' => 10],
            [['religion'], 'string', 'max' => 25],
            [['family_member_cnic'], 'string', 'max' => 15],
            [['profile_pic'], 'string', 'max' => 100],
            [['cnic_back','cnic_front','left_thumb','right_thumb'], 'string'],
           /* [['status'], 'string', 'max' => 3],
            [['deleted'], 'string', 'max' => 1],*/
            [['cnic'], 'match', 'pattern' => "/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/i"],
            [['family_member_cnic'], 'match', 'pattern' => "/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/i"],
            [['full_name','parentage','family_member_name'], 'match', 'pattern' => "/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/"],
           // [['cnic', 'family_member_cnic'], 'match', 'pattern' => "/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/i"],
            ['gender', 'in', 'range' => ['m', 'f', 't']],
            ['dob', 'validateDobTrim'],
            ['dob', 'validateDobConvertToIneger'],
            ['dob', 'validateAge'],

            [['cnic'], 'unique'],
            [['family_member_left_thumb','family_member_right_thumb','referral_id','family_member_cnic'], 'safe'],
        ];
        //['profile_pic', 'image', 'minWidth' => 250, 'maxWidth' => 250,'minHeight' => 250, 'maxHeight' => 250, 'extensions' => 'jpg, gif, png', 'maxSize' => 1024 * 1024 * 2],

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'birthplace' => 'Place Of Birth',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'full_name' => 'Full Name',
            'parentage' => 'Parentage',
            'parentage_type' => 'Parentage Type',
            'cnic' => 'Cnic',
            'gender' => 'Gender',
            'dob' => 'Dob',
            'education' => 'Education',
            'marital_status' => 'Marital Status',
            'family_no' => 'Family No',
            'family_member_name' => 'Family Member Name',
            'family_member_cnic' => 'Family Member Cnic',
            'religion' => 'Religion',
            'profile_pic' => 'Profile Pic',
            'referral_id' => 'Referral ID',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function validateDobTrim($attribute){

        $this->dob = str_replace(' ', '', $this->dob);
    }
    public function validateAge($attribute){

        /*$min = strtotime('+18 years', $this->dob);
        $max = strtotime('+62 years', $this->dob);
        if(time() < $min)  {
            $this->addError('dob','Your age is less then minimum limit of 18 years.');
        }
        else if(!empty($max) && time() > $max){
            $this->addError('dob','Your age is greater then maximam limit of 62 years.');
        }*/

        
        /*$age= ((time() - ($this->dob)) / (60*60*24*365));
        if($age<18){
            $this->addError('dob','Your age is less then minimum limit of 18 years.');
        }
        else if($age>60){
            $this->addError('dob','Your age is greater then maximam limit of 60 years.');
        }*/
    }
    public function validateDobConvertToIneger($attribute){
        if(!is_numeric($this->dob)) {
            $this->dob = strtotime($this->dob);
        }
    }

    public function beforeSave($insert)
    {

        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                /*if($this->branch_id == 814)
                {
                    $cnic = "00000-".mt_rand(1000000, 9999999)."-".rand(0,9);
                    while(true){
                        $member = Members::find()->select(['id'])->where(['cnic'=> $cnic])->one();
                        if($member){
                            $cnic = "00000-".mt_rand(1000000, 9999999)."-".rand(0,9);
                        }else{
                            $this->cnic = $cnic;
                            break;
                        }
                    }
                }*/
                /*$branch = StructureHelper::getBranches($this->branch_id);
                $this->region_id = isset($branch->region_id) ? $branch->region_id : 0;
                $this->area_id = isset($branch->area_id) ? $branch->area_id : 0;*/
                $this->status = isset($this->status) ? $this->status : "approved";
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                if ($this->isAttributeChanged('dob')) {
                    $old_db = $this->getOldAttribute('dob');
                    if($old_db != $this->dob){
                        $memLogs = new MembersLogs();
                        $memLogs->id = $this->id;
                        $memLogs->old_value = (string)$old_db;
                        $memLogs->new_value = (string)$this->dob;
                        $memLogs->action = 'CHANGE';
                        $memLogs->field = 'dob';
                        $memLogs->stamp = time();
                        $memLogs->user_id = Yii::$app->user->getId();
                        if(!$memLogs->save()){
                            var_dump($memLogs->getErrors());
                            die();
                        }
                    }

                }
                if ($this->isAttributeChanged('family_member_name')) {
                    $old_family_member_name = $this->getOldAttribute('family_member_name');
                    if($old_family_member_name != $this->family_member_name){
                        $memLogs = new MembersLogs();
                        $memLogs->id = $this->id;
                        $memLogs->old_value = (string)$old_family_member_name;
                        $memLogs->new_value = (string)$this->family_member_name;
                        $memLogs->action = 'CHANGE';
                        $memLogs->field = 'family_member_name';
                        $memLogs->stamp = time();
                        $memLogs->user_id = Yii::$app->user->getId();
                        if(!$memLogs->save()){
                            var_dump($memLogs->getErrors());
                            die();
                        }
                    }

                }
                if ($this->isAttributeChanged('family_member_cnic')) {
                    $old_family_member_cnic = $this->getOldAttribute('family_member_cnic');
                    if($old_family_member_cnic != $this->family_member_cnic){
                        $memLogs = new MembersLogs();
                        $memLogs->id = $this->id;
                        $memLogs->old_value = (string)$old_family_member_cnic;
                        $memLogs->new_value = (string)$this->family_member_cnic;
                        $memLogs->action = 'CHANGE';
                        $memLogs->field = 'family_member_cnic';
                        $memLogs->stamp = time();
                        $memLogs->user_id = Yii::$app->user->getId();
                        if(!$memLogs->save()){
                            var_dump($memLogs->getErrors());
                            die();
                        }
                    }

                }

                if($this->status != "approved")
                {
                    $this->status = "approved";
                }
                $this->updated_by =  isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['member_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembersAddresses()
    {
        return $this->hasMany(MembersAddress::className(), ['member_id' => 'id'])->andOnCondition(['members_address.is_current'=>1,'members_address.deleted'=>0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembersEmails()
    {
        return $this->hasMany(MembersEmail::className(), ['member_id' => 'id'])->andOnCondition(['members_email.deleted'=>0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembersLogs()
    {
        return $this->hasMany(MembersLogs::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getMembersPhones()
    {
        return $this->hasMany(MembersPhone::className(), ['member_id' => 'id'])->andOnCondition(['members_phone.is_current' => 1,'members_phone.deleted'=>0]);
    }

    public function getMembersAddress()
    {
        return $this->hasOne(MembersAddress::className(), ['member_id' => 'id'])->select(['address','address_type','member_id'])->andOnCondition(['members_address.is_current'=>1,'members_address.deleted'=>0]);
    }

    public function getMemberInfo()
    {
        return $this->hasOne(MemberInfo::className(), ['member_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembersEmail()
    {
        return $this->hasOne(MembersEmail::className(), ['member_id' => 'id'])->select(['email','member_id'])->andOnCondition(['members_email.is_current'=>1,'members_email.deleted'=>0]);
    }

    public function getMembersPhone()
    {
        return $this->hasOne(MembersPhone::className(), ['member_id' => 'id'])->select(['phone','phone_type','member_id'])->andOnCondition(['members_phone.is_current'=>1,'members_phone.deleted'=>0]);
    }
    public function getMembersMobile()
    {
        return $this->hasOne(MembersPhone::className(), ['member_id' => 'id'])->select(['phone','phone_type','member_id'])->andOnCondition(['members_phone.is_current'=>1,'phone_type'=>'mobile','members_phone.deleted'=>0]);
    }
    public function getMembersPtcl()
    {
        return $this->hasOne(MembersPhone::className(), ['member_id' => 'id'])->select(['phone','phone_type','member_id'])->andOnCondition(['members_phone.is_current'=>1,'phone_type'=>'phone','members_phone.deleted'=>0]);
    }

    public function getHomeAddress()
    {
        return $this->hasOne(MembersAddress::className(), ['member_id' => 'id'])->select(['address','address_type','member_id'])->andOnCondition(['members_address.is_current'=>1,'address_type'=>'home','members_address.deleted'=>0]);
    }

    public function getBusinessAddress()
    {
        return $this->hasOne(MembersAddress::className(), ['member_id' => 'id'])->select(['address','address_type','member_id'])->andOnCondition(['members_address.is_current'=>1,'address_type'=>'business','members_address.deleted'=>0]);
    }
    public function getMembersAccount()
    {
        return $this->hasMany(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.is_current' => 1,'members_account.deleted'=>0]);
    }
    public function getMemberAccount()
    {
        return $this->hasOne(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.is_current' => 1,'members_account.deleted'=>0/*,'members_account.status'=>0*/]);
    }
    public function getMembersAccounts()
    {
        return $this->hasOne(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.is_current' => 1,'members_account.deleted'=>0/*,'members_account.status'=>0*/]);
    }
    public function getVerifiedAccount()
    {
        return $this->hasOne(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.is_current' => 1,'members_account.deleted'=>0,'members_account.status'=>1]);
    }
    public function getVerifiedAccount1()
    {
        return $this->hasOne(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.is_current' => 1,'members_account.deleted'=>0]);
    }
    public function getMembersAccountAll()
    {
        return $this->hasMany(MembersAccount::className(), ['member_id' => 'id'])->andOnCondition(['members_account.deleted'=>0]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
    public function getApplicationsCount(){
        return $this->hasMany(Applications::className(), ['member_id' => 'id'])->count();
    }
    public function getInfo(){
        return $this->hasOne(MemberInfo::className(), ['member_id' => 'id']);
    }

    public function getNadraDoc()
    {
        return $this->hasOne(Images::className(), ['parent_id' => 'id'])->andOnCondition(['image_type' => 'nadra_document','images.parent_type'=>'members']);
    }

    public function getNadraNonVerisys()
    {
        return $this->hasOne(Images::className(), ['parent_id' => 'id']);
    }
}
