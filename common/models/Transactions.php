<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $parent_table
 * @property string $txn_mode
 * @property string $txn_no
 * @property int $received_at
 * @property int $batch_id
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 * @property int $status
 * @property int $approved_by
 * @property int $approved_at
 * @property int $deleted
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'parent_table', 'txn_mode', 'txn_no',  'created_by'], 'required'],
            [['parent_id', 'received_at', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status', 'approved_by', 'approved_at', 'deleted'], 'integer'],
            [['parent_table', 'txn_mode', 'txn_no'], 'string', 'max' => 255],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'parent_table' => 'Parent Table',
            'txn_mode' => 'Txn Mode',
            'txn_no' => 'Txn No',
            'received_at' => 'Received At',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'approved_by' => 'Approved By',
            'approved_at' => 'Approved At',
            'deleted' => 'Deleted',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else{
            return true;
        }
    }
}
