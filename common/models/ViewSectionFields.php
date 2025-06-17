<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "view_section_fields".
 *
 * @property int $id
 * @property int $section_id
 * @property string $table_name
 * @property string $field
 * @property int $sort_order
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class ViewSectionFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_section_fields';
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
            [['section_id', 'field', 'assigned_to', 'created_by'], 'required'],
            [['section_id', 'sort_order', 'assigned_to', 'created_by'], 'integer'],
            [['table_name', 'field'], 'string'],
            [[], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_id' => 'Section ID',
            'table_name' => 'Table Name',
            'field' => 'Field',
            'sort_order' => 'Sort Order',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getSectionFieldsConfigs()
    {
        return $this->hasMany(SectionFieldsConfigs::className(), ['field_id' => 'id']);
    }
    public function getSectionFieldConfigType()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'type']);
    }
    public function getSectionFieldConfigFormat()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'format']);
    }
    public function getSectionFieldConfigPlaceholder()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'place_holder']);
    }

    public function getSectionFieldConfigQuestionid()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'question_id']);
    }
    public function getSectionFieldConfigAnswer()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'answers']);
    }

    public function getSectionFieldConfigVisible()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'default_visibility']);
    }
    public function getSectionFieldConfigWidth()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'width']);
    }
    public function getSectionFieldConfigHeight()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'height']);
    }
    public function getSectionFieldConfigRequired()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'is_mandatory']);
    }
    public function getSectionFieldConfigLabel()
    {
        return $this->hasOne(SectionFieldsConfigs::className(), ['field_id' => 'id'])->andOnCondition(['section_fields_configs.key_name'=>'label']);
    }
    public function getViewSection()
    {
        return $this->hasOne(ViewSections::className(), ['id' => 'section_id']);
    }

    public function set_values()
    {
        $this->assigned_to = Yii::$app->user->getId();
        $this->created_by = Yii::$app->user->getId();
    }

    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
}
