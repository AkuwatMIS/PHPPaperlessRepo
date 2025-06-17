<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\export\ExportMenu;
use yii2tech\csvgrid\CsvGrid;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecoveriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Awp Project Wise Report(Budget)';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$dataProvider->pagination->pageSize = 50;
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

?>
<?php /*echo $this->render('_search_project_wise_budget', [
    'model' => $searchModel,
    'months'=>$months
]); */ ?>
<a style="background-color:ghostwhite"></a>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="recoveries-index">
            <div id="ajaxCrudDatatable">
                <h4><i class="glyphicon glyphicon-list"></i> <b style="font-size: 30px">AWP</b> Project Wise Report-(<?php echo $date ?>)</h4>
                <form action="/awp/awp-project-wise-budget">
                    <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default pull-right">Export to CSV</button>
                </form>
                <br>
                <br>
                <?= GridView::widget([
                    'id' => 'crud-datatable',
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'pjax' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
                    'columns' => require(__DIR__ . '/_columns_awp_project_wise_budget.php'),
                    'toolbar' => [
                        //$fullExportMenu,
                        '{export}',

                    ],

                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    /*'panel' => [
                        'type' => 'none',
                        'heading' => '<i class="glyphicon glyphicon-list"></i> <b style="font-size: 20px">AWP</b> Project Wise Report-(' . $date . ')',
                        'headingOptions' => ['style' => 'background-color:ghostwhite'],

                    ],*/
                    'pager' => [
                        'options' => ['class' => 'pagination'],   // set clas name used in ui list of pagination
                        'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
                        'nextPageLabel' => 'Next',   // Set the label for the "next" page button
                        'firstPageLabel' => 'First',   // Set the label for the "first" page button
                        'lastPageLabel' => 'Last',    // Set the label for the "last" page button
                        'nextPageCssClass' => 'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass' => 'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass' => 'last',    // Set CSS class for the "last" page button
                        'maxButtonCount' => 10,    // Set maximum number of page buttons that can be displayed
                    ],
                    'export' => [
                        'target' => GridView::TARGET_BLANK
                    ],
                    'showFooter' => true,
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
