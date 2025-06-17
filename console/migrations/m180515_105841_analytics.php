<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_analytics extends Migration
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
        $this->createTable('{{%analytics}}', [
            'id' => $this->primaryKey(),
            'user_id'=>$this->smallInteger(6)->notNull(),
            'api'=>$this->string(100)->notNull(),
            'count' => $this->integer(11)->notNull(),
            'type' => $this->string(50)->notNull(),
            'short_name' => $this->string(20)->notNull(),
            'description' => $this->text()->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),
            'deleted'=>$this->tinyInteger(4)->notNull()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_analytics cannot be reverted.\n";

        return false;
    }
}
