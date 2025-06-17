<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "application_details".
 *
 * @property int $id
 * @property int $application_id
 * @property int $parent_id
 * @property string $parent_type
 * @property int $is_shifted
 * @property int $shifted_verified_by
 * @property int $poverty_score
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 * @property int $action_date
 *
 * @property Applications $application
 */
class ApplicationDetails extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application_details';
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
            [['parent_id', 'parent_type'], 'required'],
            [['created_at', 'updated_at', 'status', 'poverty_score', 'is_shifted', 'application_id','deleted','action_date','shifted_verified_by'], 'safe'],
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
            'parent_type' => 'Parent Type',
            'key_label' => 'Key',
            'value' => 'Value',
            'status' => 'Status',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getShifted($id)
    {
        $isShifted = ApplicationDetails::find()->where(['parent_type' => 'application'])
            ->andWhere(['parent_id' => $id])
            ->one();
        return (!empty($isShifted) && $isShifted != null) ? $isShifted->is_shifted : 0;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }
}
