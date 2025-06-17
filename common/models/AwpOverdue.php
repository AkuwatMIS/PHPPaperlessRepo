<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "awp_overdue".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $month
 * @property string $date_of_opening
 * @property int $overdue_numbers
 * @property int $overdue_amount
 * @property int $awp_active_loans
 * @property int $awp_olp
 * @property int $active_loans
 * @property int $write_off_amount_new
 * @property int $write_off_loans_new
 * @property int $olp
 * @property int $def_recovered
 * @property int $diff_active_loans
 * @property int $diff_olp
 */
class AwpOverdue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $writeoff_count;
    public $writeoff_amount;
    public $date_of_opening;
    public $month_from;
    public static function tableName()
    {
        return 'awp_overdue';
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
            [['branch_id'], 'required'],
            [['branch_id', 'overdue_numbers', 'overdue_amount', 'awp_active_loans', 'awp_olp', 'active_loans', 'olp', 'diff_active_loans', 'diff_olp','deleted'], 'integer'],
            [['date_of_opening','write_off_amount','write_off_recovered','def_recovered','write_off_loans_new','write_off_amount_new'], 'safe'],
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
            'branch_id' => 'Branch ID',
            'month' => 'Month',
            //'date_of_opening' => 'Date Of Opening',
            'overdue_numbers' => 'Overdue Numbers',
            'write_off_amount_new' => 'write off amount',
            'write_off_loans_new' => 'write off loans',
            'overdue_amount' => 'Overdue Amount',
            'awp_active_loans' => 'Awp Active Loans',
            'awp_olp' => 'Awp Olp',
            'active_loans' => 'Active Loans',
            'olp' => 'Olp',
            'diff_active_loans' => 'Diff Active Loans',
            'diff_olp' => 'Diff Olp',
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
