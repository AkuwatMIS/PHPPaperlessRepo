<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_bank_recoveries extends Migration
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
        $this->createTable('{{%connection_banks}}', [
            'id' => $this->primaryKey(),
            'bank_name' => $this->string(100)->notNull(),
            'bank_code' => $this->string(100)->notNull(),
            'description'=>$this->text(),
            'charges'=>$this->integer(11)->notNull(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),


        ], $tableOptions);
        $this->createTable('{{%recovery_files}}', [
            'id' => $this->primaryKey(),
            'source' => $this->string(50)->notNull(),
            'description' => $this->string(255)->notNull(),
            'file_date'=>$this->integer(11)->notNull(),
            'file_name'=>$this->string(50)->notNull(),
            'status'=>$this->string(20),
            'total_records'=>$this->integer(11)->notNull(),
            'inserted_records'=>$this->integer(11),
            'error_records'=>$this->integer(11),
            'updated_by'=>$this->integer(11)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),


        ], $tableOptions);
        $this->createTable('{{%recovery_errors}}', [
            'id' => $this->primaryKey(),
            'recovery_files_id' => $this->integer(11)->notNull(),
            'branch_id' => $this->integer(11)->notNull()->defaultValue(0),
            'area_id'=>$this->integer(11)->notNull()->defaultValue(0),
            'region_id'=>$this->integer(11)->notNull()->defaultValue(0),
            'bank_branch_name'=>$this->string(50),
            'bank_branch_code'=>$this->string(50),
            'source'=>$this->string(50)->defaultValue('branch'),
            'sanction_no'=>$this->string(50),
            'cnic'=>$this->string(20),
            'recv_date'=>$this->integer(11)->notNull(),
            'credit'=>$this->decimal(8,0)->notNull()->defaultValue(0),
            'receipt_no'=>$this->string(20)->notNull(),
            'balance'=>$this->decimal(8,0)->notNull()->defaultValue(0),
            'error_description'=>$this->string(255)->notNull(),
            'comments'=>$this->text(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'status'=>$this->string(50),


        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_bank_recoveries cannot be reverted.\n";

        return false;
    }
}
