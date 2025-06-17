<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "files_application".
 *
 * @property int $id
 * @property string $file_path
 * @property int $appplication_id
 * @property int $status
 * @property int $type
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class FilesApplication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files_application';
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
            [['file_path', 'application_id', 'status', 'type'], 'required'],
            [['application_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['file_path'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_path' => 'File Path',
            'application_id' => 'Appplication ID',
            'status' => 'Status',
            'type' => 'Type',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function beforeSave($insert)
    {
        if( parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
}
