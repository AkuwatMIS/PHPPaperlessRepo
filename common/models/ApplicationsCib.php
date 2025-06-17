<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "applications_cib".
 *
 * @property int $id
 * @property int $application_id
 * @property string $type
 * @property string $fee
 * @property string $receipt_no
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted
 */
class ApplicationsCib extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $region_id;
    public $area_id;
    public $branch_id;
    public $member_cnic;
    public $project_id;

    public $req_amount;
    public $city_id;
    public $address;
    public $gender;
    public $member_name;
    public $parentage;
    public $app_date;
    public $cib_date;
    public $app_status;
    public $app_no;
    public $dob;

    public static function tableName()
    {
        return 'applications_cib';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'fee', 'receipt_no'], 'required'],
            [['application_id', 'created_at', 'created_by','cib_type_id','status','type','transfered','project_id'], 'integer'],
            [['fee'], 'number'],
            [['receipt_no'], 'string', 'max' => 25],
            [['response','req_amount','city_id','gender','address','app_date','cib_date','app_status','member_name','parentage','app_no','dob'], 'safe'],
            [['file_path'], 'string', 'max' => 50],
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
            'cib_type_id' => 'Type',
            'fee' => 'Fee',
            'receipt_no' => 'Receipt No',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted' => 'Deleted',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }
}
