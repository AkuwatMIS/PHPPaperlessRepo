<?php

use yii\db\Migration;

/**
 * Class m180507_110605_db_schema_5
 */
class m180507_110605_db_schema_7 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->alterColumn('{{%accounts}}', 'dt_opening',  $this->integer()->defaultValue(0). ' after purpose');
        $this->alterColumn('{{%accounts}}', 'created_at',  $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%accounts}}', 'updated_at',  $this->integer()->notNull(). ' after created_at');


        $this->alterColumn('{{%account_types}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%account_types}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%activities}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%activities}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%applications}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%applications}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%applications_logs}}', 'stamp', $this->integer()->notNull(). ' after field');

        $this->alterColumn('{{%archive_reports}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%archive_reports}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%areas}}', 'opening_date',  $this->integer()->defaultValue(0). ' after mobile');
        $this->alterColumn('{{%areas}}', 'created_at',  $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%areas}}', 'updated_at',  $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%banks}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%banks}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%ba_business_expenses}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%ba_business_expenses}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%ba_existing_investment}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%ba_existing_investment}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%ba_fixed_business_assets}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%ba_fixed_business_assets}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%ba_required_assets}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%ba_required_assets}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%branches}}', 'opening_date',  $this->integer()->defaultValue(0). ' after description');
        $this->alterColumn('{{%branches}}', 'created_at',  $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%branches}}', 'updated_at',  $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%branch_account_mapping}}', 'created_at',  $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%branch_account_mapping}}', 'updated_at',  $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%branch_projects_mapping}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%branch_projects_mapping}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%branch_requests}}', 'opening_date', $this->integer()->defaultValue(0). ' after description');
        $this->alterColumn('{{%branch_requests}}', 'recommended_on', $this->integer()->defaultValue(0). ' after remarks');
        $this->alterColumn('{{%branch_requests}}', 'approved_on', $this->integer()->defaultValue(0). ' after recommended_remarks');
        $this->alterColumn('{{%branch_requests}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%branch_requests}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%business_appraisal}}', 'approved_on', $this->integer()->defaultValue(0). ' after approved_by');
        $this->alterColumn('{{%business_appraisal}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%business_appraisal}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%cih_transactions_mapping}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%cih_transactions_mapping}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%cities}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%cities}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%countries}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%countries}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%credit_divisions}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%credit_divisions}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%devices}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%devices}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%disbursements}}', 'date_disbursed', $this->integer()->defaultValue(0). ' after id');
        $this->alterColumn('{{%disbursements}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%disbursements}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%districts}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%districts}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%divisions}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%divisions}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%donations}}', 'recv_date', $this->integer()->defaultValue(0). ' after debit');
        $this->alterColumn('{{%donations}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%donations}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%donations_logs}}', 'stamp', $this->integer()->notNull(). ' after field');

        $this->alterColumn('{{%fields}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%fields}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%groups}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%groups}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%loans}}', 'date_approved', $this->integer()->defaultValue(0). ' after project_table');
        $this->alterColumn('{{%loans}}', 'date_disbursed', $this->integer()->defaultValue(0). ' after inst_type');
        $this->alterColumn('{{%loans}}', 'cheque_dt', $this->integer()->defaultValue(0). ' after date_disbursed');
        $this->alterColumn('{{%loans}}', 'loan_expiry', $this->integer()->defaultValue(0). ' after field_id');
        $this->alterColumn('{{%loans}}', 'loan_completed_date', $this->integer()->defaultValue(0). ' after loan_expiry');
        $this->alterColumn('{{%loans}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%loans}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%loans_logs}}', 'stamp', $this->integer()->notNull(). ' after field');

        $this->alterColumn('{{%members}}', 'dob', $this->integer()->defaultValue(0). ' after gender');
        $this->alterColumn('{{%members}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%members}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%members_address}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%members_address}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%members_email}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%members_email}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%members_phone}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%members_phone}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%news}}', 'news_date', $this->integer()->defaultValue(0). ' after full_description');
        $this->alterColumn('{{%news}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%news}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%operations}}', 'receive_date', $this->integer()->defaultValue(0). ' after receipt_no');
        $this->alterColumn('{{%operations}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%operations}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%operations}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%operations}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%operations_logs}}', 'stamp', $this->integer()->notNull(). ' after field');

        $this->alterColumn('{{%products}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%products}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%progress_reports}}', 'report_date', $this->integer()->defaultValue(0). ' after id');
        $this->alterColumn('{{%progress_reports}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%progress_reports}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%progress_report_details}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%progress_report_details}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%projects}}', 'started_date', $this->integer()->defaultValue(0). ' after funding_line');
        $this->alterColumn('{{%projects}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%projects}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%project_details_disabled}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%project_details_disabled}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%project_details_tevta}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%project_details_tevta}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%provinces}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%provinces}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%recoveries}}', 'due_date', $this->integer()->defaultValue(0). ' after field_id');
        $this->alterColumn('{{%recoveries}}', 'recv_date', $this->integer()->defaultValue(0). ' after due_date');
        $this->alterColumn('{{%recoveries}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%recoveries}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%recoveries_logs}}', 'stamp', $this->integer()->notNull(). ' after field');

        $this->alterColumn('{{%regions}}', 'opening_date', $this->integer()->defaultValue(0). ' after mobile');
        $this->alterColumn('{{%regions}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%regions}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%schedules}}', 'due_date', $this->integer()->defaultValue(0). ' after branch_id');
        $this->alterColumn('{{%schedules}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%schedules}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

       /* $this->alterColumn('{{%section_fields_configs}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%section_fields_configs}}', 'updated_at', $this->integer()->notNull(). ' after created_at');*/

        $this->alterColumn('{{%social_appraisal}}', 'date_of_committee', $this->integer()->defaultValue(0). ' after total_committee');
        $this->alterColumn('{{%social_appraisal}}', 'approved_on', $this->integer()->defaultValue(0). ' after approved_by');
        $this->alterColumn('{{%social_appraisal}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%social_appraisal}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%teams}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%teams}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%transactions}}', 'deposit_date', $this->integer()->defaultValue(0). ' after deposit_slip_no');
        $this->alterColumn('{{%transactions}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%transactions}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%users}}', 'last_login_at', $this->integer()->defaultValue(0). ' after password_reset_token');
        $this->alterColumn('{{%users}}', 'joining_date', $this->integer()->defaultValue(0). ' after mobile');
        $this->alterColumn('{{%users}}', 'block_date', $this->integer()->defaultValue(0). ' after reason');
        $this->alterColumn('{{%users}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%users}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%versions}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%versions}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        $this->alterColumn('{{%view_sections}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%view_sections}}', 'updated_at', $this->integer()->notNull(). ' after created_at');

        /*$this->alterColumn('{{%view_section_fields}}', 'created_at', $this->integer()->notNull(). ' after updated_by');
        $this->alterColumn('{{%view_section_fields}}', 'updated_at', $this->integer()->notNull(). ' after created_at');*/

    }

    /**
     * {@inheritdoc}
     */


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_110605_db_schema_5 cannot be reverted.\n";

        return false;
    }
    */
}
