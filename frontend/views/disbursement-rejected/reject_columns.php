<?php

use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;
use \yii\helpers\Html;

return [
    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        'attribute' => 'borrower_name',
        'label' => 'Member Name',
        'filter' => '',
//        'value' => function ($model) {
//            return $model->disbursement->tranch->loan->application->member->full_name;
//        }
    ],
    [
        'attribute' => 'borrower_cnic',
        'label' => 'Member Cnic',
        'filter' => '',
//        'value' => function ($model) {
//            return $model->disbursement->tranch->loan->application->member->cnic;
//        }
    ],
    [
        'attribute' => 'sanction_no',
        'label' => 'Sanction No',
//        'value' => function ($model) {
//            return $model->disbursement->tranch->loan->sanction_no;
//        }
    ],
    [
        'attribute' => 'loan_amount',
        'label' => 'Loan Amount',
//        'value' => function ($model) {
//            return $model->disbursement->tranch->loan->loan_amount;
//        }
    ],

    [
        'attribute' => 'project_id',
        'label' => 'Project',
        'filter'=> $projectArray,
        'value' => function ($model) {
            return $model->project->name;
        }
    ],

    'reject_reason',
    'deposit_slip_no',
    [
        'attribute' => 'deposit_date',
        'value' => function ($model, $key, $index) {
            return date('d M Y', $model['deposit_date']);
        },
    ],
    'deposit_bank',
    'deposit_amount',
    [
        'label' => 'Rejection Status',
        'attribute' => 'status', // must match your model's attribute
        'filter' => [
            0 => 'Pending for Review',
            1 => 'Rejected',
            2 => 'Verification Pending'
        ],
        'contentOptions' => function ($model, $key, $index, $column) {
            return ['style' => 'font-weight:bold;color:' . ($model->status == 1 ? 'green' : 'red')];
        },
        'value' => function ($model) {
            if ($model->status == 0) {
                return 'Pending for Review';
            } elseif ($model->status == 1) {
                return 'Rejected';
            } else {
                return 'Verification Pending';
            }
        }
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                if (empty($model->loan)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], ['title' => 'view']);
                }
            },
            'delete' => function ($url, $model, $key) {
                if ($model->is_verified == 0 && in_array(Yii::$app->user->getId(), [$model->created_by, 5507])) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete', 'id' => $model->id],
                        [
                            'title' => 'Delete',
                            'data-toggle' => 'tooltip',
                            'data-method' => 'post',
                            'data-confirm' => 'Are You Sure You Want To Delete This Item?'
                        ]);
                }
            }
        ],
//        'visible' => (Yii::$app->user->identity->designation_id == 1),
    ],
];