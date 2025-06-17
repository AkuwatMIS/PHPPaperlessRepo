<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Schedules */
?>
<div class="schedules-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'loan_id',
            //'branch_id',
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>function($data){return isset($data->branch->name)?$data->branch->name:'';}
            ],
            'due_date',
            'schdl_amnt',
            'overdue',
            'overdue_log',
            'advance',
            'advance_log',
            'due_amnt',
            'credit',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
