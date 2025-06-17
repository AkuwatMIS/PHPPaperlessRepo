<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "section_fields_configs".
 *
 * @property int $id
 * @property int $field_id
 * @property string $key_name
 * @property string $value
 * @property int $parent_id
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class SectionFieldsConfigs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'section_fields_configs';
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
            [['field_id', 'key_name', 'assigned_to', 'created_by'], 'required'],
            [['field_id', 'parent_id', 'assigned_to', 'created_by'], 'integer'],
            [['key_name', 'value'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_id' => 'Field ID',
            'key_name' => 'Key Name',
            'value' => 'Value',
            'parent_id' => 'Parent ID',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getViewSectionField()
    {
        return $this->hasOne(ViewSectionFields::className(), ['id' => 'field_id']);
    }

    public function set_values()
    {
        /*if($this->parent_id == null)
        {
            $this->parent_id = 0;
        }*/
        if($this->value == null)
        {
            $this->value = NULL;
        }
        $this->assigned_to = Yii::$app->user->getId();
        $this->created_by = Yii::$app->user->getId();
    }

}
