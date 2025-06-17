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

$chart_data = '';
foreach ($dataProvider->models as $model) {
    $chart_data .= '["' . $model->api . '",' . $model->count . '],';
}
$chart_data = rtrim($chart_data, ',');
$chart_data1 = '';
foreach ($dataProvider_report->models as $model) {
    $chart_data1 .= '["' . $model->api . '",' . $model->count . '],';
}
$chart_data1 = rtrim($chart_data1, ',');
/*echo '<pre>';
print_r($dataProvider->models);
die();*/
CrudAsset::register($this);
$js = 'google.charts.load(\'current\', {\'packages\':[\'bar\']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          [\'\', \'\'],
          ' . $chart_data . '
        ]);

        var options = {
          width: 1100,
          legend: { position: \'none\' },
          chart: {
            title: \'Apis Analytics\',
             },
          axes: {
            x: {
            }
          },
          bar: { groupWidth: "50%" }
        };

        var chart = new google.charts.Bar(document.getElementById(\'types\'));
        // Convert the Classic options to Material options.
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }';
$js.= 'google.charts.load(\'current\', {\'packages\':[\'bar\']});
      google.charts.setOnLoadCallback(drawStufff);

      function drawStufff() {
        var data1 = new google.visualization.arrayToDataTable([
          [\'\', \'\'],
          ' . $chart_data1 . '
        ]);

        var optionss = {
          width: 1100,
          legend: { position: \'none\' },
          chart: {
            title: \'Apis Analytics\',
             },
          axes: {
            x: {
            }
          },
          bar: { groupWidth: "50%" }
        };

        var chart = new google.charts.Bar(document.getElementById(\'typess\'));
        // Convert the Classic options to Material options.
        chart.draw(data1, google.charts.Bar.convertOptions(optionss));
      }';
$this->registerJs($js);

$js.= '$( "#tarvsach" ).click(function() {
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
$js.= '$( "#awp-overdue" ).click(function() {
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
$js.= '$( "#sustainability" ).click(function() {
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
$js.= '$( "#awp" ).click(function() {
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
$js.= '$( "#loan_management_cost" ).click(function() {
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
//die();
?>

<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab_content1" id="tarvsach" role="tab" data-toggle="tab"
                                              aria-expanded="true"><strong>Paperless App</strong></a>
    </li>
    <li role="presentation" class=""><a href="#tab_content1" id="sustainability" role="tab" data-toggle="tab"
                                        aria-expanded="true"><strong>Reports App</strong></a>
    </li>
</ul>
<br>
<div id="tar-vs-ach">
    <div class="awp-target-vs-achievement-index">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center" style="margin-bottom: 10px;">
                    <div id="types"></div>
                </div>
            </div>
        </div>
        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                //'pjax' => true,
                'columns' => require(__DIR__ . '/_columns_analytics.php'),
                'toolbar' => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) .
                        '{toggleData}' .
                        '{export}'
                    ],
                ],
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'panel' => [
                    'type' => 'primary',
                    'heading' => '<i class="glyphicon glyphicon-list"></i> Analytics listing(Paperless App)',
                ]
            ]) ?>
        </div>
    </div>
</div>


<div id="branch-sustainability" style="display: none">
    <div class="awp-target-vs-achievement-index">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center" style="margin-bottom: 10px;">
                    <div id="typess"></div>
                </div>
            </div>
        </div>

        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider_report,
                'filterModel' => $searchModel_report,
                //'pjax' => true,
                'columns' => require(__DIR__ . '/_columns_analytics.php'),
                'toolbar' => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) .
                        '{toggleData}' .
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
            ]) ?>
        </div>
    </div>
</div>
<div id="overdue" style="display: none">
</div>
<div id="branch-loan-management-cost" style="display: none">
</div>
<div id="awp-final" style="display: none">
</div>