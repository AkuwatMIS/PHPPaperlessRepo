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
            'no_of_loans',
            'avg_loan_size',
            'disbursement_amount',
            'monthly_olp',
            'active_loans',
            'monthly_closed_loans',
            'monthly_recovery',
            'avg_recovery',
            'funds_required',
            'actual_recovery',
            'actual_disbursement',
            'actual_no_of_loans',
        ],
    ]) ?>

</div>
