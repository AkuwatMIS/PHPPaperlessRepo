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
$dataProvider->pagination->pageSize = 50;
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <a class="pull-right btn btn-primary" href="/loans/disbursement-summary">Daily Disbursement Summary Report</a>
        <h6 class="address-heading"><span class="fa fa-list"></span> Disbursement Summary Report(Monthly)</h6>
        <?php echo $this->render('_search_loan_summary', [
            'model' => $searchModel,
            'regions' => $regions,
            'projects' => $projects,
        ]); ?>
        <div class="dropdown pull-right" style="margin-bottom:5px">
            <button title="Export to CSV" class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                <i class="glyphicon glyphicon-export"></i>
                <span class="caret"></span></button>

            <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation">
                    <form action="/account-reports/disbursement-summary">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[region_id]"
                               value="<?php echo $searchModel->region_id ?>">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[area_id]" value="<?php echo $searchModel->area_id ?>">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[branch_id]"
                               value="<?php echo $searchModel->branch_id ?>">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[report_date]"
                               value="<?php echo $searchModel->report_date ?>">

                        <input type="hidden" name="ArcAccountReportDetailsSearch[from_date]"
                               value="<?php echo $searchModel->from_date ?>">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[to_date]"
                               value="<?php echo $searchModel->to_date ?>">
                        <input type="hidden" name="ArcAccountReportDetailsSearch[project_ids]"
                               value="<?php if(!empty($searchModel->project_ids)) { echo implode(', ', $searchModel->project_ids);}?>">

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
