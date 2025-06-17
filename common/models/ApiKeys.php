<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "api_keys".
 *
 * @property int $id
 * @property string $api_key
 * @property string $purpose
 */
class ApiKeys extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_keys';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_key', 'purpose'], 'required'],
            [['api_key'], 'string', 'max' => 255],
            [['purpose'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_key' => 'Api Key',
            'purpose' => 'Purpose',
        ];
    }
}
