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
        'label' => 'Member Name',
        'filter' => '',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->application->member->full_name;
        }
    ],
    [
        'label' => 'Member Cnic',
        'filter' => '',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->application->member->cnic;
        }
    ],
    [
        'label' => 'Borrower Account No',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->application->member->membersAccounts->account_no;
        }
    ],
    [
        'label' => 'Tranche Amount',
        'value' => function ($model) {
            return $model->disbursement->tranch->tranch_amount;
        }
    ],
    [
        'label' => 'Project Name',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->project->name;
        }
    ],
    [
        'label' => 'Sanction No',
        'filter' => '',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->sanction_no;
        }
    ],
    [
        'label' => 'Loan Amount',
        'value' => function ($model) {
            return $model->disbursement->tranch->loan->loan_amount;
        }
    ],

    'reject_reason',
    'tranche_no',
    [
        'label' => 'Rejection Status',
        'contentOptions' => function ($model, $key, $index, $column) {
            return ['style' => '\'bold\'=>true;color:'
                . ($model->status == 1 ? 'green' : 'red')];
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
        'attribute' => 'created_at',
        'label' => 'Date Of Rejection',
        'value' => function ($model) {
            Yii::$app->formatter->locale = 'en-US';
            return Yii::$app->formatter->asDate($model->created_at);
        }

    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}{delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                if (empty($model->loan)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], ['title' => 'view']);
                }
            },
            'delete' => function ($url, $model, $key) {
                if ($model->is_verified == 0 && in_array(Yii::$app->user->getId(),[$model->created_by,5507])) {
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