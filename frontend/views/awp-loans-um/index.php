<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpLoansUmSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Awp Loans/UM';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-loans-um-index">

    <!--<h3><?/*= Html::encode($this->title) */?></h3>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:30px'],
        'rowOptions'=>function($model){
            if($model->branch->type != 'branch'){
                return ['style' => 'background-color:yellow'];
            }
        },
        'showFooter' => true,

        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute'=>'branch_id',
                'value'=>'branch.name',
                'label'=>'Branch Name',
                'footer' =>'<b>Total</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Branch Name</b>',
            ],
            ['attribute'=>'active_loans',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->active_loans);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Active Loans</b>',
            ],
            ['attribute'=>'no_of_branch_managers',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->no_of_branch_managers);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'no_of_branch_managers').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">No of BM/ABM</b>',
            ],
            ['attribute'=>'no_of_um',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->no_of_um);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'no_of_um').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">No of UM</b>',
            ],
            ['attribute'=>'active_loans_per_um',
                //'format'=>'decimal',
                'value'=>function($data){return number_format($data->active_loans_per_um);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getDivideTwoColumns($dataProvider->getModels(), 'active_loans','no_of_um').'</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Active Loans/UM</b>',
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
