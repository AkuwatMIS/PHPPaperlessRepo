<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Funds */
?>
<div class="funds-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'total_fund',
            'fund_received',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
