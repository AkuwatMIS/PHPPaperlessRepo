<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CreditDivisions */
?>
<div class="credit-divisions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'code',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
