<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoanTranches */
?>
<div class="loan-tranches-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'loan_id',
            'tranch_no',
            'tranch_amount',
            'tranch_charges_amount',
            'date_disbursed',
            'disbursement_id',
            'attendance_status',
            'cheque_no',
            'cheque_date',
            'start_date',
            'fund_request_id',
            'tranch_date',
            'status',
            'deleted',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'platform',
        ],
    ]) ?>

</div>
