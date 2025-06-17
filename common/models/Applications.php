<?php

namespace common\models;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ConfigHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "applications".
 *
 * @property int $id
 * @property int $member_id
 * @property string $fee
 * @property string $application_no
 * @property int $project_id
 * @property string $project_table
 * @property int $activity_id
 * @property int $product_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property int $no_of_times
 * @property string $bzns_cond
 * @property string $who_will_work
 * @property string $name_of_other
 * @property string $other_cnic
 * @property string $req_amount
 * @property int $status
 * @property int $is_biometric
 * @property int $is_urban
 * @property string $reject_reason
 * @property int $is_lock
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $applicant_property_id
 * @property int $is_pledged
 *
 * @property Activities $activity
 * @property Areas $area
 * @property Branches $branch
 * @property Members $member
 * @property NadraVerisys $nadra
 * @property Products $product
 * @property Projects $project
 * @property Regions $region
 * @property ApplicationsLogs[] $applicationsLogs
 * @property BaBusinessExpenses[] $baBusinessExpenses
 * @property BaExistingInvestment[] $baExistingInvestments
 * @property BaFixedBusinessAssets[] $baFixedBusinessAssets
 * @property BaRequiredAssets[] $baRequiredAssets
 * @property AppraisalsBusiness[] $businessAppraisals
 * @property Donations[] $donations
 * @property Loans[] $loans
 * @property Operations[] $operations
 * @property ProjectsDisabled[] $ProjectsDisabled
 * @property ProjectsSidb[] $ProjectsSidb
 * @property ProjectsTevta[] $ProjectsTevta
 * @property Recoveries[] $recoveries
 * @property Schedules[] $schedules
 * @property Projects[] $projects
 * @property SocialAppraisalCopy[] $socialAppraisals
 * @property AppraisalsLivestock[] $appraisalsLivestock
 * @property MemberInfo[] $memberInfo
 * @property ApplicationDetails[] $applicationDetails
 */
class Applications extends \yii\db\ActiveRecord
{
    public $grp_no;
    public $type;
    public $appraisal_id;
    public $name;
    public $bank_name;
    public $project_name;
    public $account_no;
    public $title;
    public $parentage;
    public $cnic;
    public $visit_count;
    public $image_count;
    public $coordinates;
    public $poverty_index;
    public $nadra_verisys;
    public $visit_id;
    public $is_shifted;
    public $last_action_at;

    public static function tableName()
    {
        return 'applications';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'member_id', 'product_id', 'activity_id', 'project_id', 'application_no', 'req_amount', 'status', 'reject_reason', 'group_id', 'who_will_work', 'name_of_other', 'other_cnic', 'client_contribution', 'recommended_amount', 'referral_id'],
                'table' => "applications_logs",
                //'ignored' => ['updated_at'],
            ]/*,'ConfigsBehavior' => [
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
            [['member_id', 'application_no', 'project_id', 'product_id', 'activity_id', 'region_id', 'area_id', 'branch_id', 'no_of_times', 'req_amount', 'is_urban', 'team_id', 'field_id'], 'required'],
            [['platform', 'member_id', 'project_id', 'activity_id', 'product_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'group_id', 'assigned_to', 'created_by', 'updated_by', 'deleted', 'no_of_times', 'is_urban', 'is_lock', 'client_contribution'], 'integer'],
            [['fee', 'req_amount', 'recommended_amount'], 'number'],
            [['activity_id'], 'default', 'value' => '0'],
            [['reject_reason', 'applicant_property_id', 'sub_activity'], 'string'],
            [['application_no', 'other_cnic', 'status'], 'string', 'max' => 15],
            [['project_table'], 'string', 'max' => 50],
            [['grp_no', 'application_date','nadra_verisys','is_pledged','visit_id','is_shifted','last_action_at'], 'safe'],
            //[['no_of_times', 'status', 'is_urban', 'is_lock'], 'string', 'max' => 3],
            [['bzns_cond'], 'string', 'max' => 20],
            [['who_will_work'], 'string', 'max' => 30],
            [['name_of_other'], 'string', 'max' => 25],
            //[['deleted'], 'string', 'max' => 1],
            //[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activities::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            ['req_amount', 'validateRequestedAmount'],
            ['project_id', 'validateProject'],
            ['application_date', 'validateApplicationDate'],
            ['application_no', 'validateApplicationNo'],
            [['comments', 'is_biometric', 'referral_id'], 'safe'],
            [['other_cnic'], 'match', 'pattern' => "/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/i"],
            [['who_will_work'], 'match', 'pattern' => "/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/"],
            ['applicant_property_id', 'required', 'when' => function ($model) {
                return $model->project_id == 132;
                        }, 'whenClient' => "function (attribute, value) {
                return $('#applications-applicant_property_id').val() == 132;
            }"]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'fee' => 'Fee',
            'application_no' => 'Application No',
            'project_id' => 'Project ID',
            'project_table' => 'Project Table',
            'activity_id' => 'Activity ID',
            'product_id' => 'Product ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'group_id' => 'Group ID',
            'no_of_times' => 'No Of Times',
            'bzns_cond' => 'Bzns Cond',
            'who_will_work' => 'Who Will Work',
            'name_of_other' => 'Name Of Other',
            'other_cnic' => 'Other Cnic',
            'req_amount' => 'Req Amount',
            'status' => 'Status',
            'is_biometric' => 'Is Biometric',
            'is_urban' => 'Is Urban',
            'reject_reason' => 'Reject Reason',
            'is_lock' => 'Is Lock',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'referral_id' => 'Referred By',
            'applicant_property_id' => 'Applicant Property',
            'is_pledged' => 'Referred By',
            'last_action_at' => 'Last Action Taken Date'
        ];
    }

    public function validateRequestedAmount($attribute)
    {
        $project_amount = Projects::find()->select('loan_amount_limit')->where(['id' => $this->project_id])->one();
        if ($this->req_amount > $project_amount->loan_amount_limit) {
            $this->addError('req_amount', 'Requested Amount is greater then project limit');

        }
    }

    public function validateProject($attribute)
    {

        /*$min = strtotime('+18 years', $this->member->dob);
           $max = strtotime('+62 years', $this->member->dob);
           if($this->project_id==1){
               $max = strtotime('+62 years', $this->member->dob);
           }else{
               $max = strtotime('+60 years', $this->member->dob);
           }
           if(!empty($max) && time() > $max){
              $this->addError('dob','Your age is greater then maximam limit.');
           }*/

    }

    public function validateApplicationNo($attribute)
    {
        if ($this->isNewRecord) {

            $app_no = self::find()->select('application_no')->where(['application_no' => $this->application_no, 'deleted' => '0'])->andWhere(['area_id' => $this->area_id])->one();
            if ($app_no) {
                $this->addError('application_no', 'Duplicate Application No.');
            }
        } else {
            $app_no = self::find()->select('application_no')->where(['application_no' => $this->application_no, 'deleted' => '0'])->andWhere(['area_id' => $this->area_id])->count();
            if ($app_no > 1) {
                $this->addError('application_no', 'Duplicate Application No.');
            }
        }
    }

    public function validateApplicationDate($attribute)
    {
        /*if($this->platform != 2 && !is_numeric($this->application_date)) {
            $this->application_date = strtotime($this->application_date);
        }*/
        if ($this->isNewRecord) {
            if ($this->platform == 2) {
                $this->application_date = strtotime($this->application_date);
            }
            if ($this->platform != 2 && !is_numeric($this->application_date)) {
                $this->application_date = strtotime($this->application_date);
            }
            $currect_date = strtotime("now");
            $third_of_every_month = strtotime('+2 days', strtotime("now first day of this month"));

            /*if($currect_date > $third_of_every_month){
                $last_months = strtotime('now last day of last month');
            }else{
                $last_months = strtotime('now first day of last month');
                $last_months = strtotime('+19 days',strtotime("now first day of last month"));
            }*/

            if ($this->application_date > $currect_date) {
                $this->addError('application_date', 'You cannot post Application in future date.');
            }
//            $last_months = strtotime('-30 days',strtotime("now"));
//            if($this->project_id!=3){
//                if($this->application_date < $last_months){
//                    $this->addError('application_date','You cannot post Application before 30 days.');
//                }
//            }
        } elseif (!is_numeric($this->application_date)) {
            $this->application_date = strtotime($this->application_date);
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "pending";
                $this->is_lock = isset($this->is_lock) ? $this->is_lock : 0;
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                if (in_array($this->project_id, StructureHelper::trancheProjectsExclude()) && empty(ApplicationActions::findOne(['parent_id' => $this->id, 'action' => 'housing_appraisal']))) {

                    $this->status = "pending";
                    ApplicationActions::deleteAll(['parent_id' => $this->id]);
                    if (isset($this->socialAppraisal)) {
                        AppraisalsSocial::findOne(['application_id' => $this->id])->delete();
                    }
                    if (isset($this->agricultureAppraisal)) {
                        AppraisalsAgriculture::findOne(['application_id' => $this->id])->delete();
                    }
                    if (isset($this->businessAppraisal)) {
                        AppraisalsBusiness::findOne(['application_id' => $this->id])->delete();
                    }
                    if ($this->group_id > 0) {
                        $this->group_id = 0;
                        Groups::findOne(['id' => $this->group_id])->delete();
                    }
                    ActionsHelper::insertActions('application', $this->project_id, $this->id, $this->created_by);
                }
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->isNewRecord && in_array($this->project_id,[106,121,131])) {
            if ($this->project_id == 106 && $this->product_id == 15) {
                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'agriculture_appraisal';
                $model->save();

            } elseif (in_array($this->project_id,[106,121,131]) && $this->product_id == 14) {
                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'livestock_appraisal';
                $model->save();
            } elseif (in_array($this->project_id,[106,121,131]) && in_array($this->product_id,[1,13,16])) {
                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'business_appraisal';
                $model->save();
            }
        } else {
            if (in_array($this->project_id,[106,121,131]) && $this->product_id == 15 && $this->status == 'pending') {
                \Yii::$app
                    ->db
                    ->createCommand()
                    ->delete('application_actions', ['parent_id' => $this->id])
                    ->execute();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'agriculture_appraisal';
                $model->save();
            } elseif (in_array($this->project_id,[106,121,131]) && $this->product_id == 14 && $this->status == 'pending') {
                \Yii::$app
                    ->db
                    ->createCommand()
                    ->delete('application_actions', ['parent_id' => $this->id])
                    ->execute();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'livestock_appraisal';
                $model->save();
            } elseif (in_array($this->project_id,[106,121,131]) && in_array($this->product_id, [1,13,16]) && $this->status == 'pending') {
                \Yii::$app
                    ->db
                    ->createCommand()
                    ->delete('application_actions', ['parent_id' => $this->id])
                    ->execute();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'family_member_info';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'social_appraisal';
                $model->save();

                $model = new ApplicationActions();
                $model->parent_id = $this->id;
                $model->user_id = $this->created_by;
                $model->action = 'business_appraisal';
                $model->save();
            }else {
                parent::afterSave($insert, $changedAttributes);
                ActionsHelper::insertActions('application', $this->project_id, $this->id, $this->created_by, 0, $this->product_id);
            }
        }
    }

    public function set_application_hierarchy($user)
    {
        $this->region_id = isset($user->region->obj_id) ? $user->region->obj_id : 0;
        $this->area_id = isset($user->area->obj_id) ? $user->area->obj_id : 0;
        $this->branch_id = isset($user->branch->obj_id) ? $user->branch->obj_id : 0;
        $this->team_id = isset($user->team->obj_id) ? $user->team->obj_id : 0;
        $this->field_id = isset($user->field->obj_id) ? $user->field->obj_id : 0;
    }

    public function get_application_no()
    {
        $application_no = (string)rand(99, 99999999);
        while (true) {
            $application = Applications::find()->select(['id'])->where(['application_no' => $application_no])->one();
            if ($application) {
                $application_no = (string)rand(99, 99999999);
            } else {
                $this->application_no = $application_no;
                break;
            }
        }
    }

    public function set_application_project()
    {
        $project = Projects::find()->select(['project_table'])->where(['id' => $this->project_id, 'status' => 1])->one();
        if (isset($project) || !empty($project)) {
            $this->project_table = $project['project_table'];
        } else {
            $this->project_table = null;
        }
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }

    public function getNadra()
    {
        return $this->hasOne(NadraVerisys::className(), ['application_id' => 'id']);
    }

    public function getApplicationDetails()
    {
        return $this->hasOne(ApplicationDetails::className(), ['parent_id' => 'id'])->andOnCondition(['application_details.parent_type' => 'application'])->select(['is_shifted']);
    }

    public function getPmtStatus()
    {
        return $this->hasOne(ApplicationDetails::className(), ['application_id' => 'id'])->andOnCondition(['application_details.parent_type' => 'member']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplicationsLogs()
    {
        return $this->hasMany(ApplicationsLogs::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaBusinessExpenses()
    {
        return $this->hasMany(BaBusinessExpenses::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaExistingInvestments()
    {
        return $this->hasMany(BaExistingInvestment::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaFixedBusinessAssets()
    {
        return $this->hasMany(BaFixedBusinessAssets::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaRequiredAssets()
    {
        return $this->hasMany(BaRequiredAssets::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBusinessAppraisal()
    {
        return $this->hasOne(AppraisalsBusiness::className(), ['application_id' => 'id']);
    }

    public function getHousingAppraisal()
    {
        return $this->hasOne(AppraisalsHousing::className(), ['application_id' => 'id']);
    }

    public function getAppraisalsLivestock()
    {
        return $this->hasOne(AppraisalsLivestock::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonations()
    {
        return $this->hasMany(Donations::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['application_id' => 'id'])->andOnCondition(['loans.deleted' => '0']);
    }

    public function getPendingLoan()
    {
        return $this->hasOne(Loans::className(), ['application_id' => 'id'])->andOnCondition(['loans.deleted' => '0'])->andOnCondition(['loans.status' => 'pending']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operations::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsDisabled()
    {
        return $this->hasMany(ProjectsDisabled::className(), ['application_id' => 'id']);
    }

    public function getProjectsSidb()
    {
        return $this->hasMany(ProjectsSidb::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsTevta()
    {
        return $this->hasMany(ProjectsTevta::className(), ['application_id' => 'id']);
    }

    public function getProjectsAgriculture()
    {
        return $this->hasMany(ProjectsAgriculture::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecoveries()
    {
        return $this->hasMany(Recoveries::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedules::className(), ['application_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialAppraisal()
    {
        return $this->hasOne(SocialAppraisal::className(), ['application_id' => 'id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }

    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    public function getprojects()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
    }

    public function getActions()
    {
        return $this->hasMany(ApplicationActions::className(), ['parent_id' => 'id']);
    }

    public function getLoansCount()
    {
        return $this->hasMany(Loans::className(), ['application_id' => 'id'])->andOnCondition(['loans.deleted' => '0'])->count();
    }

    public function getVisitsCount()
    {
        return $this->hasMany(Visits::className(), ['parent_id' => 'id'])->count();
    }

    public function getVisits()
    {
        return $this->hasMany(Visits::className(), ['parent_id' => 'id'])->andOnCondition(['visits.deleted' => '0']);
    }

    public function getReferral()
    {
        return $this->hasOne(Referrals::className(), ['id' => 'referral_id']);
    }

    public function getLastVisit()
    {
        return $this->hasOne(Visits::className(), ['parent_id' => 'id'])->orderBy('visits.id desc')->andOnCondition(['visits.deleted' => '0']);
    }

    public function getAppraisalsSocial()
    {
        return $this->hasOne(AppraisalsSocial::className(), ['application_id' => 'id']);
    }

    public function getAppraisalsHousing()
    {
        return $this->hasOne(AppraisalsHousing::className(), ['application_id' => 'id']);
    }

    public function getAppraisalsBusiness()
    {
        return $this->hasOne(AppraisalsBusiness::className(), ['application_id' => 'id']);
    }

    public function getAgricultureAppraisal()
    {
        return $this->hasOne(AppraisalsAgriculture::className(), ['application_id' => 'id']);
    }

    public function getAppraisalsAgriculture()
    {
        return $this->hasOne(AppraisalsAgriculture::className(), ['application_id' => 'id']);
    }

    public function getEmergencyAppraisal()
    {
        return $this->hasOne(AppraisalsEmergency::className(), ['application_id' => 'id']);
    }

    public function getCibFile()
    {
        return $this->hasOne(FilesApplication::className(), ['application_id' => 'id']);
    }

    public function getCib()
    {
        return $this->hasOne(ApplicationsCib::className(), ['application_id' => 'id']);
    }

    public function getCibstatus()
    {
        return $this->hasOne(ApplicationsCib::className(), ['application_id' => 'id'])->andOnCondition(['applications_cib.status' => '1']);
    }

    public function getMemberAccount()
    {
        return $this->hasOne(MembersAccount::className(), ['member_id' => 'member_id'])->andOnCondition(['members_account.is_current' => 1, 'members_account.deleted' => 0/*,'members_account.status'=>0*/]);
    }

    public function getMemberInfo()
    {
        return $this->hasOne(MemberInfo::className(), ['member_id' => 'member_id']);
    }
}
