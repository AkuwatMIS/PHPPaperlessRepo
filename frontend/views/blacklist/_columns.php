<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;
$js='$(function () {
    $(\'.update-modal-click\').click(function () {
        $(\'#update-modal\')
            .modal(\'show\');
            $("#delete-form").attr("action", $(this).attr(\'value\')); 
    });
});';
$this->registerJs($js);
return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    /*[
        'class'=>'yii\grid\DataColumn',
        'attribute'=>'member_id',
    ],*/
    [
        'class'=>'yii\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'parentage',
        'label' => 'Parentage',
        'format' => 'raw',
        'value' => function ($data) {
           if(isset($data->parentage) && $data->parentage!=null){
               $parentage = $data->parentage;
           }else{
               $parentage = 'NA';
           }
            return $parentage;
        },
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'cnic',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'cnic_invalid',
        'label' => 'Invalid Cnic',
        'format' => 'raw',
        'value' => function ($data) {
            if(isset($data->cnic_invalid) && $data->cnic_invalid!=null){
                $parentage = $data->cnic_invalid;
            }else{
                $parentage = 'NA';
            }
            return $parentage;
        },
    ],
    [
         'class'=>'\yii\grid\DataColumn',
         'attribute'=>'type',
         'filter'=>["soft"=>"Soft","hard"=>"Hard"],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'reason',
        'filter'=>["NACTA Defaulter"=>"NACTA Defaulter","NAB Defaulter"=>"NAB Defaulter","UNSCR"=>"UNSCR"],
        'label'=>'Institute Name'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'province',
        'filter'=>["Punjab"=>"Punjab","Sindh"=>"Sindh","KP"=>"KP","Balochistan"=>"Balochistan","KP(Ex-FATA)"=>"KP (Ex-FATA)","Gilgit_Baltistan"=>"Gilgit Baltistan","ajk"=>"Azad Jammu & Kashmir","Islamabad"=>"Islamabad Capital Territory"],
        'value' => function ($data) {
            if(isset($data->province) && $data->province!=null){
                $parentage = $data->province;
            }else{
                $parentage = 'NA';
            }
            return $parentage;
        },
        'label'=>'Province'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'description',
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'location',
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
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    /*[
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:100px;'],
        //'dropdown' => false,
        //'vAlign'=>'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [

            'blacklist-delete' => function ($url, $model, $key) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id],
                        [
                            'role' => 'modal-remote', 'title' => 'Delete', 'data-toggle' => 'tooltip',
                            'data-confirm' => true,
                            'data-confirm-title' => 'Delete Loan',
                            'data-confirm' => 'Are you sure want to delete entry against CNIC:' . $model->cnic . '',
                        ]
                    );
            },
        ],
        'template' => '{view} {update} {blacklist-delete}',

    ],*/
    [
        'class' => 'yii\grid\ActionColumn',
        'options'=>['class'=>'action-column'],
        'buttons'=>[
            'audit' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('logs', ['/blacklist/logs','id'=>$model->id],['role'=>'modal-remote','title'=>'Audit','data-toggle'=>'tooltip']);
            },
            'delete' => function($url,$model,$key){
                $btn = \yii\helpers\Html::button("<span class=\"glyphicon glyphicon-trash\"></span>",[
                    'value'=>Yii::$app->urlManager->createUrl('blacklist/delete?id='.$key), //<---- here is where you define the action that handles the ajax request
                    'class'=>'update-modal-click grid-action',
                    'data-toggle'=>'tooltip',
                    'data-placement'=>'bottom',
                    'title'=>'Delete',
                    'style'=>'background-color:white;border-style:none'
                ]);
                return $btn;
            }
        ],
        'template'=>'{view} {update}  {delete} {audit}',
    ],

];
?>

