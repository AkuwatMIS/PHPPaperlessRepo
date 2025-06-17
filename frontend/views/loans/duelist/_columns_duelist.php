<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
use kartik\date\DatePicker;
return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
        //'format'=>'integer',
        //'pageSummary' =>'Total',

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'sanction_no',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->sanction_no, ['loans/ledger', 'id' => $data->id], ['target' => '_blank'], ['title' => 'Borrower']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->name, ['members/view', 'id' => $data->member_id], ['target' => '_blank'], ['title' => 'Borrower']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'parentage',
        'label' => 'Parentage',
        'value' => 'parentage'
    ],

    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'team_id',
        'value'=> function ($data) {
            return $data->team_name;
        },
        'label' => 'Team Name',
        //'filter' => $teams,
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'report_date',
        'value' => function ($data) {
            return isset($_GET['DuelistSearch']['report_date']) && !empty($_GET['DuelistSearch']['report_date']) ? ($_GET['DuelistSearch']['report_date']) : date('Y-m');
        },
        'label' => 'Report date',
        'filter' => DatePicker::widget([
            'name' => 'DuelistSearch[report_date]',
            'value' => isset($_GET['DuelistSearch']['report_date']) && !empty($_GET['DuelistSearch']['report_date']) ? ($_GET['DuelistSearch']['report_date']) : date('Y-m'),
            'options' => ['placeholder' => 'Report Date'/*,'value'=>date('Y-m')*/],
            'type' => \kartik\date\DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-m',
            ]])

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('loan_amount'),
        'value'=>function($data){return number_format($data->loan_amount);},
        //'format' => ['decimal'],
        'label' => 'Amount',
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('tranch_amount'),
        'value'=>function($data){
        return number_format($data->tranch_amount);},
        //'format' => ['decimal'],
        'label' => 'Tranche Amount',
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('tranch_no'),

        //'format' => ['decimal'],
        'label' => 'Tranche No',
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'due_amount',
        'label' => 'Due Amount',
        //'format' => ['decimal'],

        'value' => function ($data) {
            $schedule_amount = $data['inst_amnt'];
            $credit = $data['credit'];
            $outstanding_balance = $data['outstanding_balance'];
            if ($schedule_amount > $outstanding_balance) {
                return number_format($outstanding_balance);
            } else {
                if (($schedule_amount - $credit) > 0) {
                    return round($data['due_amount']);
                    // return number_format($schedule_amount - $credit);
                } else {
                    return number_format($schedule_amount);
                }
            }
        },
        // 'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'credit',
        'label' => 'Recv Amount',
        //'format' => ['decimal'],
        'value' => function ($data) {
            return ($data->credit > 0 ? (number_format($data->credit)) : '0');
        },


        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'outstanding_balance',
        'label' => 'Balance',
        //'format' => ['decimal'],
        'value' => function ($data) {
            return ($data->outstanding_balance > 0 ? (number_format($data->outstanding_balance)) : '0');
        },

        // 'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'grpno',
        'label' => 'Group No',
        'value' => 'grpno'
    ],

    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'address',
        'label' => 'Address',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'mobile',
        'label' => 'Mobile',
        'value' => 'mobile'
    ],
];   