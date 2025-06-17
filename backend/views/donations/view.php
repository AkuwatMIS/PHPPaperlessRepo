<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Donations */
?>
<div class="donations-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'loan_id',
            'schedule_id',
            [
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>function($data){return isset($data->branch->name)?$data->branch->name:'';}
            ],
            //'branch_id',
            [
                'attribute'=>'project_id',
                'label'=>'Project',
                'value'=>function($data){return isset($data->project->name)?$data->project->name:'';}
            ],
            //'project_id',
            'amount',
            'receive_date',
            'receipt_no',
            'deleted',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
