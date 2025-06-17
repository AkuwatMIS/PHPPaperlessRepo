<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
?>
<div class="dynamic-reports-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'report_defination_id',
            'filters',
            'visibility',
            'notification',
            'created_by',
            'created_at',
            'status',
        ],
    ]) ?>

</div>
