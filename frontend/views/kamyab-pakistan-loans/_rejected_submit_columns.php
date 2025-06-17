<?php
use yii\helpers\Url;
use \common\models\Images;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'region_id',
//        'label'=>'Region',
//        'value'=>'region.name',
//    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'area_id',
//        'label'=>'Area',
//        'value'=>'area.name',
//    ],
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
        'value'=>'info.member.full_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'label'=>'Parentage',
        'value'=>'info.member.parentage',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'label'=>'CNIC',
        'value'=>'info.member.cnic',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_issue_date',
        'label'=>'CNIC Issue Date',
        'value'=>'info.cnic_issue_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_expiry_date',
        'label'=>'CNIC Expiry Date',
        'value'=>'info.cnic_expiry_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'reject_reason',
        'value'=>'reject_reason'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'remarks',
        'value'=>'remarks'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'rejected_date',
        'label'=>'Re_Submitted Date',
        'value'=>function ($model) {
            if(isset($model->rejected_date)) {
                $nadra =  \common\components\Helpers\StringHelper::dateFormatter($model->rejected_date);
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
            'submit' => function ($url, $model, $key) {

                $nadra_doc=\common\models\NadraVerisys::find()->where(['application_id'=>$model->application_id])->andWhere(['document_type'=>'nadra_document'])->andWhere(['status'=>1])->one();
//                $nadra_doc=Images::find()->where(['parent_id'=>$model['info']['member_id']])->andWhere(['image_type'=>'nadra_document'])->one();

                if($nadra_doc) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-edit"></span>', ['submit-rejected-nadra-verisys', 'id' => $model->id, 'status' => 2], ['title' => 'Submit']);

                }else {
                    return \yii\helpers\Html::button('<span class="glyphicon glyphicon-edit"></span>', ['submit-rejected-nadra-verisys', 'id' => $model->id, 'status' => 2,'title' => 'Nadra Verysis Incomplete','data-toggle'=>'tooltip'] );
                }
            },
            'reject' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['reject-submited-nadra-verisys','id'=>$model->id],['role'=>'modal-remote','title'=>'Reject Nadra Verisys','data-toggle'=>'tooltip']);
            },
            'delete' => function ($url, $model, $key) {
                
                if(\Yii::$app->user->id == 5915) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-remove"></span>', ['delete-submit-nadra-verisys','id'=>$model->id],['role'=>'modal-remote', 'title'=>'Delete Nadra Verisys','data-toggle'=>'tooltip']);
                }
                
            }
        ],
        'template' => '{submit} {reject} {delete}',

    ],
];   