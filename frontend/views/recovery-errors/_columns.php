<?php
use yii\helpers\Url;
use kartik\date\DatePicker;

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
        'attribute'=>'recovery_files_id',
        'value' => 'recoveryFile.file_name',
        'label' => 'Recovery File',
        'filter' => $files_list,
    ],
   /* [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'source',
        'filter' => $source,
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'recv_date',
        'value' => function($model) {
            Yii::$app->formatter->locale = 'en-US';
            return Yii::$app->formatter->asDate($model->recv_date);
        },
        'filter' => DatePicker::widget([
            'name'=>'RecoveryErrors[recv_date]',
            'type' => DatePicker::TYPE_INPUT,
            //'value' => '23-Feb-1982',
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'M d, yyyy'
            ],
        ]),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'credit',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'receipt_no',
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'balance',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'error_description',
        /*'value' => function($model) {
            $error_string = '';
            $arr = [];
            $errors =json_decode($model->error_description,true);
            foreach ($errors as $key => $value) {
                foreach ($value as $error){
                    $arr[] = $error;
                }
            }
            foreach ($arr as $err)
            {
                $error_string .= $err.PHP_EOL;
            }
            return $error_string;
        },*/

    ],
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
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'value' => function($model)
        {
            if($model->status == '0') {
                return 'Open';
            } else if($model->status == '1') {
                return 'In-Process';
            } else {
                return 'Resolved';
            }
        },
        'filter' => $status,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'template' => '{view} {update}',
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   