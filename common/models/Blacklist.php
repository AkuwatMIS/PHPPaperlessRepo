<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "blacklist".
 *
 * @property int $id
 * @property int $member_id
 * @property string $cnic
 * @property string $cnic_invalid
 * @property string $name
 * @property string $parentage
 * @property string $reason
 * @property string $province
 * @property string $description
 * @property string $location
 * @property string $type
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class Blacklist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blacklist';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['province','parentage','cnic_invalid','member_id','name','cnic','reason','reject_reason','description','location','type','deleted',],
                'table' => "blacklist_logs",
                //'ignored' => ['updated_at'],
            ]/*,'ConfigsBehavior' => [
                'class' => 'common\behavior\ConfigsBehavior',
            ]*/
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id','created_by' ,'created_at', 'updated_at', 'deleted'], 'integer'],
            [['reason', 'type' ,/*'created_by',*/'name',/*, 'created_at', 'updated_at'*/], 'required'],
            [['name','parentage','reason','reject_reason', 'description', 'province'], 'string'],
            [['cnic'],'unique','filter' => ['deleted' => 0]],
            [['cnic', 'type'], 'string', 'max' => 20],
            [['cnic_invalid', 'type'], 'string', 'max' => 50],
            [['location'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'cnic_invalid' => 'Cnic Invalid',
            'name' => 'Name',
            'parentage' => 'Parentage',
            'Province' => 'province',
            'cnic' => 'Cnic',
            'reason' => 'Reason',
            'description' => 'Description',
            'location' => 'Location',
            'type' => 'Type',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            $this->cnic = !empty($this->cnic) ? $this->cnic : NULL;
            return true;
        } else {
            return false;
        }
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }


}
