<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "project_details_agriculture".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property string $owner
 * @property double $land_area_size
 * @property string $land_area_type
 * @property string $village_name
 * @property string $uc_number
 * @property string $uc_name
 * @property string $crop_type
 * @property string $crops
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class ProjectsAgriculture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'projects_agriculture';
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
            [['application_id', 'owner', 'land_area_size', 'land_area_type', 'village_name', 'uc_number', 'uc_name', 'crop_type', 'crops'], 'required'],
            [['application_id', 'loan_id', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['land_area_size'], 'number'],
            [['owner'], 'string', 'max' => 100],
            [['land_area_type', 'uc_number'], 'string', 'max' => 20],
            [['village_name', 'uc_name', 'crops'], 'string', 'max' => 255],
            [['crop_type'], 'string', 'max' => 50],
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
            'owner' => 'Owner',
            'land_area_size' => 'Land Area Size',
            'land_area_type' => 'Land Area Type',
            'village_name' => 'Village Name',
            'uc_number' => 'Uc Number',
            'uc_name' => 'Uc Name',
            'crop_type' => 'Crop Type',
            'crops' => 'Crops',
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
}
