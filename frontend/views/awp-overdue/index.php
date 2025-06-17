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
<div class="awp-overdue-index">

    <!--<h3><?/*= Html::encode($this->title) */?></h3>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--<p>
        <?/*= Html::a('Create Awp Overdue', ['create'], ['class' => 'btn btn-success']) */?>
    </p>
-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $filterModel,
        //'tableOptions' =>['class' => 'table table-striped table-bordered table-hover'],
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:20px'],
        'rowOptions'=>function($model){
        if($model->branch->status==0){
            return ['style' => 'display:none'];
        }else if (in_array($model->branch->code,\common\components\Helpers\AwpHelper::getClosedBranches())){
                return ['style' => 'background-color:red'];
            }/*else if($model->branch->type != 'branch'){
                return ['style' => 'background-color:yellow'];
            }*/
        },
        'showFooter' => true,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            /*['attribute'=>'region_id',
                'value'=>'region.name'],
            ['attribute'=>'area_id',
                'value'=>'area.name'],*/

            //'month',
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
                'url' => \yii\helpers\Url::to(['/awp/overdue-detail']),
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
            ['attribute'=>'branch_id',
                //'format'=>'decimal',
                'value'=>function($data){return date('F,Y',$data->branch->opening_date);},
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Date of Opening</b>',
            ],
            ['attribute'=>'active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Active Loans</b>',
            ],
            ['attribute'=>'olp',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->olp);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'olp').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">OLP</b>',
            ],
            ['attribute'=>'overdue_numbers',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->overdue_numbers);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'overdue_numbers').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Overdue Numbers</b>',
            ],
            ['attribute'=>'overdue_amount',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->overdue_amount);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'overdue_amount').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Overdue Amount</b>',
            ],

//            ['attribute'=>'write_off_loans_new',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->write_off_loans_new);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_loans_new').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Write Off Loan</b>',
//            ],
//
//            ['attribute'=>'write_off_amount_new',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->write_off_amount_new);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_amount_new').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Write Off Amount</b>',
//            ],

//            ['attribute'=>'diff_olp',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->diff_olp);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'diff_olp').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">Total Deferred</b>',
//            ],
//            ['attribute'=>'awp_olp',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->awp_olp);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_olp').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">1 Pending</b>',
//            ],
//            ['attribute'=>'write_off_recovered',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->write_off_amount);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_amount').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">2 Pending</b>',
//            ],
//            ['attribute'=>'write_off_recovered',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->write_off_recovered);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_recovered').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">3 Pending</b>',
//            ],
//            ['attribute'=>'awp_active_loans',
//                //'format'=>'decimal',
//                'value'=>function($data){return number_format($data->awp_active_loans);},
//                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_active_loans').'</b>',
//                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
//                'header'=>'<b style="color: White">No Payment</b>',
//            ],
            /*['attribute'=>'awp_active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->awp_active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">AWP Active Loans</b>',
            ],
                /*['attribute'=>'write_off_amount',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->write_off_amount);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_amount').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Write off Amount</b>',
            ],
            ['attribute'=>'write_off_recovered',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->write_off_recovered);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'write_off_recovered').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Write off Recovered</b>',
            ],*/

            /*['attribute'=>'awp_active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->awp_active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">AWP Active Loans</b>',
            ],
            ['attribute'=>'awp_olp',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->awp_olp);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'awp_olp').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">AWP OLP</b>',
            ],
            ['attribute'=>'writeoff_count',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->writeoff_count);},
                'label'=>'Writeoff Number',
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'writeoff_count').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Writeoff Number</b>',
            ],
            ['attribute'=>'writeoff_amount',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->writeoff_amount);},
                'label'=>'Writeoff Amount',
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'writeoff_amount').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Writeoff Amount</b>',
            ],
            ['attribute'=>'active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->active_loans);},
                'label'=>'Active Loans as on 31 January,2019',
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Active Loans as on 31 January,2019</b>',
            ],
            ['attribute'=>'olp',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->olp);},
                'label'=>'OLP as on 31 January,2019',
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'olp').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">OLP as on 31 January,2019</b>',
            ],*/
            /*['attribute'=>'diff_active_loans',
                'format'=>'decimal',
                'label'=>'Active Loans Difference',
                'footer' =>'<b>'.\common\components\AwpHelper::getTotal($dataProvider->getModels(), 'diff_active_loans').'</b>'
            ],
            ['attribute'=>'diff_olp',
                'format'=>'decimal',
                'label'=>'Olp Difference',
                'footer' =>'<b>'.\common\components\AwpHelper::getTotal($dataProvider->getModels(), 'diff_olp').'</b>'
            ],*/
            /*'overdue_numbers',
            'overdue_amount',
            'awp_active_loans',
            'awp_olp',
            'active_loans',
            'olp',
            'diff_active_loans',
            'diff_olp',*/

            /*['class' => 'yii\grid\ActionColumn'],*/
        ],
    ]); ?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>