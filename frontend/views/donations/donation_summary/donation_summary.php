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

$this->title = 'Donations-Summary';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$dataProvider->pagination->pageSize = 50;
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <a class="pull-right btn btn-primary" href="/account-reports/donation-summary">Monthly Donation Summary Report</a>
        <h6 class="address-heading"><span class="fa fa-gift"></span> Donation Summary Report(Daily)</h6>
        <?php echo $this->render('_search_donation_summary', [
            'model' => $searchModel,
            'regions' => $regions,
            'projects' => $projects,
            'crop_types' => $crop_types,
        ]); ?>
        <div class="dropdown pull-right" style="margin-bottom:5px">
            <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                <i class="glyphicon glyphicon-export"></i>
                <span class="caret"></span></button>

            <ul class="dropdown-menu pull-left" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation">
                    <form action="/donations/donation-summary">
                        <input type="hidden" name="DonationsSearch[region_id]"
                               value="<?php echo $searchModel->region_id ?>">
                        <input type="hidden" name="DonationsSearch[area_id]"
                               value="<?php echo $searchModel->area_id ?>">
                        <input type="hidden" name="DonationsSearch[branch_id]"
                               value="<?php echo $searchModel->branch_id ?>">
                        <input type="hidden" name="DonationsSearch[receive_date]"
                               value="<?php echo $searchModel->receive_date ?>">
                        <button type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
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
                    'columns' => require(__DIR__ . '/_columns_donation_summary.php'),
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
