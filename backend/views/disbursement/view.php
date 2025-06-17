<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
?>
<div class="disbursements-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'date_disbursed',
            'venue',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
