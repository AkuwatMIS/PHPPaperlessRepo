<?php

use yii\db\Migration;

/**
 * Class m180509_050533_db_schema_6
 */
class m180509_050533_db_schema_6 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%members}}', 'is_lock', $this->tinyInteger(). ' after status');

        $this->alterColumn('{{%applications}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%applications}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%archive_reports}}', 'team_id', $this->integer()->defaultValue(0));
        $this->alterColumn('{{%archive_reports}}', 'field_id', $this->integer()->defaultValue(0));

        $this->alterColumn('{{%groups}}', 'branch_id', $this->integer()->notNull());
        $this->alterColumn('{{%groups}}', 'area_id', $this->integer()->notNull());
        $this->alterColumn('{{%groups}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%groups}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%loans}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%loans}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%operations}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%operations}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%progress_report_details}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%progress_report_details}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%recoveries}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%recoveries}}', 'field_id', $this->integer()->notNull());

        $this->alterColumn('{{%transactions}}', 'team_id', $this->integer()->notNull());
        $this->alterColumn('{{%transactions}}', 'field_id', $this->integer()->notNull());

        $this->dropForeignKey('FK_loans__project_details_tevta','{{%project_details_tevta}}');
        $this->alterColumn('{{%project_details_tevta}}', 'loan_id', $this->integer());

        $this->dropForeignKey('FK_applications_project_details_disabled','{{%project_details_disabled}}');
        $this->alterColumn('{{%project_details_disabled}}', 'loan_id', $this->integer());

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%api_keys}}', [
            'id' => $this->primaryKey(),
            'api_key' => $this->string()->notNull(),
            'purpose' => $this->string(50)->notNull(),
        ], $tableOptions);

        $this->addColumn('{{%accounts}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%account_types}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%activities}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->alterColumn('{{%archive_reports}}', 'do_delete',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%areas}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%banks}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%ba_business_expenses}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%ba_existing_investment}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%ba_fixed_business_assets}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%ba_required_assets}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%branches}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%branch_account_mapping}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%branch_projects_mapping}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%branch_requests}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%business_appraisal}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%cih_transactions_mapping}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%cities}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%countries}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%credit_divisions}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%devices}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%disbursements}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%districts}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%divisions}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%fields}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%groups}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%members_address}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%members_email}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%members_phone}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%news}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%operations}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%products}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->alterColumn('{{%progress_reports}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%progress_report_details}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%projects}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%project_details_disabled}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%project_details_tevta}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%provinces}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%regions}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%schedules}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%social_appraisal}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%teams}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%transactions}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%users}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');
        $this->addColumn('{{%versions}}', 'deleted',  $this->boolean()->defaultValue(0)->notNull(). ' after updated_at');


        $this->addColumn('{{%users}}', 'cnic',  $this->string(20)->notNull(). ' after email');
        //$this->createIndex('cnic_index_users', '{{%users}}','cnic', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180509_050533_db_schema_6 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180509_050533_db_schema_6 cannot be reverted.\n";

        return false;
    }
    */
}
