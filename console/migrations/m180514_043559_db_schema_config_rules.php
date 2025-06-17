<?php

use yii\db\Migration;

/**
 * Class m180514_043559_db_schema_config_rules
 */
class m180514_043559_db_schema_config_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%config_rules}}', [
            'id' => $this->primaryKey(),
            'group' => $this->string(20)->notNull(),
            'priority' => $this->smallInteger()->notNull(),
            'key' => $this->string(20)->notNull(),
            'value' => $this->string(100)->notNull(),
            'parent_type' => $this->string(20)->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->notNull(),
        ], $tableOptions);
    }

}
