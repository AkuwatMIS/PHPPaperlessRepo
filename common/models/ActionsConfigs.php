<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "actions_configs".
 *
 * @property int $id
 * @property string $parent_type
 * @property string $parent_table
 * @property string $flow
 * @property int $sort_order
 * @property int $project_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class ActionsConfigs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'actions_configs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_type', 'parent_table', 'flow', 'sort_order', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['sort_order', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['parent_type', 'parent_table'], 'string', 'max' => 40],
            [['flow'], 'string', 'max' => 100],
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
            'parent_table' => 'Parent Table',
            'flow' => 'Flow',
            'sort_order' => 'Sort Order',
            'project_id' => 'Project ID',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
