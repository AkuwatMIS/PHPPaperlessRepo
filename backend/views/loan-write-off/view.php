<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */
?>
<div class="loan-write-off-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'loan_id',
            'recovery_id',
            'amount',
            'cheque_no',
            'voucher_no',
            'bank_name',
            'bank_account_no',
            'type',
            'reason',
            'deposit_slip_no',
            'borrower_name',
            'borrower_cnic',
            'who_will_work',
            'other_name',
            'other_cnic',
            'write_off_date',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
