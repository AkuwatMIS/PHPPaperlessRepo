<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loans_logs".
 *
 * @property int $id
 * @property string $old_value
 * @property string $new_value
 * @property string $action
 * @property string $field
 * @property int $stamp
 * @property int $user_id
 *
 * @property Loans $id0
 */
class LoanTranchesLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_tranches_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'action', 'stamp'], 'required'],
            [['id', 'stamp', 'user_id'], 'integer'],
            [['old_value', 'new_value'], 'string', 'max' => 255],
            [['action', 'field'], 'string', 'max' => 100],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => LoanTranches::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'action' => 'Action',
            'field' => 'Field',
            'stamp' => 'Stamp',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(LoanTranches::className(), ['id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
