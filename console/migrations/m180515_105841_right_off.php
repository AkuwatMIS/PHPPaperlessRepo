<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_right_off extends Migration
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
        $this->createTable('{{%right_off}}', [
            'id' => $this->primaryKey(),
            'loan_id'=>$this->integer(11)->notNull(),
            'type'=>$this->string(20)->notNull(),
            'reason' => $this->text()->notNull(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->notNull()->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),
            'deleted'=>$this->tinyInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_right_off cannot be reverted.\n";

        return false;
    }
}
