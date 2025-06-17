<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_awp extends Migration
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
        $this->createTable('{{%awp}}', [
            'id' => $this->primaryKey(),
            'branch_id'=>$this->integer(5)->notNull(),
            'area_id'=>$this->integer(5)->notNull(),
            'region_id'=>$this->integer(5)->notNull(),
            'project_id'=>$this->integer(5)->defaultValue(0)->notNull(),
            'month' => $this->string(20)->notNull(),
            'no_of_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'amount_disbursed' => $this->integer(11)->defaultValue(0)->notNull(),
            'active_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_olp' => $this->integer(11)->defaultValue(0)->notNull(),
            'avg_loan_size' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_closed_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'avg_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'funds_required'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_disbursement'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_no_of_loans'=>$this->integer(11)->defaultValue(0)->notNull(),
            'status'=>$this->integer(11)->defaultValue(0)->notNull(),
            'is_lock'=>$this->tinyInteger(4)->defaultValue(0)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%awp_project_mapping}}', [
            'id' => $this->primaryKey(),
            'awp_id'=>$this->integer(11)->defaultValue(0)->notNull(),
            'project_id'=>$this->integer(11)->defaultValue(0)->notNull(),
            'month' => $this->string(20)->notNull(),
            'no_of_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'avg_loan_size' => $this->integer(11)->defaultValue(0)->notNull(),
            'disbursement_amount' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_olp' => $this->integer(11)->defaultValue(0)->notNull(),
            'active_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_closed_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'monthly_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'avg_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'funds_required'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_recovery'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_disbursement'=>$this->integer(11)->defaultValue(0)->notNull(),
            'actual_no_of_loans'=>$this->integer(11)->defaultValue(0)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);

        $this->createTable('{{%awp_branch_sustainability}}', [
            'id' => $this->primaryKey(),
            'branch_id'=>$this->integer(11)->notNull(),
            'branch_code'=>$this->integer(11)->notNull(),
            'region_id'=>$this->integer(11)->notNull(),
            'area_id'=>$this->integer(11)->notNull(),
            'month' => $this->string(150)->defaultValue(0)->notNull(),
            'amount_disbursed' => $this->integer(11)->defaultValue(0)->notNull(),
            'percentage' => $this->double()->defaultValue(5)->notNull(),
            'income' => $this->double()->defaultValue(0)->notNull(),
            'actual_expense' => $this->double()->defaultValue(0)->notNull(),
            'surplus_deficit' => $this->double()->defaultValue(0)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%awp_loan_management_cost}}', [
            'id' => $this->primaryKey(),
            'branch_id'=>$this->integer(5)->notNull(),
            'area_id'=>$this->integer(5)->notNull(),
            'region_id'=>$this->integer(5)->notNull(),
            'date_of_opening' => $this->integer(11)->notNull(),
            'opening_active_loans' => $this->integer(11)->notNull(),
            'closing_active_loans' => $this->integer(11)->notNull(),
            'average' => $this->integer(11)->notNull(),
            'amount' => $this->integer(11)->notNull(),
            'lmc' => $this->integer(11)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%awp_overdue}}', [
            'id' => $this->primaryKey(),
            'branch_id'=>$this->integer(5)->notNull(),
            'area_id'=>$this->integer(5)->notNull(),
            'region_id'=>$this->integer(5)->notNull(),
            'month' => $this->string(15),
            'date_of_opening' => $this->integer(11),
            'overdue_members' => $this->integer(11),
            'overdue_amount' => $this->integer(11),
            'awp_active_loans' => $this->integer(11),
            'awp_olp' => $this->integer(11),
            'active_loans' => $this->integer(11),
            'olp' => $this->integer(11),
            'diff_active_loans' => $this->integer(11),
            'diff_olp' => $this->integer(11),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
        $this->createTable('{{%awp_target_vs_achievement}}', [
            'id' => $this->primaryKey(),
            'region_id'=>$this->integer(11)->notNull(),
            'area_id'=>$this->integer(11)->notNull(),
            'branch_id'=>$this->integer(11)->notNull(),
            'project_id'=>$this->integer(11)->defaultValue(0)->notNull(),
            'month' => $this->string(15),
            'target_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'target_amount' => $this->integer(11)->defaultValue(0)->notNull(),
            'achieved_loans' => $this->integer(11)->defaultValue(0)->notNull(),
            'achieved_amount' => $this->integer(11)->defaultValue(0)->notNull(),
            'loans_dif' => $this->integer(11)->defaultValue(0)->notNull(),
            'amount_dif' => $this->integer(11)->defaultValue(0)->notNull(),
            'created_at'=>$this->integer(11)->notNull(),
            'updated_at'=>$this->integer(11)->notNull(),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_awp cannot be reverted.\n";

        return false;
    }
}
