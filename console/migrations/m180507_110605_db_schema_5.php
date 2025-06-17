<?php

use yii\db\Migration;

/**
 * Class m180507_110605_db_schema_5
 */
class m180507_110605_db_schema_5 extends Migration
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

        $this->createTable('{{%teams}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(10)->notNull(),
            'branch_id' => $this->integer()->notNull(),
            'description' => $this->text(),
            'status' => $this->tinyInteger(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
        ], $tableOptions);
        $this->createIndex('branch_id_index_teams', '{{%teams}}','branch_id', false);
        $this->addForeignKey('FK_branches_teams','{{%teams}}', 'branch_id', '{{%branches}}', 'id');

         $this->createTable('{{%fields}}', [
             'id' => $this->primaryKey(),
             'name' => $this->string(10)->notNull(),
             'team_id' => $this->integer()->notNull(),
             'description' => $this->text(),
             'status' => $this->tinyInteger(),
             'assigned_to' => $this->integer()->notNull(),
             'created_by' => $this->integer()->notNull(),
             'updated_by' => $this->integer()->defaultValue(0),
             'created_at' => $this->dateTime()->notNull(),
             'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
         ], $tableOptions);
        $this->createIndex('team_id_index_fields', '{{%fields}}','team_id', false);
        $this->addForeignKey('FK_teams_fields','{{%fields}}', 'team_id', '{{%teams}}', 'id');

        $this->addColumn('{{%applications}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%applications}}', 'field_id', $this->integer(). ' after team_id');
        $this->alterColumn('{{%applications}}', 'is_urban',  $this->tinyInteger()->notNull(). ' after status');


        $this->alterColumn('{{%members_phone}}', 'status', $this->tinyInteger()->defaultValue(1). ' after mobile');
        $this->alterColumn('{{%members_email}}', 'status', $this->tinyInteger()->defaultValue(1). ' after email');

        $this->addColumn('{{%archive_reports}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%archive_reports}}', 'field_id', $this->integer(). ' after team_id');

        $this->alterColumn('{{%groups}}', 'branch_id', $this->integer(). ' after region_id');
        $this->alterColumn('{{%groups}}', 'area_id', $this->integer(). ' after region_id');
        $this->addColumn('{{%groups}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%groups}}', 'field_id', $this->integer(). ' after team_id');

        $this->addColumn('{{%loans}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%loans}}', 'field_id', $this->integer(). ' after team_id');

        $this->addColumn('{{%operations}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%operations}}', 'field_id', $this->integer(). ' after team_id');

        $this->addColumn('{{%progress_report_details}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%progress_report_details}}', 'field_id', $this->integer(). ' after team_id');

        $this->addColumn('{{%recoveries}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%recoveries}}', 'field_id', $this->integer(). ' after team_id');

        $this->addColumn('{{%transactions}}', 'team_id', $this->integer(). ' after branch_id');
        $this->addColumn('{{%transactions}}', 'field_id', $this->integer(). ' after team_id');


        $this->alterColumn('{{%accounts}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%account_types}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%activities}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%applications}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%archive_reports}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%areas}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%banks}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%branches}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%branch_account_mapping}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%branch_projects_mapping}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%branch_requests}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%cih_transactions_mapping}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%cities}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%countries}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%credit_divisions}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%devices}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%disbursements}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%districts}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%divisions}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%donations}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%groups}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%loans}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%members}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%members_address}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%members_email}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%members_phone}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%news}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%operations}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%products}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%progress_reports}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%progress_report_details}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%projects}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%project_details_disabled}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%project_details_tevta}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%provinces}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%recoveries}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%regions}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%schedules}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%section_fields_configs}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%transactions}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%users}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%versions}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%view_sections}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%view_section_fields}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');


        $this->alterColumn('{{%business_appraisal}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%business_appraisal}}', 'status', $this->tinyInteger()->notNull(). ' after longitude');
        $this->alterColumn('{{%business_appraisal}}', 'approved_by', $this->integer()->notNull(). ' after status');
        $this->alterColumn('{{%business_appraisal}}', 'approved_on', $this->date()->notNull(). ' after approved_by');

        $this->alterColumn('{{%social_appraisal}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
        $this->alterColumn('{{%social_appraisal}}', 'status', $this->tinyInteger()->notNull(). ' after longitude');
        $this->alterColumn('{{%social_appraisal}}', 'approved_by', $this->integer()->notNull(). ' after status');
        $this->alterColumn('{{%social_appraisal}}', 'approved_on', $this->date()->notNull(). ' after approved_by');

        $this->alterColumn('{{%ba_business_expenses}}', 'application_id', $this->integer()->notNull(). ' after ba_id');
        $this->alterColumn('{{%ba_business_expenses}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');

        $this->alterColumn('{{%ba_existing_investment}}', 'application_id', $this->integer()->notNull(). ' after ba_id');
        $this->alterColumn('{{%ba_existing_investment}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');

        $this->alterColumn('{{%ba_fixed_business_assets}}', 'application_id', $this->integer()->notNull(). ' after ba_id');
        $this->alterColumn('{{%ba_fixed_business_assets}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');

        $this->alterColumn('{{%ba_required_assets}}', 'application_id', $this->integer()->notNull(). ' after ba_id');
        $this->alterColumn('{{%ba_required_assets}}', 'updated_by', $this->integer()->defaultValue(0). ' after created_by');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180507_110605_db_schema_5 cannot be reverted.\n";

        return false;
    }

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
