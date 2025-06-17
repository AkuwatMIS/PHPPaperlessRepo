<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "templates".
 *
 * @property int $id
 * @property string $template_name
 * @property string $template_text
 * @property string $template_type
 * @property string $subject
 * @property string $email
 * @property string $send_to
 * @property int $is_active
 */
class Templates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'templates';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module','template_name', 'template_text', 'template_type', 'send_to'], 'required'],
            [['is_active', 'created_by', 'updated_by','created_at','updated_at','deleted'], 'integer'],
            [['template_text'], 'safe'],
            [['template_name', 'subject'], 'string', 'max' => 50],
            [['template_type','module'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 255],
            [['send_to'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_name' => 'Template Name',
            'template_text' => 'Template Text',
            'template_type' => 'Template Type',
            'subject' => 'Subject',
            'email' => 'Email',
            'send_to' => 'Send To',
            'is_active' => 'Is Active',
        ];
    }
    /*public function getEvent()
    {
        return $this->hasOne(Events::className(), ['id' => 'event_id']);
    }*/
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }
}
