<?php

use yii\db\Migration;

/**
 * Class m180509_050533_db_schema_6
 */
class m180509_050533_db_schema_users_address_cnic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {

        $this->alterColumn('{{%users}}', 'password', $this->string(100)->notNull());
        $this->alterColumn('{{%users}}', 'cnic', $this->string(20)->notNull()->unique());
        $this->addColumn('{{%users}}', 'address', $this->text(). ' after cnic');
        $this->addColumn('{{%users}}', 'term_and_condition', $this->tinyInteger()->defaultValue(0). ' after status');

    }
}
