<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
?>
<div class="recoveries-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'schedule_id',
            'loan_id',
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>function($data){return isset($data->region->name)?$data->region->name:'';}
            ],
            //'region_id',
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value'=>function($data){return isset($data->area->name)?$data->area->name:'';}
            ],
            //'area_id',
            [
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>function($data){return isset($data->branch->name)?$data->branch->name:'';}
            ],
            //'branch_id',
            [
            'attribute'=>'team_id',
            'label'=>'Team',
            'value'=>function($data){return isset($data->team->name)?$data->team->name:'';}
        ],
           // 'team_id',
            'field_id',
            'due_date',
            'receive_date',
            'mdp',
            'amount',
            'receipt_no',
            'project_id',
            'type',
            'source',
            'is_locked',
            'deleted',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
