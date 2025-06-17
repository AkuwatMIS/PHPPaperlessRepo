<?php

use yii\db\Migration;

/**
 * Class m180507_074022_db_schema_4
 */
class m180507_074022_db_schema_4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%members_phone}}', 'status', $this->tinyInteger()->defaultValue(1));
        $this->addColumn('{{%members_email}}', 'status', $this->tinyInteger()->defaultValue(1));
        $this->alterColumn('{{%members_address}}', 'status', $this->tinyInteger()->defaultValue(1));
        $this->addColumn('{{%applications}}', 'is_urban', $this->tinyInteger()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180507_074022_db_schema_4 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_074022_db_schema_4 cannot be reverted.\n";

        return false;
    }
    */
}
