<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpTargetVsAchievementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Target vs Achievement';
$this->params['breadcrumbs'][] = $this->title;
$js = '$( "#tarvsach" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "block";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
      var a = document.getElementById("overdue_pending");
   a.style.display = "none";
    var b = document.getElementById("awp-final");
   b.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
   var d = document.getElementById("active_loans_per_loan_officer");
   d.style.display = "none";
   var v = document.getElementById("recovery_percent");
   v.style.display = "none";
});';
$js .= '$( "#awp-overdue" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "block";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
      var a = document.getElementById("overdue_pending");
   a.style.display = "none";
    var b = document.getElementById("awp-final");
   b.style.display = "none";
  
});';
$js .= '$( "#sustainability" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "block";
      var a = document.getElementById("overdue_pending");
   a.style.display = "none";
    var b = document.getElementById("awp-final");
   b.style.display = "none";
    var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
   var d = document.getElementById("active_loans_per_loan_officer");
   d.style.display = "none";
   var v = document.getElementById("recovery_percent");
   v.style.display = "none";
});';
$js .= '$( "#overdue-pending" ).click(function() {
   var a = document.getElementById("overdue_pending");
   a.style.display = "block";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
   var d = document.getElementById("active_loans_per_loan_officer");
   d.style.display = "none";
   var v = document.getElementById("recovery_percent");
   v.style.display = "none";
});';
$js .= '$( "#loan_management_cost" ).click(function() {
   var a = document.getElementById("branch-loan-management-cost");
   a.style.display = "block";
   var a = document.getElementById("awp-final");
   a.style.display = "none";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
   var d = document.getElementById("active_loans_per_loan_officer");
   d.style.display = "none";
   var v = document.getElementById("recovery_percent");
   v.style.display = "none";
});';
$js .= '$( "#active_loans_per_um" ).click(function() {
   var a = document.getElementById("active_loans_per_loan_officer");
   a.style.display = "block";
   var a = document.getElementById("awp-final");
   a.style.display = "none";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
   var v = document.getElementById("recovery_percent");
   v.style.display = "none";
});';
$js .= '$( "#recovery_percentage" ).click(function() {
   var v = document.getElementById("recovery_percent");
   v.style.display = "block";
   var a = document.getElementById("active_loans_per_loan_officer");
   a.style.display = "none";
   var a = document.getElementById("overdue_pending");
   a.style.display = "none";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
});';
$js .= '$( "#awp" ).click(function() {
 var a = document.getElementById("awp-final");
   a.style.display = "none";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
   var p = document.getElementById("overdue_pending");
   p.style.display = "none";
});';
$this->registerJs($js);
?>

<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <?php
        echo $this->render('_search_overall', [
            'model' => $searchModel,
            'regions' => $regions,
            'areas' => $areas,
            'branches' => $branches,
            'projects' => $projects

        ]);
        ?>
        <br>
        <br>
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_content1" id="tarvsach" role="tab" data-toggle="tab"
                                                      aria-expanded="true"><strong>Target vs Achievement</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_content2" id="sustainability" role="tab" data-toggle="tab"
                                                aria-expanded="true"><strong>Sustainability</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_content3" id="loan_management_cost" role="tab"
                                                data-toggle="tab" aria-expanded="true"><strong>Loan Management
                        Cost</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_content4" role="tab" id="awp-overdue" data-toggle="tab"
                                                aria-expanded="false"><strong>Overdue</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_conten5" role="tab" id="active_loans_per_um"
                                                data-toggle="tab" aria-expanded="false"><strong>Active Loans/UM</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_conten6" role="tab" id="recovery_percentage"
                                                data-toggle="tab" aria-expanded="false"><strong>Recovery
                        Percentage</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_content7" role="tab" id="overdue-pending" data-toggle="tab"
                                                aria-expanded="false"><strong>Deferred</strong></a>
            </li>
            <li role="presentation" class=""><a href="#tab_content8" role="tab" id="awp" data-toggle="tab"
                                                aria-expanded="false"><strong>AWP</strong></a>
            </li>
        </ul>
        <br>
        <br>

        <div id="tar-vs-ach">
            <div class="row">
                <div class="col-md-10">
                    <!--<h3><? /*= Html::encode($this->title) */ ?></h3>-->
                </div>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                <!--<p>
        <? /*= Html::a('Create Awp Target Vs Achievement', ['create'], ['class' => 'btn btn-success']) */ ?>
    </p>-->

                <form action="index">
                    <input type="hidden" name="AwpTargetVsAchievementSearch[region_id]"
                           value=<?= "$searchModel->region_id" ?>>
                    <input type="hidden" name="AwpTargetVsAchievementSearch[area_id]"
                           value=<?= "$searchModel->area_id" ?>>
                    <input type="hidden" name="AwpTargetVsAchievementSearch[month]" value=<?= "$searchModel->month" ?>>
                    <input type="hidden" name="AwpTargetVsAchievementSearch[month_from]"
                           value=<?= "$searchModel->month_from" ?>>
                    <input type="hidden" name="AwpTargetVsAchievementSearch[project_id]"
                           value=<?= "$searchModel->project_id" ?>>
                    <div class="col-md-2 pull-right" style="margin-top: 5px;margin-left: 40px">
                        <button type="submit" name="export" value="export" class="btn btn-primary pull-left"> Export
                        </button>
                    </div>
                    <br>
                    <br>
                </form>
            </div>
            <!--            ok-->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'showFooter' => true,
                // table-striped table-bordered table-hover
                'tableOptions' => ['class' => 'table table-bordered table-hover', 'style' => 'background-color:White;color:Black;font-size:20px'],
                //'footerOptions'=>['style'=>'background-color:Black;color:Black'],
                'rowOptions' => function ($model) {
                    if ($model->branch->status == 0) {
                        return ['style' => 'display:none'];
                    } else if (in_array($model->branch->code, \common\components\Helpers\AwpHelper::getClosedBranches())) {
                        return ['style' => 'background-color:red'];
                    } else if (($model->target_amount != 0) && ($model->achieved_amount / $model->target_amount) >= 1 && ($model->achieved_amount / $model->target_amount) <= 1.10) {
                        return ['style' => 'background-color:#90ee90'];
                    }/*else if($model->branch->type != 'branch'){
                       return ['style' => 'background-color:yellow'];
                   }*/
                },
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    /*['attribute'=>'region_id',
                        'value'=>'region.name'],
                    ['attribute'=>'area_id',
                        'value'=>'area.name'],*/
                    ['attribute' => 'branch_id',
                        'value' => function ($data) {

                            if (in_array($data->branch->code, \common\components\Helpers\AwpHelper::getAgriBranches())) {
                                return '<i style="color:green;" class="fa fa-leaf"></i>' . ' ' . $data->branch->name;
                                //return '<i style="color:white;" class="fa fa-house"></i>'. ' ' . $data->branch->code;
                            } else if ($data->branch->type != 'branch') {
                                return '<i style="color:green;" class="fa fa-home"></i>' . ' ' . $data->branch->name;
                            } else {
                                return $data->branch->name;
                            }
                        },
                        'label' => 'Branch Name',
                        'footer' => '<b>Total</b>',
                        'format' => 'html',
                        'footerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'headerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'header' => '<b style="color: White">Branch Name</b>',
                    ],
                    //'project_id',
                    //'month',
                    ['attribute' => 'target_loans',
                        //'format'=>'decimal',
                        'value' => function ($data) {
                            return number_format($data->target_loans);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'target_loans') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'headerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'header' => '<b style="color: White">Target Loans</b>',
                    ],
                    ['attribute' => 'target_amount',
                        //'format'=>'decimal',
                        'value' => function ($data) {
                            return number_format($data->target_amount);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'target_amount') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'headerOptions' => ['style' => 'background-color:#00a8ff;color:White'],
                        'header' => '<b style="color: White">Target Amount</b>',
                    ],
                    ['attribute' => 'achieved_loans',
                        //'format'=>'decimal',
                        'value' => function ($data) {
                            return number_format($data->achieved_loans);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'achieved_loans') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#0f6742;color:White'],
                        'headerOptions' => ['style' => 'background-color:#0f6742;color:White'],
                        'header' => '<b style="color: White">Achieved Loans</b>',
                    ],
                    ['attribute' => 'achieved_amount',
                        //'format'=>'decimal',
                        'value' => function ($data) {
                            return number_format($data->achieved_amount);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'achieved_amount') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#0f6742;color:White'],
                        'headerOptions' => ['style' => 'background-color:#0f6742;color:White'],
                        'header' => '<b style="color: White">Achieved Amount</b>',
                    ],
                    ['attribute' => 'loans_dif',
                        //'format'=>'decimal',
                        'label' => 'Loans Difference',
                        'value' => function ($data) {
                            return number_format($data->achieved_loans - $data->target_loans);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotalTwoColumns($dataProvider->getModels(), 'achieved_loans', 'target_loans') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#D2691E;color:White'],
                        'headerOptions' => ['style' => 'background-color:#D2691E;color:White'],
                        'header' => '<b style="color: White">Loans Difference</b>',
                    ],
                    ['attribute' => 'amount_dif',
                        //'format'=>'decimal',
                        'label' => 'Amount Difference',
                        'value' => function ($data) {
                            return number_format($data->achieved_amount - $data->target_amount);
                        },
                        'footer' => '<b>' . \common\components\Helpers\AwpHelper::getTotalTwoColumns($dataProvider->getModels(), 'achieved_amount', 'target_amount') . '</b>',
                        'footerOptions' => ['style' => 'background-color:#D2691E;color:White'],
                        'headerOptions' => ['style' => 'background-color:#D2691E;color:White'],
                        'header' => '<b style="color: White">Amount Difference</b>',
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


        <div id="branch-sustainability" style="display: none">
            <?php
            echo $this->render('/awp-branch-sustainability/index', [   //ok
                'dataProvider' => $dataProvider_branch_sus,
                'filterModel' => $searchModel_branch_sus,
            ]);
            ?>
        </div>

        <div id="overdue" style="display: none">

            <?php
            echo $this->render('/awp-overdue/index', [     //ok
                'dataProvider' => $dataProvider_overdue,
                'filterModel' => $searchModel_overdue,

            ]);
            ?>
        </div>

        <div id="branch-loan-management-cost" style="display: none">
            <?php
            echo $this->render('/awp-loan-management-cost/index', [   //ok
                'dataProvider' => $dataProvider_branch_mang,
                'filterModel' => $searchModel_branch_mang,
            ]);
            ?>
        </div>

        <div id="overdue_pending" style="display: none">
            <?php
            echo $this->render('/awp-overdue/pending_index', [   //ok
                'dataProvider' => $dataProvider_overdue_pending,
                'filterModel' => $searchModel_overdue_ending,
            ]);
            ?>
        </div>

        <div id="awp-final" style="display: none">
            <?php
            echo $this->render('/awp/index', [    //ok
                'dataProvider' => $dataProvider_awp_final,
                'filterModel' => $searchModel_awp_final,
            ]);
            ?>
        </div>

        <!--        <div id="awp-new" style="display: none">-->
        <?php
        //
        //                 $this->render('/awp/index', [
        //                    'dataProvider' => $dataProvider_awp_final,
        //                    'filterModel' => $searchModel_awp_final,
        //                ]);
        //
        //                echo $this->render('/awp-final/index', [
        //                    'dataProvider' => $dataProvider_awp_final,
        //                    'filterModel' => $searchModel_awp_final,
        //                ]);
        ?>
        <!--        </div>-->
        <div id="active_loans_per_loan_officer" style="display: none">
            <?php

            echo $this->render('/awp-loans-um/index', [      //ok
                'dataProvider' => $dataProvider_loans_per_um,
                'filterModel' => $searchModel_loans_per_um,
            ]);
            ?>
        </div>
        <div id="recovery_percent" style="display: none">
            <?php

            echo $this->render('/awp-recovery-percentage/index', [     //ok
                'dataProvider' => $dataProvider_recovery_percent,
                'filterModel' => $searchModel_recovery_percent,
            ]);
            ?>
        </div>
    </div>
</div>
