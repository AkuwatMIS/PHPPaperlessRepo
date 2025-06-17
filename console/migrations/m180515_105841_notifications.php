<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_notifications extends Migration
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
        $this->createTable('{{%notifications}}', [
            'id' => $this->primaryKey(),
            'parent_id'=>$this->integer(11)->notNull(),
            'parent_type'=>$this->string(50)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'token'=>$this->string(255)->notNull(),
            'message'=>$this->text()->notNull(),
            'is_read'=>$this->tinyInteger(1)->defaultValue(0)->notNull(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_notifications cannot be reverted.\n";

        return false;
    }
}
