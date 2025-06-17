<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Banks */
?>
<div class="banks-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'bank_name',
            'branch_detail',
            'branch_code',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
