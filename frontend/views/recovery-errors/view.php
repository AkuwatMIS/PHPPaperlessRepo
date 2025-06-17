<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RecoveryErrors */
?>
<div class="recovery-errors-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'recovery_files_id',
            'branch_id',
            'area_id',
            'region_id',
            'source',
            'sanction_no',
            'recv_date',
            'credit',
            'receipt_no',
            'bank_branch_name',
            'bank_branch_code',
            'balance',
            'error_description',
            'comments',
            'assigned_to',
            'created_by',
            'created_at',
            'updated_at',
            'status',
        ],
    ]) ?>

</div>
