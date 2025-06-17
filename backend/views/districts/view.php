<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Districts */
?>
<div class="districts-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'code',
            ['attribute'=>'division_id',
                'label'=>'Division',
                'value'=>function($data){return isset($data->division->name)?$data->division->name:'';},],
            // 'division_id',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
