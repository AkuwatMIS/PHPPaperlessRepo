<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mobile_permissions".
 *
 * @property int $id
 * @property string $role
 * @property int $screen_id
 * @property int $permission
 * @property int $deleted
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class MobilePermissions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mobile_permissions';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className()
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'mobile_screen_id'], 'required'],
            [['mobile_screen_id', 'permission', 'deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['role'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'screen_id' => 'Screen ID',
            'permission' => 'Permission',
            'deleted' => 'Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->permission = 1;
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getMobileScreen()
    {
        return $this->hasOne(MobileScreens::className(), ['id' => 'mobile_screen_id']);
    }
}
