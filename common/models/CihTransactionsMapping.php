<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cih_transactions_mapping".
 *
 * @property int $id
 * @property int $cih_type_id
 * @property int $transaction_id
 * @property string $type
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Transactions $transaction
 */
class CihTransactionsMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cih_transactions_mapping';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cih_type_id', 'transaction_id', 'type'], 'required'],
            [['cih_type_id', 'transaction_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['type'], 'string', 'max' => 10],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transactions::className(), 'targetAttribute' => ['transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cih_type_id' => 'Cih Type ID',
            'transaction_id' => 'Transaction ID',
            'type' => 'Type',
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

    public function getRecovery(){
        return $this->hasOne(Recoveries::className(),['id'=>'cih_type_id']);
    }
    public function getDonation(){
        return $this->hasOne(Donations::className(),['id'=>'cih_type_id']);
    }
    public function getCihtype(){
        return $this->hasOne(Donations::className(),['id'=>'cih_type__id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transactions::className(), ['id' => 'transaction_id']);
    }
}
