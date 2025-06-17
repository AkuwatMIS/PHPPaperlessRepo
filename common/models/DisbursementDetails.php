<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "disbursement_details".
 *
 * @property int $id
 * @property int $tranche_id
 * @property string $bank_name
 * @property string $account_no
 * @property string $transferred_amount
 * @property int $disbursement_id
 * @property int $status
 * @property int $response_code
 * @property string $response_description
 * @property int $bank_disb_date
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class DisbursementDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $branch_id;
    public $region_id;
    public $area_id;
    public $project_id;
    public $sanction_no;
    public $date_disbursed;
    public $cnic;
    public $title;
    public $type; //for multiselect in fund-allocation
    public $activity_id; //for multiselect in fund-allocation
    public $pmt;
    public $province_id;
    public $district_id;
    public $age;
    public $gender;

    public static function tableName()
    {
        return 'disbursement_details';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['tranche_id','bank_name','account_no','transferred_amout','disbursement_id','status','response_code','response_description','payment_method_id'],
                'table' => "disbursement_details_logs",
                //'ignored' => ['updated_at'],
            ]/*,
            'ConfigsBehavior' => [
                'class' => 'common\behavior\ConfigsBehavior',
            ]*/
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tranche_id', 'bank_name', 'account_no', 'transferred_amount', 'disbursement_id',/* 'created_by', 'created_at', 'updated_at'*/], 'required'],
            [['activity_id','tranche_id', 'disbursement_id', 'status', 'response_code', 'created_by', 'updated_by', 'updated_at', 'deleted','payment_method_id'], 'integer'],
            [['transferred_amount'], 'number'],
            [['response_description'], 'string'],
            [['bank_name'], 'string', 'max' => 20],
            [['account_no'], 'string', 'max' => 30],
            [['pmt','branch_id','area_id','region_id','sanction_no','date_disbursed','title','new_value','old_value', 'created_at','batch_id','bank_disb_date','gender','age','district_id','province_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tranche_id' => 'Tranche ID',
            'bank_name' => 'Bank Name',
            'account_no' => 'Account No',
            'transferred_amount' => 'Transferred Amount',
            'disbursement_id' => 'Disbursement ID',
            'status' => 'Status',
            'response_code' => 'Response Code',
            'response_description' => 'Response Description',
            'bank_disb_date' => 'Bank Disbursement Date',
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
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($this->isNewRecord)
        {
            $approval_hierarchy_model = new DisbursementDetailsApprovalActions();
            $approval_hierarchy_model->setValues($this->id, 'published', $this->created_by, 1);
            $approval_hierarchy_model->save();

            $approval_hierarchy_model = new DisbursementDetailsApprovalActions();
            $approval_hierarchy_model->setValues($this->id, 'transaction_complete', $this->created_by);
            $approval_hierarchy_model->save();

            $approval_hierarchy_model = new DisbursementDetailsApprovalActions();
            $approval_hierarchy_model->setValues($this->id, 'transaction_rejected', $this->created_by);
            $approval_hierarchy_model->save();

            $approval_hierarchy_model = new DisbursementDetailsApprovalActions();
            $approval_hierarchy_model->setValues($this->id, 'disbursed', $this->created_by);
            $approval_hierarchy_model->save();

        } else {
            if($this->status == 1) {
                $approval_hierarchy_model = DisbursementDetailsApprovalActions::find()->where(['parent_id' => $this->id, 'action' => 'transaction_complete','status' =>0])->one();
                if(isset($approval_hierarchy_model) && !empty($approval_hierarchy_model)) {
                    $approval_hierarchy_model->user_id = $this->updated_by;
                    $approval_hierarchy_model->status = 1;
                    $approval_hierarchy_model->updated_by = $this->updated_by;
                    $approval_hierarchy_model->save();
                }
            }
            else if($this->status == 2) {
                $approval_hierarchy_model = DisbursementDetailsApprovalActions::find()->where(['parent_id' => $this->id, 'action' => 'transaction_rejected','status' =>0])->one();
                if(isset($approval_hierarchy_model) && !empty($approval_hierarchy_model)) {
                    $approval_hierarchy_model->user_id = $this->updated_by;
                    $approval_hierarchy_model->status = 1;
                    $approval_hierarchy_model->updated_by = $this->updated_by;
                    $approval_hierarchy_model->save();
                }
            }
            else if($this->status == 3) {
                $approval_hierarchy_model = DisbursementDetailsApprovalActions::find()->where(['parent_id' => $this->id, 'action' => 'disbursed','status' =>0])->one();
                if(isset($approval_hierarchy_model) && !empty($approval_hierarchy_model)) {
                    $approval_hierarchy_model->user_id = $this->updated_by;
                    $approval_hierarchy_model->status = 1;
                    $approval_hierarchy_model->updated_by = $this->updated_by;
                    $approval_hierarchy_model->save();
                }
            }

        }
    }

    public function getTranch()
    {
        return $this->hasOne(LoanTranches::className(), ['id' => 'tranche_id']);
    }
    public function getPayment()
    {
        return $this->hasOne(PaymentMethods::className(), ['id' => 'payment_method_id']);
    }
    public function getLogs()
    {
        return $this->hasOne(DisbursementDetailsLogs::className(), ['id' => 'id']);
    }
}
