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

$this->title = 'Disbursement-Summary';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$data['dataProvider']->pagination->pageSize = 50;
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <a class="pull-right btn btn-primary" href="/account-reports/disbursement-summary">Monthly Disbursement Summary Report</a>
        <h6 class="address-heading"><span class="fa fa-list"></span> Disbursement Summary Report(Daily)</h6>
        <?php echo $this->render('_search_loan_summary', [
            'model' => $data['searchModel'],
            'regions' => $data['regions'],
            'projects' => $data['projects'],
            'crop_types' => $data['crop_types'],
        ]); ?>
        <div class="dropdown pull-right" style="margin-bottom:5px">
            <button title="Export to CSV" class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                <i class="glyphicon glyphicon-export"></i>
                <span class="caret"></span></button>

            <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation">
                    <form action="/loans/disbursement-summary">
                        <input type="hidden" name="LoansSearch[region_id]"
                               value="<?php echo $data['searchModel']->region_id ?>">
                        <input type="hidden" name="LoansSearch[area_id]" value="<?php echo $data['searchModel']->area_id ?>">
                        <input type="hidden" name="LoansSearch[branch_id]"
                               value="<?php echo $data['searchModel']->branch_id ?>">
                        <input type="hidden" name="LoansSearch[date_disbursed]"
                               value="<?php echo $data['searchModel']->date_disbursed ?>">
                        <input type="hidden" name="LoansSearch['project_ids']"
                               value="<?php echo $data['searchModel']->project_ids ?>">

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
                    'dataProvider' => $data['dataProvider'],
                    //'filterModel' => $searchModel,
                    'columns' => require(__DIR__ . '/_columns_loan_summary.php'),
                    'footerRowOptions'=>['style'=>'font-weight:bold;'],
                    'showFooter' => true,
                ]) ?>
            </div>
        </div>
    </div>
    <?php Modal::begin([
        "id" => "ajaxCrudModal",
        "footer" => "",// always need it for jquery plugin
    ]) ?>
    <?php Modal::end(); ?>
