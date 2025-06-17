<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "nadra_verisys".
 *
 * @property int $id
 * @property int $member_id
 * @property int $application_id
 * @property string $document_type
 * @property string $document_name
 *
 * @property Applications $application
 * @property Members $member
 */
class NadraVerisys extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nadra_verisys';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'application_id'], 'required'],
            [['upload_by','upload_at','approved_by','approved_at'], 'integer'],
            [['document_type','document_name'], 'string', 'max' => 100],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member',
            'application_id' => 'Application',
            'document_type' => 'Type',
            'document_name' => 'Name',
            'status' => 'Status'
        ];
    }


    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }
}
