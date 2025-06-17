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
    ['class' => 'yii\grid\SerialColumn'],
    [
       // 'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_name',
        'label' => 'Region',
        'footer' =>'Total',
        'value' => function ($data) {
            $array = \common\components\Helpers\StructureHelper::getStructureList('regions', 'id', $data['region_name']);
            return isset($array['0']['name'])?$array['0']['name']:'Not Set';
        }
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
      //  'class'=>'\kartik\grii\DataColumn',
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
        'footer' => number_format($total['no_of_loans']),
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
        //'format' => ['decimal'],
        'value'=>function($data){return number_format($data['amount']);},
        'label' => 'Recovery Amount(PKR)',
        'footer' =>number_format($total['amount']),
    ],
];   