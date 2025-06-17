<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProgressReportUpdate */
?>
<div class="progress-report-update-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'report_id',
            'region_id',
            'area_id',
            'branch_id',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
