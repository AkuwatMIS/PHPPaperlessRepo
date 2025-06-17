<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "analytics".
 *
 * @property int $id
 * @property int $user_id
 * @property string $api
 * @property int $count
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property int $deleted
 */
class Analytics extends \yii\db\ActiveRecord
{
    public $users_count;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'analytics';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'api', 'count','type', 'description'], 'required'],
            [['user_id', 'count', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at','users_count','type'], 'safe'],
            [['api'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'api' => 'Api',
            'count' => 'Count',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
