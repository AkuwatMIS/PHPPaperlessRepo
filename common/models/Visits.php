<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "visits".
 *
 * @property int $id
 * @property string $parent_type
 * @property int $parent_id
 * @property string $name
 * @property string $comments
 * @property double $longitude
 * @property double $latitude
 * @property int $status
 * @property int $is_shifted
 * @property int $shifted_verified_by
 * @property int $construction_verified_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Visits extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visits';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_type', 'parent_id', 'comments','percent'], 'required'],
            [['parent_id','estimated_completion_time','estimated_start_date', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by','is_tranche','is_shifted','shifted_verified_by','construction_verified_by'], 'integer'],
            [['comments','estimated_figures'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['parent_type'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_type' => 'Parent Type',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'comments' => 'Comments',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'estimated_figures' => 'Estimated Figures',
            'estimated_start_date' => 'Estimated Start Date',
            'estimated_completion_time' => 'Estimated Completion Time',
            'status' => 'Status',
            'Shifted' => 'is_shifted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                if(isset($this->is_tranche) && !empty($this->is_tranche) && ($this->is_tranche==0)){
                    $this->percent=0;
                }
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getImages()
    {
        return $this->hasMany(Images::className(), ['parent_id' => 'id'])->andOnCondition(['parent_type' => 'visits']);
    }
    public function getImagesCount()
    {
        return $this->hasMany(Images::className(), ['parent_id' => 'id'])->andOnCondition(['parent_type' => 'visits'])->count();
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
}
