<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "structure_transfer".
 *
 * @property int $id
 * @property string $type
 * @property int $old_value
 * @property int $new_value
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class StructureTransfer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'structure_transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obj_type', 'obj_id', 'old_value', 'new_value', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'required'],
            [['old_value', 'obj_id', 'new_value', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['obj_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_type' => 'Type',
            'obj_id' => 'ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'status' => 'Status',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
