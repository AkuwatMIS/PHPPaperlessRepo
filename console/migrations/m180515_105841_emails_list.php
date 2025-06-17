<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_emails_list extends Migration
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
        $this->createTable('{{%emails_list}}', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull(),
            'sender_email'=>$this->string(100)->notNull(),
            'status' => $this->tinyInteger(4)->defaultValue(0)->notNull(),
            'deleted'=>$this->tinyInteger(4)->defaultValue(0)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%emails_list_details}}', [
            'id' => $this->primaryKey(),
            'email_list_id'=>$this->integer(11)->notNull(),
            'receiver_email'=>$this->string(100)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->notNull(),
            'deleted'=>$this->integer(11)->defaultValue(0)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%email_logs}}', [
            'id' => $this->primaryKey(),
            'type'=>$this->string(50)->notNull(),
            'sender_email'=>$this->string(100)->notNull(),
            'receiver_email'=>$this->string(100)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_emails_list cannot be reverted.\n";

        return false;
    }
}
