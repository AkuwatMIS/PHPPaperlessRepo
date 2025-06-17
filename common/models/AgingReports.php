<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "aging_reports".
 *
 * @property int $id
 * @property string $type
 * @property int $start_month
 * @property double $one_month
 * @property double $next_three_months
 * @property double $next_six_months
 * @property double $next_one_year
 * @property double $next_two_year
 * @property double $next_three_year
 * @property double $next_five_year
 * @property double $total
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class AgingReports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aging_reports';
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
            [['type', 'start_month'], 'required'],
            [[/*'start_month',*/ 'status', 'created_at', 'updated_at','deleted'], 'integer'],
            [['one_month', 'next_three_months', 'next_six_months', 'next_one_year', 'next_two_year', 'next_three_year', 'next_five_year', 'total'], 'number'],
            [['type'], 'string', 'max' => 50],
            [['file_name'], 'string', 'max' => 255],
            ['start_month', 'validateStartMonthConvertToIneger'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'start_month' => 'Start Month',
            'one_month' => 'One Month',
            'next_three_months' => 'Next Three Months',
            'next_six_months' => 'Next Six Months',
            'next_one_year' => 'Next One Year',
            'next_two_year' => 'Next Two Year',
            'next_three_year' => 'Next Three Year',
            'next_five_year' => 'Next Five Year',
            'total' => 'Total',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function validateStartMonthConvertToIneger($attribute){
            //$this->start_month = strtotime($this->start_month);
            $this->start_month = date('Y-m-t',strtotime($this->start_month));
    }
    public static function cal_od_aging_expire_loans($months, $inst_amnt,$loan_expiry,$start_month){
        $array = array(
            '1'=>0,
            '2'=>0,
            '3'=>0,
            '6'=>0,
            '12'=>0,
            '36'=>0,
            '72'=>0,
        );
        foreach ($array as $key=>$value){
            if($months <= 0){
                break;
            }else if(($months > $key) && ($loan_expiry >= $start_month)){
                $array[$key] = $key * $inst_amnt;
                $months = $months - $key;
            }else if($loan_expiry >= $start_month){
                $array[$key] = $months * $inst_amnt;
                $months = $months - $key;
            }
            $start_month = strtotime("-".$key." months",($start_month));
        }
        return $array;
    }
    public static function cal_od_aging($months, $inst_amnt){
        $array = array(
            '1'=>0,
            '2'=>0,
            '3'=>0,
            '6'=>0,
            '12'=>0,
            '36'=>0,
            '72'=>0,
        );
        foreach ($array as $key=>$value){
            if($months <= 0){
                break;
            }else if($months > $key){
                $array[$key] = $key * $inst_amnt;
            }else{
                $array[$key] = $months * $inst_amnt;
            }
            $months = $months - $key;
        }
        return $array;
    }
}
