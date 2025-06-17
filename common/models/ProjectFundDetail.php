<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_fund_detail".
 *
 * @property int $id
 * @property int $fund_id
 * @property int $batch_no
 * @property int $project_id
 * @property int $fund_batch_amount
 * @property int $debit
 * @property int $credit
 * @property int $no_of_loans
 * @property int $allocation_date
 * @property string $disbursement_source
 * @property int $status  0=>pending,1=>processed,2=>fund_received
 * @property int $created_at
 * @property int $updated_at
 */
class ProjectFundDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $txn_mode;
    public $txn_no;
    public $received_date;
    public $bank_name;

    public static function tableName()
    {
        return 'project_fund_detail';
    }

    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['batch_no','fund_id','project_id','fund_batch_amount','no_of_loans','allocation_date','status','disbursement_source'],
                'table' => "project_fund_detail_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    public function rules()
    {
        return [
            [['project_id','fund_id', 'fund_batch_amount', 'disbursement_source'], 'required'],
            [['allocation_date','batch_no','debit','credit','received_date'], 'safe'],
            [['project_id', 'fund_batch_amount',  'no_of_loans',  'status', 'created_at', 'updated_at'], 'integer'],
            [['fund_id'], 'exist', 'skipOnError' => true, 'targetClass' => Funds::className(), 'targetAttribute' => ['fund_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'fund_received' => 'Fund Received',
            'fund_balance' => 'Fund Balance',
            'no_of_loans' => 'No Of Loans',
            'disbursement_source' => 'Disbursement Source',
            'batch_no' => 'Batch No',
            'debit' => 'Debit Amount',
            'credit' => 'Credit Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'received_date' => 'Received Date',
            'updated_at' => 'Updated At',
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

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    public function getFund()
    {
        return $this->hasOne(Funds::className(), ['id' => 'fund_id']);
    }

    public function getTransaction()
    {
        return $this->hasOne(Transactions::className(), ['parent_id' => 'id']);
    }


}
