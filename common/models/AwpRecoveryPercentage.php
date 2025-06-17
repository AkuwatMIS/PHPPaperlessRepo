<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "awp_recovery_percentage".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $area_id
 * @property int $region_id
 * @property int $branch_code
 * @property int $recovery_count
 * @property int $recovery_one_to_ten
 * @property int $recovery_eleven_to_twenty
 * @property int $recovery_twentyone_to_thirty
 * @property int $created_at
 * @property int $updated_at
 */
class AwpRecoveryPercentage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'awp_recovery_percentage';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month'], 'required'],
            [['branch_id', 'area_id', 'region_id', 'branch_code', 'recovery_count', 'recovery_one_to_ten', 'recovery_eleven_to_twenty', 'recovery_twentyone_to_thirty', 'created_at', 'updated_at'], 'integer'],
            [['month'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'area_id' => 'Area ID',
            'region_id' => 'Region ID',
            'branch_code' => 'Branch Code',
            'recovery_count' => 'Recovery Count',
            'recovery_one_to_ten' => 'Recovery One To Ten',
            'recovery_eleven_to_twenty' => 'Recovery Eleven To Twenty',
            'recovery_twentyone_to_thirty' => 'Recovery Twentyone To Thirty',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
}
