<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoanTranchesActions */
?>
<div class="loan-tranches-actions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent_id',
            'user_id',
            'action',
            'status',
            'pre_action',
            'expiry_date',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
