<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $parent_type
 * @property string $image_name
 * @property int $created_at
 * @property int $updated_at
 */
class Images extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'images';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
public $image_data;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'parent_type', 'image_type'], 'required'],
            [['parent_id'], 'integer'],
            [['parent_type', 'image_name', 'image_type'], 'string', 'max' => 255],
            ['image_data', 'image', /*'minWidth' => 0, 'minHeight' =>0 ,'maxWidth' => 1000, 'maxHeight' => 1000,*/ 'extensions' => 'jpg, png', 'maxSize' => 1024 * 1024 * 0.5],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'parent_type' => 'Parent Type',
            'image_type' => 'Image Type',
            'image_name' => 'Image Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
