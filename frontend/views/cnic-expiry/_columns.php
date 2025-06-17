<?php

use common\components\Helpers\ImageHelper;
use common\models\Images;
use yii\helpers\Url;
use common\components\Helpers\MemberHelper;


return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'full_name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'parentage',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'parentage_type',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->parentage_type);
        },
        'filter'=> \common\components\Helpers\MemberHelper::getParentageType()
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'cnic',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute' => 'dob',
        'label' => 'CNIC Issue',
        'value'=> function ($model, $key, $index, $column){
            return \common\components\Helpers\StringHelper::dateFormatter(strtotime($model->info->cnic_issue_date));
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute' => 'dob',
        'label' => 'CNIC Expiry',
        'value'=> function ($model, $key, $index, $column){
            return \common\components\Helpers\StringHelper::dateFormatter(strtotime($model->info->cnic_expiry_date));
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'gender',
        'value'=>function($data){
        if($data->gender=='m'){return 'Male';}
        else if($data->gender=='f'){return 'Female';}
        else{return 'Transgender';}
        },
        'filter'=> \common\components\Helpers\MemberHelper::getGender()
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'dob',
        'value'=> function ($model, $key, $index, $column){
            return \common\components\Helpers\StringHelper::dateFormatter($model->dob);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'education',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->education);
        },
        'filter'=> \common\components\Helpers\MemberHelper::getEducation()
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'marital_status',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->marital_status);
        },
        'filter'=> \common\components\Helpers\MemberHelper::getMaritalStatus()
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'status',
        'filter'=> \common\components\Helpers\MemberHelper::getMemberStatus()
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{application} {view}',
        'buttons' => [
            'application' => function ($url, $model, $key) {

                if(empty($model->applications)){
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-edit"></span>', ['/applications/create', 'id'=>$model->id], [
                        'title' => Yii::t('yii', 'Add Application'),
                    ]);
                }

            },
            'download' => function ($url, $model, $key) {
                $image = Images::findOne(['parent_id' => $model->id, 'parent_type' => 'members', 'image_type' => 'nadra_document']);
                $attachment_path = ImageHelper::getAttachmentPath().'/uploads/members/'.$model->id.'/'.$image->image_name;
                if(file_exists($attachment_path)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download"></span>', ['pdf', 'id' => $model->id], ['target' => '_blank'], ['title' => 'Download Nadra File']);
                }
            },
        ],
        'contentOptions' => ['style' => 'width:90px;'],
    ],

];   