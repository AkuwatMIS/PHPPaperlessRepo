<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RecoveryFiles */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Recovery File</h4>
                </div>
            </div>
        </div>
    </header>
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'source',
            'description',
            'file_date',
            'file_name',
            'status',
            'total_records',
            'inserted_records',
            'error_records',
            'updated_by',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
