<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'attribute' => 'member_name',
        'value'=>'application.member.full_name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'value'=>'application.member.cnic',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'label'=>'Region',
        'value'=>'region.name',
        'filter'=>$array['regions'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'label'=>'Area',
        'value'=>'area.name',
        'filter'=>$array['areas'],

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'label'=>'branch',
        'value'=>'branch.name',
        'filter'=>$array['branches'],

    ],

    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'value'=> function ($model, $key, $index, $column){
            return \kartik\grid\GridView::ROW_COLLAPSED;
        },
        'detail' => function($model, $key, $index, $column){
            return $this->render('_member-details', [
                'member' => \common\models\Members::find()->where(['id' => $model->application->member_id])->one(),
                'model' => $model
            ]);
        },
    ],
  /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_id',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'value'=>'project.name',
        'filter'=>$array['projects']

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date_disbursed',
        'value'=>function ($model, $key, $index) {
            return date('d M Y',$model->date_disbursed);
        },
    ],
//  [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'project_table',
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date_approved',
        'value'=>function($data){return date('d-M-Y', $data->date_approved) ;},
        'filter'=>\kartik\date\DatePicker::widget([
            'name' => 'LoansSearch[date_approved]',
            'options' => ['placeholder' => 'Date Approved'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
            ]])
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan_amount',
        'format'=>'integer'
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'cheque_no',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_amnt',
         'format'=>'integer'
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_months',
         'format'=>'integer'

     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_type',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'date_disbursed',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'cheque_dt',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'disbursement_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'activity_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'product_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'group_id',
    // ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'team_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'field_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'loan_expiry',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'loan_completed_date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'old_sanc_no',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'remarks',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'br_serial',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'sanction_no',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'due',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'overdue',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'balance',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
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
    ],

];   