<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpLoanManagementCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loan Management Cost';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="awp-loan-management-cost-index">

    <h1><?/*= Html::encode($this->title) */?></h1>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); */?>

    <p>
        <?/*= Html::a('Create Awp Loan Management Cost', ['create'], ['class' => 'btn btn-success']) */?>
    </p>

    <?/*= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'branch_id',
            'area_id',
            'region_id',
            'date_of_opening',
            //'opening_active_loans',
            //'closing_active_loans',
            //'average',
            //'amount',
            //'lmc',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); */?>
</div>
-->
<div class="awp-target-vs-achievement-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--<p>
        <?/*= Html::a('Create Awp Target Vs Achievement', ['create'], ['class' => 'btn btn-success']) */?>
    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        // table-striped table-bordered table-hover
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:35px'],
        //'footerOptions'=>['style'=>'background-color:Black;color:Black'],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            // 'id',
            /*['attribute'=>'region_id',
                'value'=>'region.name'],
            ['attribute'=>'area_id',
                'value'=>'area.name'],*/
            ['attribute'=>'branch_id',
                'value'=>'branch.name',
                'label'=>'Branch Name',
                'footer' =>'<b>Total</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Branch Name</b>',
            ],
            ['attribute'=>'date_of_opening',
                'value'=>function($data){return date('M-Y',($data->branch->opening_date));},
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Date of Opening</b>',
            ],
            //'project_id',
            //'month',
            ['attribute'=>'opening_active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->opening_active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'opening_active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Opening Active Loans</b>',
            ],
            ['attribute'=>'closing_active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->closing_active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'closing_active_loans').'</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Closing Active Loans</b>',
            ],
            ['attribute'=>'average',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->average);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'average').'</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Average Active Loans</b>',
            ],
            ['attribute'=>'amount',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->amount);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'amount').'</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Total Expenses</b>',
            ],
            ['attribute'=>'lmc',
                //'format'=>'decimal',
                'label'=>'Loans Difference',
                'value'=>function($data){return number_format($data->lmc);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'lmc').'</b>',
                'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                'header'=>'<b style="color: White">Annual Loan Management Cost</b>',
            ],
            /*'target_loans',
            'target_amount',
            'achieved_loans',
            'achieved_amount',
            'loans_dif',
            'amount_dif',*/

            /*['class' => 'yii\grid\ActionColumn'],*/
        ],
    ]); ?>
</div>