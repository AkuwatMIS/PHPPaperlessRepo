<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AgingReports */
?>
<div class="aging-reports-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'start_month',
            'one_month',
            'next_three_months',
            'next_six_months',
            'next_one_year',
            'next_two_year',
            'next_three_year',
            'next_five_year',
            'total',
            'status',
            'updated_at',
        ],
    ]) ?>

</div>
