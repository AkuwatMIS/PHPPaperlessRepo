<?php

use yii\db\Migration;

/**
 * Class m180515_105841_auth_rules
 */
class m180515_105841_auth_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsProject();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsArea();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsRegion();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsBranch();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsTeam();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsField();
        $auth->add($rule);

        $auth = Yii::$app->authManager;
        $rule = new \common\rules\IsOwner();
        $auth->add($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_105841_auth_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180515_105841_auth_rules cannot be reverted.\n";

        return false;
    }
    */
}
