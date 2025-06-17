<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementDetails */
?>
<div class="disbursement-details-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tranche_id',
            'bank_name',
            'account_no',
            'transferred_amount',
            'disbursement_id',
            'status',
            'response_code',
            'response_description:ntext',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
