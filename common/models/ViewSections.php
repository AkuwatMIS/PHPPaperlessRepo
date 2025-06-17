<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "view_sections".
 *
 * @property int $id
 * @property string $section_name
 * @property string $section_description
 * @property string $section_table_name
 * @property int $sort_order
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class ViewSections extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_sections';
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
            [['section_name', 'section_description', 'section_table_name', 'sort_order', 'assigned_to', 'created_by'], 'required'],
            [['section_name', 'section_description', 'section_table_name'], 'string'],
            [['sort_order', 'assigned_to', 'created_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_name' => 'Section Name',
            'section_description' => 'Section Description',
            'section_table_name' => 'Section Table Name',
            'sort_order' => 'Sort Order',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function set_values()
    {
        $this->assigned_to = Yii::$app->user->getId();
        $this->created_by = Yii::$app->user->getId();
    }

    public function getViewSectionFields()
    {
        return $this->hasMany(ViewSectionFields::className(), ['section_id' => 'id']);
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
