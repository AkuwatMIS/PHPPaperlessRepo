<?php

use yii\db\Migration;

class m130524_201442_table_schema extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'sqlsrv') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            // $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->userTables();
        $this->rbacTables();
        $this->logsTables();
        $this->membersTables();
        $this->applicationsTables();
        $this->appraisalsTables();
        $this->basicTables();
        $this->reportsTables();
        $this->branchesTables();
        $this->projectsTables();
        $this->accountsTables();
        $this->configTables();
        $this->structureTables();
        $this->dynamicFormTables();


        $this->addForeignKeys();

        //$this->execute('alter table applications NOCHECK CONSTRAINT FK_members_applications');
    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey('FK_applications_applications_logs','{{%applications_logs}}', 'id', '{{%applications}}', 'id');

        $this->addForeignKey('FK_donations_donations_logs','{{%donations_logs}}', 'id', '{{%donations}}', 'id');

        $this->addForeignKey('FK_loans_loans_logs','{{%loans_logs}}', 'id', '{{%loans}}', 'id');

        $this->addForeignKey('FK_members_members_logs','{{%members_logs}}', 'id', '{{%members}}', 'id');

        $this->addForeignKey('FK_operations_operations_logs','{{%operations_logs}}', 'id', '{{%operations}}', 'id');

        $this->addForeignKey('FK_recoveries_recoveries_logs','{{%recoveries_logs}}', 'id', '{{%recoveries}}', 'id');

        $this->addForeignKey('FK_cities_users','{{%users}}', 'city_id', '{{%cities}}', 'id');

        $this->addForeignKey('FK_members_members_address','{{%members_address}}', 'member_id', '{{%members}}', 'id');

        $this->addForeignKey('FK_members_members_email','{{%members_email}}', 'member_id', '{{%members}}', 'id');

        $this->addForeignKey('FK_members_members_phone','{{%members_phone}}', 'member_id', '{{%members}}', 'id');

        $this->addForeignKey('FK_members_applications','{{%applications}}', 'member_id', '{{%members}}', 'id');
        $this->addForeignKey('FK_projects_applications','{{%applications}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_activities_applications','{{%applications}}', 'activity_id', '{{%activities}}', 'id');
        $this->addForeignKey('FK_products_applications','{{%applications}}', 'product_id', '{{%products}}', 'id');
        $this->addForeignKey('FK_regions_applications','{{%applications}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_applications','{{%applications}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_applications','{{%applications}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_applications_ba','{{%business_appraisal}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_loans_ba','{{%business_appraisal}}', 'loan_id', '{{%loans}}', 'id');

        $this->addForeignKey('FK_ba_ba_business_expenses','{{%ba_business_expenses}}', 'ba_id', '{{%business_appraisal}}', 'id');

        $this->addForeignKey('FK_ba_ba_existing_investment','{{%ba_existing_investment}}', 'ba_id', '{{%business_appraisal}}', 'id');

        $this->addForeignKey('FK_ba_ba_fixed_business_assets','{{%ba_fixed_business_assets}}', 'ba_id', '{{%business_appraisal}}', 'id');

        $this->addForeignKey('FK_ba_ba_required_assets','{{%ba_required_assets}}', 'ba_id', '{{%business_appraisal}}', 'id');

        $this->addForeignKey('FK_applications_social_appraisal','{{%social_appraisal}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_loans_social_appraisal','{{%social_appraisal}}', 'loan_id', '{{%loans}}', 'id');

        $this->addForeignKey('FK_applications_loans','{{%loans}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_projects_loans','{{%loans}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_activities_loans','{{%loans}}', 'activity_id', '{{%activities}}', 'id');
        $this->addForeignKey('FK_products_loans','{{%loans}}', 'product_id', '{{%products}}', 'id');
        $this->addForeignKey('FK_regions_loans','{{%loans}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_loans','{{%loans}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_loans','{{%loans}}', 'branch_id', '{{%branches}}', 'id');
        $this->addForeignKey('FK_groups_loans','{{%loans}}', 'group_id', '{{%groups}}', 'id');

        $this->addForeignKey('FK_transactions_cih_transactions_mapping','{{%cih_transactions_mapping}}', 'transaction_id', '{{%transactions}}', 'id');

        $this->addForeignKey('FK_applications_donations','{{%donations}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_projects_donations','{{%donations}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_loans_donations','{{%donations}}', 'loan_id', '{{%loans}}', 'id');
        $this->addForeignKey('FK_recoveries_donations','{{%donations}}', 'recovery_id', '{{%recoveries}}', 'id');
        $this->addForeignKey('FK_branches_donations','{{%donations}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_regions_groups','{{%groups}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_groups','{{%groups}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_groups','{{%groups}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_applications_operations','{{%operations}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_projects_operations','{{%operations}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_loans_operations','{{%operations}}', 'loan_id', '{{%loans}}', 'id');
        $this->addForeignKey('FK_transactions_operations','{{%operations}}', 'transaction_id', '{{%transactions}}', 'id');
        $this->addForeignKey('FK_regions_operations','{{%operations}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_operations','{{%operations}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_operations','{{%operations}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_applications_recoveries','{{%recoveries}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_projects_recoveries','{{%recoveries}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_loans_recoveries','{{%recoveries}}', 'loan_id', '{{%loans}}', 'id');
        $this->addForeignKey('FK_schedules_recoveries','{{%recoveries}}', 'schedule_id', '{{%schedules}}', 'id');
        $this->addForeignKey('FK_regions_recoveries','{{%recoveries}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_recoveries','{{%recoveries}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_recoveries','{{%recoveries}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_applications_schedules','{{%schedules}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_loans_schedules','{{%schedules}}', 'loan_id', '{{%loans}}', 'id');
        $this->addForeignKey('FK_branches_schedules','{{%schedules}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_accounts_transactions','{{%transactions}}', 'account_id', '{{%accounts}}', 'id');
        $this->addForeignKey('FK_regions_transactions','{{%transactions}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_transactions','{{%transactions}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_transactions','{{%transactions}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_projects_archive_reports','{{%archive_reports}}', 'project_id', '{{%projects}}', 'id');
        $this->addForeignKey('FK_activities_archive_reports','{{%archive_reports}}', 'activity_id', '{{%activities}}', 'id');
        $this->addForeignKey('FK_products_archive_reports','{{%archive_reports}}', 'product_id', '{{%products}}', 'id');
        $this->addForeignKey('FK_regions_archive_reports','{{%archive_reports}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_archive_reports','{{%archive_reports}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_archive_reports','{{%archive_reports}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_progress_reports_pr_details','{{%progress_report_details}}', 'progress_report_id', '{{%progress_reports}}', 'id');
        $this->addForeignKey('FK_districts_pr_details','{{%progress_report_details}}', 'district_id', '{{%districts}}', 'id');
        $this->addForeignKey('FK_divisions_pr_details','{{%progress_report_details}}', 'division_id', '{{%divisions}}', 'id');
        $this->addForeignKey('FK_provinces_pr_details','{{%progress_report_details}}', 'province_id', '{{%provinces}}', 'id');
        $this->addForeignKey('FK_countries_pr_details','{{%progress_report_details}}', 'country_id', '{{%countries}}', 'id');
        $this->addForeignKey('FK_regions_pr_details','{{%progress_report_details}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_pr_details','{{%progress_report_details}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_branches_pr_details','{{%progress_report_details}}', 'branch_id', '{{%branches}}', 'id');
        $this->addForeignKey('FK_cities_pr_details','{{%progress_report_details}}', 'city_id', '{{%cities}}', 'id');

        $this->addForeignKey('FK_projects_progress_reports','{{%progress_reports}}', 'project_id', '{{%projects}}', 'id');

        $this->addForeignKey('FK_districts_branches','{{%branches}}', 'district_id', '{{%districts}}', 'id');
        $this->addForeignKey('FK_divisions_branches','{{%branches}}', 'division_id', '{{%divisions}}', 'id');
        $this->addForeignKey('FK_provinces_branches','{{%branches}}', 'province_id', '{{%provinces}}', 'id');
        $this->addForeignKey('FK_countries_branches','{{%branches}}', 'country_id', '{{%countries}}', 'id');
        $this->addForeignKey('FK_credit_divisions_branches','{{%branches}}', 'cr_division_id', '{{%credit_divisions}}', 'id');
        $this->addForeignKey('FK_regions_branches','{{%branches}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_branches','{{%branches}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_cities_branches','{{%branches}}', 'city_id', '{{%cities}}', 'id');

        $this->addForeignKey('FK_districts_branch_requests','{{%branch_requests}}', 'district_id', '{{%districts}}', 'id');
        $this->addForeignKey('FK_divisions_branch_requests','{{%branch_requests}}', 'division_id', '{{%divisions}}', 'id');
        $this->addForeignKey('FK_provinces_branch_requests','{{%branch_requests}}', 'province_id', '{{%provinces}}', 'id');
        $this->addForeignKey('FK_countries_branch_requests','{{%branch_requests}}', 'country_id', '{{%countries}}', 'id');
        $this->addForeignKey('FK_credit_divisions_branch_requests','{{%branch_requests}}', 'cr_division_id', '{{%credit_divisions}}', 'id');
        $this->addForeignKey('FK_regions_branch_requests','{{%branch_requests}}', 'region_id', '{{%regions}}', 'id');
        $this->addForeignKey('FK_areas_branch_requests','{{%branch_requests}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_cities_branch_requests','{{%branch_requests}}', 'city_id', '{{%cities}}', 'id');

        $this->addForeignKey('FK_branches_branch_projects_mapping','{{%branch_projects_mapping}}', 'branch_id', '{{%branches}}', 'id');
        $this->addForeignKey('FK_projects_branch_projects_mapping','{{%branch_projects_mapping}}', 'project_id', '{{%projects}}', 'id');

        $this->addForeignKey('FK_branches_branch_account_mapping','{{%branch_account_mapping}}', 'branch_id', '{{%branches}}', 'id');
        $this->addForeignKey('FK_accounts_branch_account_mapping','{{%branch_account_mapping}}', 'account_id', '{{%accounts}}', 'id');

        $this->addForeignKey('FK_branches_accounts','{{%accounts}}', 'branch_id', '{{%branches}}', 'id');

        $this->addForeignKey('FK_products_activities','{{%activities}}', 'product_id', '{{%products}}', 'id');

        $this->addForeignKey('FK_regions_areas','{{%areas}}', 'region_id', '{{%regions}}', 'id');

        $this->addForeignKey('FK_provinces_cities','{{%cities}}', 'province_id', '{{%provinces}}', 'id');

        $this->addForeignKey('FK_divisions_districts','{{%districts}}', 'division_id', '{{%divisions}}', 'id');

        $this->addForeignKey('FK_provinces_divisions','{{%divisions}}', 'province_id', '{{%provinces}}', 'id');

        $this->addForeignKey('FK_countries_provinces','{{%provinces}}', 'country_id', '{{%countries}}', 'id');

        $this->addForeignKey('FK_credit_divisions_regions','{{%regions}}', 'cr_division_id', '{{%credit_divisions}}', 'id');

        $this->addForeignKey('FK_view_sections_view_section_fields','{{%view_section_fields}}', 'section_id', '{{%view_sections}}', 'id');

        $this->addForeignKey('FK_view_section_fields_section_fields_configs','{{%section_fields_configs}}', 'field_id', '{{%view_section_fields}}', 'id');

        $this->addForeignKey('FK_applications_project_details_tevta','{{%project_details_tevta}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_loans__project_details_tevta','{{%project_details_tevta}}', 'loan_id', '{{%loans}}', 'id');

        $this->addForeignKey('FK_applications_project_details_disabled','{{%project_details_disabled}}', 'application_id', '{{%applications}}', 'id');
        $this->addForeignKey('FK_loans_project_details_disabled','{{%project_details_disabled}}', 'loan_id', '{{%loans}}', 'id');
    }

    protected function userTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer(),
            'username' => $this->string(50)->notNull(),
            'fullname' => $this->string(50)->notNull(),
            'father_name' => $this->string(60),
            'email' => $this->string()->notNull()->unique(),
            'cnic' => $this->string(20)->notNull()->unique(),
            'address' => $this->text(),
            'alternate_email' => $this->string(),
            'password' => $this->string(100)->notNull(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string(),
            'last_login_at' => $this->integer()->defaultValue(0),
            'last_login_token' => $this->string(),
            'latitude' => $this->float()->notNull()->defaultValue(0),
            'longitude' => $this->float()->notNull()->defaultValue(0),
            'image' => $this->string(),
            'mobile' => $this->string(50),
            'joining_date' => $this->integer()->defaultValue(0),
            'emp_code' => $this->string(50),
            'designation_id' => $this->integer()->notNull()->defaultValue(0),
            'is_block' => $this->tinyInteger(3)->notNull()->defaultValue(0),
            'reason' => $this->string(20),
            'block_date' => $this->integer()->defaultValue(0),
            'team_name' => $this->string(20),
            'status' => $this->tinyInteger(3)->notNull()->defaultValue(0),
            'term_and_condition' => $this->tinyInteger(3)->defaultValue(0),
            'do_reset_password' => $this->tinyInteger()->notNull()->defaultValue(0),
            'do_complete_profile' => $this->tinyInteger(3)->defaultValue(0),
            'left_thumb_impression' => $this->text(),
            'right_thumb_impression' => $this->text(),
            'post_token' => $this->string(),
            'created_date' => $this->integer()->notNull()->defaultValue(0),
            'expires_at' => $this->integer()->notNull()->defaultValue(0),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createIndex('username_index_users', '{{%users}}','username', false);
        $this->createIndex('email_index_users', '{{%users}}','email', false);
        $this->createIndex('city_index_users', '{{%users}}','city_id', false);


        $this->createTable('{{%user_structure_mapping}}', [
            'user_id' => $this->integer()->notNull(),
            'obj_id' => $this->integer()->notNull(),
            'obj_type' => $this->string(50)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%user_projects_mapping}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%user_devices}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'device_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    protected function rbacTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('Pk_auth_item', '{{%auth_item}}','name');
        $this->createIndex('rule_name_index_auth_item', '{{%auth_item}}','rule_name', false);
        $this->createIndex('type_index_auth_item', '{{%auth_item}}','type', false);


        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('Pk_auth_assignment', '{{%auth_assignment}}',['item_name','user_id']);


        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('Pk_auth_item_child', '{{%auth_item_child}}',['parent','child']);
        $this->createIndex('child_index_auth_item_child', '{{%auth_item_child}}',['child']);


        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('Pk_auth_rule', '{{%auth_rule}}','name');


        $this->createTable('{{%actions}}', [
            'id' => $this->primaryKey(),
            'module' => $this->string(100)->notNull(),
            'action' => $this->string(100)->notNull(),
            'module_type' => $this->string(20)->notNull(),
        ], $tableOptions);
    }

    protected function logsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%applications_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);

        /*$this->createIndex('action_index_application_logs', '{{%application_logs}}','action', false);
        $this->createIndex('application_id_index_application_logs', '{{%application_logs}}','id', false);
        $this->createIndex('field_index_application_logs', '{{%application_logs}}','field',false);
        $this->createIndex('user_id_index_application_logs', '{{%application_logs}}','user_id', false);*/


        $this->createTable('{{%loans_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);
        /*$this->createIndex('action_index_loan_logs', '{{%loan_logs}}','action', false);
        $this->createIndex('loan_id_index_loan_logs', '{{%loan_logs}}','id', false);
        $this->createIndex('field_index_loan_logs', '{{%loan_logs}}','field',false);
        $this->createIndex('user_id_index_loan_logs', '{{%loan_logs}}','user_id', false);*/


        $this->createTable('{{%donations_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),

        ], $tableOptions);
        /*$this->createIndex('action_index_donation_logs', '{{%donation_logs}}','action', false);
        $this->createIndex('doantion_id_index_donation_logs', '{{%donation_logs}}','id', false);
        $this->createIndex('field_index_donation_logs', '{{%donation_logs}}','field',false);
        $this->createIndex('user_id_index_donation_logs', '{{%donation_logs}}','user_id', false);*/


        $this->createTable('{{%members_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);
        /*$this->createIndex('action_index_member_logs', '{{%member_logs}}','action', false);
        $this->createIndex('member_id_index_member_logs', '{{%member_logs}}','id', false);
        $this->createIndex('field_index_member_logs', '{{%member_logs}}','field',false);
        $this->createIndex('user_id_index_member_logs', '{{%member_logs}}','user_id', false);*/


        $this->createTable('{{%operations_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),

        ], $tableOptions);
        /*$this->createIndex('action_index_operation_logs', '{{%operation_logs}}','action', false);
        $this->createIndex('operation_id_index_operation_logs', '{{%operation_logs}}','id', false);
        $this->createIndex('field_index_operation_logs', '{{%operation_logs}}','field',false);
        $this->createIndex('user_id_index_operation_logs', '{{%operation_logs}}','user_id', false);*/


        $this->createTable('{{%recoveries_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);

        $this->createTable('{{%appraisals_social_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);

        $this->createTable('{{%appraisals_social_logs}}', [
            'id' => $this->integer()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string(100)->notNull(),
            'field' => $this->string(100),
            'stamp' => $this->integer()->notNull(),
            'user_id' => $this->smallInteger(),
        ], $tableOptions);
    }

    protected function membersTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%members}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'full_name' => $this->string(50)->notNull(),
            'parentage' => $this->string(50),
            'parentage_type' => $this->string(50),
            'cnic' => $this->string(20)->notNull()->unique(),
            'gender' => $this->string(6)->notNull(),
            'dob' => $this->integer()->notNull()->defaultValue(0),
            'education' => $this->string(20)->notNull(),
            'marital_status' => $this->string(10)->notNull(),
            'family_no' => $this->string(50),
            'family_member_name' => $this->string(25),
            'family_member_cnic' => $this->string(15),
            'religion' => $this->string(20)->notNull(),
            'is_disable' => $this->tinyInteger()->notNull(),
            'disability_type' => $this->string(30),
            'left_index' => $this->text(),
            'right_index' => $this->text(),
            'left_thumb' => $this->text(),
            'right_thumb' => $this->text(),
            'profile_pic' => $this->string(100),
            'status' => $this->string(10)->notNull(),
            'is_lock' => $this->tinyInteger(3)->notNull()->defaultValue(0),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('cnic_index_members', '{{%members}}','cnic', true);
        $this->createIndex('status_index_members', '{{%members}}','status', false);
        $this->createIndex('marital_status_index_members', '{{%members}}','marital_status', false);
        $this->createIndex('gender_index_members', '{{%members}}','gender', false);
        $this->createIndex('full_name_index_members', '{{%members}}','full_name', false);
        $this->createIndex('created_by_index_members', '{{%members}}','created_by', false);


        $this->createTable('{{%members_address}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull(),
            'address' => $this->string()->notNull(),
            'address_type' => $this->string(50)->notNull(),
            'is_current' => $this->tinyInteger(3)->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('index_members_address', '{{%members_address}}','member_id', false);


        $this->createTable('{{%members_email}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull(),
            'email' => $this->string(100)->notNull(),
            'is_current' => $this->tinyInteger(3)->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('index_members_email', '{{%members_email}}','member_id', false);


        $this->createTable('{{%members_phone}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull(),
            'phone' => $this->string(30)->notNull(),
            'phone_type' => $this->string(10)->notNull(),
            'is_current' => $this->tinyInteger(3)->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('index_members_phone', '{{%members_phone}}','member_id', false);
    }

    protected function applicationsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%applications}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull(),
            'fee' => $this->decimal(19,0)->defaultValue(0),
            'application_no' => $this->string(15)->notNull(),
            'project_id' => $this->integer(5)->notNull(),
            'project_table' => $this->string(50),
            'activity_id' => $this->integer(5)->defaultValue(-1),
            'product_id' => $this->integer(5)->notNull(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'field_area_id' => $this->integer()->defaultValue(0)->notNull(),
            'group_id' => $this->integer()->defaultValue(0),
            'no_of_times' => $this->tinyInteger(3)->notNull(),
            'bzns_cond' => $this->string(5),
            'who_will_work' => $this->string(20),
            'name_of_other' => $this->string(25),
            'other_cnic' => $this->string(15),
            'req_amount' => $this->decimal(19,0)->notNull(),
            'status' => $this->string(10)->notNull(),
            'is_urban' => $this->tinyInteger(3)->notNull(),
            'reject_type' => $this->string(15),
            'reject_reason' => $this->text(),
            'comments' => $this->text(),
            'is_lock' => $this->tinyInteger(3)->defaultValue(0)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'is_biometric' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('member_id_index_applications', '{{%applications}}','member_id', false);
        $this->createIndex('req_amount_index_applications', '{{%applications}}','req_amount', false);
        $this->createIndex('group_id_index_applications', '{{%applications}}','group_id', false);
        $this->createIndex('deleted_index_applications', '{{%applications}}','deleted', false);
        $this->createIndex('status_index_applications', '{{%applications}}','status', false);
    }

    protected function appraisalsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%appraisals}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'appraisal_table' => $this->string(50)->notNull(),
            'status' => $this->boolean()->defaultValue(0)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%project_appraisals_mapping}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'appraisal_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%appraisals_business}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'place_of_business' => $this->string(15)->notNull(),
            'fixed_business_assets' => $this->text(),
            'fixed_business_assets_amount' => $this->decimal(19,0),
            'running_capital' => $this->text(),
            'running_capital_amount' => $this->decimal(19,0),
            'business_expenses' => $this->text(),
            'business_expenses_amount' => $this->decimal(19,0),
            'new_required_assets' => $this->text()->notNull(),
            'new_required_assets_amount' => $this->decimal(19,0)->notNull(),
            'latitude' => $this->float()->notNull(),
            'longitude' => $this->float()->notNull(),
            'status' => $this->string(15)->notNull(),
            'bm_verify_latitude' => $this->float()->defaultValue(0)->notNull(),
            'bm_verify_longitude' => $this->float()->defaultValue(0)->notNull(),
            'business_appraisal_address' => $this->text(),
            'is_lock' => $this->tinyInteger(3)->defaultValue(0)->notNull(),
            'approved_by' => $this->integer()->defaultValue(0)->notNull(),
            'approved_on' => $this->integer()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('application_id_index_business', '{{%appraisals_business}}','application_id', false);

        $this->createTable('{{%appraisals_social}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'poverty_index' => $this->string(20),
            'house_ownership' => $this->string(10)->notNull(),
            'house_rent_amount' => $this->decimal(19,0),
            'land_size' => $this->integer()->notNull(),
            'total_family_members' => $this->integer()->notNull(),
            'no_of_earning_hands' => $this->integer()->notNull(),
            'ladies' => $this->integer()->notNull(),
            'gents' => $this->integer()->notNull(),
            'source_of_income' => $this->string(15)->notNull(),
            'total_household_income' => $this->decimal(19,0)->notNull(),
            'utility_bills' => $this->decimal(19,4)->notNull(),
            'educational_expenses' => $this->decimal(19,4)->notNull(),
            'medical_expenses' => $this->decimal(19,4)->notNull(),
            'kitchen_expenses' => $this->decimal(19,4)->notNull(),
            'monthly_savings' => $this->string(15)->notNull(),
            'amount' => $this->decimal(19,0),
            'date_of_maturity' => $this->integer(),
            'other_expenses' => $this->decimal(19,4)->notNull(),
            'total_expenses' => $this->decimal(19,0),
            'other_loan' => $this->tinyInteger()->notNull(),
            'loan_amount' => $this->decimal(19,0),
            'economic_dealings' => $this->string(10)->notNull(),
            'social_behaviour' => $this->string(10)->notNull(),
            'fatal_disease' => $this->tinyInteger()->defaultValue(0)->notNull(),
            'business_income' => $this->decimal(19,0)->defaultValue(0)->notNull(),
            'job_income' => $this->decimal(19,0)->defaultValue(0)->notNull(),
            'house_rent_income' => $this->decimal(19,0)->defaultValue(0)->notNull(),
            'other_income' => $this->decimal(19,0)->defaultValue(0)->notNull(),
            'expected_increase_in_income' => $this->decimal(19,0),
            'social_appraisal_address' => $this->text(),
            'description' => $this->text(),
            'description_image' => $this->string(200),
            'latitude' => $this->float()->notNull(),
            'longitude' => $this->float()->notNull(),
            'status' => $this->string(15)->notNull(),
            'bm_verify_latitude' => $this->float()->defaultValue(0)->notNull(),
            'bm_verify_longitude' => $this->float()->defaultValue(0)->notNull(),
            'is_lock' => $this->tinyInteger(3)->defaultValue(0)->notNull(),
            'approved_by' => $this->integer()->defaultValue(0)->notNull(),
            'approved_on' => $this->integer()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('application_id_index_social', '{{%appraisals_social}}','application_id', false);
    }

    protected function basicTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%loans}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'project_id' => $this->integer(5)->notNull(),
            'project_table' => $this->string(50),
            'date_approved' => $this->integer()->defaultValue(0),
            'loan_amount' => $this->decimal(19,0)->notNull(),
            'disbursement_amount' => $this->decimal(19,0)->notNull(),
            'cheque_no' => $this->string(30),
            'is_disbursed' => $this->boolean()->defaultValue(0),
            'inst_amnt' => $this->decimal(19,0)->notNull(),
            'inst_months' => $this->decimal(19,0)->notNull(),
            'inst_type' => $this->string(30)->notNull(),
            'date_disbursed' => $this->integer()->defaultValue(0),
            'cheque_dt' => $this->integer()->defaultValue(0),
            'disbursement_id' => $this->integer()->defaultValue(0),
            'fund_request_id' => $this->integer()->defaultValue(0),
            'activity_id' => $this->integer(5)->defaultValue(0)->notNull(),
            'product_id' => $this->integer(5)->notNull(),
            'group_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'loan_expiry' => $this->integer(),
            'loan_completed_date' => $this->integer()->defaultValue(0),
            'old_sanc_no' => $this->string(50),
            'remarks' => $this->text(),
            'br_serial' => $this->integer()->notNull(),
            'sanction_no' => $this->string(50)->notNull(),
            'due' => $this->decimal(8,0)->notNull()->defaultValue(0),
            'overdue' => $this->decimal(8,0)->notNull()->defaultValue(0),
            'balance' => $this->decimal(8,0)->notNull()->defaultValue(0),
            'status' => $this->string(15)->notNull(),
            'reject_reason' => $this->text(),
            'attendance_status' => $this->string(20)->defaultValue('info_not_available')->notNull(),
            'is_lock' => $this->tinyInteger(3)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('application_id_index_loans', '{{%loans}}','application_id', false);
        $this->createIndex('area_id_index_loans', '{{%loans}}','area_id', false);
        $this->createIndex('branch_id_index_loans', '{{%loans}}','branch_id', false);
        $this->createIndex('date_disbursed_index_loans', '{{%loans}}','date_disbursed', false);
        $this->createIndex('loan_amount_index_loans', '{{%loans}}','loan_amount', false);
        $this->createIndex('disbursement_id_index_loans', '{{%loans}}','disbursement_id', false);
        $this->createIndex('region_id_index_loans', '{{%loans}}','region_id', false);
        $this->createIndex('sanction_no_index_loans', '{{%loans}}','sanction_no', false);
        $this->createIndex('status_index_loans', '{{%loans}}','status', false);

        $this->createTable('{{%loan_tranches}}', [
            'id' => $this->primaryKey(),
            'loan_id' => $this->integer()->notNull(),
            'tranch_no' => $this->integer()->notNull(),
            'tranch_amount' => $this->decimal(19,0),
            'date_disbursed' => $this->integer()->defaultValue(0),
            'tranch_date' => $this->integer()->defaultValue(0)->notNull(),
            'cheque_no' => $this->string(100),
            'disbursement_id' => $this->integer()->defaultValue(0),
            'fund_request_id' => $this->integer()->defaultValue(0),
            'status' => $this->boolean()->defaultValue(0)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%cih_transactions_mapping}}', [
            'id' => $this->primaryKey(),
            'cih_type_id' => $this->integer()->notNull(),
            'transaction_id' => $this->integer()->notNull(),
            'type' => $this->string(10)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%credit_divisions}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'code' => $this->string(20)->notNull(),
            'status' => $this->tinyInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%disbursements}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'date_disbursed' => $this->integer()->defaultValue(0)->notNull(),
            'venue' => $this->string()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%donations}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer(),
            'loan_id' => $this->integer(),
            'schedule_id' => $this->integer(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'project_id' => $this->integer(5)->notNull(),
            'amount' => $this->decimal(8,0),
            'receive_date' => $this->integer(),
            'receipt_no' => $this->string(20)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('loan_id_index_donations', '{{%donations}}','loan_id', false);
        $this->createIndex('credit_index_donations', '{{%donations}}','amount', false);
        $this->createIndex('branch_id_index_donations', '{{%donations}}','branch_id', false);
        $this->createIndex('recv_date_index_donations', '{{%donations}}','receive_date', false);


        $this->createTable('{{%groups}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'is_locked' => $this->tinyInteger(3)->notNull(),
            'br_serial' => $this->integer()->notNull(),
            'grp_no' => $this->string(20)->notNull(),
            'group_name' => $this->string(100)->notNull(),
            'grp_type' => $this->string(20)->notNull(),
            'group_size' => $this->integer(5)->defaultValue(0),
            'status' => $this->string(10)->notNull(),
            'reject_reason' => $this->text(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('grp_no_index_groups', '{{%groups}}','grp_no', false);


        $this->createTable('{{%operations}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer(),
            'operation_type_id' => $this->integer()->notNull(),
            'credit' => $this->decimal(10,0)->notNull(),
            'receipt_no' => $this->string(15),
            'receive_date' => $this->integer()->defaultValue(0)->notNull(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'transaction_id' => $this->integer(),
            'project_id' => $this->integer(),
            'recv_date_old' => $this->date(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%recoveries}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'schedule_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'due_date' => $this->integer()->defaultValue(0),
            'receive_date' => $this->integer()->notNull(),
            'amount' => $this->decimal(8,0)->notNull(),
            'receipt_no' => $this->string(20)->notNull(),
            'project_id' => $this->integer(5),
            'type' => $this->tinyInteger(3)->notNull(),
            'source' => $this->string(20)->notNull(),
            'is_locked' => $this->tinyInteger(3)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('region_id_index_recoveries', '{{%recoveries}}','region_id', false);
        $this->createIndex('area_id_index_recoveries', '{{%recoveries}}','area_id', false);
        $this->createIndex('branch_id_index_recoveries', '{{%recoveries}}','branch_id', false);
        $this->createIndex('loan_id_index_recoveries', '{{%recoveries}}','loan_id', false);
        $this->createIndex('schedule_id_index_recoveries', '{{%recoveries}}','schedule_id', false);
        $this->createIndex('credit_index_recoveries', '{{%recoveries}}','amount', false);


        $this->createTable('{{%schedules}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer()->defaultValue(0)->notNull(),
            'branch_id' => $this->integer()->defaultValue(0)->notNull(),
            'due_date' => $this->integer()->defaultValue(0)->notNull(),
            'schdl_amnt' => $this->decimal(10,0)->defaultValue(0)->notNull(),
            'overdue' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'overdue_log' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'advance' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'advance_log' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'due_amnt' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'credit' => $this->decimal(8,0)->defaultValue(0)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(0)->notNull(),
            'deleted_by' => $this->integer()->defaultValue(0)->notNull(),
            'platform' => $this->tinyInteger()->defaultValue(1)->notNull(),
        ], $tableOptions);
        $this->createIndex('due_date_index_schedules', '{{%schedules}}','due_date', false);
        $this->createIndex('credit_index_schedules', '{{%schedules}}','credit', false);
        $this->createIndex('branch_id_index_schedules', '{{%schedules}}','branch_id', false);
        $this->createIndex('loan_id_index_schedules', '{{%schedules}}','loan_id', false);
        $this->createIndex('schdl_amnt_index_schedules', '{{%schedules}}','schdl_amnt', false);


        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(10)->notNull(),
            'region_id' => $this->integer(5)->notNull(),
            'area_id' => $this->integer(5)->notNull(),
            'branch_id' => $this->integer(5)->notNull(),
            'team_id' => $this->integer(5)->notNull(),
            'field_id' => $this->integer(5)->notNull(),
            'amount' => $this->decimal(19,4)->notNull(),
            'tax' => $this->decimal(19,4)->notNull(),
            'account_id' => $this->integer()->notNull(),
            'deposit_slip_no' => $this->string(20),
            'deposit_date' => $this->integer()->defaultValue(0),
            'deposited_by' => $this->integer(),
            'status' => $this->string(20)->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    protected function reportsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%archive_reports}}', [
            'id' => $this->primaryKey(),
            'report_name' => $this->string()->notNull(),
            'region_id' => $this->integer()->defaultValue(0),
            'area_id' => $this->integer()->defaultValue(0),
            'branch_id' => $this->integer()->defaultValue(0),
            'team_id' => $this->integer()->defaultValue(0),
            'field_id' => $this->integer()->defaultValue(0),
            'project_id' => $this->integer()->defaultValue(0),
            'activity_id' => $this->integer()->defaultValue(0),
            'product_id' => $this->integer()->defaultValue(0),
            'date_filter' => $this->string(100),
            'source' => $this->string(20)->notNull(),
            'gender' => $this->string(5)->notNull()->defaultValue(0),
            'file_path' => $this->string(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'do_delete' => $this->boolean()->defaultValue(0)->notNull(),
            'requested_by' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%progress_report_details}}', [
            'id' => $this->primaryKey(),
            'progress_report_id' => $this->integer()->notNull(),
            'division_id' => $this->integer()->notNull(),
            'region_id' => $this->integer()->notNull(),
            'area_id' => $this->integer()->notNull(),
            'branch_id' => $this->integer()->notNull(),
            'team_id' => $this->integer()->notNull(),
            'field_id' => $this->integer()->notNull(),
            'country_id' => $this->integer(),
            'province_id' => $this->integer(),
            'district_id' => $this->integer(),
            'city_id' => $this->integer(),
            'branch_code' => $this->string(10),
            'gender' => $this->string(6)->notNull(),
            'no_of_loans' => $this->integer()->notNull(),
            'family_loans' => $this->integer()->notNull(),
            'female_loans' => $this->integer()->notNull(),
            'active_loans' => $this->integer()->notNull(),
            'cum_disb' => $this->bigInteger()->notNull(),
            'cum_due' => $this->bigInteger()->notNull(),
            'cum_recv' => $this->bigInteger()->notNull(),
            'overdue_borrowers' => $this->integer()->notNull(),
            'overdue_amount' => $this->bigInteger()->notNull(),
            'overdue_percentage' => $this->decimal(5,0)->notNull(),
            'par_amount' => $this->bigInteger()->notNull(),
            'par_percentage' => $this->decimal(5,0)->notNull(),
            'not_yet_due' => $this->bigInteger()->notNull(),
            'olp_amount' => $this->bigInteger()->notNull(),
            'recovery_percentage' => $this->decimal(7,4),
            'cih' => $this->bigInteger()->notNull(),
            'mdp' => $this->bigInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('progress_report_id_index_progress_report_details', '{{%progress_report_details}}','progress_report_id', false);
        $this->createIndex('division_id_index_progress_report_details', '{{%progress_report_details}}','division_id', false);
        $this->createIndex('region_id_index_progress_report_details', '{{%progress_report_details}}','region_id', false);
        $this->createIndex('area_id_index_progress_report_details', '{{%progress_report_details}}','area_id', false);
        $this->createIndex('branch_id_index_progress_report_details', '{{%progress_report_details}}','branch_id', false);
        $this->createIndex('country_id_index_progress_report_details', '{{%progress_report_details}}','country_id', false);
        $this->createIndex('province_id_index_progress_report_details', '{{%progress_report_details}}','province_id', false);
        $this->createIndex('district_id_index_progress_report_details', '{{%progress_report_details}}','district_id', false);
        $this->createIndex('city_id_index_progress_report_details', '{{%progress_report_details}}','city_id', false);


        $this->createTable('{{%progress_reports}}', [
            'id' => $this->primaryKey(),
            'report_date' => $this->integer()->defaultValue(0),
            'project_id' => $this->integer()->notNull(),
            'gender' => $this->string(6)->notNull(),
            'period' => $this->string(20)->notNull(),
            'comments' => $this->text(),
            'status' => $this->tinyInteger(),
            'is_verified' => $this->tinyInteger(),
            'do_update' => $this->integer(),
            'do_delete' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('period_index_progress_reports', '{{%progress_reports}}','period', false);
        $this->createIndex('project_id_index_progress_reports', '{{%progress_reports}}','project_id', false);
        $this->createIndex('report_date_index_progress_reports', '{{%progress_reports}}','report_date', false);

    }

    protected function branchesTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%branches}}', [

            'id' => $this->primaryKey(),
            'region_id' => $this->integer()->notNull(),
            'area_id' => $this->integer()->notNull(),
            'type' => $this->string(20),
            'name' => $this->string(50)->notNull(),
            'short_name' => $this->string(10),
            'code' => $this->string(10),
            'uc' => $this->string(25)->notNull(),
            'village' => $this->string(100)->notNull(),
            'address' => $this->string(),
            'mobile' => $this->string(20),
            'city_id' => $this->integer()->notNull(),
            'tehsil_id' => $this->integer(),
            'district_id' => $this->integer(),
            'division_id' => $this->integer(),
            'province_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
            'latitude' => $this->float()->notNull(),
            'longitude' => $this->float()->notNull(),
            'description' => $this->text(),
            'opening_date' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(),
            'cr_division_id' => $this->integer(),
            'effective_date' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'opening_date_old' => $this->date(),
            'created_on_old' => $this->dateTime(),
            'updated_on_old' => $this->timestamp(),
        ], $tableOptions);
        $this->createIndex('region_id_index_branches', '{{%branches}}','region_id', false);
        $this->createIndex('area_id_index_branches', '{{%branches}}','area_id', false);
        $this->createIndex('code_index_branches', '{{%branches}}','code', true);


        $this->createTable('{{%branch_requests}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer()->notNull(),
            'area_id' => $this->integer()->notNull(),
            'type' => $this->string(20),
            'name' => $this->string(50)->notNull(),
            'uc' => $this->string(25)->notNull(),
            'village' => $this->string(100)->notNull(),
            'address' => $this->string(),
            'city_id' => $this->integer()->notNull(),
            'tehsil_id' => $this->integer(),
            'district_id' => $this->integer(),
            'division_id' => $this->integer(),
            'province_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
            'branch_id' => $this->integer()->defaultValue(0)->notNull(),
            'projects' => $this->string(100),
            'latitude' => $this->float()->notNull(),
            'longitude' => $this->float()->notNull(),
            'description' => $this->text(),
            'opening_date' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(),
            'reject_reason' => $this->string(),
            'cr_division_id' => $this->integer(),
            'remarks' => $this->text(),
            'effective_date' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('region_id_index_branch_requests', '{{%branch_requests}}','region_id', false);
        $this->createIndex('area_id_index_branch_requests', '{{%branch_requests}}','area_id', false);


        $this->createTable('{{%branch_request_actions}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'action' => $this->string(20),
            'status' => $this->tinyInteger()->defaultValue(0)->notNull(),
            'remarks' => $this->text(),
            'pre_action' => $this->integer()->defaultValue(0)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('parent_id_index_branch_request_actions', '{{%branch_request_actions}}','parent_id', false);
        $this->createIndex('user_id_index_branch_request_actions', '{{%branch_request_actions}}','user_id', false);
        $this->createIndex('status_index_branch_request_actions', '{{%branch_request_actions}}','status', false);

        $this->createTable('{{%branch_projects_mapping}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'branch_id' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('project_id_index_branch_projects_mapping', '{{%branch_projects_mapping}}','project_id', false);
        $this->createIndex('branch_id_index_branch_projects_mapping', '{{%branch_projects_mapping}}','branch_id', false);


        $this->createTable('{{%branch_account_mapping}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);

    }

    protected function accountsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%account_types}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(30)->notNull(),
            'description' => $this->text()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%accounts}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'acc_no' => $this->string(30)->notNull(),
            'bank_info' => $this->string(100)->notNull(),
            'funding_line' => $this->string(20)->notNull(),
            'purpose' => $this->string(10)->notNull(),
            'dt_opening' => $this->date()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('branch_id_index_accounts', '{{%accounts}}','branch_id', false);
        $this->createIndex('created_by_index_accounts', '{{%accounts}}','created_by', false);


        $this->createTable('{{%banks}}', [
            'id' => $this->primaryKey(),
            'bank_name' => $this->string(50)->notNull(),
            'branch_detail' => $this->string()->notNull(),
            'branch_code' => $this->string(20)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
    }

    protected function configTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%access_tokens}}', [
            'id' => $this->primaryKey(),
            'token' => $this->string()->notNull(),
            'expires_at' => $this->integer()->notNull(),
            'auth_code' => $this->string(200)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'app_id' => $this->string(200),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%authorization_codes}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(150)->notNull(),
            'expires_at' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'app_id' => $this->string(200),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%devices}}', [
            'id' => $this->primaryKey(),
            'uu_id' => $this->string()->notNull(),
            'imei_no' => $this->string()->notNull(),
            'os_version' => $this->string(100)->notNull(),
            'device_model' => $this->string(50)->notNull(),
            'push_id' => $this->string()->notNull(),
            'access_token' => $this->string(70)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('uu_id_index_devices', '{{%devices}}','uu_id', false);
        $this->createIndex('imei_no_index_devices', '{{%devices}}','imei_no', false);
        $this->createIndex('device_model_index_devices', '{{%devices}}','device_model', false);


        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'heading' => $this->string()->notNull(),
            'short_description' => $this->string()->notNull(),
            'full_description' => $this->text()->notNull(),
            'news_date' => $this->dateTime()->notNull(),
            'image_name' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%versions}}', [
            'id' => $this->primaryKey(),
            'version_no' => $this->integer()->notNull(),
            'type' => $this->string(40)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%mobile_permissions}}', [
            'id' => $this->primaryKey(),
            'role' => $this->string(50)->notNull(),
            'mobile_screen_id' => $this->smallInteger()->notNull(),
            'permission' => $this->tinyInteger()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('role_index_mobile_permissions', '{{%mobile_permissions}}','role', false);


        $this->createTable('{{%mobile_screens}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%config_rules}}', [
            'id' => $this->primaryKey(),
            'group' => $this->string(20)->notNull(),
            'priority' => $this->smallInteger()->notNull(),
            'key' => $this->string(20)->notNull(),
            'value' => $this->string(100)->notNull(),
            'operator' => $this->string(10)->notNull(),
            'operator_desc' => $this->string(30)->notNull(),
            'parent_type' => $this->string(20)->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->defaultValue(0),
            'field_name' => $this->string(50)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%documents}}', [
            'id' => $this->primaryKey(),
            'module_type' => $this->string(30)->notNull(),
            'module_id' => $this->integer()->defaultValue(0)->notNull(),
            'parent_type' => $this->string(30),
            'name' => $this->string(50)->notNull(),
            'is_required' => $this->boolean()->defaultValue(1)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%notifications}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull(),
            'parent_type' => $this->string(50)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'is_read' => $this->boolean()->defaultValue(0)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


    }

    protected function structureTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%activities}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'name' => $this->string(100)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);



        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'code' => $this->string(10)->notNull(),
            'inst_type' => $this->string(20)->notNull(),
            'min' => $this->integer()->notNull(),
            'max' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%areas}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'code' => $this->string(20)->notNull(),
            'tags' => $this->string(100),
            'short_description' => $this->text(),
            'mobile' => $this->string(20),
            'opening_date' => $this->integer()->defaultValue(0),
            'full_address' => $this->text(),
            'region_id' => $this->integer(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'opening_date_old' => $this->date(),
            'created_on_old' => $this->dateTime(),
            'updated_on_old' => $this->timestamp(),
        ], $tableOptions);
        $this->createIndex('code_index_areas', '{{%areas}}','code', true);


        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'province_id' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('province_id_index_cities', '{{%cities}}','province_id', false);


        $this->createTable('{{%countries}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'continent' => $this->string(100)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);


        $this->createTable('{{%districts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'code' => $this->string(10),
            'division_id' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('division_id_index_districts', '{{%districts}}','division_id', false);



        $this->createTable('{{%divisions}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'province_id' => $this->integer(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('province_id_index_divisions', '{{%divisions}}','province_id', false);


        $this->createTable('{{%provinces}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer(),
            'name' => $this->string(50)->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%regions}}', [
            'id' => $this->primaryKey(),
            'cr_division_id' => $this->integer()->notNull(),
            'name' => $this->string(50)->notNull(),
            'code' => $this->string(20)->notNull(),
            'tags' => $this->string(100),
            'short_description' => $this->text(),
            'mobile' => $this->string(20),
            'opening_date' => $this->integer()->defaultValue(0),
            'full_address' => $this->text(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'opening_date_old' => $this->date(),
            'created_on_old' => $this->dateTime(),
            'updated_on_old' => $this->timestamp(),
        ], $tableOptions);
        $this->createIndex('cr_division_id_index_regions', '{{%regions}}','cr_division_id', false);

        $this->createTable('{{%teams}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(10)->notNull(),
            'branch_id' => $this->integer()->notNull(),
            'description' => $this->text(),
            'status' => $this->tinyInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('branch_id_index_teams', '{{%teams}}','branch_id', false);

        $this->createTable('{{%tehsils}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'district_id' => $this->integer(),
            'status' => $this->tinyInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);
        $this->createIndex('district_id_index_tehsils', '{{%tehsils}}','district_id', false);
    }

    protected function dynamicFormTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lists}}', [
            'id' => $this->primaryKey(),
            'list_name' => $this->string(100)->notNull(),
            'value' => $this->string()->notNull(),
            'label' => $this->string(100)->notNull(),
            'sort_order' => $this->tinyInteger()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%view_sections}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(30)->notNull(),
            'section_name' => $this->string(100)->notNull(),
            'section_description' => $this->text()->notNull(),
            'section_table_name' => $this->string(100)->notNull(),
            'sort_order' => $this->smallInteger()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%view_section_fields}}', [
            'id' => $this->primaryKey(),
            'section_id' => $this->integer()->notNull(),
            'table_name' => $this->string(100),
            'field' => $this->string(100)->notNull(),
            'sort_order' => $this->smallInteger(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%section_fields_configs}}', [
            'id' => $this->primaryKey(),
            'field_id' => $this->integer()->notNull(),
            'key_name' => $this->string(100)->notNull(),
            'value' => $this->string(100),
            'parent_id' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    protected function projectsTables(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%projects}}', [
            'id' => $this->primaryKey(),
            'project_table' => $this->string(50),
            'name' => $this->string()->notNull(),
            'code' => $this->string()->notNull(),
            'donor' => $this->string(),
            'funding_line' => $this->string(5),
            'started_date' => $this->integer()->defaultValue(0),
            'logo' => $this->string(),
            'loan_amount_limit' => $this->integer(),
            'description' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%project_product_mapping}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'product_id' => $this->string(50)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%projects_tevta}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer(),
            'institute_name' => $this->string(150),
            'type_of_diploma' => $this->string(100),
            'duration_of_diploma' => $this->string(100),
            'year' => $this->string(50),
            'pbte_or_ttb' => $this->string(),
            'registration_no' => $this->string(),
            'roll_no' => $this->string(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'dt_applied_old' => $this->date(),
        ], $tableOptions);


        $this->createTable('{{%projects_disabled}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'is_khidmat_card_holder' => $this->tinyInteger()->notNull(),
            'loan_id' => $this->integer(),
            'disability' => $this->string(20),
            'nature' => $this->string(20),
            'physical_disability' => $this->string(20),
            'visual_disability' => $this->string(20),
            'communicative_disability' => $this->string(20),
            'disabilities_instruments' => $this->string(20),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'dt_applied_old' => $this->date(),
        ], $tableOptions);

        $this->createTable('{{%projects_agriculture}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer(),
            'owner' => $this->string(100),
            'land_area_size' => $this->integer(),
            'land_area_type' => $this->string(20),
            'village_name' => $this->string(),
            'uc_number' => $this->string(20),
            'uc_name' => $this->string(),
            'crop_type' => $this->string(50),
            'crops' => $this->string(),
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->defaultValue(0)->notNull(),
            'dt_applied_old' => $this->date(),
        ], $tableOptions);

    }
}
