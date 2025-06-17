<?php
use yii\helpers\Url;

return [
    //  [
    //     'class' => 'kartik\grid\CheckboxColumn',
    //     'width' => '20px',
    //  ],
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
        'attribute'=>'full_name',
        'label'=>'Name',
        'value' => function ($data) {

            return isset($data->application->member->full_name)?$data->application->member->full_name:'';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'label'=>'Parentage',
        'value' => function ($data) {
            return isset($data->application->member->parentage)?$data->application->member->parentage:'';
        },

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'label'=>'CNIC',
        'value' => function ($data) {
            return isset($data->application->member->cnic)?$data->application->member->cnic:'';
        },

    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',

        'label'=>'Region',
        'value'=>function ($data) {
            return isset($data->application->region->name)?$data->application->region->name:'';
        },
        'filter'=>$regions
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=>$areas

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=>$branches

    ],


    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'value'=>'project.name',
        'label'=>'Project',
        'filter'=>$projects

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'Sanction Number',
        'value'=>function ($data) {


            return isset($data->loan->sanction_no)?$data->loan->sanction_no:'';
        },
        'label'=>'Sanction Number ',
        //'filter'=>$projects

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'takaful receipt no',
        'value'=>function ($data) {
            //  echo '<pre>';
            // print_r($data);
            //  die();
            return isset($data->receipt_no)?$data->receipt_no:'';
        },
        'label'=>'Takaful Receipt No',
        //'filter'=>$projects

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'takaful recive date',
        'value'=>function ($data) {
            //  echo '<pre>';
            // print_r($data);
            //  die();
            return (!empty($data->receive_date) && $data->receive_date!=null)?date('d M Y', $data->receive_date):'NA';
        },
        'label'=>'Takaful Recive Date',
        //'filter'=>$projects

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'Loan Amount',
        'value'=>function ($data) {
            //  echo '<pre>';
            // print_r($data);
            //  die();
            return isset($data->loan->loan_amount)?$data->loan->loan_amount:'';
        },
        'label'=>'Loan Amount',
        //'filter'=>$projects

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'takaful amount',
        'value'=>function ($data) {
           // echo '<pre>';
            //  print_r($data);
           //   die();
    return isset($data->credit)?$data->credit:'';
},
        'label'=>'Takaful Amount',
        //'filter'=>$projects

    ],

   /* [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_date',

        'label'=>'Application Date',
        'value'=> function ($data) {
            return (!empty($data->application_date) && $data->application_date!=null)?date('d M Y',$data->application_date):'NA';
        },


    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',

        'label'=>'Application Created Date',
        'value'=> function ($data) {
            return (!empty($data->created_at) && $data->created_at!=null)?date('d M Y',$data->created_at):'NA';
        },


    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cibstatus',
        'label'=>'CIB',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (isset($data->cib->status)==null) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>';
            }
            else if ($data->cib->status == 0) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),


    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'Nadra',
        'label'=>'NADRA',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
//             echo "<pre>";
//             print_r($data) ;
//           die();
            if (isset($data->member->nadraDoc)==null) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>';
            }
            elseif (!$data->member->nadraDoc) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('No'=>'No','Yes'=>'Yes'),

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'PMT',
        'label'=>'PMT',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            // echo "<pre>";
            //  print_r($data->applicationDetails->status) ;
            //   die();

            if (!in_array($data->project_id, \common\components\Helpers\StructureHelper::kamyaabPakitanProjects())) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-ban-circle"></span>';
            }
            elseif( isset($data->pmtStatus->status)==null ) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>';
            }    elseif ($data->pmtStatus->status == 0) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),

    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'Account_Verification',
        'label'=>'ACCOUNT VERIFICATION',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!in_array($data->project_id,\common\components\Helpers\StructureHelper::accountVerifyProjects())) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-ban-circle"></span>';
            } elseif (isset($data->loan->accountVerification->status) == null) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } elseif ($data->loan->accountVerification->status == 0) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>';
            }
            else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),



    ],
    // [
    //  'class'=>'\kartik\grid\DataColumn',
    //  'attribute'=>'user_id',
    //  ],
    //  [
    //   'class'=>'\kartik\grid\DataColumn',
    //   'attribute'=>'action',
//    ],
    /*  [
          'class'=>'\kartik\grid\DataColumn',
          'attribute'=>'status',
          'format'=>'raw',
          'hAlign'=>'center',
          'value'=> function ($data) {
              if (!$data->status) {
                  return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
              } else {
                  return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
              }
          }

      ],*/
    // [
    //   'class'=>'\kartik\grid\DataColumn',
    //  'attribute'=>'pre_action',
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
    //  [
    //     'class' => 'kartik\grid\ActionColumn',
    //    'dropdown' => false,
    //     'vAlign'=>'middle',
    //    'urlCreator' => function($action, $model, $key, $index) {
    //        return Url::to([$action,'id'=>$key]);
    //    },
    //   'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
    //   'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
    //   'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete',
    //     'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
    //      'data-request-method'=>'post',
    //     'data-toggle'=>'tooltip',
    //    'data-confirm-title'=>'Are you sure?',
    //   'data-confirm-message'=>'Are you sure want to delete this item'],
    // ],

];