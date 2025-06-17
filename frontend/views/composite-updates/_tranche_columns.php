<?php

use yii\helpers\Url;

return [
    /* [
         'class' => 'kartik\grid\CheckboxColumn',
         'width' => '20px',
     ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'id',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'loan_id',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tranch_no',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tranch_amount',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tranch_charges_amount',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'date_disbursed',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'disbursement_id',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'cheque_no',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'cheque_date',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'fund_request_id',
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url, $model, $key) {
                if ($model->date_disbursed > 0) {
                }else{
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-edit"></span>', ['/composite-updates/composite-update-tranche', 'id' => $model->id],
                        ['role' => 'modal-remote', 'title' => 'Update Loan Tranches'],[
                            'title' => Yii::t('yii', 'Update Tranche'),
                        ]);
                }
            },

            'delete' => function ($url, $model, $key) {
                if ($model->date_disbursed > 0) {

                }else{
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-remove"></span>', ['/composite-updates/composite-delete-tranche', 'id' => $model->id],
                        ['title' => 'Delete Loan Tranches'], [
                            'title' => Yii::t('yii', 'Delete Tranche'),
                        ]);
                }
            }
        ],
        'contentOptions' => ['style' => 'width:90px;'],
    ]

];
