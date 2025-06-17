<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\export\ExportMenu;
use yii2tech\csvgrid\CsvGrid;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RecoveriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Operations-Summary';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$dataProvider->pagination->pageSize = 50;
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-bank"></span> Operation Summary Report</h6>
        <?php echo $this->render('_search_operation_summary', [
            'model' => $searchModel,
            'regions' => $regions,
            'projects' => $projects,
            'crop_types' => $crop_types,
        ]); ?>
        <!-- --><?php /*echo'<pre>'; print_r($searchModel->region_id);*/ ?>
        <div class="dropdown" style="width: 10%">
            <button title="Export to CSV" class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                <i class="glyphicon glyphicon-export"></i>
                <span class="caret"></span></button>

            <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation">
                    <form action="/operations/operations-summary">
                        <input type="hidden" name="OperationsSearch[region_id]"
                               value="<?php echo $searchModel->region_id ?>">
                        <input type="hidden" name="OperationsSearch[area_id]"
                               value="<?php echo $searchModel->area_id ?>">
                        <input type="hidden" name="OperationsSearch[branch_id]"
                               value="<?php echo $searchModel->branch_id ?>">
                        <input type="hidden" name="OperationsSearch[receive_date]"
                               value="<?php echo $searchModel->receive_date ?>">

                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>

                    </form>
                </li>
            </ul>
        </div>

        <div class="recoveries-index">
            <div id="ajaxCrudDatatable">
                <?= GridView::widget([
                    'id' => 'crud-datatable',
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'columns' => require(__DIR__ . '/_columns_operation_summary.php'),
                    'footerRowOptions'=>['style'=>'font-weight:bold;'],
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
