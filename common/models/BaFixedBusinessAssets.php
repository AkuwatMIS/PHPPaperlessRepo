<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "ba_fixed_business_assets".
 *
 * @property int $id
 * @property int $ba_id
 * @property int $application_id
 * @property string $assets
 * @property int $quantity
 * @property string $existing_price
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Applications $application
 * @property BusinessAppraisal $ba
 */
class BaFixedBusinessAssets extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ba_fixed_business_assets';
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
            [['ba_id', 'application_id', 'assets', 'quantity', 'existing_price'], 'required'],
            [['ba_id', 'application_id', 'quantity', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','deleted'], 'integer'],
            [['existing_price'], 'number'],
            [['assets'], 'string', 'max' => 50],
            //[['deleted'], 'string', 'max' => 1],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['ba_id'], 'exist', 'skipOnError' => true, 'targetClass' => BusinessAppraisal::className(), 'targetAttribute' => ['ba_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ba_id' => 'Ba ID',
            'application_id' => 'Application ID',
            'assets' => 'Assets',
            'quantity' => 'Quantity',
            'existing_price' => 'Existing Price',
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
        return $this->hasOne(BusinessAppraisal::className(), ['id' => 'ba_id']);
    }
}
