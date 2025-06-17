<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "operations".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property int $operation_type_id
 * @property string $credit
 * @property string $form_no
 * @property string $receipt_no
 * @property string $receive_date
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property int $transaction_id
 * @property int $project_id
 * @property int $region_id
 * @property int $area_id
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications $application
 * @property Areas $area
 * @property Branches $branch
 * @property Loans $loan
 * @property Projects $project
 * @property Regions $region
 * @property Transactions $transaction
 * @property OperationsLogs[] $operationsLogs
 */
class Operations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operations';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['credit'],
                'table' => "operations_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'operation_type_id', 'credit', 'branch_id', 'team_id', 'field_id', 'region_id', 'area_id'], 'required'],
            [['platform','application_id', 'loan_id', 'operation_type_id', 'receive_date', 'branch_id', 'team_id', 'field_id', 'project_id', 'region_id', 'area_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','deleted_at', 'deleted_by'], 'integer'],
            [['credit'], 'number'],
            [['receipt_no'], 'string', 'max' => 25],
            //[['deleted'], 'string', 'max' => 1],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            //['receipt_no', 'validateReceipt'],
            //['receipt_no', 'unique', 'message'=>'Duplicate Receipt No'],
            [['receipt_no'], 'unique', 'targetAttribute' => ['receipt_no','operation_type_id','branch_id'],'message' => 'Duplicate Receipt No.'],
            /*[['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loan_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'loan_id' => 'Loan ID',
            'operation_type_id' => 'Operation Type ID',
            'credit' => 'Credit',
            'receipt_no' => 'Receipt No',
            'receive_date' => 'Receive Date',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'project_id' => 'Project ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
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

    public function load_values($application){
        $this->branch_id = $application->branch_id;
        $this->team_id = $application->team_id;
        $this->field_id = $application->field_id;
        $this->project_id = $application->project_id;
        $this->region_id = $application->region_id;
        $this->area_id = $application->area_id;
        $this->application_id = $application->id;
        $this->credit = $application->fee;
        //$this->receipt_no = (string)rand(99, 9999999);
        $this->receipt_no = self::get_receipt_no();
        $this->operation_type_id = 1;
        $this->receive_date = $application->created_at;
    }
    public function get_receipt_no()
    {
        $receipt_no = (string)rand(99, 99999999);
        while(true){
            $operation = Operations::find()->select(['id'])->where(['receipt_no'=> $receipt_no])->one();
            if($operation){
                $receipt_no = (string)rand(99, 99999999);
            }else{
                $this->receipt_no = $receipt_no;
                break;
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
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
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
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
    public function getOperationsLogs()
    {
        return $this->hasMany(OperationsLogs::className(), ['id' => 'id']);
    }
}
