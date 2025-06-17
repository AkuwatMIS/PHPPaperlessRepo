<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpRecoveryPercentageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-recovery-percentage-index">

    <!--<h3><?/*= Html::encode($this->title) */?></h3>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:30px'],
        'rowOptions'=>function($model){
            if (in_array($model->branch->code,\common\components\Helpers\AwpHelper::getClosedBranches())){
                return ['style' => 'background-color:red'];
            }/*else if($model->branch->type != 'branch'){
                return ['style' => 'background-color:yellow'];
            }*/else if(number_format(($model->recovery_one_to_ten/$model->recovery_count)*100)==100){
                return ['style' => 'background-color:#90ee90'];
            }
        },
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            //'id',
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
                'url' => \yii\helpers\Url::to(['/awp/last-year-detail']),
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
            /*['attribute' => 'branch_id',
                'value' => 'branch.name',
                'header' => '<b>Branch</b>',
                //'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Branch</b>',
            ],*/
            //'area_id',
            //'region_id',
            //'branch_code',
            //'recovery_count',
            ['attribute' => 'recovery_count',
                'value' => function($data){
                   return number_format($data->recovery_count);
                },
                'label' => 'Recovery Count',
                'header' => '<b>No of Recoveries</b>',
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">No of Recoveries</b>',
            ],
            ['attribute' => 'recovery_one_to_ten',
                'value' => function($data){
                    return number_format(($data->recovery_one_to_ten/$data->recovery_count)*100).'%';
                },
                'label' => '1st to 10',
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">1st to 10th</b>',
            ],
            ['attribute' => 'recovery_eleven_to_twenty',
                'value' => function($data){
                    return number_format(($data->recovery_eleven_to_twenty/$data->recovery_count)*100).'%';
                },
                'label' => '11 to 20',
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">11th to 20th</b>',
            ],
            ['attribute' => 'recovery_twentyone_to_thirty',
                'value' => function($data){
                    return number_format(($data->recovery_twentyone_to_thirty/$data->recovery_count)*100).'%';
                },
                'label' => '21 to last',
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">21st to Month End</b>',
            ],
            //'recovery_one_to_ten',
            //'recovery_eleven_to_twenty',
            //'recovery_twentyone_to_thirty',
            //'created_at',
            //'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
