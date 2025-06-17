<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "notification_logs".
 *
 * @property int $id
 * @property int $nadra_verisys_rejected_id
 * @property int $status
 * @property int $parent_id
 * @property int $sort_order
 * @property string $reject_reason
 * @property string $remarks
 * @property string $rejected_date
 * @property string $created_at
 * @property string $updated_at
 */
class NadraVerisysRejectReasons extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nadra_verisys_reject_resons';
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
            [['nadra_verisys_rejected_id', 'rejected_date'], 'required'],
            [['status', 'reject_reason', 'remarks', 'parent_id'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'reject_reason' => 'Reject Reason',
            'rejected_date' => 'Reject Date',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
