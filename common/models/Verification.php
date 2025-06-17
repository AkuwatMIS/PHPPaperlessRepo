<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "verification".
 *
 * @property int $id
 * @property int $application_id
 * @property int $assigned_to
 * @property int $status
 * @property string $skip_reason
 * @property double $longitude
 * @property double $latitude
 * @property int $verification_at
 * @property string $thumb_impression
 */
class Verification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'verification';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'assigned_to'], 'required'],
            [['application_id', 'assigned_to', 'verified_at'], 'integer'],
            [['skip_reason','thumb_impression'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'assigned_to' => 'Assigned To',
            'status' => 'Status',
            'skip_reason' => 'Skip Reason',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'verified_at' => 'Verification At',
            'thumb_impression' => 'Thumb Impression',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "pending";
            }
            return true;
        } else {
            return false;
        }
    }
}
