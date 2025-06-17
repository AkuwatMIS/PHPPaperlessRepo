<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_aging_reports extends Migration
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
        $this->createTable('{{%aging_reports}}', [
            'id' => $this->primaryKey(),
            'typ'=>$this->string(50)->notNull(),
            'start_month'=>$this->integer(11)->notNull(),
            'one_month' => $this->double()->defaultValue(0),
            'next_three_months' => $this->double()->defaultValue(0)->notNull(),
            'next_six_months' => $this->double()->defaultValue(0)->notNull(),
            'next_one_year' => $this->double()->defaultValue(0)->notNull(),
            'next_two_year' => $this->double()->defaultValue(0)->notNull(),
            'next_three_year' => $this->double(0)->defaultValue(0)->notNull(),
            'next_five_year'=>$this->double()->defaultValue(0)->notNull(),
            'total'=>$this->double()->defaultValue(0)->notNull(),
            'status'=>$this->tinyInteger(4)->defaultValue(0)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),
        ], $tableOptions);
       }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_aging_reports cannot be reverted.\n";

        return false;
    }
}
