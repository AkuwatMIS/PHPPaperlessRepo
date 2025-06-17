<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "social_appraisal_diseases_mapping".
 *
 * @property int $id
 * @property int $social_appraisal_id
 * @property int $disease_id
 * @property string $type
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $upated_at
 * @property int $deleted
 */
class SocialAppraisalDiseasesMapping extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social_appraisal_diseases_mapping';
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
            [['social_appraisal_id', 'disease_id'], 'required'],
            [['social_appraisal_id', 'disease_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','deleted'], 'integer'],
            [['type'], 'string'],
            //[['deleted'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'social_appraisal_id' => 'Social Appraisal ID',
            'disease_id' => 'Disease ID',
            'type' => 'Type',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'upated_at' => 'Upated At',
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

    public function getDisease()
    {
        return $this->hasOne(Diseases::className(), ['id' => 'disease_id']);
    }
}
