<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_branch_requests extends Migration
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
        $this->createTable('{{%branch_requests}}', [
            'id' => $this->primaryKey(),
            'region_id'=>$this->integer(11)->notNull(),
            'area_id'=>$this->integer(11)->notNull(),
            'type' => $this->string(20),
            'name' => $this->string(50)->notNull(),
            'short_name' => $this->string(10),
            'code' => $this->string(10),
            'uc' => $this->string(25)->notNull(),
            'village' => $this->string(100),
            'address' => $this->string(255),
            'city_id'=>$this->integer(11)->notNull(),
            'tehsil_id'=>$this->integer(11)->notNull(),
            'district_id'=>$this->integer(11)->notNull(),
            'division_id'=>$this->integer(11)->notNull(),
            'province_id'=>$this->integer(11)->notNull(),
            'country_id'=>$this->integer(11)->notNull(),
            'latitude'=>$this->float()->notNull(),
            'longitude'=>$this->integer()->notNull(),
            'description'=>$this->text(),
            'opening_date'=>$this->integer(11)->defaultValue(0),
            'status'=>$this->tinyInteger(3),
            'reject_reason' => $this->string(255),
            'cr_division_id'=>$this->integer(11)->notNull(),
            'remarks'=>$this->text(),
            'recommended_on'=>$this->integer(11)->defaultValue(0),
            'recommended_by'=>$this->integer(11),
            'recommended_remarks'=>$this->text(),
            'effective_date'=>$this->integer(11)->defaultValue(0),

            'assigned_to'=>$this->integer(11)->notNull(),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),
            'deleted'=>$this->tinyInteger(1)->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->createTable('{{%branch_request_actions}}', [
            'id' => $this->primaryKey(),
            'parent_id'=>$this->integer(11)->notNull(),
            'user_id'=>$this->integer(11)->notNull(),
            'action' => $this->string(20)->notNull(),
            'status' => $this->tinyInteger(4)->notNull()->defaultValue(0),
            'remarks' => $this->text(),
            'pre_action' => $this->integer(11)->notNull()->defaultValue(0),
            'created_by'=>$this->integer(11)->notNull(),
            'updated_by'=>$this->integer(11)->defaultValue(0),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->addForeignKey('FK_areas_branch_requests','{{%branch_requests}}', 'area_id', '{{%areas}}', 'id');
        $this->addForeignKey('FK_cities_branch_requests','{{%branch_requests}}', 'city_id', '{{%cities}}', 'id');
        $this->addForeignKey('FK_countries_branch_requests','{{%branch_requests}}', 'country_id', '{{%countries}}', 'id');
        $this->addForeignKey('FK_districts_branch_requests','{{%branch_requests}}', 'district_id', '{{%districts}}', 'id');
        $this->addForeignKey('FK_divisions_branch_requests','{{%branch_requests}}', 'division_id', '{{%divisions}}', 'id');
        $this->addForeignKey('FK_provinces_branch_requests','{{%branch_requests}}', 'province_id', '{{%provinces}}', 'id');
        $this->addForeignKey('FK_regions_branch_requests','{{%branch_requests}}', 'region_id', '{{%regions}}', 'id');

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
