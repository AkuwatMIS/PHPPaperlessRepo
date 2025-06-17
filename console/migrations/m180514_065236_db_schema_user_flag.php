<?php

use yii\db\Migration;

/**
 * Class m180514_065236_db_schema_user_flag
 */
class m180514_065236_db_schema_user_flag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%users}}', 'do_complete_profile', $this->tinyInteger()->defaultValue(0) . ' after term_and_condition');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180514_065236_db_schema_user_flag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_065236_db_schema_user_flag cannot be reverted.\n";

        return false;
    }
    */
}
