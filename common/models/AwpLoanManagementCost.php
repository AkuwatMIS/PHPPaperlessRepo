<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "awp_loan_management_cost".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $area_id
 * @property int $region_id
 * @property string $date_of_opening
 * @property int $opening_active_loans
 * @property int $closing_active_loans
 * @property int $average
 * @property int $amount
 * @property int $lmc
 */
class AwpLoanManagementCost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $date_of_opening;
    public static function tableName()
    {
        return 'awp_loan_management_cost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id', 'area_id', 'region_id', 'opening_active_loans', 'closing_active_loans', 'average', 'amount', 'lmc'], 'required'],
            [['branch_id', 'area_id', 'region_id', 'opening_active_loans', 'closing_active_loans', 'average', 'amount', 'lmc','deleted'], 'integer'],
            [['date_of_opening'], 'safe'],
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
            'area_id' => 'Area ID',
            'region_id' => 'Region ID',
            //'date_of_opening' => 'Date Of Opening',
            'opening_active_loans' => 'Opening Active Loans',
            'closing_active_loans' => 'Closing Active Loans',
            'average' => 'Average',
            'amount' => 'Amount',
            'lmc' => 'Lmc',
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
