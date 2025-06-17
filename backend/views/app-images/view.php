<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppImages */
?>
<div class="app-images-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'path',
            'sort_order',
            'status',
        ],
    ]) ?>

</div>
