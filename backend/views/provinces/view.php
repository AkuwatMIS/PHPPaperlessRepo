<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Provinces */
?>
<div class="provinces-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute'=>'country_id',
                'label'=>'Country',
                'value'=>function($data){return isset($data->country->name)?$data->country->name:'';},],
            //'country_id',
            'name',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
