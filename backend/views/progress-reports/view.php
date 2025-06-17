<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProgressReports */
?>
<div class="progress-reports-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'report_date',
            'project_id',
            'gender',
            'period',
            'comments:ntext',
            'status',
            'is_verified',
            'do_update',
            'do_delete',
            'deleted',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
