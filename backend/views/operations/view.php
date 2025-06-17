<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Operations */
?>
<div class="operations-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'loan_id',
            'operation_type_id',
            'credit',
            'receipt_no',
            'receive_date',
            'branch_id',
            'team_id',
            'field_id',
            'project_id',
            'region_id',
            'area_id',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
