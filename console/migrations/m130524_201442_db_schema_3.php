<?php

/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 5/7/2018
 * Time: 10:20 AM
 */
use yii\db\Migration;
class m130524_201442_db_schema_3 extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%product_activity_mapping}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'activity_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createTable('{{%project_product_mapping}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
        ], $tableOptions);
    }

}