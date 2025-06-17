<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\jui\DatePicker;
use common\models\Borrowers;
use common\models\Loans;
/*print_r($searchModel->recv_date);
die();*/
$visible_branch = 0;
$visible_area = 0;
if(isset($searchModel->region_id) && !empty($searchModel->region_id)){
    $visible_area = 1;
}
if(isset($searchModel->area_id) && !empty($searchModel->area_id)){
    $visible_branch = 1;
}
return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '20px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fund_source',
        'label'=>'Fund Source'

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
        'label'=>'Project Name'

    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_fund',
        'value'=>function($data){return number_format(str_replace( ',', '', $data['total_fund']));},
        'label'=>'Project Funds(PKR)',

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fund_received',
        'value'=>function($data){return number_format(str_replace( ',', '', $data['fund_received']));},
        'label'=>'Funds Received(PKR)',

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'olp',
        'value'=>function($data){return number_format($data['olp']);},
        'label'=> 'OLP(PKR) as on '.$olp_date.'',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'actual_no_of_loans',
        'label'=>'Actual no of Loans',


    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'disbursement_amount',
        'label'=>'Disbursement Target(PKR)',
        'value'=>function($data){return number_format($data['disbursement_amount']);},

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'expected_recovery',
        'label'=>'Expected Recovery(PKR)',
        'value'=>function($data){return number_format($data['expected_recovery']);},


    ],
    [
        'label'=>'Funds Available',
        'value'=>function($data){return number_format($data['fund_received']-$data['olp']-$data['disbursement_amount']+$data['expected_recovery']);},
        'label'=>'Funds Available(PKR)',

    ],


    /*[
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'],
    ],*/

];   