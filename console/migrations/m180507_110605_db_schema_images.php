<?php

use yii\db\Migration;

/**
 * Class m180507_110605_db_schema_5
 */
class m180507_110605_db_schema_images extends Migration
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
        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull(),
            'parent_type' => $this->string(255)->notNull(),
            'image_name' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }
}
