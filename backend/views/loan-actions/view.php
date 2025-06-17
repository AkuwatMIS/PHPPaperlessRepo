<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoanActions */
?>
<div class="loan-actions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent_id',
            'user_id',
            'action',
            'status',
            'pre_action',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
