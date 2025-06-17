<?php
use yii\helpers\Url;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'short_name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'code',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'label'=>'Region',
        'value'=>'region.name',
        'filter'=>$array['regions'],
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'label'=>'Area',
        'value'=>'area.name',
        'filter'=>$array['areas'],

    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'code',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'uc',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'village',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'address',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'mobile',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'city_id',
         'label'=>'City',
         'value'=>'city.name',
         'filter'=>$array['cities'],

     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'tehsil_id',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'district_id',
         'label'=>'District',
         'value'=>'district.name',
         'filter'=>$array['districts'],

     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'division_id',
         'label'=>'Division',
         'value'=>'division.name',
         'filter'=>$array['divisions'],

     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'province_id',
         'label'=>'Province',
         'value'=>'province.name',
         'filter'=>$array['provinces'],

     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'country_id',
         'label'=>'Country',
         'value'=>'country.name',
         'filter'=>$array['countries'],

     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'latitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'longitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'description',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'opening_date',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'cr_division_id',
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

            'update' => function ($url,$model) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['quick-update','id'=>$model->id], [
                    'data-method' => 'post', 'data-pjax' => '0',
                ]);
            }
        ],

        'template' => '{view}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];