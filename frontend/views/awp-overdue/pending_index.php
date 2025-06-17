<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpOverdueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Awp Overdues';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="awp-overdue-index expend-cell-tb">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:20px'],
        'rowOptions'=>function($model){
        if($model->branch->status==0){
            return ['style' => 'display:none'];
        }else if (in_array($model->branch->code,\common\components\Helpers\AwpHelper::getClosedBranches())){
                return ['style' => 'background-color:red'];
            }
        },
        'showFooter' => true,
        'columns' => [
            [
                'class' => \dimmitri\grid\ExpandRowColumn::class,
                'attribute' => 'branch_id',
                'value' => function ($data) {

                    if (in_array($data->branch->code,\common\components\Helpers\AwpHelper::getAgriBranches())) {
                        return '<i style="color:green;" class="fa fa-leaf"></i>'. ' ' . $data->branch->name;
                        //return '<i style="color:white;" class="fa fa-house"></i>'. ' ' . $data->branch->code;
                    }else if($data->branch->type != 'branch'){
                        return '<i style="color:green;" class="fa fa-home"></i>'. ' ' . $data->branch->name;
                    }else{
                        return $data->branch->name;
                    }
                },
                'format'=>'html',
                'ajaxErrorMessage' => 'Oops',
                'ajaxMethod' => 'GET',
                'url' => \yii\helpers\Url::to(['/awp-overdue/awp-overdue-month','branch_id'=>$model->branch_id]),
                'submitData' => function ($model, $key, $index) {
                    return ['branch_id' => $model->branch_id];
                },
                'enableCache' => false,
                'format' => 'raw',
                'expandableOptions' => [
                    'title' => 'Click me!',
                    'class' => 'my-expand',
                ],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Branch</b>',
                /*'contentOptions' => [
                    'style' => 'display: flex; justify-content: space-between;',
                ],*/
            ],
//            ['attribute'=>'branch_id',
//                //'format'=>'decimal',
//                'value'=>function($data){return date('F,Y',$data->branch->opening_date);},
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Date of Opening</b>',
//            ],
//            ['attribute'=>'active_loans',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->active_loans);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans').'<b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Active Loans</b>',
//            ],
//            ['attribute'=>'overdue_numbers',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->overdue_numbers);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'overdue_numbers').'<b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Overdue Numbers</b>',
//            ],
            ['attribute'=>'month',
                //'format'=>'decimal',
                'value'=>function($data){return 'June 2022';},
//                'value'=>function($data){return date("F", strtotime($data->month));},
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Month</b>',
            ],
            ['attribute'=>'diff_olp',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->diff_olp);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'diff_olp').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Total Deferred</b>',
            ],
            ['attribute'=>'def_recovered',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->def_recovered);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'def_recovered').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Fully Recovered</b>',
            ],
            ['attribute'=>'awp_olp',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->awp_olp);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_olp').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">1 Pending</b>',
            ],
            ['attribute'=>'write_off_recovered',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->write_off_amount);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_amount').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">2 Pending</b>',
            ],
            ['attribute'=>'write_off_recovered',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->write_off_recovered);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_recovered').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">3 Pending</b>',
            ],
            ['attribute'=>'awp_active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->awp_active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">No Payment</b>',
            ],
            /*['class' => 'yii\grid\ActionColumn'],*/
        ],
    ]); ?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>