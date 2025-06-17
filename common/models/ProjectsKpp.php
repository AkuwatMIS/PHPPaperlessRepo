<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_kpp".
 *
 * @property int $id
 * @property int $application_id
 * @property int $training_required
 * @property string $trainee_type
 * @property string $trainee_name
 * @property string $trainee_guardian
 * @property string $trainee_cnic
 * @property string $trainee_relation
 * @property int $has_sehat_card
 * @property int $want_sehat_card
 * @property int $deleted
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications $application
 */
class ProjectsKpp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_kpp';
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
            [['application_id','training_required', 'has_sehat_card', 'want_sehat_card','deleted'], 'integer'],
            [['trainee_name','trainee_guardian','trainee_cnic'], 'string', 'max' => 100],
            [['trainee_type', 'trainee_relation'], 'string', 'max' => 50],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
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
            'training_required' => 'Training Required',
            'has_sehat_card' => 'Has Sehat Card',
            'want_sehat_card' => 'Want Sehat Card',
            'trainee_name' => 'Trainee Name',
            'trainee_guardian' => 'Trainee Guardian',
            'trainee_cnic' => 'Trainee Cnic',
            'trainee_type' => 'Trainee Type',
            'trainee_relation' => 'Trainee Relation',
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
