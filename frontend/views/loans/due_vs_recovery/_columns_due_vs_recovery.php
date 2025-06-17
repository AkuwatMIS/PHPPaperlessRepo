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
            return Html::a($data->sanction_no, ['loans/ledger', 'id' => $data->id],['target'=>'_blank'],['title'=>'Ledger']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->name, ['members/view', 'id' => $data->member_id],['target'=>'_blank'],['title'=>'Borrower']);
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
        'attribute' => 'team_name',
        'label' => 'Team Name',
        'value' => function ($data) {
            return $data->team_name;
        },
        'filter' =>$data['teams'] ,
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'report_date',
        'value'=>function($data){return isset($_GET['DuelistSearch']['report_date'])&&!empty($_GET['DuelistSearch']['report_date'])?($_GET['DuelistSearch']['report_date']):date('Y-m');},
        'label' => 'Report date',
        'filter'=>DatePicker::widget([
            'value'=>isset($_GET['DuelistSearch']['report_date'])&&!empty($_GET['DuelistSearch']['report_date'])?($_GET['DuelistSearch']['report_date']):date('Y-m'),
            'name' => 'DuelistSearch[report_date]',
            'options' => ['placeholder' => 'Report Date','value'=>date('Y-m')],
            'type'=> \kartik\date\DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-mm',
            ]])
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'loan_amount',
        'label' => 'Amount',
        //'format'=>['decimal'],
        'value' => function ($data) {return number_format($data->loan_amount);},
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('tranch_amount'),
        'value'=>function($data){return number_format($data->tranch_amount);},
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
        //'format'=>['decimal'],
        'value' => function ($data) {
            return (!isset($data->due_amount)||($data->due_amount) < 0) ? 0 : (number_format($data->due_amount));
        },
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'this_month_recovery',
        'label' => 'This Month Recovery',
        //'format'=>['decimal'],
        'value' => function ($data) {
            return isset($data->this_month_recovery) ?(number_format($data->this_month_recovery)) : 0;
        },
        //'pageSummary' => true,

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'outstanding_balance',
        'label' => 'Balance',
        //'format'=>['decimal'],
        'value' => function ($data) {
            return $data->outstanding_balance>0?(number_format($data->outstanding_balance)):0;
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
        'value' => 'address'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'mobile',
        'label' => 'Mobile',
        'value' => 'mobile'
    ],
];   