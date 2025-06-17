<?php

namespace common\models;

use common\components\Helpers\MemberHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "members_account".
 *
 * @property int $id
 * @property int $member_id
 * @property string $bank_name
 * @property string $title
 * @property string $account_no
 * @property int $is_current
 * @property int $acc_file_id
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class MembersAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'members_account';
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
            [['member_id','account_type' /*'bank_name', 'account_no',*//* 'assigned_to', 'created_by', 'created_at'*/], 'required'],
            [['member_id', 'is_current', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted','status','verified_by','verified_at','acc_file_id'], 'integer'],
            [['bank_name', 'account_no','title','account_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */

    /*account type:
    'bank_accounts' => 'Bank',
    'coc_accounts' => 'COC',
    'cheque_accounts' => 'Cheque'*/

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'bank_name' => 'Bank Name',
            'title' => 'Title',
            'account_no' => 'Account No',
            'is_current' => 'Is Current',
            'acc_file_id' => 'Accounts File ID',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->status == 1)
            {
                $this->verified_by = isset($this->verified_by) ? $this->verified_by : Yii::$app->user->getId();
                $this->verified_at = strtotime('now');
            }
            if ($this->isNewRecord) {
                $this->is_current = 1;
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by: Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }
}
