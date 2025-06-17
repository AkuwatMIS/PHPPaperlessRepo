<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "funds".
 *
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $email
 * @property string $description
 * @property int $amount
 * @property int $total_fund
 * @property int $recovery
 * @property int $status
 * @property int $recovery_last_update
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Projects $project
 */
class Funds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $fund_utilized;
    public $balance;

    public static function tableName()
    {
        return 'funds';
    }

    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['total_fund','fund_received','recovery','status', 'created_at', 'updated_at','recovery_last_update','project_id'], 'integer'],
            [['name', 'description','email'], 'string', 'max' => 255],
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
            'email' => 'Email',
            'project_id' => 'Project',
            'description' => 'Description',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
