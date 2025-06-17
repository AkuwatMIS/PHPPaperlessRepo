<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "recovery_files".
 *
 * @property int $id
 * @property string $source
 * @property string $bank_branch_name
 * @property string $bank_branch_code
 * @property string $description
 * @property string $file_date
 * @property string $file_name
 * @property string $status
 * @property int $total_records
 * @property int $inserted_records
 * @property int $error_records
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class RecoveryFiles extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recovery_files';
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
            [['source', 'status'], 'string'],
            [['description', 'file_date','updated_by' /*,'created_at'*/,'source','file'], 'required'],
            [['created_at'/* 'updated_at'*/], 'safe'],
            [['total_records', 'inserted_records', 'error_records', 'updated_by',/*'file_date'*/], 'integer'],
            [['file_name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['file'], 'file', 'extensions' => 'csv', 'checkExtensionByMimeType' => false],
            [['file_date'],'validateFileDateConvertToIneger']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source' => 'Source',
            'description' => 'Description',
            'file_date' => 'File Date',
            'file_name' => 'File Name',
            'status' => 'Status',
            'total_records' => 'Total Records',
            'inserted_records' => 'Inserted Records',
            'error_records' => 'Error Records',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function validateFileDateConvertToIneger($attribute){

        if(gettype($this->file_date) == 'string') {
            $this->file_date = strtotime($this->file_date);
        }
    }
    public function getRecoveryErrors()
    {
        return $this->hasMany(RecoveryErrors::className(), ['recovery_files_id' => 'id']);
    }
}
