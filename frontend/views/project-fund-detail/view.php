<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectFundDetail */
?>
<div class="project-fund-detail-view">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i>Failure!</h4>
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php else: ?>
        <h4>
            Fund received details added successfully!.
        </h4>
    <?php endif; ?>
    <?php /* DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'project_id',
            'fund_batch_amount',
            'no_of_loans',
            //'allocation_date',
            [
                'attribute' => 'allocation_date',
                'value' => function($data) {
                    return date('d M Y',$data->allocation_date);
                }
            ],

            //'receive_date',
            'status',
        ],
    ]) */?>

</div>
