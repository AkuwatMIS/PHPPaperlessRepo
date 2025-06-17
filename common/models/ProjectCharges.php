<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_charges".
 *
 * @property int $id
 * @property int $project_id
 * @property string $allocated_funds
 * @property string $received_funds
 * @property string $total_disbursement
 * @property string $due_amount
 * @property string $received_amount
 * @property string $pending_amount
 * @property int $received_date
 * @property int $status
 */
class ProjectCharges extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];

    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_charges';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'received_amount', 'pending_amount','remaining_amount'], 'required'],
            [['project_id','remaining_amount', 'received_amount', 'pending_amount', 'status'], 'integer'],
            ['received_date', 'dateConvertToIneger'],
            ['request_date', 'requestDateConvertToIneger'],
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
            'allocated_funds' => 'Allocated Funds',
            'received_funds' => 'Received Funds',
            'total_disbursement' => 'Total Disbursement',
            'due_amount' => 'Due Amount',
            'received_amount' => 'Received Amount',
            'pending_amount' => 'Pending Amount',
            'received_date' => 'Received Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    public function dateConvertToIneger($attribute){
        if(!is_numeric($this->received_date)) {
            $this->received_date = strtotime($this->received_date);
        }
    }

    public function requestDateConvertToIneger($attribute){
        if(!is_numeric($this->request_date)) {
            $this->request_date = strtotime($this->request_date);
        }
    }

}
