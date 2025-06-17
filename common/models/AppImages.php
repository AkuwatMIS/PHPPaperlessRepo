<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "app_images".
 *
 * @property int $id
 * @property string $type
 * @property string $path
 * @property int $sort_order
 * @property int $status
 */
class AppImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'path'], 'required'],
            [['sort_order', 'status'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['path'], 'string', 'max' => 100],
            [['path'],'file'],
            [['target'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'path' => 'Path',
            'sort_order' => 'Sort Order',
            'status' => 'Status',
        ];
    }
}
