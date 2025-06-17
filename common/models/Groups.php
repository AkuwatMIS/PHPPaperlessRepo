<?php

namespace common\models;

use common\components\Helpers\ConfigHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property int $is_locked
 * @property int $br_serial
 * @property string $grp_no
 * @property string $group_name
 * @property string $grp_type
 * @property string $co_code_count_temp
 * @property int $status
 * @property string $reject_reason
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Areas $area
 * @property Branches $branch
 * @property Regions $region
 * @property Loans[] $loans
 */
class Groups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            /*'ConfigsBehavior' => [
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
            [['region_id', 'br_serial', 'grp_no',  'grp_type'], 'required'],
            [['platform','region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'br_serial', 'assigned_to', 'created_by', 'updated_by','is_locked','group_size'], 'integer'],
            [['reject_reason', 'status'], 'string'],
            //[['is_locked', 'status'], 'string', 'max' => 3],
            [['grp_no', 'grp_type'], 'string', 'max' => 20],
            [['grp_no'], 'unique','filter' => ['deleted' => 0]],
            [['group_name'], 'string', 'max' => 100],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            //['grp_no', 'validateProject'],
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
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'is_locked' => 'Is Locked',
            'br_serial' => 'Br Serial',
            'grp_no' => 'Grp No',
            'group_name' => 'Group Name',
            'grp_type' => 'Grp Type',
            'group_size' => 'Group Size',
            'status' => 'Status',
            'reject_reason' => 'Reject Reason',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function validateProject($attribute){
        $project_id = $this;
        $session = Yii::$app->session;
        foreach ($session['applications'] as $application)
        {
            $config_rules = ConfigHelper::globalConfigs('group_size','project',$application->project_id);
            if( count($config_rules) > 1 )
            {
                $keys = array_keys($config_rules);
                foreach ($keys as $key)
                {
                    if (strpos($key, 'min') !== false) {
                        $min_value = $config_rules[$key];
                    } else if (strpos($key, 'max') !== false) {
                        $max_value = $config_rules[$key];
                    }
                }
            }

            if($this->group_size < $min_value){
                $this->addError('group_size','Group size less than '. $min_value .' not allowed.');
            }
            if($this->group_size > $max_value){
                $this->addError('group_size','Group size more than '. $max_value .' not allowed.');
            }
        }

    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "pending";
                $this->is_locked = isset($this->is_locked) ? $this->is_locked : 0;
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

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
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
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
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
    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['group_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['group_id' => 'id']);
    }
    public function getActions()
    {
        return $this->hasMany(GroupActions::className(), ['parent_id' => 'id']);
    }
    public function getGuarantors()
    {
        return $this->hasMany(Guarantors::className(), ['group_id' => 'id']);
    }

}
