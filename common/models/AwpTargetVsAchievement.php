<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "awp_target_vs_achievement".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $project_id
 * @property string $month
 * @property int $target_loans
 * @property int $target_amount
 * @property int $achieved_loans
 * @property int $achieved_amount
 * @property int $loans_dif
 * @property int $amount_dif
 */
class AwpTargetVsAchievement extends \yii\db\ActiveRecord
{
    public $month_from;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'awp_target_vs_achievement';
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
            [['region_id', 'area_id', 'branch_id', 'month'], 'required'],
            [['region_id', 'area_id', 'branch_id', 'project_id', 'target_loans', 'target_amount', 'achieved_loans', 'achieved_amount', 'loans_dif', 'amount_dif','deleted'], 'integer'],
            [['month'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'project_id' => 'Project ID',
            'month' => 'Month',
            'target_loans' => 'Target Loans',
            'target_amount' => 'Target Amount',
            'achieved_loans' => 'Achieved Loans',
            'achieved_amount' => 'Achieved Amount',
            'loans_dif' => 'Loans Dif',
            'amount_dif' => 'Amount Dif',
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
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    public function getAwp()
    {
        return $this->hasMany(Awp::className(), ['month' => 'month', 'branch_id' => 'branch_id']);
    }
}
