<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "awp_branch_sustainability".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $branch_code
 * @property int $region_id
 * @property int $area_id
 * @property int $amount_disbursed
 * @property int $percentage
 * @property int $income
 * @property int $actual_expense
 * @property int $surplus_deficit
 */
class AwpBranchSustainability extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'awp_branch_sustainability';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
        ];
    }
    public $month_from;
public $surplus_deficit_total;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id', 'branch_code', 'region_id', 'area_id'], 'required'],
            [['branch_id', 'branch_code', 'region_id', 'area_id','amount_disbursed', 'percentage', 'income', 'actual_expense', 'surplus_deficit','deleted'], 'integer'],
            [['month'],'safe']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'branch_code' => 'Branch Code',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'amount_disbursed' => 'Amount Disbursed',
            'percentage' => 'Percentage',
            'income' => 'Income',
            'actual_expense' => 'Actual Expense',
            'surplus_deficit' => 'Surplus Deficit',
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
