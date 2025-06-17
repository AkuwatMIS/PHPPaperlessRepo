<?php

use yii\db\Migration;

/**
 * Class m180510_055657_db_schema_8
 */
class m180510_055657_db_schema_8 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%sms_logs}}', [
            'id' => $this->primaryKey(),
            'sms_type' => $this->string(50)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'number' => $this->decimal(19,4)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->notNull(),
        ], $tableOptions);

        $this->addColumn('{{%images}}', 'image_type', $this->string()->notNull() . ' after parent_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180510_055657_db_schema_8 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_055657_db_schema_8 cannot be reverted.\n";

        return false;
    }
    */
}
