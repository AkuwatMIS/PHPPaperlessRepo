<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Dynamic File</h4>
                </div>
            </div>
        </div>
    </header>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'report_defination_id',
            'sql_filters',
            'visibility',
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>function($data){return isset($data->region->name)?$data->region->name:'Not Set';}
            ],
            //'area_id',
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value'=>function($data){return isset($data->area->name)?$data->area->name:'Not Set';}
            ],
            //'branch_id',
            [
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>function($data){return isset($data->branch->name)?$data->branch->name:'Not Set';}
            ],
            [
                'attribute'=>'project_id',
                'label'=>'Project',
                'value'=>function($data){return isset($data->project->name)?$data->project->name:'Not Set';}
            ],
            [
                'attribute'=>'is_approved',
                'label'=>'Approve Status',
                'value'=>function($data){
                   if($data->is_approved==0){
                       return 'Pending';
                   }else if($data->is_approved==1){
                       return 'Approved';
                   }else{
                      return 'Rejected';
                   }
                }
            ],

            'notification',
            'created_by',
            'created_at',
            'status',
        ],
    ]) ?>

</div>
