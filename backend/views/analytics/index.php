<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AnalyticsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Analytics';
$this->params['breadcrumbs'][] = $this->title;
$js='$( "#tarvsach" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "block";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
      var a = document.getElementById("awp-final");
   a.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
});';
$js.='$( "#awp-overdue" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "block";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
      var a = document.getElementById("awp-final");
   a.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   a=b.style.display = "none";
});';
$js.='$( "#sustainability" ).click(function() {
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "block";
      var a = document.getElementById("awp-final");
   a.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
});';
$js.='$( "#awp" ).click(function() {
   var a = document.getElementById("awp-final");
   a.style.display = "block";
   var x = document.getElementById("tar-vs-ach");
   x.style.display = "none";
   var y = document.getElementById("overdue");
   y.style.display = "none";
   var z = document.getElementById("branch-sustainability");
   z.style.display = "none";
   var b = document.getElementById("branch-loan-management-cost");
   b.style.display = "none";
});';
$js.='$( "#loan_management_cost" ).click(function() {
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
});';
$this->registerJs($js);
CrudAsset::register($this);

?>

<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab_content1" id="tarvsach" role="tab" data-toggle="tab" aria-expanded="true"><strong>Paperless App</strong></a>
    </li>
    <li role="presentation" class=""><a href="#tab_content1" id="sustainability" role="tab" data-toggle="tab" aria-expanded="true"><strong>Reports App</strong></a>
    </li>
    <!--<li role="presentation" class=""><a href="#tab_content2" id="loan_management_cost" role="tab" data-toggle="tab" aria-expanded="true"><strong>Loan Management Cost</strong></a>
    </li>
    <li role="presentation" class=""><a href="#tab_content3" role="tab" id="awp-overdue" data-toggle="tab" aria-expanded="false"><strong>Overdue</strong></a>
    </li>
    <li role="presentation" class=""><a href="#tab_content4" role="tab" id="awp" data-toggle="tab" aria-expanded="false"><strong>Annual Work Plan</strong></a>
    </li>-->
</ul>
<br>
<div id="tar-vs-ach">
    <div class="awp-target-vs-achievement-index">
        <div class="analytics-index">
            <div id="ajaxCrudDatatable">
                <?=GridView::widget([
                    'id'=>'crud-datatable',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                   // 'pjax'=>true,
                    'columns' => require(__DIR__.'/_columns.php'),
                    'toolbar'=> [
                        ['content'=>
                        /*Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                        ['role'=>'modal-remote','title'=> 'Create new Analytics','class'=>'btn btn-default']).*/
                            Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                            '{toggleData}'.
                            '{export}'
                        ],
                    ],
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    'panel' => [
                        'type' => 'primary',
                        'heading' => '<i class="glyphicon glyphicon-list"></i> Analytics listing(Paperless App)',
                        //'before'=>'<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>',
                        /*'after'=>BulkButtonWidget::widget([
                                    'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
                                        ["bulkdelete"] ,
                                        [
                                            "class"=>"btn btn-danger btn-xs",
                                            'role'=>'modal-remote-bulk',
                                            'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                                            'data-request-method'=>'post',
                                            'data-confirm-title'=>'Are you sure?',
                                            'data-confirm-message'=>'Are you sure want to delete this item'
                                        ]),
                                ]).
                                '<div class="clearfix"></div>',*/
                    ]
                ])?>
            </div>
        </div>
        <?php /*Modal::begin([
            "id"=>"ajaxCrudModal",
            "footer"=>"",// always need it for jquery plugin
        ])*/?><!--
        --><?php /*Modal::end(); */?>
    </div>
</div>


<div id="branch-sustainability" style="display: none">
    <div class="awp-target-vs-achievement-index">
        <!--<div class="analytics-index">
            <div id="ajaxCrudDatatable">-->
                <?=GridView::widget([
                    'id'=>'crud-datatable',
                    'dataProvider' => $dataProvider_report,
                    'filterModel' => $searchModel_report,
                   // 'pjax'=>true,
                    'columns' => require(__DIR__.'/_columns.php'),
                    'toolbar'=> [
                        ['content'=>
                            Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                            '{toggleData}'.
                            '{export}'
                        ],
                    ],
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    'panel' => [
                        'type' => 'primary',
                        'heading' => '<i class="glyphicon glyphicon-list"></i> Analytics listing(Reports App)',
                    ]
                ])?>
            <!--</div>
        </div>-->
       <!-- <?php /*Modal::begin([
            "id"=>"ajaxCrudModal",
            "footer"=>"",// always need it for jquery plugin
        ])*/?>
        --><?php /*Modal::end(); */?>
    </div>
</div>
<div id="overdue" style="display: none">
    <h2>gg</h2>
</div>
<div id="branch-loan-management-cost" style="display: none">
    <h1>jhk</h1>
</div>
<div id="awp-final" style="display: none">

</div>






