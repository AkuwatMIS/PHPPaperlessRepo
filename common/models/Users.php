<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $last_login_at
 * @property string $last_login_token
 * @property string $access_token
 * @property string $fullname
 * @property string $father_name
 * @property string $email
 * @property string $image
 * @property string $mobile
 * @property string $joining_date
 * @property string $role
 * @property string $created
 * @property string $modified
 * @property int $designation_id
 * @property int $emp_code
 * @property int $area_id
 * @property int $region_id
 * @property int $branch_id
 * @property int $isblock
 * @property string $reason
 * @property string $block_date
 * @property string $team_name
 * @property int $status
 * @property string $created_on
 * @property string $updated_on
 * @property int $updated_at
 * @property int $created_at
 */
class Users extends ActiveRecord implements IdentityInterface
{
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
    public $region_id;
    public $area_id;
    public $branch_id;
    public $team_id;
    public $field_id;
    public $role_name;

    public $devices_count;
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['password'],
                'table' => "users_logs",
                //'ignored' => ['updated_at'],
            ]
        ];
    }
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fullname', 'mobile'], 'required'],
            //[['username', 'password', 'fullname', 'email',  'status'], 'required'],
            //[['username', 'password', 'auth_key', 'password_hash', 'password_reset_token', 'last_login_token', 'access_token', 'fullname', 'father_name', 'email', 'image', 'mobile', 'joining_date', 'reason', 'team_name'], 'string'],
            //[['last_login_at', 'created', 'modified', 'block_date', 'created_on', 'updated_on'], 'safe'],
            //[['designation_id', 'emp_code', 'area_id', 'region_id', 'branch_id', 'isblock', 'status', 'updated_at', 'created_at'], 'integer'],
            [['password','emp_code'], 'string'],
            [['username','address'], 'safe'],
            [['term_and_condition',/*'joining_date',*/'city_id','is_block','status','do_reset_password','do_complete_profile'], 'integer'],
            [['do_complete_profile'], 'integer'],
            [['fullname', 'mobile'], 'string', 'max' => 50],
            [['father_name'], 'string', 'max' => 60],            [['alternate_email'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['alternate_email','email'], 'email'],
            [['team_name'], 'string', 'max' => 20],
            [['joining_date','cnic','left_thumb_impression','right_thumb_impression'],'safe'],
            [['mobile'], 'match', 'pattern'=>'/^((92)|(0092))\-{0,1}\d{3}\-{0,1}\d{7}$|^\d{11}$|^\d{4}\-\d{7}$/', 'skipOnError'=>true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'username' => 'Username',
            'fullname' => 'Fullname',
            'father_name' => 'Father Name',
            'email' => 'Email',
            'cnic' => 'Cnic',
            'alternate_email' => 'Alternate Email',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'last_login_at' => 'Last Login At',
            'last_login_token' => 'Last Login Token',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'image' => 'Image',
            'mobile' => 'Mobile',
            'joining_date' => 'Joining Date',
            'emp_code' => 'Emp Code',
            'is_block' => 'Is Block',
            'reason' => 'Reason',
            'block_date' => 'Block Date',
            'team_name' => 'Team Name',
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
                $this->status = isset($this->status) ? $this->status : 1;
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
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $access_token = AccessTokens::findOne(['token' => $token]);
        if ($access_token) {
            if ($access_token->expires_at < time()) {
                Yii::$app->api->sendFailedResponse(400,'Access token expired');
            }

            return static::findOne(['id' => $access_token->user_id]);
        } else {
            return (false);
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        /*echo '<pre>';
        print_r(static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]));
        die();*/
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by emp_code
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmpcode($username)
    {
        return static::findOne(['emp_code' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegionNetwork()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['region.obj_type'=>'region']);
    }

    public function getAreaNetwork()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['area.obj_type'=>'area']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranchNetwork()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['branch.obj_type'=>'branch']);
    }

    public function getRegion()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'region']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'area']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id'])->andOnCondition(['user_structure_mapping.obj_type'=>'branch']);
    }

    public function getDivisions()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'division']);
    }

    public function getRegions()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'region']);
    }

    public function getAreas()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'area']);
    }

    public function getBranches()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'branch']);
    }

    public function getTeams()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'team']);
    }

    public function getFields()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'field']);
    }

    public function getProjects()
    {
        return $this->hasMany(UserProjectsMapping::className(), ['user_id' => 'id'])->select(['user_id','project_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'team']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(UserStructureMapping::className(), ['user_id' => 'id'])->select(['user_id','obj_id','obj_type'])->andOnCondition(['user_structure_mapping.obj_type'=>'field']);
    }
    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['created_by' => 'id']);
    }
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['created_by' => 'id']);
    }

    public function getRole()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function getDesignation()
    {
        return $this->hasOne(Designations::className(), ['id' => 'designation_id']);
    }
    public function getRegionname()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getAreaname()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getBranchname()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getTeamname()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }
    public function getFieldname()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }
    public function getAnalytics()
    {
        return $this->hasMany(Analytics::className(), ['user_id' => 'id']);
    }
    public function getStructure()
    {
        return $this->hasMany(UserStructureMapping::className(), ['user_id' => 'id']);
    }
    public static function getCollectorById($id)
    {
        $getRolesByUser = Yii::$app->authManager->getRolesByUser($id);
        $role = '';
        if(isset($getRolesByUser)) {
            foreach ($getRolesByUser as $r) {
                $role = $r->name;
                if($role == 'Collector')
                {
                    break;
                }
            }
            return $role;
        } else {
            return false;
        }
    }
}
