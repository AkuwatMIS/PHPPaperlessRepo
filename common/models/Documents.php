<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property string $module_type
 * @property int $module_id
 * @property string $parent_type
 * @property string $name
 * @property int $is_required
 */
class Documents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_type', 'name'], 'required'],
            [['module_id', 'is_required'], 'integer'],
            [['module_type', 'parent_type'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_type' => 'Module Type',
            'module_id' => 'Module ID',
            'parent_type' => 'Parent Type',
            'name' => 'Name',
            'is_required' => 'Is Required',
        ];
    }
}
