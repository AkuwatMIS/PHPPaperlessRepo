<?php

namespace common\models;

use common\components\Helpers\RecoveriesHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "transactions_cih".
 *
 * @property string $id
 * @property string $type
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property double $amount
 * @property double $tax
 * @property string $account_id
 * @property string $deposit_slip_no
 * @property string $deposit_date
 * @property string $deposited_by
 * @property string $created_by
 * @property string $created
 * @property string $updated
 * @property string $status
 */
class TransactionsCih extends \yii\db\ActiveRecord
{
    public $recovery_ids = array();
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transactions_cih';
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
            [['type'], 'string'],
            [['region_id', 'area_id', 'branch_id', 'amount', 'account_id', 'created_by'], 'required'],
            [['region_id', 'area_id', 'branch_id', 'account_id', 'created_by'], 'integer'],
            [['amount', 'tax'], 'number'],
            [['deposit_date', 'created_at', 'updated_at','status'], 'safe'],
            [['deposit_slip_no', 'deposited_by'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'amount' => 'Amount',
            'tax' => 'Tax',
            'account_id' => 'Account ID',
            'deposit_slip_no' => 'Deposit Slip No',
            'deposit_date' => 'Deposit Date',
            'deposited_by' => 'Deposited By',
            'created_by' => 'Created By',
            'created' => 'Created',
            'updated' => 'Updated',
            'status' => 'Status',
        ];
    }

    public function set_values()
    {

        $a=RecoveriesHelper::getAreaofbranch($this->branch_id);
        $b=RecoveriesHelper::getRegionofbranch($this->branch_id);
        /*echo'<pre>';
        print_r($this);
        die("here");*/
        // $this->area_id=$a;
        $this->area_id=$a[0]['area_id'];
        $this->region_id=$b[0]['region_id'];
        $this->status='New';
        $this->created_by= isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
        //print_r($this->area_id);
        //die("here");
        //  $this->created_on = date('Y-m-d h:i:s');
        /*echo '<pre>';
        print_r($this->errors);
        die();*/
    }
    public function getCihs()
    {
        return $this->hasMany(CihTransactionsMapping::className(), ['transaction_id' => 'id']);
    }
    public function getRecoveries()
    {
        return $this->hasMany(Recoveries::className(), ['transaction_id' => 'id']);
    }
    public function getBranch(){
        return $this->hasOne(Branches::className(),['id'=>'branch_id']);
    }
}
