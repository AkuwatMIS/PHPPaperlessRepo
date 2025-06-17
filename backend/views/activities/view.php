<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Activities */
?>
<div class="activities-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            ['attribute'=>'product_id',
                'value'=>function($data){return isset($data->product->name)?($data->product->name):'';}],
            'name',
            //'status',

        ],
    ]) ?>

</div>
