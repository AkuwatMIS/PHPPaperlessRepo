<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Countries */
?>
<div class="countries-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'continent',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
