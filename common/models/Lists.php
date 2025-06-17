<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lists".
 *
 * @property int $id
 * @property string $list_name
 * @property string $value
 * @property string $label
 * @property int $sort_order
 */
class Lists extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_name', 'value', 'label', 'sort_order'], 'required'],
            [['list_name', 'value', 'label', 'sort_order'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_name' => 'List Name',
            'value' => 'Value',
            'label' => 'Label',
            'sort_order' => 'Sort Order',
        ];
    }
}
