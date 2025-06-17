<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pmyp_reports".
 *
 * @property int $id
 * @property int $province_id
 * @property int $project_id
 * @property int $received_application
 * @property int $rejected_application
 * @property int $received_application_amnt
 * @property int $rejected_application_amnt
 * @property int $total_pending_application
 * @property int $total_rejected_application
 * @property int $total_approved_application
 * @property int $total_pending_application_amnt
 * @property int $total_rejected_application_amnt
 * @property int $total_approved_application_amnt
 * @property int $loan_count
 * @property int $loan_amount
 * @property int $total_loan_count
 * @property int $total_loan_amount
 * @property int $disb_loan_count
 * @property int $disb_loan_amount
 * @property int $disb_total_loan_count
 * @property int $disb_total_loan_amount
 * @property int $active_loan_count
 * @property string $report_date
 * @property int $olp_amount
 * @property string $province
 * @property string $sector
 * @property string $product_type
 * @property string $gender
 */
class PmypReport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pmyp_reports';
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
            [['received_application', 'rejected_application','total_pending_application','total_rejected_application','total_approved_application'], 'number'],
            [['active_loan_count', 'olp_amount','loan_count', 'loan_amount','total_loan_count','total_loan_amount','disb_loan_count','disb_loan_amount','disb_total_loan_count','disb_total_loan_amount'], 'number'],
            [['province', 'sector','product_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province' => 'province',
            'Sector' => 'sector',
            'product_type' => 'Product Type',
        ];
    }
}
