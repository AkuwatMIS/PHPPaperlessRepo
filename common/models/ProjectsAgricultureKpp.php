<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_kpp".
 *
 * @property int $application_id
 * @property int $loan_id
 * @property string $kpp_owner
 * @property double $kpp_land_area_size
 * @property string $kpp_land_area_type
 * @property string $kpp_village_name
 * @property string $kpp_uc_number
 * @property string $kpp_uc_name
 * @property string $kpp_crop_type
 * @property string $kpp_crops
 * @property int $kpp_training_required
 * @property string $kpp_trainee_type
 * @property string $kpp_trainee_name
 * @property string $kpp_trainee_guardian
 * @property string $kpp_trainee_cnic
 * @property string $kpp_trainee_relation
 * @property int $kpp_has_sehat_card
 * @property int $kpp_want_sehat_card
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property Applications $application
 */
class ProjectsAgricultureKpp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_agriculture_kpp';
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
            [['application_id', 'kpp_owner', 'kpp_land_area_size', 'kpp_land_area_type', 'kpp_village_name', 'kpp_uc_number', 'kpp_uc_name', 'kpp_crop_type', 'kpp_crops'], 'required'],
            [['application_id', 'loan_id', 'created_by', 'updated_by', 'created_at', 'updated_at','kpp_training_required', 'kpp_has_sehat_card', 'kpp_want_sehat_card','deleted'], 'integer'],
            [['kpp_trainee_name','kpp_trainee_guardian','kpp_trainee_cnic'], 'string', 'max' => 100],
            [['kpp_land_area_size'], 'number'],
            [['kpp_owner'], 'string', 'max' => 100],
            [['kpp_land_area_type', 'kpp_uc_number'], 'string', 'max' => 20],
            [['kpp_village_name', 'kpp_uc_name', 'kpp_crops'], 'string', 'max' => 255],
            [['kpp_crop_type'], 'string', 'max' => 50],
            [['kpp_trainee_type', 'kpp_trainee_relation'], 'string', 'max' => 50],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'application_id' => 'Application ID',
            'loan_id' => 'Loan ID',
            'kpp_owner' => 'kpp_owner',
            'kpp_land_area_size' => 'Land Area Size',
            'kpp_land_area_type' => 'Land Area Type',
            'kpp_village_name' => 'Village Name',
            'kpp_uc_number' => 'Uc Number',
            'kpp_uc_name' => 'Uc Name',
            'kpp_crop_type' => 'Crop Type',
            'kpp_crops' => 'kpp_crops',
            'kpp_training_required' => 'Training Required',
            'kpp_has_sehat_card' => 'Has Sehat Card',
            'kpp_want_sehat_card' => 'Want Sehat Card',
            'kpp_trainee_name' => 'Trainee Name',
            'kpp_trainee_guardian' => 'Trainee Guardian',
            'kpp_trainee_cnic' => 'Trainee Cnic',
            'kpp_trainee_type' => 'Trainee Type',
            'kpp_trainee_relation' => 'Trainee Relation',
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

}
