<?php

namespace common\models;

use common\components\Helpers\StringHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "disbursements".
 *
 * @property int $id
 * @property string $date_disbursed
 * @property string $venue
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Disbursements extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'disbursements';
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
            [['venue','branch_id'], 'required'],
            [['platform','assigned_to', 'created_by', 'updated_by','region_id', 'area_id', 'branch_id'], 'integer'],
            [['venue','date_disbursed'], 'string', 'max' => 255],
            ['date_disbursed', 'validateDateDisbursedConvertToIneger'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_disbursed' => 'Date Disbursed',
            'venue' => 'Venue',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function validateDateDisbursedConvertToIneger($attribute){
        $this->date_disbursed = strtotime($this->date_disbursed);
        if($this->isNewRecord){
            $currect_date = strtotime("now");
            //$third_of_every_month = strtotime('+3 days',strtotime("now first day of this month"));
           // $third_of_every_month = strtotime("now first day of this month");
            $third_of_every_month = "1601550907";
            //$third_of_every_month = '1562111999';

            if($currect_date > $third_of_every_month){
                $last_months = strtotime('now last day of last month');
               // $last_months = strtotime('+15 days',strtotime("now first day of last month"));
            }else{
                $last_months = strtotime('now first day of last month');
                $last_months = strtotime('+13 days',strtotime("now first day of last month"));
                //$last_months = strtotime('+15 days',strtotime("now first day of last month"));
            }
            if($this->date_disbursed > $currect_date){
                $this->addError('date_disbursed','You cannot post Disbursement in future date.');
            }
            if($this->date_disbursed <= $last_months){
                $this->addError('date_disbursed','You cannot post Disbursement in previous month.');
            }
        }
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                if(isset($this->platform)){
                    $this->date_disbursed = strtotime(date('Y-m-d'));
                }
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
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
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
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['disbursement_id' => 'id']);
    }
    public function getTranches()
    {
        return $this->hasMany(LoanTranches::className(), ['disbursement_id' => 'id']);
    }
}
