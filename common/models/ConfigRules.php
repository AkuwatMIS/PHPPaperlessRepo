<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "config_rules".
 *
 * @property int $id
 * @property string $group
 * @property int $priority
 * @property string $key
 * @property string $value
 * @property string $parent_type
 * @property int $parent_id
 * @property int $project_id
 */
class ConfigRules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'priority', 'key', 'value', 'parent_type', 'parent_id', 'project_id','field_name'], 'required'],
            [['priority', 'parent_id', 'project_id'], 'integer'],
            [['group', 'key', 'parent_type'], 'string', 'max' => 20],
            [['field_name'], 'string', 'max' => 50],
            [['value'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => 'Group',
            'priority' => 'Priority',
            'key' => 'Key',
            'value' => 'Value',
            'parent_type' => 'Parent Type',
            'parent_id' => 'Parent ID',
            'project_id' => 'Project ID',
        ];
    }
    public function beforeSave($insert=true)
    {
        if($this->parent_type=='global'){
            $this->priority = 0;
        }
        else if($this->parent_type=='project') {
            $this->priority = 1;

        }
        else if($this->parent_type=='region') {
            $this->priority = 2;

        }

        else if($this->parent_type=='area') {
            $this->priority = 3;

        }
        else if($this->parent_type=='branch') {
            $this->priority = 4;

        }
        else if($this->parent_type=='team') {
            $this->priority = 5;

        }
        else if($this->parent_type=='field') {
            $this->priority = 6;

        }
        else if($this->parent_type=='user') {
            $this->priority = 7;

        }
        return true;




    }
}
