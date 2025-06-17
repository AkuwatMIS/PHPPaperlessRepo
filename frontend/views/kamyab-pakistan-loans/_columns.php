<?php
use yii\helpers\Url;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'value'=>'project.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'label'=>'Region',
        'value'=>'region.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'label'=>'Area',
        'value'=>'area.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'label'=>'Branch',
        'value'=>'branch.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'full_name',
        'label'=>'Name',
        'value'=>'member.full_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'label'=>'Parentage',
        'value'=>'member.parentage',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'label'=>'CNIC',
        'value'=>'member.cnic',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_issue_date',
        'label'=>'CNIC Issue Date',
        'value'=>'member.info.cnic_issue_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_expiry_date',
        'label'=>'CNIC Expiry Date',
        'value'=>'member.info.cnic_expiry_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'image_name',
        'label'=>'NADRA Verisys',
        'value'=>function ($model, $key, $index) {
//            if(isset($model->member->nadraDoc->image_name)) {
            if($model->nadra->status == 1) {
                $nadra =  'YES';
            } else {
                $nadra =  'NO';
            }
            return  $nadra;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'updated_at',
        'label'=>'NADRA Verisys Date',
        'value'=>function ($model) {
//            if(isset($model->member->nadraDoc->updated_at)) {
            if(isset($model->nadra->updated_at)) {
                $nadra =  \common\components\Helpers\StringHelper::dateFormatter($model->nadra->updated_at);
            } else {
                $nadra =  'NULL';
            }
            return  $nadra;
        },
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:100px;'],
        //'dropdown' => false,
        //'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'reject' => function ($url, $model, $key) {
                if(isset($model->member->nadra->status) && $model->member->nadra->status == 1) {
                } else {
                    $modelRejection = \common\models\RejectedNadraVerisys::find()
                        ->where(['application_id'=>$model->id])
                        ->andWhere(['member_info_id'=>$model->memberInfo->id])
                        ->andWhere(['in','status',[0,1]])
                        ->one();
                    if(empty($modelRejection) && $modelRejection==null){
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['reject-nadra-verisys','application_id'=>$model->id, 'member_info_id'=>$model->memberInfo->id],['role'=>'modal-remote','title'=>'Reject Nadra Verisys','data-toggle'=>'tooltip']);
                    }
                }
            }
        ],
        'template' => '{reject}',

    ],
];   