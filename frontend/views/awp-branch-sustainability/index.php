<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpBranchSustainabilitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Awp Branch Sustainabilities';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pull-right">
    <form action="/awp-target-vs-achievement/index">
        <input type="hidden" name="branch_sustain" value="<?php if(isset($_GET['branch_sustain']) && ($_GET['branch_sustain']=='1')){echo '';}else{echo '1';}?>">
        <input type="hidden" name="AwpTargetVsAchievementSearch[region_id]" value="<?php echo $_GET['AwpTargetVsAchievementSearch']['region_id'] ?>">
        <input type="hidden" name="AwpTargetVsAchievementSearch[area_id]" value="<?php echo $_GET['AwpTargetVsAchievementSearch']['area_id'] ?>">
        <input type="hidden" name="AwpTargetVsAchievementSearch[month]" value="<?php echo $_GET['AwpTargetVsAchievementSearch']['month'] ?>">
        <input type="hidden" name="AwpTargetVsAchievementSearch[month_from]" value="<?php echo $_GET['AwpTargetVsAchievementSearch']['month_from'] ?>">
        <button class="btn btn-success" type="submit"><?php if(isset($_GET['branch_sustain']) && ($_GET['branch_sustain']=='1')){echo 'Unsustained Branches';}else{echo 'All Branches';}?></button>
    </form>
</div>
<div class="awp-branch-sustainability-index">

    <!--<h3><?/*= Html::encode($this->title) */?></h3>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?/*= Html::a('Create Awp Branch Sustainability', ['create'], ['class' => 'btn btn-success']) */?>
    </p>
-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $filterModel,
        'showFooter' => true,
        //'tableOptions' =>['class' => 'table table-striped table-bordered table-hover'],
        'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:20px'],
        'rowOptions'=>function($model){
//            if($model->branch->status==0){
//                return ['style' => 'display:none'];
//            }else
                if (in_array($model->branch->code,\common\components\Helpers\AwpHelper::getClosedBranches())){
                return ['style' => 'background-color:red'];
            }/*else if($model->branch->type != 'branch'){
                return ['style' => 'background-color:yellow'];
            }*/
        },
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            /*'id',
            'branch_code',*/
            /*['attribute'=>'region_id',
                'value'=>'region.name'],
            ['attribute'=>'area_id',
                'value'=>'area.name'],*/
            ['attribute'=>'branch_id',
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
                'label'=>'Branch Name',
                'footer' =>'<b>Total</b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Branch Name</b>',
            ],
            //'month',
            ['attribute'=>'amount_disbursed',
                //'format' => ['decimal',0],
                'value'=>function($data){return number_format($data->amount_disbursed);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'amount_disbursed').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Amount Disbursed</b>',
            ],
            //'percentage',
            ['attribute'=>'income',
                //'format'=>'decimal',
//                'label'=>'5%  Disbursement',
                'label'=>'Income',
                'value'=>function($data){return number_format($data->income);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'income').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Income</b>',


            ],
            ['attribute'=>'actual_expense',
                //'format' => ['decimal',0] ,
                'value'=>function($data){return number_format($data->actual_expense);},
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'actual_expense').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Actual Expense</b>',
            ],
            /*['attribute'=>'surplus_deficit',
                'format' => ['decimal',0],
               'label'=>'Difference',
                'footer' =>\common\components\AwpHelper::getTotal($dataProvider->getModels(), 'surplus_deficit')
            ],*/
            ['attribute'=>'surplus_deficit_total',
                'value'=>function($data){return number_format($data->income - $data->actual_expense);},
                //'format' => ['decimal',0],
                'label'=>'Difference',
                'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'surplus_deficit_total').'<b>',
                'footerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'headerOptions'=>['style'=>'background-color:#00a8ff;color:White'],
                'header'=>'<b style="color: White">Difference</b>',
            ],
            //'amount_disbursed',
            //'income',
            //'actual_expense',
            //'surplus_deficit',

            /*['class' => 'yii\grid\ActionColumn'],*/
        ],
    ]); ?>
</div>
