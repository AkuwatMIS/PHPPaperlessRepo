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
if(isset($data['searchModel']->region_id) && !empty($data['searchModel']->region_id)){
    $visible_area = 1;
}
if(isset($data['searchModel']->area_id) && !empty($data['searchModel']->area_id)){
    $visible_branch = 1;
}
return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_name',
        'label' => 'Region',
        'footer' =>'Total',
        'value' => function ($data) {
            $array = \common\components\Helpers\StructureHelper::getStructureList('regions', 'id', $data['region_name']);
            return isset($array['0']['name'])?$array['0']['name']:'Not Set';
        },
        //'value' => $regions['region_id'],
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_name',
        'label' => 'Area',
        'value' => function ($data) {
            $array = \common\components\Helpers\StructureHelper::getStructureList('areas', 'id', $data['area_name']);
            return isset($array['0']['name'])?$array['0']['name']:'Not Set';
        },
        'visible' => $visible_area
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_name',
        'label' => 'Branch Code',
        'value' => function ($data) {
            $array = \common\components\Helpers\StructureHelper::getStructureList('branches', 'id', $data['branch_name']);
            return isset($array['0']['code'])?$array['0']['code']:'Not Set';
        },
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
        //'format' => ['decimal'],
        'value'=>function($data){return number_format($data['no_of_loans']);},
        'label' => 'No of Loans',
        'footer' => number_format($data['total']['no_of_loans']),
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan_amount',
        //'format' => ['decimal'],
        'value'=>function($data){return number_format($data['loan_amount']);},
        'label' => 'Disbursement Amount(PKR)',
        'footer' => number_format($data['total']['loan_amount']),
    ],
];   