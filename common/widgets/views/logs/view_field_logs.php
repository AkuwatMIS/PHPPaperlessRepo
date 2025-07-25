<?php

use yii\widgets\DetailView;
use common\models\Users;
use common\components\Helpers\LogsHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */
?>
<div class="view-logs">
    <table class="table table-striped table-bordered detail-view">
        <thead>
        <th>Field</th>
        <th>Diff</th>
        <th>Changed By</th>
        <th>Change Date</th>
        </thead>
        <tbody>
        <?php
        if(isset($model_logs)) {
            foreach ($model_logs as $model_log)
            {
                $diff = LogsHelper::getDiffHtml($model_log);?>
                <tr>
                    <td><?=$model_log->field?></td>
                    <td><?=LogsHelper::htmlDiff($model_log->old_value, $model_log->new_value)?></td>
                    <?php if($model_log->module_type == 1) {
                        $user = \common\models\User::find()->select('username')->where(['id' => $model_log->user_id])->one(); } else {
                        $user = Users::find()->select('username')->where(['id' => $model_log->user_id])->one();
                    } ?>
                    <td><?=$user->username?></td>
                    <td><?= Yii::$app->formatter->asDate($model_log->stamp).' '.Yii::$app->formatter->asTime($model_log->stamp)?></td>

                </tr>
        <?php
            }
        } ?>
        </tbody>
    </table>
</div>
