<?php

use yii\db\Migration;

/**
 * Class m180505_114556_db_schema_2
 */
class m180505_114556_db_schema_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('{{%social_appraisal}}', 'business_type', $this->boolean()->notNull());
        $this->addColumn('{{%social_appraisal}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%social_appraisal}}', 'approved_by', $this->integer()->notNull());
        $this->addColumn('{{%social_appraisal}}', 'approved_on', $this->date()->notNull());
        $this->addColumn('{{%social_appraisal}}', 'status', $this->tinyInteger()->notNull());
        $this->dropForeignKey('FK_loans_social_appraisal','{{%social_appraisal}}');
        $this->dropColumn('{{%social_appraisal}}', 'loan_id');


        $this->alterColumn('{{%business_appraisal}}', 'business_type', $this->boolean()->notNull());
        $this->addColumn('{{%business_appraisal}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%business_appraisal}}', 'approved_by', $this->integer()->notNull());
        $this->addColumn('{{%business_appraisal}}', 'approved_on', $this->date()->notNull());
        $this->addColumn('{{%business_appraisal}}', 'status', $this->tinyInteger()->notNull());
        $this->dropForeignKey('FK_loans_ba','{{%business_appraisal}}');
        $this->dropColumn('{{%business_appraisal}}', 'loan_id');
        $this->dropColumn('{{%business_appraisal}}', 'ba_table');
        $this->dropColumn('{{%business_appraisal}}', 'name');
        $this->dropColumn('{{%business_appraisal}}', 'appraiser_name');


        $this->alterColumn('{{%ba_business_expenses}}', 'qty', $this->integer()->notNull());
        $this->addColumn('{{%ba_business_expenses}}', 'application_id', $this->integer()->defaultValue(0));
        $this->addColumn('{{%ba_business_expenses}}', 'updated_by', $this->integer()->notNull());
        $this->addForeignKey('FK_applications_ba_business_expenses','{{%ba_business_expenses}}', 'application_id', '{{%applications}}', 'id');

        $this->addColumn('{{%ba_existing_investment}}', 'application_id', $this->integer()->notNull());
        $this->addColumn('{{%ba_existing_investment}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addForeignKey('FK_applications_ba_existing_investment','{{%ba_existing_investment}}', 'application_id', '{{%applications}}', 'id');

        $this->addColumn('{{%ba_fixed_business_assets}}', 'application_id', $this->integer()->notNull());
        $this->addColumn('{{%ba_fixed_business_assets}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addForeignKey('FK_applications_ba_fixed_business_assets','{{%ba_fixed_business_assets}}', 'application_id', '{{%applications}}', 'id');

        $this->addColumn('{{%ba_required_assets}}', 'application_id', $this->integer()->notNull());
        $this->addColumn('{{%ba_required_assets}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addForeignKey('FK_applications_ba_required_assets','{{%ba_required_assets}}', 'application_id', '{{%applications}}', 'id');

        $this->dropColumn('{{%projects}}', 'ba_table');

        $this->addColumn('{{%accounts}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%account_types}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%activities}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%applications}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%archive_reports}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%areas}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%banks}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%branches}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%branch_account_mapping}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%branch_projects_mapping}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%branch_requests}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%cih_transactions_mapping}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%cities}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%countries}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%credit_divisions}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%devices}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%disbursements}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%districts}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%divisions}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%donations}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%groups}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%loans}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%members}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%members_address}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%members_email}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%members_phone}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%news}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%operations}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%products}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%progress_reports}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%progress_report_details}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%projects}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%project_details_disabled}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%project_details_tevta}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%provinces}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%recoveries}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%regions}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%schedules}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%section_fields_configs}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%transactions}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%users}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%versions}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%view_sections}}', 'updated_by', $this->integer()->defaultValue(0));
        $this->addColumn('{{%view_section_fields}}', 'updated_by', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180505_114556_db_schema_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180505_114556_db_schema_2 cannot be reverted.\n";

        return false;
    }
    */
}
