<?php

use yii\db\Migration;

/**
 * Class m180514_063630_db_schema_member_update
 */
class m180514_063630_db_schema_member_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->renameColumn('{{%members_address}}', 'status', 'is_current');
        $this->renameColumn('{{%members_phone}}', 'status', 'is_current');
        $this->renameColumn('{{%members_phone}}', 'mobile', 'phone_type');
        $this->renameColumn('{{%members_email}}', 'status', 'is_current');

        $this->alterColumn('{{%members_phone}}', 'phone', $this->string(20)->notNull());
        $this->alterColumn('{{%members_phone}}', 'phone_type', $this->string(10)->notNull());

        $this->alterColumn('{{%applications}}', 'fee', $this->decimal(19,4));
        $this->alterColumn('{{%applications}}', 'project_table', $this->string(50));
        $this->addColumn('{{%applications}}', 'group_id', $this->integer()->defaultValue(0) . ' after field_id');
        $this->renameColumn('{{%applications}}', 'form_no', 'application_no');

        $this->addColumn('{{%project_details_disabled}}', 'is_khidmat_card_holder', $this->tinyInteger()->notNull() . ' after application_id');

        $this->addColumn('{{%members}}', 'region_id', $this->integer()->notNull() . ' after id');
        $this->addColumn('{{%members}}', 'area_id', $this->integer()->notNull() . ' after region_id');
        $this->addColumn('{{%members}}', 'branch_id', $this->integer()->notNull() . ' after area_id');
        $this->addColumn('{{%members}}', 'team_id', $this->integer()->notNull() . ' after branch_id');
        $this->addColumn('{{%members}}', 'field_id', $this->integer()->notNull() . ' after team_id');
        $this->addColumn('{{%members}}', 'is_disable', $this->tinyInteger()->notNull() . ' after religion');
        $this->addColumn('{{%members}}', 'disability', $this->string(20) . ' after is_disable');
        $this->addColumn('{{%members}}', 'nature', $this->string(20) . ' after disability');
        $this->addColumn('{{%members}}', 'disability_type', $this->string(30) . ' after nature');
        $this->alterColumn('{{%members}}', 'profile_pic', $this->string(100));

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%project_details_agriculture}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer(),
            'owner' => $this->string(100)->notNull(),
            'land_area_size' => $this->float()->notNull(),
            'land_area_type' => $this->string(20)->notNull(),
            'village_name' => $this->string()->notNull(),
            'uc_number' => $this->string(20)->notNull(),
            'uc_name' => $this->string()->notNull(),
            'crop_type' => $this->string(50)->notNull(),
            'crops' => $this->string()->notNull(),
            /*'bank_branch_code' => $this->string(10)->notNull(),
            'product_code' => $this->string(4)->notNull()->defaultValue(4558),
            'primary_document' => $this->string(4)->notNull()->defaultValue('CNIC'),
            'mother_name' => $this->string(50)->notNull(),
            'mailing_address_code' => $this->string(20)->notNull(),
            'permanent_address_code' => $this->string(20)->notNull(),
            'cnic_expiry' => $this->integer()->notNull(),
            'exempt_withholding_tax' => $this->string(5)->notNull()->defaultValue('N'),
            'zakat_deduction' => $this->string(5)->notNull()->defaultValue('CNIC'),
            'kin_name' => $this->string(50)->notNull(),
            'kin_cnic' => $this->string(20)->notNull(),
            'kin_relation' => $this->string(20)->notNull(),
            'kin_address' => $this->string()->notNull(),
            'kin_address_areacode' => $this->string(20)->notNull(),
            'kin_contact' => $this->string(50)->notNull(),*/
            'assigned_to' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180514_063630_db_schema_member_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_063630_db_schema_member_update cannot be reverted.\n";

        return false;
    }
    */
}
