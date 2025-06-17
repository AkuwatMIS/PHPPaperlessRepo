<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
use kartik\date\DatePicker;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
        //'format'=>'integer',
        //'pageSummary' => 'Total',

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
            return Html::a($data->name, ['member/view', 'id' => $data->application->member->id], ['target' => '_blank'], ['title' => 'Borrower']);
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
        'attribute' => 'report_date',
        'value' => function ($data) {
            return isset($_GET['OverduelistSearch']['report_date']) ? ($_GET['OverduelistSearch']['report_date']) : date('Y-m');
        },
        'label' => 'Disb. date',
        'label' => 'Report date',
        'filter' => DatePicker::widget([
            'name' => 'OverduelistSearch[report_date]',
            'value' => isset($_GET['OverduelistSearch']['report_date']) && !empty($_GET['OverduelistSearch']['report_date']) ? ($_GET['OverduelistSearch']['report_date']) : date('Y-m'),

            'options' => ['placeholder' => 'Report Date',/*'value'=>date('Y-m')*/],
            'type' => \kartik\date\DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-mm',
            ]])
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'loan_amount',
        'value'=>function($data){return number_format($data->loan_amount);},
        'label' => 'Loan Amount',
        ///'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'overdue_amount',
        'label' => 'Overdue',
        //'format' => 'decimal',
        'value' => function ($data) {
            return ($data->overdue_amount > 0 ? (number_format($data->overdue_amount)) : 0);
        },
        // 'pageSummary' => true,
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'outstanding_balance',
        'label' => 'Balance',
        //'format' => 'decimal',
        'value' => function ($data) {
           return ($data->outstanding_balance > 0 ? (number_format($data->outstanding_balance)) : 0);
        },
    ],
];   