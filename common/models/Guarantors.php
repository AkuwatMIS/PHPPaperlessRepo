<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "guarantors".
 *
 * @property int $id
 * @property string $name
 * @property string $parentage
 * @property string $cnic
 * @property string $address
 * @property string $phone
 * @property int $assigned_to
 * @property int $created_by
 * @property int $upated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class Guarantors extends \yii\db\ActiveRecord
{
    public $reject_reason;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guarantors';
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
            [['group_id', 'name', 'parentage', 'cnic', 'address', 'phone'], 'required'],
            [['address','left_thumb'], 'string'],
            [['reject_reason'], 'safe'],
            [['name'], 'match', 'pattern' => "/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/"],
            [['platform','assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted','group_id','monthly_income'], 'integer'],
            [['name', 'parentage'], 'string', 'max' => 50],
            [['cnic', 'phone'], 'string', 'max' => 20],
            [['marital_status'], 'string', 'max' => 10],
            [['source_of_income'], 'string', 'max' => 15],
            [['guarantor_relation'], 'string', 'max' => 30],
            [['cnic'], 'match', 'pattern' => "/^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/i"],
            [['phone'], 'match', 'pattern' => "/^[0-9]{12}$/"],
            [['name','parentage'], 'match', 'pattern' => "/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parentage' => 'Parentage',
            'cnic' => 'Cnic',
            'address' => 'Address',
            'phone' => 'Phone',
            'left_thumb' => 'Left Thumb',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Upated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
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
