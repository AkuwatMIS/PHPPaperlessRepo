<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Accounts */
?>
<div class="accounts-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            ['attribute'=>'branch_id',
                'value'=>function($data){
                  return isset($data->branch->name)?$data->branch->name:'';

                },
                'label'=>'Branch'
                ],
            'acc_no',
            'bank_info',
            'funding_line',
            'purpose',
            //'dt_opening',
            ['attribute'=>'dt_opening',
                'value'=>date('d-M-Y', $model->dt_opening)],
            /*'assigned_to',
            'created_by',
            'updated_by',*/
           /* 'created_at',
            'updated_at',*/
        ],
    ]) ?>

</div>
