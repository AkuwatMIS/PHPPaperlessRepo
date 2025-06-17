<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Divisions */
?>
<div class="divisions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
           // 'province_id',
            [
                'attribute'=>'province_id',
                'label'=>'Province',
                'value'=>function($data){return isset($data->province->name)?$data->province->name:'';}
            ],
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
