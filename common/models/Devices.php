<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "devices".
 *
 * @property int $id
 * @property string $uu_id
 * @property string $imei_no
 * @property string $os_version
 * @property string $device_model
 * @property string $push_id
 * @property string $access_token
 * @property int $status
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Devices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'devices';
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
            [['uu_id', 'imei_no', 'os_version', 'device_model', 'access_token'], 'required'],
            [['assigned_to', 'created_by', 'updated_by','status'], 'integer'],
            [['uu_id', 'imei_no', 'push_id'], 'string', 'max' => 255],
            [['uu_id', 'imei_no'], 'unique'],
            [['os_version'], 'string', 'max' => 100],
            [['device_model'], 'string', 'max' => 50],
            [['access_token'], 'string', 'max' => 70],
            //[['status'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uu_id' => 'Uu ID',
            'imei_no' => 'Imei No',
            'os_version' => 'Os Version',
            'device_model' => 'Device Model',
            'push_id' => 'Push ID',
            'access_token' => 'Access Token',
            'status' => 'Status',
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
                $this->status = isset($this->status) ? $this->status : 0;
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
}
