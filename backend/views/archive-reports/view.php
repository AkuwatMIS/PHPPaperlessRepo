<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ArchiveReports */
?>
<div class="archive-reports-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'report_name',
            'source',
            //'region_id',
            //'area_id',
            //'branch_id',
            //'project_id',
            'date_filter',
            //'activity_id',
            //'product_id',
            //'gender',
            //'requested_by',
            //'file_path',
            'branch_codes',
            'status',
            'created_at',
            'updated_at',
            //'do_delete',
        ],
    ]) ?>

</div>
