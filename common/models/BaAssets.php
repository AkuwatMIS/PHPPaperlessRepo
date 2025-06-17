<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ba_assets".
 *
 * @property int $id
 * @property int $ba_id
 * @property int $application_id
 * @property string $type
 * @property string $assets_list
 * @property string $total_amount
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Applications $application
 * @property AppraisalsBusiness $ba
 */
class BaAssets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals_business_details';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['type','assets_list','total_amount'],
                'table' => "business_appraisal_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ba_id', 'application_id', 'type', 'assets_list', 'total_amount'/*, 'created_at', 'updated_at'*/], 'required'],
            [['ba_id', 'application_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['assets_list'], 'string'],
            [['total_amount'], 'number'],
            [['type'], 'string', 'max' => 50],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['ba_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppraisalsBusiness::className(), 'targetAttribute' => ['ba_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ba_id' => 'Ba ID',
            'application_id' => 'Application ID',
            'type' => 'Type',
            'assets_list' => 'Assets List',
            'total_amount' => 'Total Amount',
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
    public function getBa()
    {
        return $this->hasOne(AppraisalsBusiness::className(), ['id' => 'ba_id']);
    }


}
