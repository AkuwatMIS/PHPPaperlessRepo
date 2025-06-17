<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoansDisbursement */
?>
<div class="loans-disbursement-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'loan_id',
            'tranche_id',
            'payment_method_id',
            'created_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
