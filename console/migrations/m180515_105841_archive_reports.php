<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_archive_reports extends Migration
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
        $this->createTable('{{%archive_reports}}', [
            'id' => $this->primaryKey(),
            'report_name' => $this->string(255)->notNull(),
            'region_id'=>$this->integer(11)->defaultValue(0),
            'area_id'=>$this->integer(11)->defaultValue(0),
            'branch_id' => $this->integer(11)->defaultValue(0),
            'branch_codes' => $this->text()->notNull(),
            'team_id' => $this->integer(11)->defaultValue(0),
            'field_id' => $this->integer(11)->defaultValue(0),
            'project_id' => $this->integer(11)->defaultValue(0),
            'activity_id' => $this->integer(11)->defaultValue(0),
            'product_id' => $this->integer(11)->defaultValue(0),
            'date_filter' => $this->string(100),
            'source' => $this->string(20)->notNull(),
            'gender' => $this->string(5)->notNull()->defaultValue('0'),
            'file_path' => $this->string(255),
            'status' => $this->tinyInteger(3)->notNull()->defaultValue(0),
            'requested_by'=>$this->integer(11)->notNull(),
            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),
            'do_delete'=>$this->tinyInteger(1)->notNull()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_archive_reports cannot be reverted.\n";

        return false;
    }
}
