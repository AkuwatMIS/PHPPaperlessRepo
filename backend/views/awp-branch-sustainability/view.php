<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */
?>
<div class="awp-branch-sustainability-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'branch_id',
            'branch_code',
            'region_id',
            'area_id',
            'month',
            'amount_disbursed',
            'percentage',
            'income',
            'actual_expense',
            'surplus_deficit',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
