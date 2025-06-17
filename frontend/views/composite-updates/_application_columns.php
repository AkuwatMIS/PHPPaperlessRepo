<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
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

        'attribute'=>'cnic',
        'value'=>'member.cnic',
    ],
    [

        'attribute'=>'application_no',
    ],
     [
         'attribute'=>'region_id',
         'value'=>'region.name',
         'label'=>'Region'
     ],
     [
         'attribute'=>'area_id',
         'value'=>'area.name',
         'label'=>'Area'
     ],
     [
         'attribute'=>'branch_id',
         'value'=>'branch.name',
         'label'=>'Branch',
         'filter'=> $branches
     ],
     [
         'attribute'=>'req_amount',
         'value'=> function ($model, $key, $index, $column){
             return number_format($model->req_amount);
         },
     ],
    [
        'attribute'=>'application_date',
        'value'=>function ($data, $key, $index) {
            return \common\components\Helpers\StringHelper::dateFormatter($data->application_date);
        },
    ],
        [
                'attribute'=>'created_at',
                'value'=>function ($data, $key, $index) {
                        return \common\components\Helpers\StringHelper::dateFormatter($data->created_at);
                },
            'label'=>'Created date',
        ],
    [

        'attribute'=>'project_id',
        'value'=>'project.name',
        'label'=>'Project',
        'filter'=> $projects
    ],
    [

        'attribute'=>'cib',
        'value'=>function($model){
            if ($model->cib->status==1) {
                return 'Yes';
            }else{
                return 'No';
            }
        },
        'label'=>'Cib Verification',
    ],
    [

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

         'attribute'=>'status',
         'filter'=> common\components\Helpers\ApplicationHelper::getAppStatus(),
     ],
    [
        'class' => 'yii\grid\ActionColumn',
        'buttons'=>[
            'edit' => function ($url, $model, $key) {
                if (empty($model->loan) && !empty($model->application_no) && ($model->status!='rejected')) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['applications/update', 'id' => $model->id], ['title' => 'Update']);
                }
            },
            'delete' => function ($url, $model, $key) {
                if (($model->group_id==0)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['applications/delete', 'id' => $model->id], ['title' => 'Delete']);
                }
            }
        ],
        'template'=>'{edit} {delete}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   