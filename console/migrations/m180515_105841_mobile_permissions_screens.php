<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_mobile_permissions_screens extends Migration
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
        $this->createTable('{{%mobile_permissions}}', [
            'id' => $this->primaryKey(),
            'role'=>$this->string(50)->notNull(),
            'mobile_screen_id'=>$this->smallInteger(6)->notNull(),
            'permission' => $this->tinyInteger(4)->defaultValue(0)->notNull(),
            'deleted'=>$this->tinyInteger(4)->defaultValue(0)->notNull(),
            'created_by'=>$this->smallInteger(6)->notNull(),
            'updated_by'=>$this->smallInteger(6)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%mobile_screens}}', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull(),
            'deleted'=>$this->tinyInteger(4)->defaultValue(0)->notNull(),
            'created_by'=>$this->smallInteger(6)->notNull(),
            'updated_by'=>$this->smallInteger(6)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_mobile_permissions_screens cannot be reverted.\n";

        return false;
    }
}
