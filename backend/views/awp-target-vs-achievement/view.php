<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */
?>
<div class="awp-target-vs-achievement-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'region_id',
            'area_id',
            'branch_id',
            'project_id',
            'month',
            'target_loans',
            'target_amount',
            'achieved_loans',
            'achieved_amount',
            'loans_dif',
            'amount_dif',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
