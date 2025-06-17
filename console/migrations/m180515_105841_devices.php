<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_devices extends Migration
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
        $this->createTable('{{%devices}}', [
            'id' => $this->primaryKey(),
            'uu_id'=>$this->string(255)->notNull(),
            'imei_no'=>$this->string(255)->notNull(),
            'os_version' => $this->string(100)->notNull(),
            'device_model' => $this->string(50)->notNull(),
            'push_id' => $this->string(255),
            'access_token' => $this->string(70)->notNull(),
            'status' => $this->tinyInteger(3)->defaultValue(0)->notNull(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
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
        echo "m180515_105841_devices cannot be reverted.\n";

        return false;
    }
}
