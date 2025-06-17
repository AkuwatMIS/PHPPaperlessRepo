<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    // advanced example
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'full_name',
        'value'=>'member.full_name',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->member_id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
        /*'contentOptions' => [
            'style' => 'display: flex; justify-content: space-between;',
        ],*/
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'value'=>'member.cnic',
    ],
    //[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'fee',
   // ],
    [
      //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_table',
    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'activity_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'product_id',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'region_id',
         'value'=>'region.name',
         'label'=>'Region',
         'filter'=> $regions
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'area_id',
         'value'=>'area.name',
         'label'=>'Area',
         'filter'=> $areas
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'branch_id',
         'value'=>'branch.name',
         'label'=>'Branch',
         'filter'=> $branches
     ],
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'team_id',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'field_id',
     ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'no_of_times',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'bzns_cond',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'who_will_work',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'name_of_other',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'other_cnic',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'req_amount',
         'value'=> function ($model, $key, $index, $column){
             return number_format($model->req_amount);
         },
     ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_date',
        'value'=>function ($data, $key, $index) {
            return \common\components\Helpers\StringHelper::dateFormatter($data->application_date);
        },
    ],
        [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'created_at',
                'value'=>function ($data, $key, $index) {
                        return \common\components\Helpers\StringHelper::dateFormatter($data->created_at);
                },
        ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'value'=>'project.name',
        'label'=>'Project',
        'filter'=> $projects
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cib',
        'value'=>function($model){

           ;
            if ($model->cib->status==1) {
                return 'Yes';
            }else{
                return 'No';
            }
        },
        'label'=>'Cib Verification',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nadra_verisys',
        'value'=>function($model){

            ;
            if ($model->nadra->status==1) {
                return 'Completed';
            }else{
                return 'Pending';
            }
        },
        'label'=>'Nadra Verisys',
    ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
         'filter'=> common\components\Helpers\ApplicationHelper::getAppStatus(),
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_urban',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'reject_reason',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_lock',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'assigned_to',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],

    [
        'class' => 'yii\grid\ActionColumn',
        'buttons'=>[
            'edit' => function ($url, $model, $key) {
                if (empty($model->loan) && !empty($model->application_no) && ($model->status!='rejected')) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['title' => 'Update']);
                }
            },
            'delete' => function ($url, $model, $key) {
                if (($model->group_id==0)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], ['title' => 'Delete']);
                }
            },
            'visit_history' => function ($url, $model, $key) {
                if (in_array($model->project_id,StructureHelper::trancheProjects())) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-picture"></span>', ['visit-details', 'id' => $model->id], ['target'=>'_blank'], ['title' => 'Visit History']);
                }
            },
            /*'download' => function ($url, $model, $key) {
                if (!empty($model->cibFile)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download"></span>', ['download-file', 'id' => $model->id], ['target'=>'_blank'], ['title' => 'Download File']);
                }
            },*/
            'download' => function ($url, $model, $key) {
                if (!empty($model->cibstatus)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download"></span>', ['cib', 'application_id' => $model->id], ['target'=>'_blank'], ['title' => 'Download File']);
                }
            },
            'nadra' => function ($url, $model, $key) {
                $image = \common\models\NadraVerisys::find()
                ->where(['application_id' => $model->id])
                ->andWhere(['member_id' => $model->member_id])
                ->andWhere(['document_type' => 'nadra_document'])
                ->andWhere(['status'=>1])
                ->one();
                if(!empty($image) && $image!=null){
                    $attachment_path_app = \common\components\Helpers\ImageHelper::getAttachmentPath().'/uploads/members/'.$model->id.'/'.$image->document_name;
                    if(file_exists($attachment_path_app)) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download"></span>', ['pdf', 'id' => $model->id,'type'=>'app'], ['target' => '_blank'], ['title' => 'Download Nadra File']);
                    }else{
                        $image = \common\models\Images::find()
                            ->where(['parent_id' => $model->member_id])
                            ->andWhere(['parent_type' => 'members'])
                            ->andWhere(['image_type' => 'nadra_document'])
                            ->one();
                        $attachment_path = \common\components\Helpers\ImageHelper::getAttachmentPath().'/uploads/members/'.$model->member_id.'/'.$image->image_name;
                        if(file_exists($attachment_path)) {
                            return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download"></span>', ['pdf', 'id' => $model->member_id,'type'=>'member'], ['target' => '_blank'], ['title' => 'Download Nadra File']);
                        }
                    }

                }
            }
        ],
        'template'=>'{view} {edit} {delete} {visit_history} {download} {nadra}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   