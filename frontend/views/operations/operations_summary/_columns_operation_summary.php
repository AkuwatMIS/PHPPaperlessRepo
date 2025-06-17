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
    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_name',
        'label' => 'Region',
        'footer' =>'Total',
        //'value' => $regions['region_id'],
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_name',
        'label' => 'Area',
        'visible' => $visible_area
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_name',
        'label' => 'Branch Code',
        'visible' => $visible_branch
        //'value' => 'loan.branch.name'
    ],
     /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'project_name',
         'label' => 'Project',
         //'value' => 'project.name'
     ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'no_of_loans',
        'format' => ['decimal'],
        'label' => 'No of Loans',
        'footer' => number_format($total['no_of_loans']),
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
        'value'=>function($data){
        if($data['operation_type_id']=='1'){return $data['amount'].' fee';}
        else if($data['operation_type_id']=='2'){return $data['amount'].' takaf';}
        },
        //'format' => ['decimal'],
        'label' => 'Amount(PKR)',
        'footer' => number_format($total['amount']),
    ],
   /* [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fee',
        'format' => ['decimal'],
        'label' => 'Fee Amount(PKR)',
        //'footer' => number_format($total['fee']),
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'mdp',
        'label' => 'MDP Amount(PKR)',
        'format' => ['decimal'],
        'footer' => number_format($total['mdp']),
    ],*/

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